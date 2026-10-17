<?php

namespace App\Http\Controllers\Hotspot;

use App\Classes\HotspotPayments;
use App\Classes\HotspotPlans;
use App\Classes\HotspotPortal;
use App\Classes\HotspotTickets;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Captive portal (public, no session): /hs/{hotspot_key}. The router's own login files (served by
 * routerFile() and downloaded onto the router by the crontab agent) send customers here with their
 * MAC, IP and the router's login/logout links. Customers pick a package (payment: Stage 8) or enter
 * a ticket code; the page then submits the code to the router's login link for them.
 */
class Portal extends Controller
{
    function index(Request $request, $key)
    {
        $found = HotspotPortal::resolve($key);
        if (!$found) {
            return $this->unavailable();
        }
        $router = $found['router'];
        $login_url = (string) $request->query("login", "");
        return response()->view("hotspot.portal.login", [
            "key" => $key,
            "branding" => HotspotPortal::branding($found['organization'], $router->router_id),
            "plans" => HotspotPlans::forPortal(),
            "login_url" => HotspotPortal::loginUrlAllowed($login_url, $router) ? $login_url : null,
            "mac" => $this->mac($request->query("mac")),
            "dst" => HotspotPortal::safeLink($request->query("dst")),
            "error" => mb_substr(trim(strip_tags((string) $request->query("error", ""))), 0, 200),
            "payments" => HotspotPayments::config(),
            "logged_out" => $request->boolean("logged_out"),
            // after logout the router passes the code back, so logging in again is one tap
            "code" => $request->boolean("logged_out") && preg_match('/^[A-Z0-9]{4,12}$/', (string) $request->query("user")) ? $request->query("user") : null,
            "preview" => false,
        ])->header("Cache-Control", "no-store");
    }

    // is this ticket code good for this router? The page then logs in on the router with it.
    function code(Request $request, $key)
    {
        $found = HotspotPortal::resolve($key);
        if (!$found) {
            return response()->json(["ok" => false, "message" => "This hotspot is not available."], 404);
        }
        [$ok, $message, $ticket] = HotspotPortal::checkCode($request->input("code"), $found['router']);
        return response()->json($ok ? ["ok" => true, "code" => $ticket->ticket_code] : ["ok" => false, "message" => $message]);
    }

    // start a purchase: M-Pesa prompt (stk) or Paybill with the account code (paybill)
    function pay(Request $request, $key)
    {
        $found = HotspotPortal::resolve($key);
        if (!$found) {
            return response()->json(["ok" => false, "message" => "This hotspot is not available."], 404);
        }
        $plan = DB::connection("mysql2")->table("hotspot_plans")->where("plan_id", $request->input("plan_id"))
            ->where("deleted", 0)->where("is_active", 1)->first();
        if (!$plan) {
            return response()->json(["ok" => false, "message" => "Choose a package."]);
        }
        $method = $request->input("method") === "paybill" ? "paybill" : "stk";
        $phone = $this->formatKenyanPhone(preg_replace('/\s+/', '', (string) $request->input("phone")));
        if (!preg_match('/^254(7|1)\d{8}$/', (string) $phone)) {
            return response()->json(["ok" => false, "message" => "Enter your M-Pesa phone number, e.g. 0712345678."]);
        }
        [$payment, $error] = HotspotPayments::start($found['organization'], $found['router'], $plan, $phone, $this->mac($request->input("mac")), $request->ip(), $method);
        if (!$payment) {
            return response()->json(["ok" => false, "message" => $error]);
        }
        $config = HotspotPayments::config();
        return response()->json([
            "ok" => true,
            "payment_id" => $payment->payment_id,
            "token" => $payment->public_token,
            "method" => $payment->method,
            "amount" => rtrim(rtrim(number_format($payment->amount, 2, ".", ""), "0"), "."),
            "account" => $payment->account_code,
            "shortcode" => $config['shortcode'],
        ]);
    }

    // the portal polls this while the customer pays
    function payment(Request $request, $key, $payment_id)
    {
        $found = HotspotPortal::resolve($key);
        if (!$found) {
            return response()->json(["state" => "failed", "message" => "This hotspot is not available."], 404);
        }
        $payment = HotspotPayments::find($payment_id);
        if (!$payment || (int) $payment->router_id !== (int) $found['router']->router_id || !hash_equals((string) $payment->public_token, (string) $request->query("t"))) {
            return response()->json(["state" => "failed", "message" => "Payment not found."], 404);
        }
        $payment = HotspotPayments::refresh($found['organization'], $payment);
        if ($payment->status === "pending") {
            return response()->json(["state" => "waiting"]);
        }
        if ($payment->status !== "paid") {
            $messages = [
                "cancelled" => "The payment was cancelled on the phone.",
                "expired" => "No payment was received in time. If you paid, enter your M-Pesa details under \"I paid but have no code\".",
            ];
            return response()->json(["state" => "failed", "message" => $messages[$payment->status] ?? ("The payment did not go through" . ($payment->result_desc ? ": " . $payment->result_desc : ".") )]);
        }
        $ticket = DB::connection("mysql2")->table("hotspot_tickets")->where("payment_id", $payment->payment_id)->first();
        if (!$ticket || $ticket->provision_status !== "done") {
            return response()->json(["state" => "activating", "code" => $ticket ? $ticket->ticket_code : null]);
        }
        return response()->json(["state" => "ready", "code" => $ticket->ticket_code]);
    }

    // "You're online": shown by the router's alogin.html / status.html
    function online(Request $request, $key)
    {
        $found = HotspotPortal::resolve($key);
        if (!$found) {
            return $this->unavailable();
        }
        $router = $found['router'];
        $branding = HotspotPortal::branding($found['organization'], $router->router_id);
        $redirect = HotspotPortal::safeLink($branding['redirect_url']);
        if ($redirect && !$request->boolean("status")) {
            return redirect()->away($redirect);
        }

        $ticket = null;
        $plan = null;
        $code = strtoupper(trim((string) $request->query("user", "")));
        if (preg_match('/^[A-Z0-9]{4,12}$/', $code)) {
            $ticket = DB::connection("mysql2")->table("hotspot_tickets")->where("ticket_code", $code)
                ->where("router_id", $router->router_id)->where("deleted", 0)->first();
            $plan = $ticket ? DB::connection("mysql2")->table("hotspot_plans")->where("plan_id", $ticket->plan_id)->first() : null;
        }
        $logout_url = (string) $request->query("logout", "");
        return response()->view("hotspot.portal.online", [
            "key" => $key,
            "branding" => $branding,
            "ticket" => $ticket,
            "plan" => $plan,
            "usage" => $ticket ? HotspotTickets::usage($ticket, $plan) : null,
            "logout_url" => HotspotPortal::logoutUrlAllowed($logout_url, $router) ? $logout_url : null,
            "dst" => HotspotPortal::safeLink($request->query("dst")),
            "preview" => false,
        ])->header("Cache-Control", "no-store");
    }

    // the small redirect pages the router serves from its hypbits-hs html directory
    function routerFile($key, $name)
    {
        if (!in_array($name, HotspotPortal::ROUTER_FILES, true)) {
            abort(404);
        }
        $found = HotspotPortal::resolve($key);
        if (!$found) {
            abort(404);
        }
        // title and colour for login.html's fallback form (copied when the router downloads it)
        $branding = HotspotPortal::branding($found['organization'], $found['router']->router_id);
        $view = "hotspot.router-files." . str_replace(".html", "", $name);
        return response()->view($view, [
            "portal" => config("hotspot.portal_url") . "/hs/" . $key,
            "title" => $branding['title'],
            "color" => $branding['primary_color'],
        ])->header("Content-Type", "text/html; charset=utf-8");
    }

    // 1x1 image: the router's login.html loads it to check the portal can be reached
    function ping()
    {
        return response(base64_decode("R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"), 200)
            ->header("Content-Type", "image/gif")
            ->header("Cache-Control", "no-store");
    }

    private function unavailable()
    {
        return response()->view("hotspot.portal.unavailable", [], 404);
    }

    private function mac($mac)
    {
        $mac = strtoupper(trim((string) $mac));
        return preg_match('/^([0-9A-F]{2}:){5}[0-9A-F]{2}$/', $mac) ? $mac : null;
    }
}
