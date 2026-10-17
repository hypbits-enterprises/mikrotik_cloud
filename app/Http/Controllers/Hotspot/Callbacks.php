<?php

namespace App\Http\Controllers\Hotspot;

use App\Classes\HotspotPayments;
use App\Http\Controllers\Controller;
use App\Http\Controllers\login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * M-Pesa callbacks for hotspot payments (no session). The org comes from org_key in the URL and
 * the request must carry that org's callback_token (manager org_hotspot_settings); otherwise
 * nothing is done (golden rule 12). Safaricom always gets an "Accepted" reply for a known org, so
 * it does not retry; the payment is completed exactly once (HotspotPayments::complete()).
 */
class Callbacks extends Controller
{
    function stk(Request $request, $org_key, $callback_token)
    {
        $organization = $this->organization($org_key, $callback_token);
        if (!$organization) {
            return response()->json(["ResultCode" => 1, "ResultDesc" => "Rejected"], 403);
        }
        $body = $request->json()->all();
        $result = $body["Body"]["stkCallback"] ?? null;
        $checkout = is_array($result) ? (string) ($result["CheckoutRequestID"] ?? "") : "";
        $payment = $checkout !== "" ? DB::connection("mysql2")->table("hotspot_payments")->where("checkout_request_id", $checkout)->first() : null;
        if (!$payment) {
            Log::warning("Hotspot STK callback for an unknown payment", ["org" => $organization->organization_id, "checkout" => $checkout]);
            return response()->json(["ResultCode" => 0, "ResultDesc" => "Accepted"]);
        }
        HotspotPayments::applyStkResult($payment, $result, "callback", json_encode($body));
        return response()->json(["ResultCode" => 0, "ResultDesc" => "Accepted"]);
    }

    // org by its public key + secret token, with hotspot on; mysql2 then points at its DB
    private function organization($org_key, $callback_token)
    {
        if (!preg_match('/^[A-Za-z0-9]{8,64}$/', (string) $org_key)) {
            return null;
        }
        $settings = DB::table("org_hotspot_settings")->where("org_key", $org_key)->where("hotspot_enabled", 1)->first();
        if (!$settings || !$settings->callback_token || !hash_equals((string) $settings->callback_token, (string) $callback_token)) {
            return null;
        }
        $organization = DB::table("organizations")->where("organization_id", $settings->organization_id)->first();
        if (!$organization || !$organization->organization_database) {
            return null;
        }
        (new login())->change_db($organization->organization_database);
        return $organization;
    }
}
