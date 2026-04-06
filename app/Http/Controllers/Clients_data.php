<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

date_default_timezone_set('Africa/Nairobi');
class Clients_data extends Controller
{
    // functions to display the students data
    function getClientInfor(){
        // change db
        $change_db = new login();
        $change_db->change_db();
        // get the clients information
        $client_id = session('client_id');
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
        $date_data  = $client_data[0]->clients_reg_date;
        $year = substr($date_data,0,4);
        $month = substr($date_data,4,2);
        $day = substr($date_data,6,2);
        $hour = substr($date_data,8,2);
        $minute = substr($date_data,10,2);
        $second = substr($date_data,12,2);
        $d = mktime($hour, $minute, $second, $month, $day, $year);
        $dates2 = date("D dS M Y  h:i:sa", $d);

        $date_data  = $client_data[0]->next_expiration_date;
        $year = substr($date_data,0,4);
        $month = substr($date_data,4,2);
        $day = substr($date_data,6,2);
        $hour = substr($date_data,8,2);
        $minute = substr($date_data,10,2);
        $second = substr($date_data,12,2);
        $d = mktime($hour, $minute, $second, $month, $day, $year);
        $dates = date("D dS M Y  h:i:sa", $d);
        return view("clients.client-profile",["client_data" => $client_data,"reg_date" => $dates2,"expiration_date" => $dates]);
    }

    function view_client_dashboard(){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // get the clients information
        $client_id = session('client_id');
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
        if (count($client_data) == 0) {
            return redirect("/Client-Login")->with("error", "Login and try again!!");
        }
        $recent_payments = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `transaction_acc_id` = '$client_id' AND `deleted` = '0' ORDER BY `transaction_date` DESC LIMIT 5");
        
        // recent refferals
        $all_clients = DB::connection("mysql2")->select("SELECT * FROM `client_tables` ORDER BY `clients_reg_date`");

        // add reffered clients to an array
        $reffered_clients = [];
        $refferal_collection = [];
        foreach ($all_clients as $client) {
            $string = str_replace("\\", "", $client->reffered_by);
            $string = str_replace("'", "\"", $string);
            if ($this->isJson($string)) {
                $reffered_by = json_decode($string, true);
                if ($reffered_by["client_acc"] == $client_data[0]->client_account && count($reffered_clients) <= 5) {
                    array_push($reffered_clients, $client);
                }
                if($reffered_by["client_acc"] == $client_data[0]->client_account){
                    foreach ($reffered_by["payment_history"] as $payment) {
                        $payment["client_name"] = $client->client_name;
                        $payment["client_id"] = $client->client_id;
                        array_push($refferal_collection, $payment);
                    }
                }
            }
        }

        $refferal_collection = $this->sortArrayByKey($refferal_collection, 'date', 'desc');
        $refferal_collection = array_slice($refferal_collection, 0, 5);

        return view("clients.clientDash", ["recent_payments" => $recent_payments, "recent_refferals" => $reffered_clients, "refferal_commisions" => $refferal_collection]);
    }

    // get the client transaction information
    function getTransaction(){
        // change db
        $change_db = new login();
        $change_db->change_db();

        $client_id = session('client_id');
        $trans_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `transaction_acc_id` = '$client_id' AND `deleted` = '0'");
        $dates = [];
        foreach ($trans_data as  $value) {
            // get the dates
            $date = $value->transaction_date;
            $date_data  = $date;
            $year = substr($date_data,0,4);
            $month = substr($date_data,4,2);
            $day = substr($date_data,6,2);
            $hour = substr($date_data,8,2);
            $minute = substr($date_data,10,2);
            $second = substr($date_data,12,2);
            $d = mktime($hour, $minute, $second, $month, $day, $year);
            $dat = date("D dS M Y  h:i:sa", $d);
            array_push($dates,$dat);
        }
        return view("clients.clienttrans",["transData" => $trans_data,"dates" => $dates]);
    }
    function viewPayment($paymentId){
        // change db
        $change_db = new login();
        $change_db->change_db();

        $payment_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `transaction_id` = '$paymentId' AND `deleted` = '0'");
        $payment = $payment_data[0];
        $dates = $payment->transaction_date;
        $date_data  = $dates;
        $year = substr($date_data,0,4);
        $month = substr($date_data,4,2);
        $day = substr($date_data,6,2);
        $hour = substr($date_data,8,2);
        $minute = substr($date_data,10,2);
        $second = substr($date_data,12,2);
        $d = mktime($hour, $minute, $second, $month, $day, $year);
        $dat = date("D dS M Y  h:i:sa", $d);

        return view("clients.viewpay",["payments" => $payment,"dates" => $dat]);
    }
    function confirm_mpesa($mpesa_id){
        // change db
        $change_db = new login();
        $change_db->change_db();
        
        $mpesa_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `transaction_mpesa_id` = '$mpesa_id' AND `transaction_status` = '0' AND `deleted` = '0'");
        return $mpesa_data;
    }
    function change_password(Request $req){
        // change db
        $change_db = new login();
        $change_db->change_db();
        
        // check if the new password are the same
        $new_password = $req->input('new_password');
        $repeat_password = $req->input('new_password');
        $old_password = $req->input('old_password');
        if ($new_password == $repeat_password) {
            // proceed and check if the old password is correct
            $client_id = session('client_id');
            $client_datas = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `client_password` = '$old_password' AND `deleted` = '0'");
            if (count($client_datas) > 0) {
                // update the client data
                session()->flash("success","You have successfully changed your passwords!");
                return redirect("/Credentials");
            }else {
                // update the client data
                session()->flash("error","You have provided the wrong password please try again!");
                return redirect("/Credentials");
            }
        }else {
            // update the client data
            session()->flash("error","Your passwords don`t match!");
            return redirect("/Credentials");
        }
        return $req->input();
    }
}
