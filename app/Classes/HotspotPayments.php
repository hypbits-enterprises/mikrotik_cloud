<?php

namespace App\Classes;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Hotspot payments from the captive portal. Every purchase gets a random account code: it is the
 * STK AccountReference / the Paybill account number the customer types, and it becomes the ticket
 * code. A payment is completed exactly once (hotspot_transactions row + paid payment + one ticket),
 * whichever confirmation arrives first: the STK callback, the Daraja STK query, or the C2B
 * confirmation (shared Paybill: through /Transact's hotspot guard). All methods work without a
 * session: mysql2 must point at the org DB.
 */
class HotspotPayments
{
    // STK push: ask Daraja after this many seconds without a callback, at most this often
    const QUERY_AFTER_SECONDS = 20;
    const QUERY_EVERY_SECONDS = 10;
    // a payment nobody completed is given up after this long
    const EXPIRE_MINUTES = 10;

    /**
     * How this org takes hotspot payments (org settings keyword rows).
     *
     * @return array ['mode' => shared|separate, 'shortcode', 'shortcode_type' => paybill|till,
     *               'stk' => bool (keys present), 'keys' => billing|hotspot]
     */
    public static function config()
    {
        $rows = DB::connection("mysql2")->table("settings")->where("deleted", "0")
            ->whereIn("keyword", ["hotspot_paybill_mode", "hotspot_shortcode", "hotspot_shortcode_type", "paybill", "consumer_key", "consumer_secret", "passkey",
                "hotspot_consumer_key", "hotspot_consumer_secret", "hotspot_passkey"])
            ->pluck("value", "keyword");
        $filled = function ($name) use ($rows) {
            return isset($rows[$name]) && trim((string) $rows[$name]) !== "";
        };
        if (($rows["hotspot_paybill_mode"] ?? "shared") === "separate") {
            return [
                "mode" => "separate",
                "shortcode" => trim((string) ($rows["hotspot_shortcode"] ?? "")),
                "shortcode_type" => ($rows["hotspot_shortcode_type"] ?? "paybill") === "till" ? "till" : "paybill",
                "stk" => $filled("hotspot_shortcode") && $filled("hotspot_consumer_key") && $filled("hotspot_consumer_secret") && $filled("hotspot_passkey"),
                "keys" => "hotspot",
            ];
        }
        return [
            "mode" => "shared",
            "shortcode" => trim((string) ($rows["paybill"] ?? "")),
            "shortcode_type" => "paybill",
            "stk" => $filled("paybill") && $filled("consumer_key") && $filled("consumer_secret") && $filled("passkey"),
            "keys" => "billing",
        ];
    }

    // a code that is neither a ticket code nor another payment's account code
    public static function newAccountCode()
    {
        for ($try = 0; $try < 20; $try++) {
            $code = HotspotTickets::newCode();
            if (!DB::connection("mysql2")->table("hotspot_payments")->where("account_code", $code)->exists()) {
                return $code;
            }
        }
        throw new \RuntimeException("Could not find a free account code.");
    }

    // the org's callback base: /hs-callback/stk/{org_key}/{callback_token}; null without keys
    public static function callbackUrl($organization_id)
    {
        $settings = DB::table("org_hotspot_settings")->where("organization_id", $organization_id)->first();
        if (!$settings || !$settings->org_key || !$settings->callback_token) {
            return null;
        }
        return config("hotspot.portal_url") . "/hs-callback/stk/" . $settings->org_key . "/" . $settings->callback_token;
    }

    private static function mpesa($organization, $config)
    {
        return new MpesaService([
            "database" => $organization->organization_database,
            "callback_url" => self::callbackUrl($organization->organization_id) ?: "https://mydomain.com/path",
            "keys" => $config["keys"],
        ]);
    }

    /**
     * Start a purchase. method stk = M-Pesa prompt on the phone; paybill = the customer pays to the
     * Paybill with the account code shown on the portal.
     *
     * @return array [payment row or null, error message or null]
     */
    public static function start($organization, $router, $plan, $phone, $mac, $ip, $method)
    {
        $config = self::config();
        if ($method === "stk" && !$config["stk"]) {
            return [null, "Paying by M-Pesa prompt is not available. Use the Paybill option."];
        }
        if ($method === "paybill" && ($config["shortcode"] === "" || $config["shortcode_type"] !== "paybill")) {
            return [null, "Paying to a Paybill is not available here."];
        }
        $now = now()->format('YmdHis');
        $payment_id = null;
        for ($try = 0; $try < 5 && !$payment_id; $try++) {
            try {
                $payment_id = DB::connection("mysql2")->table("hotspot_payments")->insertGetId([
                    "phone" => $phone,
                    "amount" => $plan->price,
                    "plan_id" => $plan->plan_id,
                    "router_id" => $router->router_id,
                    "mac_address" => $mac,
                    "ip_address" => $ip,
                    "account_code" => self::newAccountCode(),
                    "method" => $method,
                    "public_token" => bin2hex(random_bytes(16)),
                    "status" => "pending",
                    "date_created" => $now,
                    "date_changed" => $now,
                    "deleted" => 0,
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                if (!str_contains($e->getMessage(), "uniq_account_code")) {
                    throw $e;
                }
            }
        }
        $payment = self::find($payment_id);
        if ($method !== "stk") {
            return [$payment, null];
        }

        $mpesa = self::mpesa($organization, $config);
        if ($mpesa->configError) {
            self::fail($payment, "config", $mpesa->configError);
            return [null, "Paying by M-Pesa prompt is not available right now."];
        }
        try {
            $reply = $mpesa->stkPush($phone, (int) ceil($plan->price), $payment->account_code, "WiFi " . mb_substr($plan->plan_name, 0, 12));
        } catch (\Throwable $e) {
            $reply = ["errorMessage" => $e->getMessage()];
        }
        if (!is_array($reply) || empty($reply["CheckoutRequestID"])) {
            $reason = is_array($reply) ? ($reply["errorMessage"] ?? ($reply["CustomerMessage"] ?? "no reply")) : "no reply";
            Log::warning("Hotspot STK push not accepted", ["payment_id" => $payment->payment_id, "reason" => $reason]);
            self::fail($payment, "push", mb_substr($reason, 0, 250));
            return [null, "M-Pesa did not accept the payment request. Check the phone number and try again."];
        }
        DB::connection("mysql2")->table("hotspot_payments")->where("payment_id", $payment->payment_id)->update([
            "checkout_request_id" => $reply["CheckoutRequestID"],
            "merchant_request_id" => $reply["MerchantRequestID"] ?? null,
            "date_changed" => now()->format('YmdHis'),
        ]);
        return [self::find($payment->payment_id), null];
    }

    public static function find($payment_id)
    {
        return DB::connection("mysql2")->table("hotspot_payments")->where("payment_id", $payment_id)->first();
    }

    /**
     * While the portal waits: ask Daraja about an STK push whose callback has not arrived.
     */
    public static function refresh($organization, $payment)
    {
        if ($payment->status !== "pending" || $payment->method !== "stk" || !$payment->checkout_request_id) {
            return $payment;
        }
        $now = time();
        if ($now - strtotime($payment->date_created) < self::QUERY_AFTER_SECONDS) {
            return $payment;
        }
        if ($payment->last_query_at && $now - strtotime($payment->last_query_at) < self::QUERY_EVERY_SECONDS) {
            return $payment;
        }
        DB::connection("mysql2")->table("hotspot_payments")->where("payment_id", $payment->payment_id)->update(["last_query_at" => now()->format('YmdHis')]);
        $mpesa = self::mpesa($organization, self::config());
        try {
            $reply = $mpesa->stkQuery($payment->checkout_request_id);
        } catch (\Throwable $e) {
            return self::find($payment->payment_id);
        }
        self::applyStkResult($payment, $reply, "query");
        return self::find($payment->payment_id);
    }

    /**
     * Result of an STK push from the callback (stkCallback) or the query reply.
     */
    public static function applyStkResult($payment, $result, $from, $raw = null)
    {
        $code = isset($result["ResultCode"]) ? (string) $result["ResultCode"] : null;
        if ($code === null) {
            return; // still processing, or Daraja error: try again later
        }
        if ($code !== "0") {
            self::fail($payment, $code, $result["ResultDesc"] ?? "", $raw);
            return;
        }
        $meta = [];
        foreach ($result["CallbackMetadata"]["Item"] ?? [] as $item) {
            if (isset($item["Name"])) {
                $meta[$item["Name"]] = $item["Value"] ?? null;
            }
        }
        // the query reply has no receipt: a placeholder until the C2B confirmation brings it
        $receipt = $meta["MpesaReceiptNumber"] ?? ("STK-" . $payment->checkout_request_id);
        self::complete($payment, [
            "trans_id" => $receipt,
            "source" => "stk",
            "amount" => isset($meta["Amount"]) ? (float) $meta["Amount"] : (float) $payment->amount,
            "phone" => isset($meta["PhoneNumber"]) ? (string) $meta["PhoneNumber"] : $payment->phone,
            "trans_time" => isset($meta["TransactionDate"]) ? (string) $meta["TransactionDate"] : now()->format('YmdHis'),
            "raw" => $raw,
            "note" => $from === "query" ? "Confirmed by Daraja STK query." : null,
        ]);
    }

    public static function fail($payment, $code, $description, $raw = null)
    {
        DB::connection("mysql2")->table("hotspot_payments")->where("payment_id", $payment->payment_id)->where("status", "pending")->update([
            "status" => $code === "1032" ? "cancelled" : "failed",
            "result_code" => mb_substr((string) $code, 0, 10),
            "result_desc" => mb_substr((string) $description, 0, 255),
            "raw_callback" => $raw,
            "date_changed" => now()->format('YmdHis'),
        ]);
    }

    /**
     * Mark a payment paid, exactly once: hotspot_transactions row, payment paid, ticket (code =
     * account code), customer totals; then the ticket is queued to the router and SMSed.
     *
     * @return int|null ticket_id
     */
    public static function complete($payment, array $money)
    {
        $now = now()->format('YmdHis');
        $organization = self::organization();
        $result = DB::connection("mysql2")->transaction(function () use ($payment, $money, $now) {
            $locked = DB::connection("mysql2")->table("hotspot_payments")->where("payment_id", $payment->payment_id)->lockForUpdate()->first();
            if ($locked->status === "paid") {
                self::recordTransaction($locked, $money, "matched", $now);
                return ["ticket_id" => DB::connection("mysql2")->table("hotspot_tickets")->where("payment_id", $locked->payment_id)->value("ticket_id"), "new" => false];
            }
            if (!in_array($locked->status, ["pending", "expired", "failed", "cancelled"], true)) {
                return ["ticket_id" => null, "new" => false];
            }
            self::recordTransaction($locked, $money, "matched", $now);
            DB::connection("mysql2")->table("hotspot_payments")->where("payment_id", $locked->payment_id)->update([
                "status" => "paid",
                "mpesa_receipt" => str_starts_with($money["trans_id"], "STK-") ? null : $money["trans_id"],
                "result_code" => "0",
                "raw_callback" => $money["raw"] ?? $locked->raw_callback,
                "paid_at" => $now,
                "date_changed" => $now,
            ]);
            $plan = DB::connection("mysql2")->table("hotspot_plans")->where("plan_id", $locked->plan_id)->first();
            $router = DB::connection("mysql2")->table("remote_routers")->where("router_id", $locked->router_id)->first();
            $ticket_id = HotspotTickets::issue($plan, $router, $locked->phone, $locked->mac_address, $locked->method === "stk" ? "stk" : "paybill", $locked->payment_id, null, $locked->account_code);
            DB::connection("mysql2")->table("hotspot_transactions")->where("payment_id", $locked->payment_id)->update(["ticket_id" => $ticket_id]);
            DB::connection("mysql2")->statement(
                "INSERT INTO `hotspot_customers` (`phone`, `first_seen_at`, `last_purchase_at`, `purchase_count`, `total_spent`, `date_changed`, `deleted`) VALUES (?, ?, ?, 1, ?, ?, 0)
                 ON DUPLICATE KEY UPDATE `last_purchase_at` = VALUES(`last_purchase_at`), `purchase_count` = `purchase_count` + 1, `total_spent` = `total_spent` + VALUES(`total_spent`), `date_changed` = VALUES(`date_changed`)",
                [$locked->phone, $now, $now, $locked->amount, $now]
            );
            return ["ticket_id" => $ticket_id, "new" => true];
        });

        if ($result["new"] && $result["ticket_id"]) {
            $ticket = DB::connection("mysql2")->table("hotspot_tickets")->where("ticket_id", $result["ticket_id"])->first();
            HotspotTickets::provision($ticket->router_id, [$ticket->ticket_id], $organization ? $organization->organization_database : null, "hotspot-payment");
            if ($ticket->phone && $organization) {
                $plan = DB::connection("mysql2")->table("hotspot_plans")->where("plan_id", $ticket->plan_id)->first();
                HotspotTickets::sendCode($ticket, $plan, $organization);
            }
        }
        return $result["ticket_id"];
    }

    // one row per M-Pesa transaction; a placeholder STK-<checkout> row gets the real receipt later
    private static function recordTransaction($payment, array $money, $status, $now)
    {
        $existing = DB::connection("mysql2")->table("hotspot_transactions")->where("trans_id", $money["trans_id"])->first();
        if ($existing) {
            return;
        }
        $placeholder = $payment->checkout_request_id
            ? DB::connection("mysql2")->table("hotspot_transactions")->where("trans_id", "STK-" . $payment->checkout_request_id)->first()
            : null;
        if ($placeholder && !str_starts_with($money["trans_id"], "STK-")) {
            DB::connection("mysql2")->table("hotspot_transactions")->where("transaction_id", $placeholder->transaction_id)->update([
                "trans_id" => $money["trans_id"],
                "phone" => $money["phone"] ?? $placeholder->phone,
                "payer_name" => $money["payer_name"] ?? $placeholder->payer_name,
                "trans_time" => $money["trans_time"] ?? $placeholder->trans_time,
                "note" => "Receipt from the M-Pesa confirmation.",
                "date_changed" => $now,
            ]);
            DB::connection("mysql2")->table("hotspot_payments")->where("payment_id", $payment->payment_id)->whereNull("mpesa_receipt")->update(["mpesa_receipt" => $money["trans_id"]]);
            return;
        }
        if ($payment->status === "paid" && DB::connection("mysql2")->table("hotspot_transactions")->where("payment_id", $payment->payment_id)->exists()) {
            return; // already recorded under another receipt
        }
        DB::connection("mysql2")->table("hotspot_transactions")->insert([
            "trans_id" => $money["trans_id"],
            "source" => $money["source"],
            "amount" => $money["amount"],
            "phone" => $money["phone"] ?? null,
            "payer_name" => $money["payer_name"] ?? null,
            "bill_ref" => $money["bill_ref"] ?? $payment->account_code,
            "shortcode" => $money["shortcode"] ?? null,
            "trans_time" => $money["trans_time"] ?? null,
            "payment_id" => $payment->payment_id,
            "status" => $status,
            "note" => $money["note"] ?? null,
            "raw" => $money["raw"] ?? null,
            "date_created" => $now,
            "date_changed" => $now,
            "deleted" => 0,
        ]);
    }

    /**
     * Is this Paybill account number a hotspot account code? (/Transact guard, shared Paybill)
     */
    public static function isAccountCode($bill_ref)
    {
        $bill_ref = strtoupper(trim((string) $bill_ref));
        if (!preg_match('/^[A-Z0-9]{6}$/', $bill_ref)) {
            return false;
        }
        return DB::connection("mysql2")->table("hotspot_payments")->where("account_code", $bill_ref)->where("deleted", 0)->exists();
    }

    /**
     * /Transact guard (shared Paybill). True = it was a hotspot payment and is recorded; /Transact
     * then stops. Client accounts always win, and any error here leaves /Transact to work as before.
     */
    public static function handleBillingC2B($organization, array $c2b)
    {
        try {
            $bill_ref = trim((string) ($c2b["BillRefNumber"] ?? ""));
            if (!self::isAccountCode($bill_ref) || !HotspotAccess::enabledForOrg($organization->organization_id)) {
                return false;
            }
            if (DB::connection("mysql2")->table("client_tables")->where("deleted", "0")->where("client_account", $bill_ref)->exists()) {
                return false;
            }
            return self::fromC2B($c2b, "billing_c2b");
        } catch (\Throwable $e) {
            Log::error("Hotspot check in /Transact failed; handled as a normal payment", ["error" => $e->getMessage()]);
            return false;
        }
    }

    /**
     * A C2B confirmation (Paybill payment) whose account number is a hotspot account code.
     *
     * @param string $source billing_c2b (shared Paybill, via /Transact) or hotspot_c2b
     * @return bool handled (the caller does nothing else with it)
     */
    public static function fromC2B(array $c2b, $source)
    {
        $bill_ref = strtoupper(trim((string) ($c2b["BillRefNumber"] ?? "")));
        $payment = DB::connection("mysql2")->table("hotspot_payments")->where("account_code", $bill_ref)->where("deleted", 0)->first();
        if (!$payment) {
            return false;
        }
        $money = [
            "trans_id" => trim((string) ($c2b["TransID"] ?? "")),
            "source" => $source,
            "amount" => (float) ($c2b["TransAmount"] ?? 0),
            "phone" => isset($c2b["MSISDN"]) ? (string) $c2b["MSISDN"] : null,
            "payer_name" => trim(((string) ($c2b["FirstName"] ?? "")) . " " . ((string) ($c2b["LastName"] ?? ""))) ?: null,
            "bill_ref" => $bill_ref,
            "shortcode" => isset($c2b["BusinessShortCode"]) ? (string) $c2b["BusinessShortCode"] : null,
            "trans_time" => isset($c2b["TransTime"]) ? (string) $c2b["TransTime"] : null,
            "raw" => json_encode($c2b),
        ];
        if ($money["trans_id"] === "") {
            return false;
        }
        if ($payment->status !== "paid" && $money["amount"] + 0.001 < (float) $payment->amount) {
            // paid less than the package: kept for the ISP to decide
            self::recordTransaction($payment, $money + ["note" => "Paid KES " . $money["amount"] . " for a KES " . $payment->amount . " package."], "review", now()->format('YmdHis'));
            return true;
        }
        self::complete($payment, $money);
        return true;
    }

    /**
     * Payments still pending: ask Daraja about STK pushes older than 2 minutes, give up after
     * EXPIRE_MINUTES. Run every minute by php artisan hotspot:reconcile-payments.
     *
     * @return array counts
     */
    public static function reconcile($organization)
    {
        $counts = ["checked" => 0, "paid" => 0, "expired" => 0];
        $cutoff = now()->subMinutes(2)->format('YmdHis');
        $expire = now()->subMinutes(self::EXPIRE_MINUTES)->format('YmdHis');
        $pending = DB::connection("mysql2")->table("hotspot_payments")->where("status", "pending")->where("deleted", 0)
            ->where("date_created", "<=", $cutoff)->orderBy("payment_id")->limit(50)->get();
        foreach ($pending as $payment) {
            $counts["checked"]++;
            if ($payment->method === "stk" && $payment->checkout_request_id) {
                $after = self::refresh($organization, $payment);
                if ($after->status === "paid") {
                    $counts["paid"]++;
                    continue;
                }
                if ($after->status !== "pending") {
                    continue;
                }
            }
            if ($payment->date_created <= $expire) {
                DB::connection("mysql2")->table("hotspot_payments")->where("payment_id", $payment->payment_id)->where("status", "pending")
                    ->update(["status" => "expired", "result_desc" => "No payment confirmation within " . self::EXPIRE_MINUTES . " minutes.", "date_changed" => now()->format('YmdHis')]);
                $counts["expired"]++;
            }
        }
        return $counts;
    }

    // the org whose DB mysql2 points at (session-less callers)
    private static function organization()
    {
        $database = config("database.connections.mysql2.database");
        return DB::table("organizations")->where("organization_database", $database)->first();
    }
}
