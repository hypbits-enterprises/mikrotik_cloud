<?php

namespace App\Http\Controllers\Hotspot;

use App\Classes\HotspotAccess;
use App\Classes\HotspotPayments;
use App\Classes\MpesaService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Hotspot → Payments: hotspot earnings (hotspot_transactions: money received for hotspot, never a
 * PPPoE transaction) and the portal's payment attempts.
 */
class Payments extends Controller
{
    const LIST_LIMIT = 300;

    private function useOrgDb()
    {
        $change_db = new login();
        $change_db->change_db();
    }

    // settings keyword rows for the separate hotspot Paybill; the Daraja keys are write-only
    const SETTING_KEYS = ["hotspot_consumer_key", "hotspot_consumer_secret", "hotspot_passkey"];

    private function readOnly()
    {
        return HotspotAccess::readOnly(session("priviledges"), "Hotspot Payments");
    }

    private function admin()
    {
        return session("Usernames") ?: ("admin " . session("Userid"));
    }

    private function filters(Request $request)
    {
        $from = $request->input("from", now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input("to", now()->format('Y-m-d'));
        foreach (["from", "to"] as $name) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $$name)) {
                $$name = $name === "from" ? now()->startOfMonth()->format('Y-m-d') : now()->format('Y-m-d');
            }
        }
        return [
            "from" => $from,
            "to" => $to,
            "status" => $request->input("status", ""),
            "q" => trim((string) $request->input("q", "")),
        ];
    }

    private function transactions(array $filters)
    {
        $query = DB::connection("mysql2")->table("hotspot_transactions AS t")
            ->leftJoin("hotspot_payments AS p", "p.payment_id", "=", "t.payment_id")
            ->leftJoin("hotspot_plans AS pl", "pl.plan_id", "=", "p.plan_id")
            ->leftJoin("hotspot_tickets AS k", "k.ticket_id", "=", "t.ticket_id")
            ->where("t.deleted", 0)
            ->whereBetween("t.date_created", [str_replace("-", "", $filters['from']) . "000000", str_replace("-", "", $filters['to']) . "235959"]);
        if (in_array($filters['status'], ["matched", "unmatched", "review", "rejected"], true)) {
            $query->where("t.status", $filters['status']);
        }
        if ($filters['q'] !== "") {
            $q = $filters['q'];
            $query->where(function ($inner) use ($q) {
                $inner->where("t.trans_id", "like", "%" . $q . "%")->orWhere("t.bill_ref", "like", "%" . $q . "%")
                    ->orWhere("p.phone", "like", "%" . $q . "%")->orWhere("t.phone", "like", "%" . $q . "%");
            });
        }
        return $query;
    }

    function index(Request $request)
    {
        $this->useOrgDb();
        $filters = $this->filters($request);
        $base = $this->transactions($filters);
        $totals = (clone $base)->select("t.status", DB::raw("COUNT(*) AS n"), DB::raw("SUM(t.amount) AS total"))->groupBy("t.status")->get()->keyBy("status");
        $rows = (clone $base)->select("t.*", "p.phone AS customer_phone", "p.method", "pl.plan_name", "k.ticket_code", "k.ticket_id AS ticket")
            ->orderBy("t.transaction_id", "DESC")->limit(self::LIST_LIMIT)->get();
        $attempts = DB::connection("mysql2")->table("hotspot_payments AS p")
            ->leftJoin("hotspot_plans AS pl", "pl.plan_id", "=", "p.plan_id")
            ->where("p.deleted", 0)->where("p.status", "!=", "paid")
            ->whereBetween("p.date_created", [str_replace("-", "", $filters['from']) . "000000", str_replace("-", "", $filters['to']) . "235959"])
            ->select("p.*", "pl.plan_name")->orderBy("p.payment_id", "DESC")->limit(50)->get();
        return view("hotspot.payments", [
            "filters" => $filters,
            "totals" => $totals,
            "rows" => $rows,
            "attempts" => $attempts,
            "config" => HotspotPayments::config(),
            "limit" => self::LIST_LIMIT,
            "reviews" => DB::connection("mysql2")->table("hotspot_payments AS p")->leftJoin("hotspot_plans AS pl", "pl.plan_id", "=", "p.plan_id")
                ->where("p.deleted", 0)->where("p.status", "review")->select("p.*", "pl.plan_name")->orderBy("p.payment_id")->get(),
            "unmatched" => DB::connection("mysql2")->table("hotspot_transactions")->where("deleted", 0)->whereIn("status", ["unmatched", "review"])
                ->whereNull("payment_id")->orderBy("transaction_id")->get(),
            "plans" => DB::connection("mysql2")->table("hotspot_plans")->where("deleted", 0)->orderBy("sort_order")->orderBy("price")->get(),
            "routers" => DB::connection("mysql2")->table("remote_routers")->where("deleted", 0)->where("hotspot_enabled", 1)->orderBy("router_name")->get(),
            "readonly" => $this->readOnly(),
        ]);
    }

    private function setting($keyword)
    {
        return DB::connection("mysql2")->table("settings")->where("keyword", $keyword)->where("deleted", "0")->value("value");
    }

    private function putSetting($keyword, $value)
    {
        $now = now()->format('YmdHis');
        $updated = DB::connection("mysql2")->table("settings")->where("keyword", $keyword)->where("deleted", "0")->update(["value" => $value, "date_changed" => $now]);
        if (!$updated && !DB::connection("mysql2")->table("settings")->where("keyword", $keyword)->where("deleted", "0")->exists()) {
            DB::connection("mysql2")->table("settings")->insert(["keyword" => $keyword, "value" => $value, "status" => 1, "date_changed" => $now, "deleted" => 0]);
        }
    }

    // hotspot C2B links of this org (separate Paybill); null until the org has callback keys
    private function c2bLinks()
    {
        $settings = DB::table("org_hotspot_settings")->where("organization_id", session("organization_id"))->first();
        if (!$settings || !$settings->org_key || !$settings->callback_token) {
            return null;
        }
        return [
            "confirmation" => route("hs_callback_c2b", [$settings->org_key, $settings->callback_token]),
            "validation" => route("hs_callback_c2b_validate", [$settings->org_key, $settings->callback_token]),
        ];
    }

    function settings()
    {
        $this->useOrgDb();
        $keys_set = [];
        foreach (self::SETTING_KEYS as $keyword) {
            $keys_set[$keyword] = trim((string) $this->setting($keyword)) !== "";
        }
        return view("hotspot.payment-settings", [
            "config" => HotspotPayments::config(),
            "mode" => $this->setting("hotspot_paybill_mode") === "separate" ? "separate" : "shared",
            "shortcode" => $this->setting("hotspot_shortcode"),
            "shortcode_type" => $this->setting("hotspot_shortcode_type") === "till" ? "till" : "paybill",
            "billing_paybill" => $this->setting("paybill"),
            "keys_set" => $keys_set,
            "links" => $this->c2bLinks(),
            "readonly" => $this->readOnly(),
        ]);
    }

    function saveSettings(Request $request)
    {
        $this->useOrgDb();
        $back = redirect(route("hotspot.payment_settings"));
        if ($this->readOnly()) {
            session()->flash("error_hotspot", "You have read-only access to hotspot payments.");
            return $back;
        }
        $mode = $request->input("mode") === "separate" ? "separate" : "shared";
        if ($mode === "separate") {
            $shortcode = trim((string) $request->input("shortcode"));
            if (!preg_match('/^\d{5,8}$/', $shortcode)) {
                session()->flash("error_hotspot", "Enter the hotspot Paybill or Till number (5 to 8 digits).");
                return $back;
            }
            if ($shortcode === trim((string) $this->setting("paybill"))) {
                session()->flash("error_hotspot", "That is your billing Paybill. Choose \"Same Paybill as billing\" instead.");
                return $back;
            }
            $this->putSetting("hotspot_shortcode", $shortcode);
            $this->putSetting("hotspot_shortcode_type", $request->input("shortcode_type") === "till" ? "till" : "paybill");
            // blank = keep the saved key (they are never shown back)
            foreach (self::SETTING_KEYS as $keyword) {
                $value = trim((string) $request->input($keyword));
                if ($value !== "") {
                    $this->putSetting($keyword, $value);
                }
            }
        }
        $this->putSetting("hotspot_paybill_mode", $mode);
        session()->flash("success_hotspot", $mode === "shared" ? "Hotspot payments use your billing Paybill." : "Hotspot payments use their own Paybill. Register its C2B link below if you have not yet.");
        return $back;
    }

    // tell Safaricom to send the hotspot Paybill's confirmations to the hotspot C2B link
    function registerC2B()
    {
        $this->useOrgDb();
        $back = redirect(route("hotspot.payment_settings"));
        if ($this->readOnly()) {
            session()->flash("error_hotspot", "You have read-only access to hotspot payments.");
            return $back;
        }
        $config = HotspotPayments::config();
        $links = $this->c2bLinks();
        if ($config["mode"] !== "separate" || !$links) {
            session()->flash("error_hotspot", $links ? "Choose \"Separate Paybill\" and save first." : "Your account has no hotspot callback keys yet. Ask Hypbits to save your hotspot settings in the manager.");
            return $back;
        }
        if (!str_starts_with($links["confirmation"], "https://")) {
            session()->flash("error_hotspot", "Safaricom only accepts HTTPS links, and this server's link is " . $links["confirmation"] . ". Register from the live server.");
            return $back;
        }
        $mpesa = new MpesaService(["database" => session("database_name"), "keys" => "hotspot"]);
        if ($mpesa->configError) {
            session()->flash("error_hotspot", $mpesa->configError);
            return $back;
        }
        $reply = $mpesa->registerC2B($links["confirmation"], $links["validation"]);
        $ok = isset($reply["ResponseCode"]) && (string) $reply["ResponseCode"] === "0";
        $reason = $reply["ResponseDescription"] ?? ($reply["errorMessage"] ?? "no reply");
        session()->flash($ok ? "success_hotspot" : "error_hotspot", $ok ? "Safaricom registered the hotspot C2B link for " . $config["shortcode"] . "." : "Safaricom did not register the link: " . $reason);
        return $back;
    }

    /**
     * Sell a package with an M-Pesa payment that has no (valid) account code: an unassigned
     * billing transaction (from its transaction page) or an unmatched hotspot transaction. The
     * code goes by SMS to the phone entered.
     */
    function assign(Request $request)
    {
        $this->useOrgDb();
        $back = $request->input("back") === "billing" && $request->input("transaction_id")
            ? redirect("/Transactions/View/" . (int) $request->input("transaction_id"))
            : redirect(route("hotspot.payments"));
        if ($this->readOnly()) {
            session()->flash("error_hotspot", "You have read-only access to hotspot payments.");
            return $back;
        }
        $plan = DB::connection("mysql2")->table("hotspot_plans")->where("plan_id", $request->input("plan_id"))->where("deleted", 0)->first();
        $router = DB::connection("mysql2")->table("remote_routers")->where("router_id", $request->input("router_id"))->where("deleted", 0)->where("hotspot_enabled", 1)->first();
        $phone = $this->formatKenyanPhone(preg_replace('/\s+/', '', (string) $request->input("phone")));
        if (!$plan || !$router) {
            session()->flash("error_hotspot", "Choose the package and the router.");
            return $back;
        }
        if (!preg_match('/^254(7|1)\d{8}$/', (string) $phone)) {
            session()->flash("error_hotspot", "Enter the customer's phone number, e.g. 0712345678. The ticket code is sent there.");
            return $back;
        }
        // the exact row the ISP opened (an M-Pesa code is not always unique in the billing table)
        if ($request->input("source") === "billing") {
            $row = DB::connection("mysql2")->table("transaction_tables")->where("transaction_id", (int) $request->input("transaction_id"))->first();
            $found = $row ? HotspotPayments::billingFound($row) : null;
            $mpesa_code = $row ? $row->transaction_mpesa_id : null;
        } else {
            $mpesa_code = DB::connection("mysql2")->table("hotspot_transactions")->where("transaction_id", (int) $request->input("transaction_id"))->value("trans_id");
            $found = $mpesa_code ? HotspotPayments::findPayment($mpesa_code) : null;
        }
        if (!$found || $found["kind"] !== ($request->input("source") === "billing" ? "billing" : "hotspot")) {
            session()->flash("error_hotspot", "That M-Pesa payment was not found.");
            return $back;
        }
        [$ticket_id, $error] = HotspotPayments::assign($found, $plan, $router, $phone, "assigned", $this->admin());
        if (!$ticket_id) {
            session()->flash("error_hotspot", $error);
            return $back;
        }
        $code = DB::connection("mysql2")->table("hotspot_tickets")->where("ticket_id", $ticket_id)->value("ticket_code");
        session()->flash("success_hotspot", "M-Pesa payment " . $mpesa_code . " is now a hotspot payment: ticket " . $code . " (" . $plan->plan_name . ") was created and sent by SMS to " . $phone . ".");
        return redirect(route("hotspot.payments"));
    }

    function approve($payment_id)
    {
        $this->useOrgDb();
        if ($this->readOnly()) {
            session()->flash("error_hotspot", "You have read-only access to hotspot payments.");
            return redirect(route("hotspot.payments"));
        }
        $payment = DB::connection("mysql2")->table("hotspot_payments")->where("payment_id", $payment_id)->where("status", "review")->first();
        if (!$payment) {
            session()->flash("error_hotspot", "That claim is no longer waiting for approval.");
            return redirect(route("hotspot.payments"));
        }
        [$ticket_id, $error] = HotspotPayments::approve($payment, $this->admin());
        $code = $ticket_id ? DB::connection("mysql2")->table("hotspot_tickets")->where("ticket_id", $ticket_id)->value("ticket_code") : null;
        session()->flash($ticket_id ? "success_hotspot" : "error_hotspot", $ticket_id ? "Approved: ticket " . $code . " was sent by SMS to " . $payment->phone . "." : $error);
        return redirect(route("hotspot.payments"));
    }

    // reject a claim waiting for review, or an unmatched hotspot transaction
    function reject(Request $request)
    {
        $this->useOrgDb();
        if ($this->readOnly()) {
            session()->flash("error_hotspot", "You have read-only access to hotspot payments.");
            return redirect(route("hotspot.payments"));
        }
        $now = now()->format('YmdHis');
        $note = "Rejected by " . $this->admin() . ".";
        if ($request->input("kind") === "claim") {
            $done = DB::connection("mysql2")->table("hotspot_payments")->where("payment_id", (int) $request->input("id"))->where("status", "review")
                ->update(["status" => "cancelled", "mpesa_receipt" => null, "result_desc" => $note, "date_changed" => $now]);
        } else {
            $done = DB::connection("mysql2")->table("hotspot_transactions")->where("transaction_id", (int) $request->input("id"))->whereIn("status", ["unmatched", "review"])
                ->whereNull("payment_id")->update(["status" => "rejected", "note" => $note, "date_changed" => $now]);
        }
        session()->flash($done ? "success_hotspot" : "error_hotspot", $done ? "Rejected." : "It was already handled.");
        return redirect(route("hotspot.payments"));
    }

    function export(Request $request)
    {
        $this->useOrgDb();
        $filters = $this->filters($request);
        $rows = $this->transactions($filters)->select("t.*", "p.phone AS customer_phone", "pl.plan_name", "k.ticket_code")->orderBy("t.transaction_id")->get();
        $name = "hotspot-payments-" . $filters['from'] . "-to-" . $filters['to'] . ".csv";
        return response()->streamDownload(function () use ($rows) {
            $out = fopen("php://output", "w");
            fputcsv($out, ["Date", "M-Pesa code", "Amount", "Phone", "Payer", "Account", "Package", "Ticket", "Source", "Status"]);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->date_created ? date("Y-m-d H:i:s", strtotime($row->date_created)) : "",
                    $row->trans_id, $row->amount, $row->customer_phone ?: $row->phone, $row->payer_name, $row->bill_ref,
                    $row->plan_name, $row->ticket_code, $row->source, $row->status,
                ]);
            }
            fclose($out);
        }, $name, ["Content-Type" => "text/csv"]);
    }
}
