<?php

namespace App\Classes;

use Illuminate\Support\Facades\DB;

/**
 * Hotspot tickets: a row in hotspot_tickets plus the router hotspot user CODE / CODE on the plan's
 * profile hypbits-hs-<plan_id>. The router user is created by the crontab agent (task
 * provision_tickets); crontab hotspot_tickets.php then tracks first login, usage and the end of the
 * ticket every minute. Manual (cash) tickets use this now; paid tickets (Stage 8) will too.
 */
class HotspotTickets
{
    // no 0/O, 1/I/L: easy to read out and type
    const CODE_ALPHABET = "ABCDEFGHJKMNPQRSTUVWXYZ23456789";
    const CODE_LENGTH = 6;
    const STATUSES = ["pending", "active", "used", "expired", "failed"];

    public static function newCode()
    {
        $alphabet = self::CODE_ALPHABET;
        for ($try = 0; $try < 20; $try++) {
            $code = "";
            for ($i = 0; $i < self::CODE_LENGTH; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            // also not a payment's account code (it becomes that payment's ticket code)
            if (!DB::connection("mysql2")->table("hotspot_tickets")->where("ticket_code", $code)->exists()
                && !DB::connection("mysql2")->table("hotspot_payments")->where("account_code", $code)->exists()) {
                return $code;
            }
        }
        throw new \RuntimeException("Could not find a free ticket code.");
    }

    /**
     * Create a ticket (not yet on the router). The caller then queues provisioning with
     * provision(). One payment can only ever have one ticket.
     *
     * @return int ticket_id
     */
    public static function issue($plan, $router, $phone, $mac, $source, $payment_id = null, $created_by = null, $code = null)
    {
        $now = now()->format('YmdHis');
        for ($try = 0; $try < 5; $try++) {
            try {
                return DB::connection("mysql2")->table("hotspot_tickets")->insertGetId([
                    // a paid ticket keeps the payment's account code
                    "ticket_code" => $code && $try == 0 ? $code : self::newCode(),
                    "phone" => $phone ?: null,
                    "mac_address" => $mac ?: null,
                    "plan_id" => $plan->plan_id,
                    "router_id" => $router->router_id,
                    "payment_id" => $payment_id,
                    "source" => $source,
                    "amount" => $plan->price,
                    "status" => "pending",
                    "provision_status" => "pending",
                    "provision_attempts" => 0,
                    "created_by" => $created_by,
                    "date_created" => $now,
                    "date_changed" => $now,
                    "deleted" => 0,
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // a payment that already has a ticket: return that one
                if ($payment_id && str_contains($e->getMessage(), "uniq_payment_id")) {
                    return DB::connection("mysql2")->table("hotspot_tickets")->where("payment_id", $payment_id)->value("ticket_id");
                }
                // code taken in the meantime: try another
                if (!str_contains($e->getMessage(), "uniq_ticket_code")) {
                    throw $e;
                }
            }
        }
        throw new \RuntimeException("Could not create the ticket.");
    }

    // put tickets on their router (crontab agent); the minute job retries if the router is offline
    public static function provision($router_id, array $ticket_ids, $database = null, $triggered_by = null)
    {
        return HotspotAgent::queue($router_id, "provision_tickets", [
            "target" => count($ticket_ids) . " ticket(s)",
            "ticket_ids" => array_values(array_map("intval", $ticket_ids)),
        ], $database, $triggered_by);
    }

    /**
     * Send a ticket's code by SMS with the org's own SMS settings, only when SMS is on for the org,
     * and log it in sms_tables. Works without a session (mysql2 on the org DB).
     *
     * @return array [sent, message for the admin]
     */
    public static function sendCode($ticket, $plan, $organization)
    {
        if (!$organization || (int) $organization->send_sms !== 1) {
            return [false, "SMS is turned off for your account, so the code was not sent."];
        }
        $controller = new \App\Http\Controllers\Controller();
        $settings = $controller->getSmsSettings();
        if (!$settings) {
            return [false, "Your SMS settings are not complete, so the code was not sent."];
        }
        $text = self::smsText($ticket, $plan, $organization->organization_name);
        $result = $controller->GlobalSendSMS($text, $ticket->phone, $settings['sms_api_key'], $settings['sms_sender'], $settings['sms_shortcode'], $settings['sms_partner_id']);
        DB::connection("mysql2")->table("sms_tables")->insert([
            "sms_content" => $text,
            "date_sent" => now()->format('YmdHis'),
            "recipient_phone" => $ticket->phone,
            "sms_status" => $result ? 1 : 0,
            "account_id" => 0,
            "sms_type" => 1,
            "channel" => "sms",
        ]);
        return $result ? [true, "Code sent by SMS to " . $ticket->phone . "."] : [false, "The SMS to " . $ticket->phone . " could not be sent."];
    }

    /**
     * Time and data used / left, for display.
     */
    public static function usage($ticket, $plan)
    {
        $total_seconds = $plan ? (int) $plan->duration_minutes * 60 : 0;
        if ($plan && $plan->validity_type == "elapsed") {
            $left = $ticket->expires_at ? max(0, strtotime($ticket->expires_at) - time()) : $total_seconds;
            $used = $total_seconds - $left;
        } else {
            $used = (int) $ticket->uptime_used_seconds;
            $left = max(0, $total_seconds - $used);
        }
        if (in_array($ticket->status, ["used", "expired", "failed"])) {
            $left = 0;
        }
        $data_used_mb = round(((int) $ticket->bytes_in + (int) $ticket->bytes_out) / 1048576, 1);
        $data_left_mb = ($plan && $plan->data_limit_mb) ? max(0, $plan->data_limit_mb - $data_used_mb) : null;
        return [
            "time_used" => self::seconds($used),
            "time_left" => self::seconds($left),
            "data_used" => $data_used_mb >= 1024 ? round($data_used_mb / 1024, 2) . " GB" : $data_used_mb . " MB",
            "data_left" => $data_left_mb === null ? "Unlimited" : ($data_left_mb >= 1024 ? round($data_left_mb / 1024, 2) . " GB" : round($data_left_mb, 1) . " MB"),
        ];
    }

    public static function seconds($seconds)
    {
        $seconds = max(0, (int) $seconds);
        $days = intdiv($seconds, 86400);
        $hours = intdiv($seconds % 86400, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        if ($days > 0) {
            return $days . "d " . $hours . "h";
        }
        if ($hours > 0) {
            return $hours . "h " . $minutes . "m";
        }
        return $minutes . "m";
    }

    public static function smsText($ticket, $plan, $organization_name)
    {
        return "Your " . $organization_name . " WiFi code: " . $ticket->ticket_code . ". Plan: " . $plan->plan_name . " ("
            . HotspotPlans::durationText($plan->duration_minutes) . "). Connect to the WiFi and enter the code on the login page.";
    }
}
