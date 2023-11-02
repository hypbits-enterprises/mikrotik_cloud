<?php

namespace App\Http\Controllers;

use App\Classes\reports\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\sms_table;
use stdClass;

date_default_timezone_set('Africa/Nairobi');
class Sms extends Controller
{
    function getOwnerPhone($account_id,$phone_number){
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = '$account_id'");
        if (count($client_data) > 0) {
            return [$account_id,ucwords(strtolower($client_data[0]->client_name))];
        }
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `clients_contacts` LIKE '%".$phone_number."%'");
        $client_name = (count($client_data)>0) ? $client_data[0]->client_name: $phone_number;
        return [(count($client_data) > 0 ? $client_data[0]->client_id:0),ucwords(strtolower($client_name))];
    }
    //get the function for the sms
    function getSms(){
        $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' ORDER BY `sms_id` DESC");
        // get the clients names
        $client_names = [];
        $dates = [];
        foreach ($sms_data as $value) {
            // get the clients data
            $client_data = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '$value->account_id'");
            $client_name = $value->recipient_phone;

            if (count($client_data) > 0) {
                $client_name = $client_data[0]->client_name;
            }else{
                // $client_name = (count($client_data)>0) ? $client_data[0]->client_name: $value->recipient_phone;
                $phone_db = (strlen($value->recipient_phone) == 12) ? substr($value->recipient_phone,3,9) : substr($value->recipient_phone,1,9);
                $user_data_return = $this->getOwnerPhone($value->account_id,$phone_db);
                $client_name = $value->recipient_phone;
            }
            array_push($client_names,$client_name);
            // get the payment dates
            // return $client_name;
            
            $date_data = $value->date_sent;
            $year = substr($date_data,0,4);
            $month = substr($date_data,4,2);
            $day = substr($date_data,6,2);
            $hour = substr($date_data,8,2);
            $minute = substr($date_data,10,2);
            $second = substr($date_data,12,2);
            $d = mktime($hour, $minute, $second, $month, $day, $year);
            $dates2 = date("D dS M Y  h:i:sa", $d);
            array_push($dates,$dates2);
        }
        // return count($client_names);
        // GET ALL THE SMS SENT TODAY
        $today = date("Ymd");
        $sms_today = DB::select("SELECT COUNT(*) AS 'Total' FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` LIKE '$today%'");
        $sms_count = $sms_today[0]->Total;
        // GET FOR THE LAST ONE WEEK
        $last_week = date("YmdHis",strtotime("-7 days"));
        $lastweek_sms = DB::select("SELECT COUNT(*) AS 'Total' FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` > $last_week;");
        $sms_week = $lastweek_sms[0]->Total;
        // GET ALL SMS SENT BY THE SYSTEM
        $total_sms = DB::select("SELECT COUNT(*) AS 'Total' FROM `sms_tables` WHERE `deleted`= '0'");
        $totalsms = $total_sms[0]->Total;

        // get the clients name username and phonenumber
        $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' ORDER BY `client_id` DESC");
        $clients_name = [];
        $clients_acc = [];
        $clients_phone = [];
        for ($index=0; $index < count($clients_data); $index++) {
            array_push($clients_name,ucwords(strtolower($clients_data[$index]->client_name)));
            array_push($clients_acc,$clients_data[$index]->client_account);
            array_push($clients_phone,$clients_data[$index]->clients_contacts);
        }
        return view("adminsms",["sms_data" =>$sms_data,"client_names" => $client_names, "dates" => $dates, "sms_count" => $sms_count, "last_week" => $sms_week,"total_sms" => $totalsms,"clients_name" => $clients_name,"clients_acc" => $clients_acc,"clients_phone" => $clients_phone]);
    }

    function sms_balance(){
        // GET THE SMS KEYS FROM THE DATABASE
        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_api_key'");
        $sms_api_key = $sms_keys[0]->value;
        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_partner_id'");
        $sms_partner_id = $sms_keys[0]->value;
        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_shortcode'");
        $sms_shortcode = $sms_keys[0]->value;


        // if send sms is 1 we send  the sms
        $partnerID = $sms_partner_id;
        $apikey = $sms_api_key;
        $shortcode = $sms_shortcode;
        // get the sms balance
        $finalURL = "https://mysms.celcomafrica.com/api/services/getbalance/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID);
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL, $finalURL);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = \curl_exec($ch);
        \curl_close($ch);
        $res = json_decode($response);
        $credit_balance = $res->credit;
        return round($credit_balance)." SMS";
    }

    // get the sms id
    function getSMSData($sms_id){
        $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `sms_id` = '$sms_id'");
        
        $date_data = $sms_data[0]->date_sent;;
        $year = substr($date_data,0,4);
        $month = substr($date_data,4,2);
        $day = substr($date_data,6,2);
        $hour = substr($date_data,8,2);
        $minute = substr($date_data,10,2);
        $second = substr($date_data,12,2);
        $d = mktime($hour, $minute, $second, $month, $day, $year);
        $dates2 = date("D dS M Y  h:i:sa", $d);

        $account_id = $sms_data[0]->account_id;
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = '$account_id'");
        $client_name = (count($client_data)>0)? $client_data[0]->client_name: "Unknown";
        $sms_type = $sms_data[0]->sms_type;
        // get the sms type
        $sms_type = ($sms_type == "1") ? "Transaction" : "Notification";
        return view("smsinfor",["sms_data" => $sms_data,"date" => $dates2, "client_name" => $client_name, "sms_type" => $sms_type]);
    }

    function delete($sms_id){
        $data = DB::delete("DELETE FROM `sms_tables` WHERE `deleted`= '0' AND `sms_id` = '$sms_id'");
        session()->flash("success_sms","Message successfully deleted");
        return redirect("/sms");
    }
    function compose(){
        // get user data
        $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0';");
        $client_names = [];
        foreach ($user_data as $key => $value) {
            array_push($client_names,$value->client_name);
        }
        $client_contacts = [];
        foreach ($user_data as $key => $value) {
            array_push($client_contacts,$value->clients_contacts);
        }
        $client_account = [];
        foreach ($user_data as $key => $value) {
            array_push($client_account,$value->client_account);
        }
        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted`= '0'");
        return view("compose",["client_names" => $client_names,"client_contacts" => $client_contacts,"client_account" => $client_account,"router_infor" => $router_data]);
    }
    function sendsms(Request $req){
        // GET THE SMS KEYS FROM THE DATABASE
        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_api_key'");
        $sms_api_key = $sms_keys[0]->value;
        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_partner_id'");
        $sms_partner_id = $sms_keys[0]->value;
        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_shortcode'");
        $sms_shortcode = $sms_keys[0]->value;
        // GET THE VALUES
        $select_recipient = $req->input('select_recipient');
        $phone_number = $req->input('phone_number') ? $req->input('phone_number') : $req->input('phone_numbers');
        $messages = $req->input('messages');
        // start with the numbers
        $sms_type = 2;
        // return $req->input();

        // from the 1
        // send sms
        $send_sms = 0;
        if ($select_recipient == "1") {
            if ($phone_number != "") {
                // TAKE THE VALUES OF PHONE NUMBERS AND DIRECTLY SEND THE MESSAGE
                // send message and save it to the database

                $new_phone = "";
                for ($i=0; $i < strlen($phone_number); $i++) { 
                    if ($phone_number[$i] == " ") {
                        continue;
                    }
                    $new_phone.=$phone_number[$i];
                }
                $phone_number = $new_phone;
                // return $phone_number;
                // send sms
                $send_sms = 1;
            }else{
                session()->flash("error_sms","Please provide a number to send the sms");
                return redirect("/sms/compose");
            }
        }elseif ($select_recipient == 5){
            // select the client
            if ($phone_number != "") {
                // TAKE THE VALUES OF PHONE NUMBERS AND DIRECTLY SEND THE MESSAGE
                // send message and save it to the database
                
                // send sms
                $new_phone = "";
                for ($i=0; $i < strlen($phone_number); $i++) { 
                    if ($phone_number[$i] == " ") {
                        continue;
                    }
                    $new_phone.=$phone_number[$i];
                }
                $phone_number = $new_phone;
                // return $phone_number;
                $send_sms = 1;
            }else{
                session()->flash("error_sms","Please provide a number to send the sms");
                return redirect("/sms/compose");
            }
        }elseif ($select_recipient == 2) {
            // send to active clients
            // get the number of the active clients
            $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_status` = 1");
            if (count($client_data) > 0) {
                $phone_number = "";
                // we proceed and get the client data
                for ($i=0; $i < count($client_data); $i++) { 
                    //get the phone of the clients
                    $phone_number.=$client_data[$i]->clients_contacts.",";
                }
                $phone_number = substr($phone_number,0,(strlen($phone_number)-1));
                // return $phone_number;
                $send_sms = 1;
            }else{
                session()->flash("error_sms","No active clients at the moment");
                return redirect("/sms/compose");
            }
        }elseif ($select_recipient == 3) {
            // send to active clients
            // get the number of the active clients
            $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_status` = 0");
            if (count($client_data) > 0) {
                $phone_number = "";
                // we proceed and get the client data
                for ($i=0; $i < count($client_data); $i++) { 
                    //get the phone of the clients
                    $phone_number.=$client_data[$i]->clients_contacts.",";
                }
                $phone_number = substr($phone_number,0,(strlen($phone_number)-1));
                // return $phone_number;
                $send_sms = 1;
            }else{
                session()->flash("error_sms","No inactive clients at the moment");
                return redirect("/sms/compose");
            }
        }elseif ($select_recipient == 4) {
            // send to active clients
            // get the number of the active clients
            $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0'");
            if (count($client_data) > 0) {
                $phone_number = "";
                // we proceed and get the client data
                for ($i=0; $i < count($client_data); $i++) { 
                    //get the phone of the clients
                    $phone_number.=$client_data[$i]->clients_contacts.",";
                }
                $phone_number = substr($phone_number,0,(strlen($phone_number)-1));
                // return $phone_number;
                $send_sms = 1;
            }else{
                session()->flash("error_sms","You have no clients to send messages at the moment");
                return redirect("/sms/compose");
            }
        }

        // message status
        if ($send_sms == 1) {
            $message_status = 0;
            // if send sms is 1 we send  the sms
            $partnerID = $sms_partner_id;
            $apikey = $sms_api_key;
            $shortcode = $sms_shortcode;
            
            $mobile = $phone_number; // Bulk messages can be comma separated
            $message = $messages;
            
            $finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
            $ch = \curl_init();
            \curl_setopt($ch, CURLOPT_URL, $finalURL);
            \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = \curl_exec($ch);
            \curl_close($ch);
            $res = json_decode($response);
            // return $res;
            $values = $res->responses[0];
            // return $values;
            foreach ($values as  $key => $value) {
                // echo $key;
                if ($key == "response-code") {
                    if ($value == "200") {
                        // if its 200 the message is sent delete the
                        $message_status = 1;
                    }
                }
            }
            // check if the phone numbers are connected as an array
            $client_phone = explode(",",$phone_number);
            if (count($client_phone) > 1) {
                for ($i=0; $i < count($client_phone); $i++) { 
                    // get the user id of the number from the database
                    $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `clients_contacts` = '$client_phone[$i]'");
                    $client_id = (count($user_data) > 0) ? $user_data[0]->client_id : 0;
                    // if the message status is one the message is already sent to the user
                    $sms_table = new sms_table();
                    $sms_table->sms_content = $messages;
                    $sms_table->date_sent = date("YmdHis");
                    $sms_table->recipient_phone = $client_phone[$i];
                    $sms_table->sms_status = $message_status;
                    $sms_table->account_id = $client_id;
                    $sms_table->sms_type = $sms_type;
                    $sms_table->save();
                    // save the clients data one by one
                }
            }else {
                // get the user id of the number from the database
                $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `clients_contacts` = '$phone_number'");
                $client_id = (count($user_data) > 0) ? $user_data[0]->client_id : 0;
                // if the message status is one the message is already sent to the user
                $sms_table = new sms_table();
                $sms_table->sms_content = $messages;
                $sms_table->date_sent = date("YmdHis");
                $sms_table->recipient_phone = $phone_number;
                $sms_table->sms_status = $message_status;
                $sms_table->account_id = $client_id;
                $sms_table->sms_type = $sms_type;
                $sms_table->save();
                // save the clients data one by one
            }
            session()->flash("message_success","Message has been successfully sent to the client");
            return redirect("/sms/compose");
        }
    }
    function sendsms_routers(Request $req){
        // return $req->input();
        // GET THE SMS KEYS FROM THE DATABASE
        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_api_key'");
        $sms_api_key = $sms_keys[0]->value;
        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_partner_id'");
        $sms_partner_id = $sms_keys[0]->value;
        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_shortcode'");
        $sms_shortcode = $sms_keys[0]->value;
        // GET THE VALUES
        $select_client_group = $req->input('select_client_group');
        $messages = $req->input('messages');
        $select_router = $req->input('select_router');
        // start with the numbers
        $sms_type = 2;
        // return $req->input();
        $router_name = "Null";
        $router_in = DB::select("SELECT * FROM `router_tables` WHERE `deleted`= '0' AND `router_id` = '$select_router'");
        if (count($router_in) > 0) {
            $router_name = $router_in[0]->router_name;
        }

        // from the 1
        // send sms
        $send_sms = 0;
        if ($select_client_group == "0") {
            // send to active clients
            // get the number of the active clients
            $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_status` = 0 AND `router_name` = '$select_router'");
            if (count($client_data) > 0) {
                $phone_number = "";
                // we proceed and get the client data
                for ($i=0; $i < count($client_data); $i++) { 
                    //get the phone of the clients
                    $phone_number.=$client_data[$i]->clients_contacts.",";
                }
                $phone_number = substr($phone_number,0,(strlen($phone_number)-1));
                // return $phone_number;
                $send_sms = 1;
            }else{
                session()->flash("error_sms","No in-active clients on selected router.");
                return redirect("/sms/compose");
            }
        }elseif ($select_client_group == "1") {
            // send to active clients
            // get the number of the active clients
            $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_status` = 1 AND `router_name` = '$select_router'");
            if (count($client_data) > 0) {
                $phone_number = "";
                // we proceed and get the client data
                for ($i=0; $i < count($client_data); $i++) { 
                    //get the phone of the clients
                    $phone_number.=$client_data[$i]->clients_contacts.",";
                }
                $phone_number = substr($phone_number,0,(strlen($phone_number)-1));
                // return $phone_number;
                $send_sms = 1;
            }else{
                session()->flash("error_sms","No Active clients on selected router.");
                return redirect("/sms/compose");
            }
        }elseif ($select_client_group == "all") {
            // send to active clients
            // get the number of the active clients
            $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `router_name` = '$select_router'");
            if (count($client_data) > 0) {
                $phone_number = "";
                // we proceed and get the client data
                for ($i=0; $i < count($client_data); $i++) { 
                    //get the phone of the clients
                    $phone_number.=$client_data[$i]->clients_contacts.",";
                }
                $phone_number = substr($phone_number,0,(strlen($phone_number)-1));
                // return $phone_number;
                $send_sms = 1;
            }else{
                session()->flash("error_sms","You have no clients to send messages on the selected router");
                return redirect("/sms/compose");
            }
        }

        // message status
        if ($send_sms == 1) {
            $message_status = 0;
            // if send sms is 1 we send  the sms
            $partnerID = $sms_partner_id;
            $apikey = $sms_api_key;
            $shortcode = $sms_shortcode;
            
            $mobile = $phone_number; // Bulk messages can be comma separated
            $message = $messages;
            
            $finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
            $ch = \curl_init();
            \curl_setopt($ch, CURLOPT_URL, $finalURL);
            \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = \curl_exec($ch);
            \curl_close($ch);
            $res = json_decode($response);
            // return $res;
            $values = $res->responses[0];
            // return $values;
            foreach ($values as  $key => $value) {
                // echo $key;
                if ($key == "response-code") {
                    if ($value == "200") {
                        // if its 200 the message is sent delete the
                        $message_status = 1;
                    }
                }
            }
            // check if the phone numbers are connected as an array
            $client_phone = explode(",",$phone_number);
            if (count($client_phone) > 1) {
                for ($i=0; $i < count($client_phone); $i++) { 
                    // get the user id of the number from the database
                    $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND  `clients_contacts` = '$client_phone[$i]'");
                    $client_id = (count($user_data) > 0) ? $user_data[0]->client_id : 0;
                    // if the message status is one the message is already sent to the user
                    $sms_table = new sms_table();
                    $sms_table->sms_content = $messages;
                    $sms_table->date_sent = date("YmdHis");
                    $sms_table->recipient_phone = $client_phone[$i];
                    $sms_table->sms_status = $message_status;
                    $sms_table->account_id = $client_id;
                    $sms_table->sms_type = $sms_type;
                    $sms_table->save();
                    // save the clients data one by one
                }
            }else {
                // get the user id of the number from the database
                $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `clients_contacts` = '$phone_number'");
                $client_id = (count($user_data) > 0) ? $user_data[0]->client_id : 0;
                // if the message status is one the message is already sent to the user
                $sms_table = new sms_table();
                $sms_table->sms_content = $messages;
                $sms_table->date_sent = date("YmdHis");
                $sms_table->recipient_phone = $phone_number;
                $sms_table->sms_status = $message_status;
                $sms_table->account_id = $client_id;
                $sms_table->sms_type = $sms_type;
                $sms_table->save();
                // save the clients data one by one
            }
            session()->flash("message_success","Message has been successfully sent to the client");
            return redirect("/sms/compose");
        }
    }
    function customsms(){
        $sms_data = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'Messages'");
        $message_content =  json_decode($sms_data[0]->value);
        // return $message_content;
        return view("customsms",["sms_data"=>$message_content]);
    }
    function save_sms_content (Request $req){
        // return $req->input();
        // save it in settings
        $sms_content = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'Messages'");
        // if we have some data we update the data
        if (count($sms_content) > 0) {
            $message_content =  json_decode($sms_content[0]->value);
            // return $message_content;
            if ($req->input('date_before')) {
                $message_content[0]->messages[0]->message = $req->input("message_contents");
                // return $message_content;
                DB::table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect("/sms/system_sms");;
            }elseif($req->input('deday')){
                $message_content[0]->messages[1]->message = $req->input("message_contents");
                // return $message_content;
                DB::table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect("/sms/system_sms");
            }elseif ($req->input('after_due_date')) {
                $message_content[0]->messages[2]->message = $req->input("message_contents");
                // return $req->input();
                DB::table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect("/sms/system_sms");
            }elseif ($req->input('correct_acc_no')) {
                $message_content[1]->messages[0]->message = $req->input("message_contents");// return $req->input();
                DB::table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect("/sms/system_sms");
            }elseif ($req->input("incorrect_acc_no")) {
                $message_content[1]->messages[1]->message = $req->input("message_contents");
                DB::table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect("/sms/system_sms");
            }elseif ($req->input('account_renewed')) {
                $message_content[2]->messages[0]->message = $req->input("message_contents");
                DB::table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect("/sms/system_sms");
            }elseif ($req->input('account_extended')) {
                // return $message_content[2]->messages[1]->message;
                $message_content[2]->messages[1]->message = $req->input("message_contents");
                DB::table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect("/sms/system_sms");
            }elseif ($req->input('welcome_sms')) {
                // return $message_content[3]->messages[0]->message;
                $message_content[3]->messages[0]->message = $req->input("message_contents");
                DB::table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect("/sms/system_sms");
            }elseif ($req->input('account_deactivated')) {
                $message_content[2]->messages[2]->message = $req->input("message_contents");
                DB::table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect("/sms/system_sms");
            }elseif ($req->input('refferer_msg')) {
                // return $message_content;
                if(isset($message_content[1]->messages[2]->message)){
                    $message_content[1]->messages[2]->message = $req->input('message_contents');
                    DB::table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect("/sms/system_sms");
                }else{
                    $msgs = array("Name" => "refferer_msg","message" => $req->input('message_contents'));
                    // return $msgs;
                    array_push($message_content[1]->messages,$msgs);
                    DB::table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect("/sms/system_sms");
                }
            }elseif ($req->input('below_min_amnt')) {
                // return $message_content;
                if(isset($message_content[1]->messages[3]->message)){
                    $message_content[1]->messages[3]->message = $req->input('message_contents');
                    DB::table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect("/sms/system_sms");
                }else{
                    $msgs = array("Name" => "refferer_msg","message" => $req->input('message_contents'));
                    // return $msgs;
                    array_push($message_content[1]->messages,$msgs);
                    DB::table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect("/sms/system_sms");
                }
            }elseif ($req->input('welcome_client_sms')) {
                // return $message_content[4];
                if(isset($message_content[4])){
                    // set the welcome client sms
                    $messages = $message_content[4]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('welcome_client_sms')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1){
                        DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" =>"welcome_client_sms", "message" => $req->input('message_contents'));
                        array_push($message_content[4]->messages,$data);
                        // return $message_content;
                        DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                    }
                }else{
                    // create the array
                    $arrayed = ["Name" => "sms_bill_manager","messages" => []];
                    array_push($message_content,$arrayed);
                    $message_content = json_decode(json_encode($message_content));
                    // proceed and add the new message
                    $message = array("Name" => $req->input('welcome_client_sms'), "message" => $req->input('message_contents'));
                    array_push($message_content[4]->messages,$message);
                    // return $message_content;
                    DB::table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect("/sms/system_sms");
                }
            }elseif ($req->input('rcv_coracc_billsms')) {
                if(isset($message_content[4])){
                    // set the welcome client sms
                    $messages = $message_content[4]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('rcv_coracc_billsms')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1) {
                        DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" =>"rcv_coracc_billsms", "message" => $req->input('message_contents'));
                        array_push($message_content[4]->messages,$data);
                        // return $message_content;
                        DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                    }
                }else{
                    // create the array
                    $arrayed = ["Name" => "sms_bill_manager","messages" => []];
                    array_push($message_content,$arrayed);
                    $message_content = json_decode(json_encode($message_content));
                    // proceed and add the new message
                    $message = array("Name" => $req->input('rcv_coracc_billsms'), "message" => $req->input('message_contents'));
                    array_push($message_content[4]->messages,$message);
                    // return $message_content;
                    DB::table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect("/sms/system_sms");
                }
            }elseif ($req->input('rcv_incoracc_billsms')) {
                // return $req->input();
                if(isset($message_content[4])){
                    // set the welcome client sms
                    $messages = $message_content[4]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('rcv_incoracc_billsms')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1) {
                        DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" =>"rcv_incoracc_billsms", "message" => $req->input('message_contents'));
                        array_push($message_content[4]->messages,$data);
                        // return $message_content;
                        DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                    }
                }else{
                    // create the array
                    $arrayed = ["Name" => "sms_bill_manager","messages" => []];
                    array_push($message_content,$arrayed);
                    $message_content = json_decode(json_encode($message_content));
                    // proceed and add the new message
                    $message = array("Name" => $req->input('rcv_incoracc_billsms'), "message" => $req->input('message_contents'));
                    array_push($message_content[4]->messages,$message);
                    // return $message_content;
                    DB::table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect("/sms/system_sms");
                }
            }elseif ($req->input('rcv_belowmin_billsms')) {
                // return $req->input();
                if(isset($message_content[4])){
                    // set the welcome client sms
                    $messages = $message_content[4]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('rcv_belowmin_billsms')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1) {
                        DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" =>"rcv_belowmin_billsms", "message" => $req->input('message_contents'));
                        array_push($message_content[4]->messages,$data);
                        // return $message_content;
                        DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                    }
                }else{
                    // create the array
                    $arrayed = ["Name" => "sms_bill_manager","messages" => []];
                    array_push($message_content,$arrayed);
                    $message_content = json_decode(json_encode($message_content));
                    // proceed and add the new message
                    $message = array("Name" => $req->input('rcv_belowmin_billsms'), "message" => $req->input('message_contents'));
                    array_push($message_content[4]->messages,$message);
                    // return $message_content;
                    DB::table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect("/sms/system_sms");
                }
            }elseif ($req->input('msg_reminder_bal')) {
                // return $req->input();
                if(isset($message_content[4])){
                    // set the welcome client sms
                    $messages = $message_content[4]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('msg_reminder_bal')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1) {
                        DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" =>"msg_reminder_bal", "message" => $req->input('message_contents'));
                        array_push($message_content[4]->messages,$data);
                        // return $message_content;
                        DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                    }
                }else{
                    // create the array
                    $arrayed = ["Name" => "sms_bill_manager","messages" => []];
                    array_push($message_content,$arrayed);
                    $message_content = json_decode(json_encode($message_content));
                    // proceed and add the new message
                    $message = array("Name" => $req->input('msg_reminder_bal'), "message" => $req->input('message_contents'));
                    array_push($message_content[4]->messages,$message);
                    // return $message_content;
                    DB::table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect("/sms/system_sms");
                }
            }elseif ($req->input('account_frozen')) {
                // return $req->input();
                if (isset($message_content[5])) {
                    // set the welcome client sms
                    $messages = $message_content[5]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('account_frozen')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1) {
                        DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" => $req->input('account_frozen'), "message" => $req->input('message_contents'));
                        array_push($message_content[5]->messages,$data);
                        // return $message_content;
                        DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                    }
                }else{
                    // create the std class
                    $message = new stdClass();
                    $message->Name = "account_freezing";
                    $message->messages = [array("Name" => $req->input('account_frozen'), "message" => $req->input('message_contents')),array("Name" => 'account_unfrozen', "message" => ''),array("Name" => 'future_account_freeze', "message" => '')];

                    // array_push index 5
                    array_push($message_content,$message);
                    // return $message_content;

                    DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                }
            }elseif ($req->input('account_unfrozen')) {
                // return $req->input();
                if (isset($message_content[5])) {
                    // set the welcome client sms
                    $messages = $message_content[5]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('account_unfrozen')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    if ($present == 1) {
                        DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" => $req->input('account_unfrozen'), "message" => $req->input('message_contents'));
                        array_push($message_content[5]->messages,$data);
                        // return $message_content;
                        DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                    }
                }else{
                    // create the std class
                    $message = new stdClass();
                    $message->Name = "account_freezing";
                    $message->messages = [array("Name" => 'account_frozen', "message" => ''),array("Name" => $req->input('account_unfrozen'), "message" => $req->input('message_contents')),array("Name" => "future_account_freeze", "message" => "")];

                    // array_push index 5
                    array_push($message_content,$message);
                    // return $message_content;

                    DB::table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect("/sms/system_sms");
                }
            }elseif ($req->input('future_account_freeze')) {
                // return $req->input();
                if (isset($message_content[5])) {
                    // set the welcome client sms
                    $messages = $message_content[5]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) {
                        if ($messages[$index]->Name == $req->input('future_account_freeze')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    if ($present == 1) {
                        DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" => $req->input('future_account_freeze'), "message" => $req->input('message_contents'));
                        array_push($message_content[5]->messages,$data);
                        // return $message_content;
                        DB::table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect("/sms/system_sms");
                    }
                }else{
                    // create the std class
                    // array("Name" => 'account_freezing', "message" => '');
                    $message = new stdClass();
                    $message->Name = "account_freezing";
                    $message->messages = [array("Name" => 'account_frozen', "message" => ''),array("Name" => 'account_unfrozen', "message" => ''),array("Name" => $req->input('future_account_freeze'), "message" => $req->input('message_contents'))];

                    // then add the rest
                    array_push($message_content,$message);
                    // return $message_content;

                    DB::table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect("/sms/system_sms");
                }
            }
            // DO NOT ADD MORE FROM THESE AREA.
            // YOU BETTER SELECT A CATEGORY TO ADD TO OR CHANGE HOW THE MESSAGES ARE GOING TO BE RETRIEVED
            // OTHERWISE ITS GOING TO BE MESSY
        }else{
            // is we dont have any data we insert the data
            return redirect("/sms/system_sms");
        }
    }

    function resend_sms($sms_id){
        $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `sms_id` = '$sms_id'");
        // return $sms_data;
        if (count($sms_data) > 0) {
            // get the sms data it contains the client data
            $messages = $sms_data[0]->sms_content;
            $phone_number = $sms_data[0]->recipient_phone;
            $client_id = $sms_data[0]->account_id;
            $sms_type = $sms_data[0]->sms_type;
            $message_status = 0;
            // get the data to display for the client list
            $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0';");
            $client_names = [];
            foreach ($user_data as $key => $value) {
                array_push($client_names,$value->client_name);
            }
            $client_contacts = [];
            foreach ($user_data as $key => $value) {
                array_push($client_contacts,$value->clients_contacts);
            }
            $client_account = [];
            foreach ($user_data as $key => $value) {
                array_push($client_account,$value->client_account);
            }        
            return view("compose",["client_names" => $client_names,"client_contacts" => $client_contacts,"client_account" => $client_account,"messages" => $messages,"phone_number" => $phone_number]);
        }else {
            session()->flash("error_sms","Message to resend not found");
            return redirect("/sms");
        }
    }
    
    function Delete_bulk_sms(Request $req){
        // return $req;
        $hold_user_id_data = $req->input("hold_user_id_data");
        
        // turn it to an array
        $hold_user_id_data = json_decode($hold_user_id_data);

        $message_count = count($hold_user_id_data);
        // delete the smses
        for ($index=0; $index < count($hold_user_id_data); $index++) { 
            DB::delete("DELETE FROM `sms_tables` WHERE `sms_id` = ?",[$hold_user_id_data[$index]]);
        }

        session()->flash("success_sms",$message_count." messages have been deleted successfully!");
        return redirect("/sms");
    }

    function Resend_bulk_sms(Request $req){
        // return $req;
        $hold_user_id_data = $req->input("hold_user_id_data");
        $hold_user_id_data = json_decode($hold_user_id_data);

        // GET THE SMS KEYS FROM THE DATABASE
        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_api_key'");
        $sms_api_key = $sms_keys[0]->value;
        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_partner_id'");
        $sms_partner_id = $sms_keys[0]->value;
        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_shortcode'");
        $sms_shortcode = $sms_keys[0]->value;

        for ($index=0; $index < count($hold_user_id_data); $index++) { 
            $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `sms_id` = '".$hold_user_id_data[$index]."'");
            if (count($sms_data) > 0) {
                $recipient_phone = $sms_data[0]->recipient_phone;
                $sms_content = $sms_data[0]->sms_content;


                $message_status = 0;
                // if send sms is 1 we send  the sms
                $partnerID = $sms_partner_id;
                $apikey = $sms_api_key;
                $shortcode = $sms_shortcode;
                
                $mobile = $recipient_phone; // Bulk messages can be comma separated
                $message = $sms_content;
                
                $finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
                $ch = \curl_init();
                \curl_setopt($ch, CURLOPT_URL, $finalURL);
                \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = \curl_exec($ch);
                \curl_close($ch);
                $res = json_decode($response);
                // return $res;
                $values = $res->responses[0];
                // return $values;
                foreach ($values as  $key => $value) {
                    // echo $key;
                    if ($key == "response-code") {
                        if ($value == "200") {
                            // if its 200 the message is sent delete the
                            $message_status = 1;
                        }
                    }
                }
                // if the message status is one the message is already sent to the user
                $sms_table = new sms_table();
                $sms_table->sms_content = $sms_content;
                $sms_table->date_sent = date("YmdHis");
                $sms_table->recipient_phone = $recipient_phone;
                $sms_table->sms_status = $message_status;
                $sms_table->account_id = $sms_data[0]->account_id;
                $sms_table->sms_type = $sms_data[0]->sms_type;
                $sms_table->save();
            }
        }

        session()->flash("success_sms",count($hold_user_id_data)." messages have been re-sent successfully!");
        return redirect("/sms");
    }

    function generateReports(Request $req){
        // return $req;
        $sms_date_option = $req->input("sms_date_option");
        $from_select_date = $req->input("from_select_date");
        $to_select_date = $req->input("to_select_date");
        $select_registration_date = $req->input("select_registration_date");
        $select_user_option = $req->input("select_user_option");
        $client_account = $req->input("client_account");
        $client_phone = $req->input("client_phone");
        $contain_text_option = $req->input("contain_text_option");
        $text_keyword = $req->input("text_keyword");

        // get the sms reports
        $sms_data = [];
        $title = "No data to display";
        if ($contain_text_option == "All") {
            if ($select_user_option == "All") {
                if ($sms_date_option == "select date") {
                    $title = "SMS sent on ".date("D dS M Y",strtotime($select_registration_date));
                    $select_registration_date = date("Ymd",strtotime($select_registration_date));
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` LIKE '".$select_registration_date."%' ORDER BY `sms_id` DESC");
                }elseif ($sms_date_option == "between dates") {
                    $title = "SMS sent between ".date("D dS M Y",strtotime($from_select_date))." and ".date("D dS M Y",strtotime($to_select_date));
                    $from = date("YmdHis",strtotime($from_select_date));
                    $to = date("Ymd",strtotime($to_select_date))."235959";
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` BETWEEN ? AND ?  ORDER BY `sms_id` DESC",[$from,$to]);
                }else{
                    $title = "All SMS sent";
                    $sms_data = DB::select("SELECT * FROM `sms_tables` ORDER BY `sms_id` DESC");
                }
            }elseif($select_user_option == "specific_user"){
                // get the user data
                $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = ?",[$client_account]);
                if (count($user_data) < 1) {
                    return "<p style='color:red;'>This account number is invalid</p>";
                }
                $client_name = ucwords(strtolower($user_data[0]->client_name));
                $client_id = $user_data[0]->client_id;
                if ($sms_date_option == "select date") {
                    $title = "SMS sent to ".$client_name." on ".date("D dS M Y",strtotime($select_registration_date));
                    $select_registration_date = date("Ymd",strtotime($select_registration_date));
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` LIKE '".$select_registration_date."%' AND `account_id` = ? ORDER BY `sms_id` DESC",[$client_id]);
                }elseif ($sms_date_option == "between dates") {
                    $title = "SMS sent to ".$client_name." between ".date("D dS M Y",strtotime($from_select_date))." to ". date("D dS M Y",strtotime($to_select_date));
                    $from = date("YmdHis",strtotime($from_select_date));
                    $to = date("Ymd",strtotime($to_select_date))."235959";
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` BETWEEN ? AND ? AND `account_id` = ? ORDER BY `sms_id` DESC",[$from,$to,$client_id]);
                }else{
                    $title = "All SMS sent to ".$client_name.".";
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `account_id` = ? ORDER BY `sms_id` DESC",[$client_id]);
                }
            }elseif($select_user_option == "specific_user_phone"){
                // get the user data
                if ($sms_date_option == "select date") {
                    $title = "SMS sent to ".$client_phone." on ".date("D dS M Y",strtotime($select_registration_date));
                    $client_phone = $client_phone*=1;
                    $select_registration_date = date("Ymd",strtotime($select_registration_date));
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` LIKE '".$select_registration_date."%' AND `recipient_phone` LIKE '%".$client_phone."%' ORDER BY `sms_id` DESC");
                }elseif ($sms_date_option == "between dates") {
                    $title = "SMS sent to ".$client_phone." between ".date("D dS M Y",strtotime($from_select_date))." to ". date("D dS M Y",strtotime($to_select_date));
                    $from = date("YmdHis",strtotime($from_select_date));
                    $to = date("Ymd",strtotime($to_select_date))."235959";
                    $client_phone = $client_phone*=1;
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` BETWEEN ? AND ? AND `recipient_phone` LIKE '%".$client_phone."%' ORDER BY `sms_id` DESC",[$from,$to]);
                }else{
                    $title = "All SMS sent to ".$client_phone.".";
                    $client_phone = $client_phone*=1;
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `recipient_phone` LIKE '%".$client_phone."%' ORDER BY `sms_id` DESC");
                }
            }
        }elseif ($contain_text_option == "text_containing") {
            if ($select_user_option == "All") {
                if ($sms_date_option == "select date") {
                    $title = "SMS sent on ".date("D dS M Y",strtotime($select_registration_date));
                    $select_registration_date = date("Ymd",strtotime($select_registration_date));
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` LIKE '".$select_registration_date."%' AND `sms_content` LIKE '%".$text_keyword."%' ORDER BY `sms_id` DESC");
                }elseif ($sms_date_option == "between dates") {
                    $title = "SMS sent between ".date("D dS M Y",strtotime($from_select_date))." and ".date("D dS M Y",strtotime($to_select_date));
                    $from = date("YmdHis",strtotime($from_select_date));
                    $to = date("Ymd",strtotime($to_select_date))."235959";
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` BETWEEN ? AND ? AND `sms_content` LIKE '%".$text_keyword."%'  ORDER BY `sms_id` DESC",[$from,$to]);
                }else{
                    $title = "All SMS sent";
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `sms_content` LIKE '%".$text_keyword."%' ORDER BY `sms_id` DESC");
                }
            }elseif($select_user_option == "specific_user"){
                // get the user data
                $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = ? ",[$client_account]);
                if (count($user_data) < 1) {
                    return "<p style='color:red;'>This account number is invalid</p>";
                }
                $client_name = ucwords(strtolower($user_data[0]->client_name));
                $client_id = $user_data[0]->client_id;
                if ($sms_date_option == "select date") {
                    $title = "SMS sent to ".$client_name." on ".date("D dS M Y",strtotime($select_registration_date));
                    $select_registration_date = date("Ymd",strtotime($select_registration_date));
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` LIKE '".$select_registration_date."%' AND `account_id` = ? AND `sms_content` LIKE '%".$text_keyword."%' ORDER BY `sms_id` DESC",[$client_id]);
                }elseif ($sms_date_option == "between dates") {
                    $title = "SMS sent to ".$client_name." between ".date("D dS M Y",strtotime($from_select_date))." to ". date("D dS M Y",strtotime($to_select_date));
                    $from = date("YmdHis",strtotime($from_select_date));
                    $to = date("Ymd",strtotime($to_select_date))."235959";
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` BETWEEN ? AND ? AND `account_id` = ? AND `sms_content` LIKE '%".$text_keyword."%' ORDER BY `sms_id` DESC",[$from,$to,$client_id]);
                }else{
                    $title = "All SMS sent to ".$client_name.".";
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `account_id` = ? AND `sms_content` LIKE '%".$text_keyword."%' ORDER BY `sms_id` DESC",[$client_id]);
                }
            }elseif($select_user_option == "specific_user_phone"){
                // get the user data
                if ($sms_date_option == "select date") {
                    $title = "SMS sent to ".$client_phone." on ".date("D dS M Y",strtotime($select_registration_date));
                    $client_phone = $client_phone*=1;
                    $select_registration_date = date("Ymd",strtotime($select_registration_date));
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` LIKE '".$select_registration_date."%' AND `recipient_phone` LIKE '%".$client_phone."%' AND `sms_content` LIKE '%".$text_keyword."%' ORDER BY `sms_id` DESC");
                }elseif ($sms_date_option == "between dates") {
                    $title = "SMS sent to ".$client_phone." between ".date("D dS M Y",strtotime($from_select_date))." to ". date("D dS M Y",strtotime($to_select_date));
                    $from = date("YmdHis",strtotime($from_select_date));
                    $to = date("Ymd",strtotime($to_select_date))."235959";
                    $client_phone = $client_phone*=1;
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` BETWEEN ? AND ? AND `recipient_phone` LIKE '%".$client_phone."%' AND `sms_content` LIKE '%".$text_keyword."%' ORDER BY `sms_id` DESC",[$from,$to]);
                }else{
                    $title = "All SMS sent to ".$client_phone.".";
                    $client_phone = $client_phone*=1;
                    $sms_data = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `recipient_phone` LIKE '%".$client_phone."%' AND `sms_content` LIKE '%".$text_keyword."%' ORDER BY `sms_id` DESC");
                }
            }
            $title.=" containing \"".$text_keyword."\"";
        }
        
        // return $sms_data;
        $new_sms_data = [];
        for ($index=0; $index < count($sms_data); $index++) { 
            $data = array(
                ($index+1),
                $sms_data[$index]->sms_content,
                $sms_data[$index]->date_sent,
                $sms_data[$index]->recipient_phone,
            );
            array_push($new_sms_data,$data);
        }

        // print as pdf
        $pdf = new PDF('L','mm',"A4");
        $pdf->setHeaderPos(300);
        $pdf->set_document_title($title);
        $pdf->AddPage();
        $pdf->SetFont('Times', 'B', 10);
        $pdf->SetMargins(3,3);
        $pdf->Cell(40, 10, "Statistics", 0, 0, 'L', false);
        $pdf->Ln();
        $pdf->SetFont('Times', 'I', 9);
        $pdf->Cell(40, 5, "SMS Count :", 'B', 0, 'L', false);
        $pdf->Cell(30, 5, count($new_sms_data) . " SMS(es)", 'B', 0, 'L', false);
        $pdf->SetFont('Helvetica', 'BU', 9);
        $pdf->Ln();
        $pdf->Cell(300,8,"SMS Table",0,1,"C",false);
        $pdf->SetFont('Helvetica', 'B', 7);
        $width = array(15,225,30,20);
        $header = array('No','SMS Content','Date Sent','Phone');
        $pdf->smsTable($header,$new_sms_data,$width);
        $pdf->Output("I",$title,false);
    }
}
