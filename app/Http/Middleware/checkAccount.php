<?php

namespace App\Http\Middleware;

use App\Http\Controllers\login;
use Closure;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class checkAccount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $days_to_expire = 2; // days before expiration to show notice
        $inactive_months = 3; // months of inactivity before account is considered inactive

        // change db
        $change_db = new login();
        $change_db->change_db();
        session()->remove("block_edits");
        session()->remove("inactive_menu");

        // get the organization account details
        $organization = DB::select("SELECT * FROM organizations WHERE organization_id = ?", [session("organization_id")]);
        if(count($organization) == 0){
            // if no organization found, redirect to login
            return redirect("/Login")->with("error", "Invalid Account. Please contact us.");
        }

        // check if the organization payment status is valid
        if($organization[0]->payment_status == 1){
            // check the expiration date if its in 5 days
            $date_today = new DateTime(date("Ymd"));
            $expiry_date = new DateTime(date("Ymd", strtotime($organization[0]->expiry_date)));
            $diff = date_diff($date_today, $expiry_date);
            $days = $diff->format("%R%a");

            if($days <= 5){
                // client expiry check
                $client_expiry = date("YmdHis", strtotime("-$inactive_months months"));
                $clients = DB::connection("mysql2")->select("SELECT * FROM client_tables WHERE next_expiration_date >= ?", [$client_expiry]);
                $client_count = count($clients);

                // GET AMOUNT TO PAY
                $amount_to_pay = $client_count > 100 ? ($client_count * 20) : 1000;
                $discount = $organization[0]->discount_type == "percentage" ? round(($organization[0]->discount_amount * $amount_to_pay) / 100) : $organization[0]->discount_amount;
                $amount_to_pay -= $discount;
                $monthly_payment = $amount_to_pay;

                // GET THE WALLET BALANCE
                $wallet_balance = $organization[0]->wallet;

                if ($wallet_balance < $amount_to_pay) {
                    // dd($days);
                    // dd($wallet_balance, $amount_to_pay);
                    $amount_to_pay -= $wallet_balance;
                    if ($days <= -1) {
                        session()->put("block_edits", "true");
                    }
                    if ($days <= -2) {
                        session()->put("inactive_menu", "true");
                    }
                    session()->put("amount_to_pay", $amount_to_pay);
                    session()->put("show_payment_notice", "true");
                    session()->put("days_to_expire", $days);
                    session()->put("wallet_balance", $wallet_balance);
                    session()->put("monthly_payment", $monthly_payment);
                    return $next($request);
                }
            }
        }
        session()->put("show_payment_notice", "false");
        return $next($request);
    }
}
