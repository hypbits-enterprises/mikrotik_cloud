<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\sms_client;
use App\Models\sms_table;
use App\Models\sms_clients_package;

class billsms_manager extends Controller
{
    function getBilledClients(){
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `sms_clients` WHERE `deleted` = '0' ORDER BY `client_id` DESC");
        $package_name = [];
        $packages_list = DB::connection("mysql2")->select("SELECT * FROM `sms_clients_packages` WHERE `deleted` = '0'");
        for ($index=0; $index < count($client_data); $index++) {
            $packages = "<span class='text-danger'>Not Set</span>";
            for ($indexed=0; $indexed < count($packages_list); $indexed++) { 
                if ($packages_list[$indexed]->package_id == $client_data[$index]->packages) {
                    $packages = $packages_list[$indexed]->package_name;
                    break;
                }
            }
            array_push($package_name,$packages);
        }
        return view('billsms',['client_data' => $client_data,"package_name" => $package_name]);
    }
    function newClient(){
        $client_accounts = [];
        $client_username = [];
        $client_lc_acc = [];
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `sms_clients` WHERE `deleted` = '0' ORDER BY `client_id` DESC");
        for ($index=0; $index < count($client_data); $index++) { 
            array_push($client_accounts,$client_data[$index]->account_number);
            array_push($client_username,$client_data[$index]->username);
            array_push($client_lc_acc,$client_data[$index]->licence_acc_number);
        }
        return view("newsmsclient", ['client_accounts' => $client_accounts,'client_username' => $client_username, 'client_lc_acc' => $client_lc_acc]);
    }
    function myPackages(){
        $myPackages = DB::connection("mysql2")->select("SELECT * FROM `sms_clients_packages` WHERE `deleted` = '0'");
        return view("packages",["packages" => $myPackages]);
    }
    function newPackage(){
        return view("newpackage");
    }
    function registerPackage(Request $req){
        // return $req->input();
        $packages = new sms_clients_package();
        $packages->package_name = $req->input("package_names");
        $packages->free_trial_period = $req->input("free_trial_period");
        $packages->payment_intervals = $req->input("payment_inetervals");
        $packages->amount_to_pay = $req->input("package_prices");
        $packages->save();
        session()->flash("data_success","The data has been saved successfully!");
        return redirect("/BillingSms/Packages");
    }
    function viewPackages($user_id){
        $packages = DB::connection("mysql2")->select("SELECT * FROM `sms_clients_packages` WHERE `deleted` = '0' AND `package_id` = '".$user_id."'");
        if (count($packages) > 0) {
            return view("viewpackage",["packages" => $packages]);
        }else {
            session()->flash("error_clients","Invalid Package");
            return redirect("/BillingSms/Packages");
        }
    }
    function updatePackage(Request $req){
        // return $req->input();
        // update the tables
        DB::connection("mysql2")->table("sms_clients_packages")->where("package_id",$req->input("package_id"))->update([
            "package_name" => $req->input("package_names"),
            "free_trial_period" => $req->input("free_trial_period"),
            "payment_intervals" => $req->input("payment_inetervals"),
            "amount_to_pay" => $req->input("package_prices"),
            "date_changed" => date("YmdHis")
        ]);
        session()->flash("data_success","Updates has been done successfully!");
        return redirect("/BillingSms/ViewPackage/".$req->input("package_id"));
    }
    function deletePackage($package_id){
        DB::connection("mysql2")->update("UPDATE `sms_clients_packages` SET `date_changed` = ?, `deleted` = '1' WHERE `id` = ?",[date("YmdHis"),$package_id]);
        session()->flash("error_clients","Package has been deleted successfuly!");
        return redirect("/BillingSms/Packages");
    }
    function showPackages(){
        $packages = DB::connection("mysql2")->select("SELECT * FROM `sms_clients_packages` WHERE `deleted` = '0'");
        $data_to_display = "<select name='package_list' id='package_list' required class='form-control'><option value='' hidden>Select an option</option>";
        for ($indexes=0; $indexes < count($packages); $indexes++) { 
            $data_to_display.="<option value='".$packages[$indexes]->package_id."'>".$packages[$indexes]->package_name."</option>";
        }
        $data_to_display.="</select>";
        if (count($packages) > 0) {
            echo $data_to_display;
        }else{
            echo "<p class='text-danger'>Please set a package to proceed!</p>";
        }
    }
    function registerClient(Request $req){
        // return $req->input();
        // store in session so that when an error is found the information is restored
        session()->flash("client_name",$req->input("client_name"));
        session()->flash("client_address",$req->input("client_address"));
        session()->flash("client_phone",$req->input("client_phone"));
        session()->flash("client_email",$req->input("client_email"));
        session()->flash("client_acc_number",$req->input("client_acc_number"));
        session()->flash("client_sms_rates",$req->input("client_sms_rates"));
        session()->flash("comments",$req->input("comments"));
        session()->flash("client_username",$req->input("client_username"));
        session()->flash("client_password",$req->input("client_password"));
        session()->flash("licence_acc_no",$req->input("licence_acc_no"));
        
        // check if the client with that account number is present
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `sms_clients` WHERE `deleted` = '0' AND (`account_number` = '".$req->input("client_acc_number")."' OR `licence_acc_number` = '".$req->input("licence_acc_no")."')");
        if (count($client_data) < 1) {
            // save the data in the database
            $sms_clients = new sms_client();
            $sms_clients->client_name = $req->input("client_name");
            $sms_clients->client_location = $req->input("client_address");
            $sms_clients->phone_number = $req->input("client_phone");
            $sms_clients->email = $req->input("client_email");
            $sms_clients->account_number = $req->input("client_acc_number");
            $sms_clients->sms_rate = $req->input("client_sms_rates");
            $sms_clients->comments = $req->input("comments");
            $sms_clients->username = $req->input("client_username");
            $sms_clients->password = $req->input("client_password");
            $sms_clients->licence_acc_number = $req->input("licence_acc_no");
            $sms_clients->packages = $req->input("package_list");
            $sms_clients->status = "1";
            $sms_clients->save();
            // if message is to be sent get the message body from the data
            $send_sms = $req->input("send_sms");
            if ($send_sms == "on") {
                $message = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'Messages' AND `deleted` = '0'");
                $value = json_decode($message[0]->value);
                $present = 0;
                $wlcm_sms = "";
                $messages = $value[4]->messages;
                for ($index=0; $index < count($messages); $index++) { 
                    $msg = $messages[$index];
                    if ($msg->Name == "welcome_client_sms") {
                        $wlcm_sms = $msg->message;
                        $present = 1;
                    }
                }
                // return $wlcm_sms;
                if ($present == 1) {
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `sms_clients` WHERE `account_number` = '".$req->input("client_acc_number")."' AND `deleted` = '0'");
                    // send the message
                    $wlcm_sms = $this->message_content_smsclients($wlcm_sms,$client_data[0]->client_id,0);
                    // return $wlcm_sms;
                    // send sms
                    // get the sms keys
                    $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_api_key' AND `deleted` = '0'");
                    $sms_api_key = $sms_keys[0]->value;
                    $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_partner_id' AND `deleted` = '0'");
                    $sms_partner_id = $sms_keys[0]->value;
                    $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_shortcode' AND `deleted` = '0'");
                    $sms_shortcode = $sms_keys[0]->value;
                    $partnerID = $sms_partner_id;
                    $apikey = $sms_api_key;
                    $shortcode = $sms_shortcode;
                    $mobile = $req->input("client_phone");

                    $message = $wlcm_sms;
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
                    $sms_type = "2";
                    $sms_table = new sms_table();
                    $sms_table->sms_content = $message;
                    $sms_table->date_sent = date("YmdHis");
                    $sms_table->recipient_phone = $mobile;
                    $sms_table->sms_status = $message_status;
                    $sms_table->account_id = $client_data[0]->client_id;
                    $sms_table->sms_type = $sms_type;
                    $sms_table->save();
                }
            }
            session()->flash("success","The user has been successfully added!");
            return redirect("/BillingSms/Manage");
        }else{
            session()->flash("data_error","The account number OR the licence number provided exists!");
            return redirect("/BillingSms/New");
        }
    }

    private function setEnv($key, $value)
    {
        $path = base_path('.env');
    
        if(is_bool(env($key)))
        {
            $old = env($key)? 'true' : 'false';
        }
        elseif(env($key)===null){
            $old = 'null';
        }
        else{
            $old = env($key);
        }
    
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                "$key=".$old, "$key=".$value, file_get_contents($path)
            ));
        }
    }

    function renew_Licence(Request $req){
        // get the action first
        $action = $req->input("lc_actions");
        // return $action;
        if ($action == "renew") {
            // generate a 20 digit code that is seperated by a hyphen of four digits
            $licence = "";
            $hex = ["0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F"];
            for ($index=1; $index <= 20; $index++) { 
                $random = rand(0,15);
                if ($index%4 == 0) {
                    $licence .= $hex[$random]."-";
                }else {
                    $licence .= $hex[$random];
                }
            }
            $licence = substr($licence,0,-1);
            $date = date("YmdHis",strtotime($req->input("lc_expiration_date")));
            // return $date;
            // go ahead and update the database of the new licence key
            DB::connection("mysql2")->table("sms_clients")->where("client_id",$req->input("clients_id"))->update([
                "licence_number" => $licence,
                "licence_expiry" => $date,
                "date_changed" => date("YmdHis")
            ]);
            session()->flash("success","The user licence has been created successfully! When the user is connected to the internet they will recieve the new licence Key");
            return redirect("/BillingSms/ViewClient/".$req->input("clients_id")."");
        }elseif ($action == "extend") {
            // CHECK IF THE LICENCE EXISTS
            $select = DB::connection("mysql2")->select("SELECT * FROM `sms_clients` WHERE `client_id` = '".$req->input("clients_id")."' AND `deleted` = '0'");
            // return $select;
            if ($select[0]->licence_number  == null || strlen(trim($select[0]->licence_number)) < 1) {
                // create a licence
                // generate a 20 digit code that is seperated by a hyphen of four digits
                $licence = "";
                $hex = ["0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F"];
                for ($index=1; $index <= 20; $index++) { 
                    $random = rand(0,15);
                    if ($index%4 == 0) {
                        $licence .= $hex[$random]."-";
                    }else {
                        $licence .= $hex[$random];
                    }
                }
                $licence = substr($licence,0,-1);
                $date = date("YmdHis",strtotime($req->input("lc_expiration_date")));
                // return $date;
                // go ahead and update the database of the new licence key
                DB::connection("mysql2")->table("sms_clients")->where("client_id",$req->input("clients_id"))->update([
                    "licence_number" => $licence,
                    "licence_expiry" => $date,
                    "date_changed" => date("YmdHis")
                ]);
                session()->flash("success","The user licence has been created successfully! When the user is connected to the internet they will recieve the new licence Key");
                return redirect("/BillingSms/ViewClient/".$req->input("clients_id")."");
            }else {
                $date = date("YmdHis",strtotime($req->input("lc_expiration_date")));
                // return $date;
                // go ahead and update the database of the new licence key
                DB::connection("mysql2")->table("sms_clients")->where("client_id",$req->input("clients_id"))->update([
                    "licence_expiry" => $date,
                    "date_changed" => date("YmdHis")
                ]);
                session()->flash("success","The user licence has been extended successfully! When the user is connected to the internet they will be extended");
                return redirect("/BillingSms/ViewClient/".$req->input("clients_id")."");
            }
        }else {
            session()->flash("data_error","Select an option before proceeding");
            return redirect("/BillingSms/ViewClient/".$req->input("clients_id")."");
        }
    }

    function displayClient($clientid){
        // get the user data oif the user is not present return them to the client table
        $user_data = DB::connection("mysql2")->select("SELECT * FROM `sms_clients` WHERE `client_id` = '".$clientid."' AND `deleted` = '0'");
        // return $user_data;
        if (count($user_data) > 0) {
            $client_data = DB::connection("mysql2")->select("SELECT * FROM `sms_clients` WHERE `client_id` != '".$clientid."' AND `deleted` = '0'");
            $client_username = [];
            for ($index=0; $index < count($client_data); $index++) { 
                array_push($client_username,$client_data[$index]->account_number);
            }
            // $user_data[0]->client_name = ucwords(strtolower($user_data[0]->client_name));
            $user_data = $user_data[0];
            // get the package the user is enrolled
            $packages = DB::connection("mysql2")->select("SELECT * FROM `sms_clients_packages` WHERE `package_id` = '".$user_data->packages."' AND `deleted` = '0'");
            $package_name = count($packages) > 0 ? $packages[0]->package_name : "Not Set";
            // return $package_name;
            return view("viewsmsclient",['client_username' => $client_username,'user_data' => $user_data,'client_id'=>$clientid, "package_name" => $package_name]);
        }else {
            session()->flash("error_clients","The user is invalid!");
            return redirect("/BillingSms/Manage");
        }
    }
    function updateClient(Request $req){
        // get the client id
        $clientid = $req->input("client_id");
        // check if its valid
        $user_data = DB::connection("mysql2")->select("SELECT * FROM `sms_clients` WHERE `client_id` = '".$clientid."' AND `deleted` = '0'");
        if (count($user_data) > 0) {
            // UPDATE THE USER
            DB::connection("mysql2")->table("sms_clients")
            ->where("client_id",$clientid)
            ->update([
                "client_name" => $req->input("client_name"),
                "client_location" => $req->input("client_address"),
                "phone_number" => $req->input("client_phone"),
                "email" => $req->input("client_email"),
                "sms_rate" => $req->input("client_sms_rates"),
                "comments" => $req->input("comments"),
                "username" => $req->input("client_username"),
                "password" => $req->input("client_password"),
                "packages" => $req->input("package_list"),
                "date_changed" => date("YmdHis")
            ]);
            session()->flash("success","The has been updated successfully!");
            return redirect("/BillingSms/ViewClient/".$clientid."");
        }else {
            session()->flash("error_clients","The user is invalid!");
            return redirect("/BillingSms/Manage");
        }
    }
    // delete client
    function deleteClient($clientid){
        // return $clientid;
        // check if the user is a valid client
        $user_data = DB::connection("mysql2")->select("SELECT * FROM `sms_clients` WHERE `client_id` = '".$clientid."' AND `deleted` = '0'");
        if (count($user_data) > 0){
            DB::connection("mysql2")->delete("DELETE FROM `sms_clients` WHERE `client_id` = '".$clientid."'");
            // DB::connection("mysql2")->update("UPDATE `sms_clients` SET `date_changed` = ? AND deleted = '1' WHERE `client_id` = ?",[date("YmdHis"),$clientid]);
            session()->flash("success","The client has been deleted successfull!");
            return redirect("/BillingSms/Manage");
        }else {
            session()->flash("data_error","The client is in-valid!");
            return redirect("/BillingSms/Manage");
        }
    }
    // deactivate client 
    function deactivateClient($client_id){
        // return $client_id;
        DB::connection("mysql2")->table("sms_clients")->where("client_id",$client_id)
        ->update(['status' => '0',"date_changed" => date("YmdHis")]);
        session()->flash("success","The has been deactivated successfully!");
        return redirect("/BillingSms/ViewClient/".$client_id."");
    }
    // deactivate client 
    function activateClient($client_id){
        // return $client_id;
        DB::connection("mysql2")->table("sms_clients")->where("client_id",$client_id)
        ->update(['status' => '1',"date_changed" => date("YmdHis")]);
        session()->flash("success","The has been activated successfully!");
        return redirect("/BillingSms/ViewClient/".$client_id."");
    }
    function changeSmsBal(Request $req){
        // return $req;
        $clients_id = $req->input("clients_id");
        $sms_balances = $req->input("sms_balances");
        DB::connection("mysql2")->table("sms_clients")->where("client_id",$clients_id)->update(['sms_balance' => $sms_balances,"date_changed" => date("YmdHis")]);
        session()->flash("success","The has been activated successfully!");
        return redirect("/BillingSms/ViewClient/".$clients_id."");
    }
    function viewTransaction(){
        $transaction_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_sms_tables` WHERE `deleted` = '0'  ORDER by `transaction_id` DESC");
        $date = date("Ymd");
        $account_names = [];
        $dates_infor = [];
        for ($index=0; $index < count($transaction_data); $index++) { 
            // return $transaction_data[$index]->transaction_account;
            $client_data = DB::connection("mysql2")->select("SELECT * FROM `sms_clients` WHERE `deleted` = '0' AND `account_number` = '".$transaction_data[$index]->transaction_account."'");
            $client_name = isset($client_data[0]->client_name) ? $client_data[0]->client_name : $transaction_data[$index]->transaction_account;
            array_push($account_names,$client_name);

            $date_data = $transaction_data[$index]->transaction_date;
            // return $date_data;
            $year = substr($date_data,0,4);
            $month = substr($date_data,4,2);
            $day = substr($date_data,6,2);
            $hour = substr($date_data,8,2);
            $minute = substr($date_data,10,2);
            $second = substr($date_data,12,2);
            $d = mktime($hour, $minute, $second, $month, $day, $year);
            $dates = date("dS-M-Y  h:i:sa", $d);
            array_push($dates_infor,$dates);
        }
        // return $dates_infor;
        $todayDate = date("YmdHis");
        $weekAgo = date("YmdHis",strtotime("-7 days"));
        $twoWeeksAgo = date("YmdHis",strtotime("-14 days"));
        $amonthAgo = date("YmdHis",strtotime("-14 days"));
        $sums = DB::connection("mysql2")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_sms_tables` WHERE `transaction_date` LIKE '$date%' AND `deleted` = '0';");
        $week = DB::connection("mysql2")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_sms_tables` WHERE `transaction_date` BETWEEN '$weekAgo' AND '$todayDate' AND `deleted` = '0'");
        $twoWeek = DB::connection("mysql2")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_sms_tables` WHERE `transaction_date` BETWEEN '$twoWeeksAgo' AND '$todayDate' AND `deleted` = '0'");
        $months = DB::connection("mysql2")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_sms_tables` WHERE `transaction_date` BETWEEN '$amonthAgo' AND '$todayDate' AND `deleted` = '0'");
        return view("billsms_transaction",["transaction_data" => $transaction_data, "today" => $sums,"week" => $week,"month" => $months,"twoweeks" => $twoWeek ,"account_name" => $account_names,"trans_dates" => $dates_infor]);
    }
    function viewTransactionDetails($transaction_id){
        // return $transaction_id;
        // get the transaction details and pass them to the ciew
        $transaction_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_sms_tables` WHERE `transaction_id` = $transaction_id AND `deleted` = '0'");
        $date_data = $transaction_data[0]->transaction_date;
        $year = substr($date_data,0,4);
        $month = substr($date_data,4,2);
        $day = substr($date_data,6,2);
        $hour = substr($date_data,8,2);
        $minute = substr($date_data,10,2);
        $second = substr($date_data,12,2);
        $d = mktime($hour, $minute, $second, $month, $day, $year);
        $dates = date("dS-M-Y  h:i:sa", $d);

        // get the client the money was paid to
        $transaction_acc_id	 = $transaction_data[0]->transaction_acc_id;
        // return $transaction_acc_id;
        $user_data = DB::connection("mysql2")->select("SELECT * FROM `sms_clients` WHERE `client_id` = '$transaction_acc_id' AND `deleted` = '0'");
        if (count($user_data) > 0) {
            $user_fullname = $user_data[0]->client_name;
        }else {
            $user_fullname = "Null";
        }

        // get the clients data
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `sms_clients` WHERE `deleted` = '0'");
        return view("billsms_transdets",["transaction_data" => $transaction_data, "dates" => $dates, "user_fullname"=>$user_fullname, "client_data"=>$client_data]);
    }
    function assignTransaction($transaction_id, $client_id){
        // return $transaction_id." ".$client_id;
        $transaction_detail = DB::connection("mysql2")->select("SELECT * FROM `transaction_sms_tables` WHERE `transaction_id` = '$transaction_id' AND `deleted` = '0'");
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `sms_clients` WHERE `client_id` = '$client_id' AND `deleted` = '0'");

        // Transaction date
        $date_data = $transaction_detail[0]->transaction_date;
        $year = substr($date_data,0,4);
        $month = substr($date_data,4,2);
        $day = substr($date_data,6,2);
        $hour = substr($date_data,8,2);
        $minute = substr($date_data,10,2);
        $second = substr($date_data,12,2);
        $d = mktime($hour, $minute, $second, $month, $day, $year);
        $dates2 = date("D dS M Y  h:i:sa", $d);
        return view("billsms_acceptTransfer",["client_data" => $client_data, "transaction_details" => $transaction_detail,"transaction_id" => $transaction_id, "transaction_date" => $dates2]);
    }
    // transfer funds
    function transferFunds($client_id,$transaction_id){
        // return $client_id." ".$transaction_id;
        // get sms rates 
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `sms_clients` WHERE `client_id` = '".$client_id."' AND `deleted` = '0'");
        $sms_rates = $client_data[0]->sms_rate;
        $sms_balance = $client_data[0]->sms_balance;
        // get the transaction amount
        $transaction_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_sms_tables` WHERE `transaction_id` = '".$transaction_id."' AND `deleted` = '0'");
        $transacion_amount = $transaction_data[0]->transacion_amount;
        $sms_data = round($transacion_amount/$sms_rates) + $sms_balance;
        // return $sms_data;
        // update the SMS balance.
        DB::connection("mysql2")->table("sms_clients")->where("client_id",$client_id)->update(['sms_balance' => $sms_data,"date_changed" => date("YmdHis")]);
        // update the transaction status.
        DB::connection("mysql2")->table("transaction_sms_tables")->where("transaction_id",$transaction_id)->update(['transaction_status' => "1","date_changed" => date("YmdHis")]);
        session()->flash("success","The transfer has been done successfully!");
        return redirect("/BillingSms/Transactions");
        
    }
	function message_content_smsclients($data,$user_id,$trans_amount) {
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `sms_clients` WHERE `client_id` = ? AND `deleted` = '0'",[$user_id]);
		$full_name = $client_data[0]->client_name;
        $f_name = ucfirst(strtolower((explode(" ",$full_name)[0])));
		$address = $client_data[0]->client_location;
		$contacts = $client_data[0]->phone_number;
		$account_no = $client_data[0]->account_number;
		$username = $client_data[0]->username;
		$password = $client_data[0]->password;
        $sms_rates = $client_data[0]->sms_rate;
        $sms_balance = $client_data[0]->sms_balance;
		$trans_amount = isset($trans_amount)?$trans_amount:"Null";
		// edited
		$today = date("dS-M-Y");
		$now = date("H:i:s");
		$data = str_replace("[client_name]", $full_name, $data);
		$data = str_replace("[client_f_name]", $f_name, $data);
		$data = str_replace("[client_addr]", $address, $data);
		$data = str_replace("[client_phone]", $contacts, $data);
		$data = str_replace("[acc_no]", $account_no, $data);
		$data = str_replace("[username]", $username, $data);
		$data = str_replace("[password]", $password, $data);
		$data = str_replace("[trans_amnt]", "Ksh ".$trans_amount, $data);
		$data = str_replace("[today]", $today, $data);
		$data = str_replace("[now]", $now,$data);
        $data = str_replace("[sms_rate]", "Ksh ".$sms_rates,$data);
        $data = str_replace("[sms_balance]", $sms_balance,$data);
		return $data;
	}
}
