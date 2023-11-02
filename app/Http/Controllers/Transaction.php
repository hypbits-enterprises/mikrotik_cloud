<?php
namespace App\Http\Controllers;

use App\Classes\reports\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\sms_table;
use App\Models\transaction_table;
use Illuminate\Contracts\Session\Session;
use App\Models\transaction_sms_table;
use mysqli;

date_default_timezone_set('Africa/Nairobi');
class Transaction extends Controller
{
    // get user data
    function getClientName($client_account){
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = ?",[$client_account]);
        $client_name = count($client_data) > 0 ? $client_data[0]->client_name : "Null";
        return ucwords(strtolower($client_name));
    }
    function getClientNames($client_account,$client_acc_id){
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = ?",[$client_account]);
        if (count($client_data) > 0) {
            return ucwords(strtolower($client_data[0]->client_name));
        }
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = ?",[$client_acc_id]);
        $client_name = count($client_data) > 0 ? $client_data[0]->client_name : "Null";
        return ucwords(strtolower($client_name));
    }
    // generate reports 
    function generateReports(Request $req){
        // return $req;
        $transaction_date_option = $req->input('transaction_date_option');
        $from_select_date = $req->input('from_select_date');
        $to_select_date = $req->input('to_select_date');
        $select_registration_date = $req->input('select_registration_date');
        $select_user_option = $req->input('select_user_option');
        $client_account = $req->input('client_account');

        // sort in two options of the client specific or a group
        $title = "";
        $transaction_data = [];
        if ($select_user_option == "All") {
            if ($transaction_date_option == "all dates") {
                $title = "All Transactions done!";
                $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' ORDER BY `transaction_id` DESC");
            }elseif ($transaction_date_option == "select date") {
                $title = "All Transactions on ".date("D dS M Y", strtotime($select_registration_date))."!";
                $date = date("Ymd",strtotime($select_registration_date));
                $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".$date."%' ORDER BY `transaction_id` DESC");
            }elseif ($transaction_date_option == "between dates") {
                $from = date("YmdHis",strtotime($from_select_date));
                $to = date("Ymd",strtotime($to_select_date))."235959";
                $title = "All Transactions done between (".date("D dS M Y", strtotime($from_select_date)).") and (".date("D dS M Y",strtotime($to_select_date)).")!";
                $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` BETWEEN ? AND ? ORDER BY `transaction_id` DESC",[$from,$to]);
            }else{
                $title = "All Transactions done!";
                $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' ORDER BY `transaction_id` DESC");
            }
        }elseif ($select_user_option == "specific_user") {
            $client_names = $this->getClientName($client_account);
            if ($transaction_date_option == "all dates") {
                $title = "All ".$client_names." Transactions done!";
                $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_account` = ? ORDER BY `transaction_id` DESC",[$client_account]);
            }elseif ($transaction_date_option == "select date") {
                $title = "All ".$client_names."`s Transactions done on ".date("D dS M Y",strtotime($select_registration_date))."!";
                $date = date("Ymd",strtotime($select_registration_date));
                $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_account` = ? AND `transaction_date` LIKE '".$date."%' ORDER BY `transaction_id` DESC",[$client_account]);
            }elseif ($transaction_date_option == "between dates") {
                $from = date("YmdHis",strtotime($from_select_date));
                $to = date("Ymd",strtotime($to_select_date))."235959";
                $title = "All ".$client_names."`s Transactions done between (".date("D dS M Y",strtotime($from_select_date)).") AND (".date("D dS M Y",strtotime($to_select_date)).")!";
                $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_account` = ? AND `transaction_date` BETWEEN ? AND ? ORDER BY `transaction_id` DESC",[$client_account,$from,$to]);
            }else{
                $title = "All ".$client_names." Transactions done!";
                $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_account` = ? ORDER BY `transaction_id` DESC",[$client_account]);
            }
        }

        // GET THE TRANSACTION INFORMATION
        $new_transaction_data = [];
        $assigned = 0;
        $un_assigned = 0;
        $assigned_amount = 0;
        $un_assigned_amount = 0;
        for ($index=0; $index < count($transaction_data); $index++) { 
            if ($transaction_data[$index]->transaction_status) {
                $assigned++;
                $assigned_amount += $transaction_data[$index]->transacion_amount;
            }else {
                $un_assigned++;
                $un_assigned_amount += $transaction_data[$index]->transacion_amount;
            }
            $data = array(
                $transaction_data[$index]->transaction_mpesa_id,
                $this->getClientNames($transaction_data[$index]->transaction_account,$transaction_data[$index]->transaction_acc_id)." {".$transaction_data[$index]->transaction_account."}",
                $transaction_data[$index]->phone_transacting,
                $transaction_data[$index]->transacion_amount,
                $transaction_data[$index]->transaction_date,
                $transaction_data[$index]->fullnames,
                ($transaction_data[$index]->transaction_status == "1" ? "Assigned" : "Un-Assigned")
            );
            array_push($new_transaction_data,$data);
        }

        // create pdf
        $pdf = new PDF("P","mm","A4");
        $pdf->set_document_title($title);
        $pdf->AddPage();
        $pdf->SetFont('Times', 'B', 10);
        $pdf->SetMargins(5,5);
        $pdf->Cell(40, 10, "Statistics", 0, 0, 'L', false);
        $pdf->Ln();
        $pdf->Cell(40, 5, "", 0, 0, 'L', false);
        $pdf->Cell(30, 5, "Records", 0, 0, 'L', false);
        $pdf->Cell(30, 5, "Amount", 0, 1, 'L', false);
        $pdf->SetFont('Times', 'I', 9);
        $pdf->Cell(40, 5, "Un-Assigned Payments :", 0, 0, 'L', false);
        $pdf->Cell(30, 5, $un_assigned . " Payment(s)", 0, 0, 'L', false);
        $pdf->Cell(30, 5, "Kes ".number_format($un_assigned_amount), 0, 1, 'L', false);
        $pdf->Cell(40, 5, "Assigned Payments :", 0, 0, 'L', false);
        $pdf->Cell(30, 5, $assigned . " Payment(s)", 0,0, 'L', false);
        $pdf->Cell(30, 5, "Kes ".number_format($assigned_amount), 0, 1, 'L', false);
        $pdf->Cell(40, 5, "Total :", 'T', 0, 'L', false);
        $pdf->Cell(30, 5, ($un_assigned+$assigned) . " Payment(s)", 'T', 0, 'L', false);
        $pdf->Cell(30, 5, "Kes ".number_format($un_assigned_amount + $assigned_amount), 'T', 0, 'L', false);
        $pdf->Ln();
        $pdf->SetFont('Helvetica', 'BU', 9);
        $pdf->Cell(200,8,"Payment(s) Table",0,1,"C",false);
        $pdf->SetFont('Helvetica', 'B', 7);
        $width = array(6,20,40,20,20,40,40,15);
        $header = array('No', 'M-Pesa Code', 'Linked To {Acc Paid To}', 'Phone Number', 'Amount','Date','M-Pesa Fullname', 'Status');
        $pdf->transactionReports($header,$new_transaction_data,$width);
        $pdf->Output("I","transaction_data.pdf",false);

    }
    //create functions to process transactions requests

    function getTransactions(){
        $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0'  ORDER by `transaction_id` DESC");
        $date = date("Ymd");
        $account_names = [];
        $dates_infor = [];
        for ($index=0; $index < count($transaction_data); $index++) { 
            // return $transaction_data[$index]->transaction_account;
            $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '".$transaction_data[$index]->transaction_account."'");
            $client_name = "Null";
            if (count($client_data) > 0) {
                $client_name = $client_data[0]->client_name;
                $transaction_data[$index]->transaction_acc_id = $client_data[0]->client_id;
            }else {
                // get the client name from the account linked to that transaction
                $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = '".$transaction_data[$index]->transaction_acc_id."'");
                $client_name = count($client_data) > 0 ? $client_data[0]->client_name : $transaction_data[$index]->transaction_acc_id;
                $transaction_data[$index]->transaction_acc_id = count($client_data) > 0 ? $client_data[0]->client_id : $transaction_data[$index]->transaction_acc_id;
            }
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
        $amonthAgo = date("YmdHis",strtotime("-1 Month"));
        $sums = DB::select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '$date%';");
        $week = DB::select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` BETWEEN '$weekAgo' AND '$todayDate';");
        $twoWeek = DB::select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` BETWEEN '$twoWeeksAgo' AND '$todayDate';");
        $months = DB::select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` BETWEEN '$amonthAgo' AND '$todayDate';");

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
        return view("mytransactions",["transaction_data" => $transaction_data, "today" => $sums,"week" => $week,"month" => $months,"twoweeks" => $twoWeek ,"account_name" => $account_names,"trans_dates" => $dates_infor,"clients_name" => $clients_name,"clients_acc" => $clients_acc,"clients_phone" => $clients_phone]);
    }
    function transDetails($trans_id){
        // get the transaction details and pass them to the ciew
        $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_id` = $trans_id");
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
        $transaction_account	 = $transaction_data[0]->transaction_account;
        // return $transaction_acc_id;
        $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = '$transaction_acc_id'");
        if (count($user_data) > 0) {
            $user_fullname = $user_data[0]->client_name;
        }else {
            $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '$transaction_account'");
            $user_fullname = (count($user_data) > 0) ? $user_data[0]->client_name : "Null";
        }

        // get the clients data
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0'");
        return view("transaction",["transaction_data" => $transaction_data, "dates" => $dates, "user_fullname"=>$user_fullname, "client_data"=>$client_data]);
    }


    function assignTransaction($transaction_id,$client_id){
        $transaction_detail = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_id` = '$transaction_id'");
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = '$client_id'");
        $date_data = $client_data[0]->next_expiration_date;
        $year = substr($date_data,0,4);
        $month = substr($date_data,4,2);
        $day = substr($date_data,6,2);
        $hour = substr($date_data,8,2);
        $minute = substr($date_data,10,2);
        $second = substr($date_data,12,2);
        $d = mktime($hour, $minute, $second, $month, $day, $year);
        $dates = date("D dS M Y  h:i:sa", $d);

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
        return view("acceptTransfer",["client_data" => $client_data, "transaction_details" => $transaction_detail,"transaction_id" => $transaction_id, "expiration_date" => $dates, "transaction_date" => $dates2]);
    }

    function confirmTransfer($client_id,$trans_id){
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = $client_id");
        $trans_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_id` = '$trans_id'");
        // update the transaction status to 1 and the transaction account id and account number to 1
        $amount = ($trans_data[0]->transacion_amount) + ($client_data[0]->wallet_amount);

        // update the users wallet and the transaction account id account number and the transaction status and return the confirmation message
        DB::table('client_tables')
        ->where('client_id', $client_id)
        ->update([
            'wallet_amount' => $amount,
            'last_changed' => date("YmdHis"),
            'date_changed' => date("YmdHis")
        ]);

        // update the transaction details
        // transaction status, transaction acc number acc id
        DB::table('transaction_tables')
        ->where('transaction_id', $trans_id)
        ->update([
            'transaction_acc_id' => $client_id,
            'transaction_status' => "1",
            'date_changed' => date("YmdHis")
        ]);
        // check if its the user or the admin
        if (session()->has('client_id')) {
            session()->flash("success","You have successfully transfered the funds to your account");
            return redirect("/Payment");
        }
        // log file capture error
        // read the data 
        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
        $existing_txt = fread($myfile,$file_sizes);
        // return $existing_txt;
        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
        $date = date("dS M Y (H:i:sa)");
        $txt = $date.":Fund successfully transfered by  ".session('Usernames')." to ".$client_data[0]->client_name."!\n".$existing_txt;
        // return $txt;
        fwrite($myfile, $txt);
        fclose($myfile);
        // end of log file
        session()->flash("success","You have successfully transfered the funds to ".$client_data[0]->client_name."");
        return redirect("/Transactions/View/$trans_id");
    }
    // HANDLE THE DASHBOARD
    function getDashboard(){
        // get the sms sent only 10 sms max
        $sms_sent = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' ORDER BY `sms_id` DESC LIMIT 5");
        // holds the content,
        // sms status
        
        // get the names of the users
        $user_fullname = [];
        $dates = [];
        foreach ($sms_sent as $value) {
            // get the user is and the date value and change them to readable values
            // fullname
            $id = $value->account_id;
            $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = '$id'");
            $name = (count($user_data) > 0) ? $user_data[0]->client_name : $value->recipient_phone;
            array_push($user_fullname,$name);
            // date
            $date = $value->date_sent;
            $date_data = $date;
            $year = substr($date_data,0,4);
            $month = substr($date_data,4,2);
            $day = substr($date_data,6,2);
            $hour = substr($date_data,8,2);
            $minute = substr($date_data,10,2);
            $second = substr($date_data,12,2);
            $d = mktime($hour, $minute, $second, $month, $day, $year);
            $dates2 = date("D dS M-Y  h:i:sa", $d);
            array_push($dates,$dates2);
        }
        // return the transactions done that day
        $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' ORDER BY `transaction_id` DESC LIMIT 8");
        // loop through the data to get the transaction data
        $fullnames = [];
        $dates_trans = [];
        foreach ($transaction_data as $value) {
            $id = $value->transaction_acc_id;
            $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = '$id'");
            $names = (count($client_data) > 0) ? $client_data[0]->client_name : $value->transaction_account;
            array_push($fullnames,$names);
            $date = $value->transaction_date;
            $date_data = $date;
            $year = substr($date_data,0,4);
            $month = substr($date_data,4,2);
            $day = substr($date_data,6,2);
            $hour = substr($date_data,8,2);
            $minute = substr($date_data,10,2);
            $second = substr($date_data,12,2);
            $d = mktime($hour, $minute, $second, $month, $day, $year);
            $dates2 = date("D dS M-Y  h:i:sa", $d);
            array_push($dates_trans,$dates2);
        }

        // get the client data 
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0'  ORDER BY `client_id` DESC LIMIT 8");
        return view("index", ["sms_sent" => $sms_sent, "fullnames" => $user_fullname, "dates" => $dates, "transaction_data" => $transaction_data, "trans_fullname" => $fullnames, "trans_dates" => $dates_trans, "client_data" => $client_data]);
    }
    // this function below recieves payments from safaricom mpesa
    function mpesaTransactions(Request $response){
        // get the transaction
        // check the account number if its the user known by the system
        // if the user is known by the system add the amount recieved to the user wallet
        // send an sms showing how much is in their wallet
        // show them if they are activated or not
        // if the user is not know register the payment as a pending payment that needs to be attended to
        // send them a message showing them that the account number they have used is invalid
        // get connection to the database and get the values of the users that are due that minute


        //data recieved from mpesa
		$mpesaResponse = $response->getContent();
            // echo $mpesaResponse;
         $jsonMpesaResponse = json_decode($mpesaResponse, true);
         if(isset($jsonMpesaResponse)){
            //  check the account number to know the user
            $acc_no = trim($jsonMpesaResponse['BillRefNumber']);
            $ipo = 0;
            // CHECK IF ITS HLC
            if (substr(strtoupper($acc_no),0,3) == "HLC") {
                $ipo = 1;
                // get the client details
                $licence_dets = DB::select("SELECT * FROM `sms_clients` WHERE `deleted`= '0' AND `licence_acc_number` = '".$acc_no."'");
                if (count($licence_dets) > 0) {
                    // the user has put correct account details
                    // check what payment plan they are in
                    $payment_plan = DB::select("SELECT * FROM `sms_clients_packages` WHERE `deleted`= '0' AND `package_id` = '".$licence_dets[0]->packages."'");
                    // check if the user has made any payments if not check when they were registered if its after the free period
                    // return $payment_plan;
                    if (count($payment_plan) > 0) {
                        if ($payment_plan[0]->amount_to_pay >= $jsonMpesaResponse['TransAmount']) {
                            // they are enrolled in a payment plan
                            $any_transaction = DB::select("SELECT * FROM `transaction_sms_tables` WHERE `deleted`= '0' AND `transaction_account` = '".$acc_no."'");
                            // if there is a transaction done this means the user was done with the free trial
                            // return $any_transaction;
                            if (count($any_transaction) > 0) {
                                // produce licences
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
                                $free_trial_period = $payment_plan[0]->free_trial_period;
                                $payment_intervals = $payment_plan[0]->payment_intervals;
                                // this means they are done with the free trial
                                // check if their expiry date is ahead of today
                                $expiry_dates = date("Ymd",strtotime($licence_dets[0]->licence_expiry));
                                $today = date("Ymd");
    
                                if ($expiry_dates > $today) {
                                    $date = date_create($expiry_dates);
                                    date_add($date,date_interval_create_from_date_string($payment_intervals));
                                    $next_expiration_date = date_format($date,"YmdHis");
    
                                    // uodate the database
                                    DB::table("sms_clients")->where("licence_acc_number",$acc_no)->update([
                                        "licence_expiry" => $next_expiration_date,
                                        "licence_number" => $licence,
                                        'date_changed' => date("YmdHis")
                                    ]);
                                    $transactions = new transaction_sms_table();
                                    $transactions->transaction_mpesa_id = $jsonMpesaResponse['TransID'];
                                    $transactions->transaction_date = $jsonMpesaResponse['TransTime'];
                                    $transactions->transacion_amount = $jsonMpesaResponse['TransAmount'];
                                    $transactions->phone_transacting = $jsonMpesaResponse['MSISDN'];
                                    $transactions->transaction_account = $jsonMpesaResponse['BillRefNumber'];
                                    $transactions->transaction_acc_id = $licence_dets[0]->client_id;
                                    $transactions->transaction_status = "1";
                                    $transactions->transaction_short_code = $jsonMpesaResponse['BusinessShortCode'];
                                    $second_name = isset($jsonMpesaResponse['MiddleName']) ? $jsonMpesaResponse['MiddleName'] : "";
                                    $last_name = isset($jsonMpesaResponse['LastName']) ? $jsonMpesaResponse['LastName'] : "";
                                    $transactions->fullnames = str_replace("'","-",$jsonMpesaResponse['FirstName']." ".$second_name." ".$last_name);
                                    $transactions->save();
                                    return $transactions;
                                }else {
                                    $today = date("YmdHis");
                                    $date = date_create($today);
                                    date_add($date,date_interval_create_from_date_string($payment_intervals));
                                    $next_expiration_date = date_format($date,"YmdHis");
    
                                    // uodate the database
                                    DB::table("sms_clients")->where("licence_acc_number",$acc_no)->update([
                                        "licence_expiry" => $next_expiration_date,
                                        "licence_number" => $licence,
                                        'date_changed' => date("YmdHis")
                                    ]);
                                    $transactions = new transaction_sms_table();
                                    $transactions->transaction_mpesa_id = $jsonMpesaResponse['TransID'];
                                    $transactions->transaction_date = $jsonMpesaResponse['TransTime'];
                                    $transactions->transacion_amount = $jsonMpesaResponse['TransAmount'];
                                    $transactions->phone_transacting = $jsonMpesaResponse['MSISDN'];
                                    $transactions->transaction_account = $jsonMpesaResponse['BillRefNumber'];
                                    $transactions->transaction_acc_id = $licence_dets[0]->client_id;
                                    $transactions->transaction_status = "1";
                                    $transactions->transaction_short_code = $jsonMpesaResponse['BusinessShortCode'];
                                    $transactions->fullnames = str_replace("'","_",$jsonMpesaResponse['FirstName']);
                                    $transactions->save();
                                    return $transactions;
                                }
                            }else {
                                // they have not made any payment
                                // check the day they were registered and add the free trial period and it should be equal to the date today 
                                // so that we know its ending that day or if its positive add the days if negative add the day from today
                                $date_reg = date("Ymd",strtotime($licence_dets[0]->date_joined));
                                $free_trial_period = $payment_plan[0]->free_trial_period;
                                $payment_intervals = $payment_plan[0]->payment_intervals;
                                $date = date_create($date_reg);
                                date_add($date,date_interval_create_from_date_string($free_trial_period));
                                $date_free_trial_end = date_format($date,"Ymd");
                                $date_today = date("Ymd");
                                // return $date_free_trial_end;
                                if ($date_free_trial_end > $date_today){
                                    // get the number of days its ahead and add the period to the date found
                                    $date1=date_create($date_today);
                                    $date2=date_create($date_free_trial_end);
                                    $diff=date_diff($date1,$date2);
                                    $days_diff =  $diff->format("%R%a days");
                                    // add the difference found in the days
                                    $today = date("YmdHis",strtotime($days_diff));
                                    // take the period between and add the date
                                    
                                    $date = date_create($today);
                                    date_add($date,date_interval_create_from_date_string($payment_intervals));
                                    $next_expiration_date = date_format($date,"YmdHis");
                                    // produce licences
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
                                    // uodate the database
                                    DB::table("sms_clients")->where("licence_acc_number",$acc_no)->update([
                                        "licence_expiry" => $next_expiration_date,
                                        "licence_number" => $licence,
                                        'date_changed' => date("YmdHis")
                                    ]);
                                    $transactions = new transaction_sms_table();
                                    $transactions->transaction_mpesa_id = $jsonMpesaResponse['TransID'];
                                    $transactions->transaction_date = $jsonMpesaResponse['TransTime'];
                                    $transactions->transacion_amount = $jsonMpesaResponse['TransAmount'];
                                    $transactions->phone_transacting = $jsonMpesaResponse['MSISDN'];
                                    $transactions->transaction_account = $jsonMpesaResponse['BillRefNumber'];
                                    $transactions->transaction_acc_id = $licence_dets[0]->client_id;
                                    $transactions->transaction_status = "1";
                                    $transactions->transaction_short_code = $jsonMpesaResponse['BusinessShortCode'];
                                    $transactions->fullnames = str_replace("'","_",$jsonMpesaResponse['FirstName']);
                                    $transactions->save();
                                    return $transactions;
                                }else {
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
                                    // add the date of expiration as today
                                    $next_expiration_date = date("YmdHis",strtotime($payment_intervals));
                                    DB::table("sms_clients")->where("licence_acc_number",$acc_no)->update([
                                        "licence_expiry" => $next_expiration_date,
                                        "licence_number" => $licence,
                                        'date_changed' => date("YmdHis")
                                    ]);
                                    $transactions = new transaction_sms_table();
                                    $transactions->transaction_mpesa_id = $jsonMpesaResponse['TransID'];
                                    $transactions->transaction_date = $jsonMpesaResponse['TransTime'];
                                    $transactions->transacion_amount = $jsonMpesaResponse['TransAmount'];
                                    $transactions->phone_transacting = $jsonMpesaResponse['MSISDN'];
                                    $transactions->transaction_account = $jsonMpesaResponse['BillRefNumber'];
                                    $transactions->transaction_acc_id = $licence_dets[0]->client_id;
                                    $transactions->transaction_status = "1";
                                    $transactions->transaction_short_code = $jsonMpesaResponse['BusinessShortCode'];
                                    $transactions->fullnames = str_replace("'","_",$jsonMpesaResponse['FirstName']);
                                    $transactions->save();
                                    return $transactions;
                                }
                            }
                        }else {
                            echo "Client has not paid enough";
                        }
                    }else {
                        return "Client not enrolled in the Payment Plan";
                    }
                }else {
                    echo "Invalid User!";
                }
            }
            // CHECK IF ITS FOR THE HSMS
            if (substr(strtoupper($acc_no),0,4) == "HSMS" && $ipo == 0) {
                $ipo = 1;
                $transStatus = 0;
                // this here processes transactions for the sms clients
                // check if the clients is a valid client
                $smsclient = DB::select("SELECT * FROM `sms_clients` WHERE `deleted`= '0' AND `account_number` = '".$acc_no."'");
                if (count($smsclient) > 0) {
                    $transStatus = 1;
                    // the client is present
                    // check if the amount paid is less than the minimum amount
                    $transaction_amnt = $jsonMpesaResponse['TransAmount'];
                    if ($transaction_amnt >= 1000) {
                        // convert the cash to sms
                        $sms_rate = $smsclient[0]->sms_rate;
                        $sms_balance = $smsclient[0]->sms_balance;
                        $phone_number = $smsclient[0]->phone_number;
                        $trans_amnt = $jsonMpesaResponse['TransAmount'];
                        $new_sms = round($trans_amnt/$sms_rate);
                        // save the new balance
                        // return $new_sms;
                        $sms_balance+=$new_sms;
                        DB::table("sms_clients")->where("client_id",$smsclient[0]->client_id)->update([
                            "sms_balance" => $sms_balance,
                            'date_changed' => date("YmdHis")
                        ]);
                        // its less than the minimum amount
                        // send message for the invalid account number
                        $sms_data = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'Messages'");
                        $sms_contents = json_decode($sms_data[0]->value);
                        $messages = $sms_contents[4]->messages;
                        $msg = "";
                        // return $messages;
                        for ($indexes=0; $indexes < count($messages); $indexes++) { 
                            $msg_data = $messages[$indexes];
                            if ($msg_data->Name == "rcv_coracc_billsms") {
                                $msg = $msg_data->message;
                            }
                        }
                        $msg = $this->message_content($msg,$smsclient[0]->client_id,$jsonMpesaResponse['TransAmount'],0,0,"sms_client");
                        // send message for the invalid account number
                        if (strlen(trim($msg)) > 0) {
                            // get the sms keys
                            $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_api_key'");
                            $sms_api_key = $sms_keys[0]->value;
                            $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_partner_id'");
                            $sms_partner_id = $sms_keys[0]->value;
                            $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_shortcode'");
                            $sms_shortcode = $sms_keys[0]->value;
            
            
                            $partnerID = $sms_partner_id;
                            $apikey = $sms_api_key;
                            $shortcode = $sms_shortcode;
                            $mobile = $phone_number;
                            $message = $msg;
                            $sms_type = 1;
                            
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
                            // get the user id of the number from the database
                            $user_data = DB::select("SELECT * FROM `sms_clients` WHERE `deleted`= '0' AND `account_number` = '".$jsonMpesaResponse['BillRefNumber']."'");
                            $client_id = (count($user_data) > 0) ? $user_data[0]->client_id : 0;
                            // if the message status is one the message is already sent to the user
                            $sms_table = new sms_table();
                            $sms_table->sms_content = $message;
                            $sms_table->date_sent = date("YmdHis");
                            $sms_table->recipient_phone = $mobile;
                            $sms_table->sms_status = $message_status;
                            $sms_table->account_id = $client_id;
                            $sms_table->sms_type = $sms_type;
                            $sms_table->save();
                        }
                    }else {
                        // convert the cash to sms
                        $sms_rate = $smsclient[0]->sms_rate;
                        $sms_balance = $smsclient[0]->sms_balance;
                        $phone_number = $smsclient[0]->phone_number;
                        $trans_amnt = $jsonMpesaResponse['TransAmount'];
                        $new_sms = round($trans_amnt/$sms_rate);
                        // save the new balance
                        $sms_balance+=$new_sms;
                        DB::table("sms_clients")->where("client_id",$smsclient[0]->client_id)->update([
                            "sms_balance" => $sms_balance,
                            'date_changed' => date("YmdHis")
                        ]);
                        // its less than the minimum amount
                        // send message for the invalid account number
                        $sms_data = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'Messages'");
                        $sms_contents = json_decode($sms_data[0]->value);
                        $messages = $sms_contents[4]->messages;
                        $msg = "";
                        for ($indexes=0; $indexes < count($messages); $indexes++) { 
                            $msg_data = $messages[$indexes];
                            if ($msg_data->Name == "rcv_belowmin_billsms") {
                                $msg = $msg_data->message;
                            }
                        }
                        $msg = $this->message_content($msg,$smsclient[0]->client_id,$jsonMpesaResponse['TransAmount'],0,0,"sms_client");
                        // send message for the invalid account number
                        if (strlen(trim($msg)) > 0) {
                            // get the sms keys
                            $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_api_key'");
                            $sms_api_key = $sms_keys[0]->value;
                            $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_partner_id'");
                            $sms_partner_id = $sms_keys[0]->value;
                            $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_shortcode'");
                            $sms_shortcode = $sms_keys[0]->value;
            
            
                            $partnerID = $sms_partner_id;
                            $apikey = $sms_api_key;
                            $shortcode = $sms_shortcode;
                            $mobile = $phone_number;
                            $message = $msg;
                            $sms_type = 1;
                            
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
                            // get the user id of the number from the database
                            $user_data = DB::select("SELECT * FROM `sms_clients` WHERE `deleted`= '0' AND `account_number`= '".$jsonMpesaResponse['BillRefNumber']."'");
                            $client_id = (count($user_data) > 0) ? $user_data[0]->client_id : 0;
                            // if the message status is one the message is already sent to the user
                            $sms_table = new sms_table();
                            $sms_table->sms_content = $message;
                            $sms_table->date_sent = date("YmdHis");
                            $sms_table->recipient_phone = $mobile;
                            $sms_table->sms_status = $message_status;
                            $sms_table->account_id = $client_id;
                            $sms_table->sms_type = $sms_type;
                            $sms_table->save();
                        }
                    }
                }else {
                    // send message for the invalid account number
                    $sms_data = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'Messages'");
                    $sms_contents = json_decode($sms_data[0]->value);
                    $messages = $sms_contents[4]->messages;
                    $msg = "";
                    for ($indexes=0; $indexes < count($messages); $indexes++) { 
                        $msg_data = $messages[$indexes];
                        if ($msg_data->Name == "rcv_incoracc_billsms") {
                            $msg = $msg_data->message;
                        }
                    }
                    $msg = $this->message_content($msg,0,$jsonMpesaResponse['TransAmount'],0,0,"sms_client");
                    // send message for the invalid account number
                    if (strlen(trim($msg)) > 0) {
                        // get the sms keys
                        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_api_key'");
                        $sms_api_key = $sms_keys[0]->value;
                        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_partner_id'");
                        $sms_partner_id = $sms_keys[0]->value;
                        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_shortcode'");
                        $sms_shortcode = $sms_keys[0]->value;
        
        
                        $partnerID = $sms_partner_id;
                        $apikey = $sms_api_key;
                        $shortcode = $sms_shortcode;
                        $mobile = $jsonMpesaResponse['MSISDN'];
                        $message = $msg;
                        $sms_type = 1;
                        
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
                        // get the user id of the number from the database
                        $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '".$jsonMpesaResponse['BillRefNumber']."'");
                        $client_id = (count($user_data) > 0) ? $user_data[0]->client_id : 0;
                        // if the message status is one the message is already sent to the user
                        $sms_table = new sms_table();
                        $sms_table->sms_content = $message;
                        $sms_table->date_sent = date("YmdHis");
                        $sms_table->recipient_phone = $mobile;
                        $sms_table->sms_status = $message_status;
                        $sms_table->account_id = $client_id;
                        $sms_table->sms_type = $sms_type;
                        $sms_table->save();
                    }
                }
                // save the data in the transaction table
                $transTable = new transaction_sms_table();
                $transTable->transaction_mpesa_id = $jsonMpesaResponse['TransID'];
                $transTable->transaction_date = $jsonMpesaResponse['TransTime'];
                $transTable->transacion_amount = $jsonMpesaResponse['TransAmount'];
                $transTable->phone_transacting = $jsonMpesaResponse['MSISDN'];
                $transTable->transaction_account = $jsonMpesaResponse['BillRefNumber'];
                $transTable->transaction_acc_id = $client_id;
                $transTable->transaction_status = $transStatus;
                $transTable->transaction_short_code = $jsonMpesaResponse['BusinessShortCode'];
                $transTable->fullnames = str_replace("'","_",$jsonMpesaResponse['FirstName']);
                $transTable->save();
            }
            // return substr(strtolower($acc_no),0,4);

            // ipo is used to check if its hypbits clients
            if ($ipo == 0) {
                $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '$acc_no'");
                $phone_number = $jsonMpesaResponse['MSISDN'];
                $client_id = 0;
                $client_transaction_id = 0;
                $transStatus = "0";
                if (count($user_data) > 0) {
                    $transStatus = "1";
                    // this is if the account number belongs to a user in the database;
                    // get the wallet and add the new wallet
                    // if the clients transaction amount is below the required minimum amount the account wont be activated
                    $wallet = $user_data[0]->wallet_amount + ($jsonMpesaResponse['TransAmount'] * 1);
                    $client_id = $user_data[0]->client_id;
                    $monthly_payments = $user_data[0]->monthly_payment;

                    // calculate the minimum amount to pay
                    $minimum_pay_percent = $user_data[0]->min_amount/100;
                    $minimum_payment = ceil($monthly_payments * $minimum_pay_percent);
                    // return $minimum_payment;


                    $client_transaction_id = $client_id;
                    // the user available amount is greater than the minimum amount
                    // update the wallet amount and send the sms to the user
                    DB::table("client_tables")->where('client_id', $user_data[0]->client_id)->update(["wallet_amount" => $wallet, 'last_changed' => date("YmdHis"),'date_changed' => date("YmdHis")]);
                    // send sms and record it
                    // GET THE SMS KEYS FROM THE DATABASE
                    // check if the user phone number is same to the one stored in the database
                    $phone_mpesa = (strlen($phone_number) == 12) ? substr($phone_number,3,9) : substr($phone_number,1,9);
                    $phone_db = (strlen($user_data[0]->clients_contacts) == 12) ? substr($user_data[0]->clients_contacts,3,9) : substr($user_data[0]->clients_contacts,1,9);
                    $same = ($phone_mpesa == $phone_db) ? 1 : 0;
    
                    // get the sms keys
                    $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_api_key'");
                    $sms_api_key = $sms_keys[0]->value;
                    $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_partner_id'");
                    $sms_partner_id = $sms_keys[0]->value;
                    $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_shortcode'");
                    $sms_shortcode = $sms_keys[0]->value;
    
    
                    $partnerID = $sms_partner_id;
                    $apikey = $sms_api_key;
                    $shortcode = $sms_shortcode;
                    $mobile = "";
                    $message = "";
                    $sms_type = 1;
    
                    $mobile = "254$phone_db"; // Bulk messages can be comma separated
                    // send sms
                    $message_contents = $this->get_sms();
                    $message = "";
                    if ($wallet >= $minimum_payment){
                        $message = $message_contents[1]->messages[0]->message;
                    }else {
                        $message = $message_contents[1]->messages[3]->message;
                    }
                    if ($message) {// replace false with message above
                        $trans_amount = $jsonMpesaResponse['TransAmount'];
                        $message = $this->message_content($message,$client_id,$trans_amount);
                        // send_sms($conn,$row['clients_contacts'],$message,$row['client_id']);
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
                        // get the user id of the number from the database
                        $user_data = DB::select("SELECT * FROM `sms_clients` WHERE `deleted`= '0' AND `account_number` = '".trim($jsonMpesaResponse['BillRefNumber'])."'");
                        $client_id = (count($user_data) > 0) ? $user_data[0]->client_id : 0;
                        // if the message status is one the message is already sent to the user
                        $sms_table = new sms_table();
                        $sms_table->sms_content = $message;
                        $sms_table->date_sent = date("YmdHis");
                        $sms_table->recipient_phone = $mobile;
                        $sms_table->sms_status = $message_status;
                        $sms_table->account_id = $client_transaction_id;
                        $sms_table->sms_type = $sms_type;
                        $sms_table->save();
                    }
                    $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '".trim($jsonMpesaResponse['BillRefNumber'])."'");
                    // check if the user has a refferer then share the cut to the user
                    $user_data[0]->reffered_by = str_replace("'","\"",$user_data[0]->reffered_by);
                    $client_refferal = strlen($user_data[0]->reffered_by) > 0? json_decode($user_data[0]->reffered_by): json_decode("{}");
                    if (isset($client_refferal->client_acc)) {
                        // echo $client_refferal->client_acc;
                        // proceed if the refferee is owed more than 1 shilling a month
                        if (($client_refferal->monthly_payment*1) > 0) {
                            // get the precentage the refferer is to be paid of the amount paid
                            $percentage = round((($client_refferal->monthly_payment * 100) / $monthly_payments),2);
                            $refferal_amount = round($percentage * ($jsonMpesaResponse['TransAmount'] * 1)) / 100;
                            $refferer_dets = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '".$client_refferal->client_acc."'");
                            // add the refferal amount to the wallet
                            $new_wallet_balance = ($refferal_amount*1) + ($refferer_dets[0]->wallet_amount*1);
                            DB::table("client_tables")->where('client_id', $refferer_dets[0]->client_id)->update(["wallet_amount" => $new_wallet_balance,'last_changed' => date("YmdHis"),'date_changed' => date("YmdHis")]);
                            $reffer_phone = $refferer_dets[0]->clients_contacts;
                            $mobile = $reffer_phone; // Bulk messages can be comma separated
                            $new_payment = array("amount" => $refferal_amount,"date" => date("YmdHis"));
                            array_push($client_refferal->payment_history,$new_payment);
                            $payments = json_encode($client_refferal);
                            // update the main client payments
                            DB::table("client_tables")->where('client_account',$jsonMpesaResponse['BillRefNumber'])->update(["reffered_by" => $payments,'date_changed' => date("YmdHis")]);
                            // send sms
                            $message_contents = $this->get_sms();
                            $message = $message_contents[1]->messages[2]->message;
                            if ($message) {// replace false with message above
                                $trans_amount = $refferal_amount;
                                $refferer_id = trim($jsonMpesaResponse['BillRefNumber']);
                                $message = $this->message_content($message,$refferer_dets[0]->client_id,$trans_amount,$trans_amount,$refferer_id);
                                // send_sms($conn,$row['clients_contacts'],$message,$row['client_id']);
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
                                // get the user id of the number from the database
                                $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '".$client_refferal->client_acc."'");
                                $client_id = (count($user_data) > 0) ? $user_data[0]->client_id : 0;
                                // if the message status is one the message is already sent to the user
                                $sms_table = new sms_table();
                                $sms_table->sms_content = $message;
                                $sms_table->date_sent = date("YmdHis");
                                $sms_table->recipient_phone = $mobile;
                                $sms_table->sms_status = $message_status;
                                $sms_table->account_id = $client_id;
                                $sms_table->sms_type = $sms_type;
                                $sms_table->save();
                            }
                        }
                    }
                }else {
                    // if the user is not known
                    // send the sms showing that the transaction is pending
    
                    // get the sms keys
                    $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_api_key'");
                    $sms_api_key = $sms_keys[0]->value;
                    $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_partner_id'");
                    $sms_partner_id = $sms_keys[0]->value;
                    $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_shortcode'");
                    $sms_shortcode = $sms_keys[0]->value;
    
    
                    $partnerID = $sms_partner_id;
                    $apikey = $sms_api_key;
                    $shortcode = $sms_shortcode;
                    $mobile = $jsonMpesaResponse['MSISDN'];
                    $message_contents = $this->get_sms();
                    $message = $message_contents[1]->messages[1]->message;
                    if ($message) {// replace false with message
                        $trans_amount = $jsonMpesaResponse['TransAmount'];
                        $message = $this->message_content($message,$client_id,$trans_amount);
                        // send the sms
                        $finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
                        $ch = \curl_init();
                        \curl_setopt($ch, CURLOPT_URL, $finalURL);
                        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        $response = \curl_exec($ch);
                        \curl_close($ch);
                        $res = json_decode($response);
                        // return $res;
                        $values = (isset($res->responses[0])) ? $res->responses[0] : $res->success ;
                        // return $values;
                        if ((isset($res->responses[0]))) {
                            if ($values != "false") {
                                $message_status = 0;
                                foreach ($values as  $key => $value) {
                                    // echo $key;
                                    if ($key == "response-code") {
                                        if ($value == "200") {
                                            // if its 200 the message is sent delete the
                                            $message_status = 1;
                                        }
                                    }
                                }
                    
                                // save to the database the transaction made
                                $sms_table = new sms_table();
                                $sms_table->sms_content = $message;
                                $sms_table->date_sent = date("YmdHis");
                                $sms_table->recipient_phone = $mobile;
                                $sms_table->sms_status = $message_status;
                                $sms_table->account_id = "0";
                                $sms_table->sms_type = "1";
                                $sms_table->save();
                            }
                        }
                    }
                }
    
                // save the data in the transaction table
                $clientelle = str_replace("'","_",$jsonMpesaResponse['FirstName']);
                $transTable = new transaction_table();
                $transTable->transaction_mpesa_id = $jsonMpesaResponse['TransID'];
                $transTable->transaction_date = $jsonMpesaResponse['TransTime'];
                $transTable->transacion_amount = $jsonMpesaResponse['TransAmount'];
                $transTable->phone_transacting = $jsonMpesaResponse['MSISDN'];
                $transTable->transaction_account = $jsonMpesaResponse['BillRefNumber'];
                $transTable->transaction_acc_id = $client_transaction_id;
                $transTable->transaction_status = $transStatus;
                $transTable->transaction_short_code = $jsonMpesaResponse['BusinessShortCode'];
                $transTable->fullnames = $clientelle;
                $transTable->save();
            }

            // log file capture error
            // read the data
            $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
            $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
            $existing_txt = fread($myfile,$file_sizes);
            // return $existing_txt;
            $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
            $date = date("dS M Y (H:i:sa)");
            $txt = $date.":Fund successfully recieved from  ".$jsonMpesaResponse['FirstName']."!\n".$existing_txt;
            // return $txt;
            fwrite($myfile, $txt);
            fclose($myfile);
            // end of log file
        }
    }

    function refferalCUt($user_data,$monthly_payments,$jsonMpesaResponse){
        // check if the user has a refferer then share the cut to the user
        $client_refferal = strlen($user_data[0]->reffered_by) > 0? json_decode($user_data[0]->reffered_by): json_decode("{}");
        if (isset($client_refferal->client_acc)) {
            // get the sms keys
            $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_api_key'");
            $sms_api_key = $sms_keys[0]->value;
            $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_partner_id'");
            $sms_partner_id = $sms_keys[0]->value;
            $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_shortcode'");
            $sms_shortcode = $sms_keys[0]->value;
            $partnerID = $sms_partner_id;
            $apikey = $sms_api_key;
            $shortcode = $sms_shortcode;

            // get the precentage the refferer is to be paid of the amount paid
            $percentage = round(($client_refferal->monthly_payment * 100) / $monthly_payments,2);
            $refferal_amount = round($percentage * ($jsonMpesaResponse['TransAmount'] * 1));
            $refferer_dets = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '".$client_refferal->client_acc."'");
            // add the refferal amount to the wallet
            $new_wallet_balance = $refferal_amount + $refferer_dets[0]->wallet_amount;
            DB::table("client_tables")->where('client_id', $refferer_dets[0]->client_id)->update(["wallet_amount" => $new_wallet_balance,'last_changed' => date("YmdHis"),'date_changed' => date("YmdHis")]);
            $reffer_phone = $refferer_dets[0]->clients_contacts;
            $mobile = $reffer_phone; // Bulk messages can be comma separated
            $new_payment = array("amount" => $refferal_amount,"date" => date("YmdHis"));
            array_push($client_refferal->payment_history,$new_payment);
            $payments = json_encode($client_refferal);
            // update the main client payments
            DB::table("client_tables")->where('client_account',$jsonMpesaResponse['BillRefNumber'])->update(["reffered_by" => $payments,'date_changed' => date("YmdHis")]);
            // send sms
            $message_contents = $this->get_sms();
            $message = $message_contents[1]->messages[0]->message;
            if ($message) {// replace false with message above
                $trans_amount = $new_wallet_balance;
                $message = $this->message_content($message,$refferer_dets[0]->client_id,$trans_amount);
                // send_sms($conn,$row['clients_contacts'],$message,$row['client_id']);
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
                $sms_type = 1;
                // get the user id of the number from the database
                $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '".$client_refferal->client_acc."'");
                $client_id = (count($user_data) > 0) ? $user_data[0]->client_id : 0;
                // if the message status is one the message is already sent to the user
                $sms_table = new sms_table();
                $sms_table->sms_content = $message;
                $sms_table->date_sent = date("YmdHis");
                $sms_table->recipient_phone = $mobile;
                $sms_table->sms_status = $message_status;
                $sms_table->account_id = $client_id;
                $sms_table->sms_type = $sms_type;
                $sms_table->save();
            }
        }
    }

    function stkpush(){
        // get the clients id 
        // push stk
        $client_id =  session("client_id");
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = '$client_id'");
        $phone_number = strlen($client_data[0]->clients_contacts) == 12? $client_data[0]->clients_contacts: "254".substr($client_data[0]->clients_contacts,1);
        $monthly_payment = $client_data[0]->monthly_payment;
        $acc_no = $client_data[0]->client_account;

        // get the consumer key
        $key = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'consumer_key'");
        $consumer_key = $key[0]->value;

        $key = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'consumer_secret'");
        $consumer_secret = $key[0]->value;

        $key = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'passkey'");
        $passkey = $key[0]->value;

        $key = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'paybill'");
        $paybillno = $key[0]->value;
        $time = date("YmdHis");

        // send stk push
		$password = base64_encode($consumer_key.':'.$consumer_secret);
		$headers = [
			'Authorization: Basic '.$password,
			'Content-Type:application/json; charset=utf8'
		];
        
        $consumerKey = $consumer_key; //Fill with your app Consumer Key
        $consumerSecret = $consumer_secret; // Fill with your app Secret
        $headers = ['Content-Type:application/json; charset=utf8'];
        $url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = json_decode($result);
        $access_token = isset($result->access_token)? $result->access_token:"0";
        // return $access_token;

        
        // after the access token get the stk push
        if ($access_token != "0") {
            // push the stk
            $ch = curl_init('https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer '.$access_token,
                'Content-Type: application/json'
            ]);
            $password = base64_encode($paybillno.$passkey.$time);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_USERPWD, $consumer_key.":".$consumer_secret);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "{
                \"BusinessShortCode\": $paybillno,
                \"Password\": \"$password\",
                \"Timestamp\": \"$time\",
                \"TransactionType\": \"CustomerPayBillOnline\",
                \"Amount\": $monthly_payment,
                \"PartyA\": $phone_number,
                \"PartyB\": $paybillno,
                \"PhoneNumber\": $phone_number,
                \"CallBackURL\": \"https://mydomain.com/path\",
                \"AccountReference\": \"$acc_no\",
                \"TransactionDesc\": \"Pay HypBits\"
            }");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $response = json_decode(curl_exec($ch));
            curl_close($ch);
            if (isset($response->ResponseCode)) {
                session()->flash("success_stk","Please check your phone and enter your password to complete your transaction");
                return redirect("/Payment");
            }else {
                session()->flash("error_stk","Please reload your page and try again OR use Paybill : $paybillno and acc no $acc_no");
                return redirect("/Payment");
            }
        }else {
            session()->flash("error_stk","Please reload your page and try again OR use Paybill : $paybillno and acc no $acc_no");
            return redirect("/Payment");
        }
    }
	function get_sms(){
        $data = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'Messages'");
        return json_decode($data[0]->value);
	}
	function message_content($data,$user_id,$trans_amount,$refferer_amount = 'Null',$refferer_acc = "0" ,$user_type = "net_client") {
        if ($user_type == "net_client") {
            $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = '$user_id'");
            $refferal_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '$refferer_acc'");
            $refferal_name = count($refferal_data) > 0? $refferal_data[0]->client_name:"Null";
            $refferal_f_name = count($refferal_data) > 0? explode(" ",$refferal_data[0]->client_name)[0]:"Null";
            if (count($client_data) > 0) {
                $exp_date = $client_data[0]->next_expiration_date;
                $reg_date = $client_data[0]->clients_reg_date;
                $monthly_payment = $client_data[0]->monthly_payment;
                $full_name = $client_data[0]->client_name;
                $f_name = ucfirst(strtolower((explode(" ",$full_name)[0])));
                $address = $client_data[0]->client_address;
                $internet_speeds = $client_data[0]->max_upload_download;
                $contacts = $client_data[0]->clients_contacts;
                $account_no = $client_data[0]->client_account;
                $wallet = $client_data[0]->wallet_amount;
                $username = $client_data[0]->client_username;
                $password = $client_data[0]->client_password;
                $trans_amount = isset($trans_amount)?$trans_amount:"Null";

                // get the minimum monthly payment
                // $number = $monthly_payment/4;
                // $minimum_payment = ceil($number/10) * 10;
                $minimum_pay_percent = $client_data[0]->min_amount/100;
                $minimum_payment = ceil($monthly_payment * $minimum_pay_percent);
                // edited
                $today = date("dS-M-Y");
                $now = date("H:i:s");
                $time = $exp_date;
                $exp_date = date("dS-M-Y",strtotime($exp_date));
                $exp_time = date("H:i:s",strtotime($time));
                $reg_date = date("dS-M-Y",strtotime($reg_date));
                $data = str_replace("[client_name]", $full_name, $data);
                $data = str_replace("[client_f_name]", $f_name, $data);
                $data = str_replace("[client_addr]", $address, $data);
                $data = str_replace("[exp_date]", $exp_date." at ".$exp_time, $data);
                $data = str_replace("[reg_date]", $reg_date, $data);
                $data = str_replace("[int_speeds]", $internet_speeds, $data);
                $data = str_replace("[monthly_fees]", "Ksh ".$monthly_payment, $data);
                $data = str_replace("[client_phone]", $contacts, $data);
                $data = str_replace("[acc_no]", $account_no, $data);
                $data = str_replace("[client_wallet]", "Ksh ".$wallet, $data);
                $data = str_replace("[username]", $username, $data);
                $data = str_replace("[password]", $password, $data);
                $data = str_replace("[trans_amnt]", "Ksh ".$trans_amount, $data);
                $data = str_replace("[today]", $today, $data);
                $data = str_replace("[now]", $now,$data);
                $data = str_replace("[min_amnt]", $minimum_payment,$data);
                $data = str_replace("[refferer_trans_amount]", $refferer_amount,$data);
                $data = str_replace("[refferer_name]", $refferal_name,$data);
                $data = str_replace("[refferer_f_name]", $refferal_f_name,$data);
                return $data;
            }else {
                $exp_date = "Null";
                $reg_date = "Null";
                $monthly_payment = "Null";
                $full_name = "Null";
                $f_name = ucfirst(strtolower((explode(" ",$full_name)[0])));
                $address = "Null";
                $internet_speeds = "Null";
                $contacts = "Null";
                $account_no = "Null";
                $wallet = "Null";
                $username = "Null";
                $password = "Null";
                $trans_amount = isset($trans_amount)?$trans_amount:"Null";
                $minimum_payment = isset($trans_amount)?(ceil($trans_amount/4)*10):"Null";
                // edited
                $today = date("dS-M-Y");
                $now = date("H:i:s");
                $time = $exp_date;
                $exp_date = date("dS-M-Y",strtotime($exp_date));
                $exp_time = date("H:i:s",strtotime($time));
                $reg_date = date("dS-M-Y",strtotime($reg_date));
                $data = str_replace("[client_name]", $full_name, $data);
                $data = str_replace("[client_f_name]", $f_name, $data);
                $data = str_replace("[client_addr]", $address, $data);
                $data = str_replace("[exp_date]", $exp_date." at ".$exp_time, $data);
                $data = str_replace("[reg_date]", $reg_date, $data);
                $data = str_replace("[int_speeds]", $internet_speeds, $data);
                $data = str_replace("[monthly_fees]", "Ksh ".$monthly_payment, $data);
                $data = str_replace("[client_phone]", $contacts, $data);
                $data = str_replace("[acc_no]", $account_no, $data);
                $data = str_replace("[client_wallet]", "Ksh ".$wallet, $data);
                $data = str_replace("[username]", $username, $data);
                $data = str_replace("[password]", $password, $data);
                $data = str_replace("[trans_amnt]", "Ksh ".$trans_amount, $data);
                $data = str_replace("[today]", $today, $data);
                $data = str_replace("[now]", $now,$data);
                $data = str_replace("[min_amnt]", $minimum_payment,$data);
                $data = str_replace("[refferer_trans_amount]", $refferer_amount,$data);
                $data = str_replace("[refferer_name]", $refferal_name,$data);
                $data = str_replace("[refferer_f_name]", $refferal_f_name,$data);
                return $data;
            }
        }elseif ($user_type == "sms_client") {
            $client_data = DB::select("SELECT * FROM `sms_clients` WHERE `deleted`= '0' AND `client_id` = '$user_id'");
            if (count($client_data) > 0) {
                $full_name = ucwords(strtolower($client_data[0]->client_name));
                $f_name = ucfirst(strtolower((explode(" ",$full_name)[0])));
                $address = $client_data[0]->client_location;
                $contacts = $client_data[0]->phone_number;
                $account_no = $client_data[0]->account_number;
                $sms_balance = $client_data[0]->sms_balance;
                $username = $client_data[0]->username;
                $password = $client_data[0]->password;
                $sms_rate = $client_data[0]->sms_rate;
                $trans_amount = isset($trans_amount)?$trans_amount:"Null";
                $minimum_payment = ceil(1000);
                // edited
                $today = date("dS-M-Y");
                $now = date("H:i:s");
                $data = str_replace("[client_name]", $full_name, $data);
                $data = str_replace("[client_f_name]", $f_name, $data);
                $data = str_replace("[client_addr]", $address, $data);
                $data = str_replace("[sms_rate]", "Ksh ".$sms_rate, $data);
                $data = str_replace("[sms_balance]", $sms_balance, $data);
                $data = str_replace("[client_phone]", $contacts, $data);
                $data = str_replace("[acc_no]", $account_no, $data);
                $data = str_replace("[username]", $username, $data);
                $data = str_replace("[password]", $password, $data);
                $data = str_replace("[trans_amnt]", "Ksh ".$trans_amount, $data);
                $data = str_replace("[today]", $today, $data);
                $data = str_replace("[now]", $now,$data);
                $data = str_replace("[min_amnt]", $minimum_payment,$data);
                return $data;
            }else {
                $full_name = "Null";
                $f_name = "Null";
                $address = "NUll";
                $contacts = "Null";
                $account_no = "Null";
                $sms_balance = "Null";
                $username = "Null";
                $password = "Null";
                $sms_rate = "Null";
                $trans_amount = isset($trans_amount)?$trans_amount:"Null";
                $minimum_payment = ceil(1000);
                // edited
                $today = date("dS-M-Y");
                $now = date("H:i:s");
                $data = str_replace("[client_name]", $full_name, $data);
                $data = str_replace("[client_f_name]", $f_name, $data);
                $data = str_replace("[client_addr]", $address, $data);
                $data = str_replace("[sms_rate]", "Ksh ".$sms_rate, $data);
                $data = str_replace("[sms_balance]", $sms_balance, $data);
                $data = str_replace("[client_phone]", $contacts, $data);
                $data = str_replace("[acc_no]", $account_no, $data);
                $data = str_replace("[username]", $username, $data);
                $data = str_replace("[password]", $password, $data);
                $data = str_replace("[trans_amnt]", "Ksh ".$trans_amount, $data);
                $data = str_replace("[today]", $today, $data);
                $data = str_replace("[now]", $now,$data);
                $data = str_replace("[min_amnt]", $minimum_payment,$data);
                return $data;
            }
        }
	}

    function transactionStatistics(){
        // get the data for weeks months and years
        $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        
        // date today
        $date_today = date("D");
        
        // get how many days we are after the week starts
        $date_index = 0;
        for ($index=0; $index < count($days); $index++) { 

            if ($date_today == $days[$index]) {
                break;
            }
            $date_index++;
        }

        // substract today with the date index value to get when the week starts
        $last_week_start = date("YmdHis",strtotime(-$date_index." days"));
        $last_end_week = $this->addDays($last_week_start,6);

        // get when the first client made their payment
        $first_payment = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' ORDER BY `transaction_date` ASC LIMIT 1");
        $first_payment_date = count($first_payment) > 0 ? $first_payment[0]->transaction_date : date("YmdHis");
        // return $first_payment_date;

        // get when the week started when the first payment was made
        $first_pay_day = date("D",strtotime($first_payment_date));
        // return $first_pay_day;

        $date_index = 0;
        for ($i=0; $i < count($days); $i++) { 
            if ($first_pay_day == $days[$i]) {
                break;
            }
            $date_index++;
        }
        // return $date_index;

        // get when the week start date
        $first_pay_week_start = $this->addDays($first_payment_date,-$date_index);
        $day_1 = $first_pay_week_start;
        // return date("D dS M Y",strtotime($day_1));

        $transaction_stats_weekly = [];
        $transaction_records_weekly = [];
        $break = false;
        $counter = 0;
        while (true) {
            $trans_stats = [];
            $trans_records = [];
            for ($index=0; $index < 7; $index++) {
                $get_amount_per_day = DB::select("SELECT SUM(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".date("Ymd",strtotime($day_1))."%'");
                $daily_trans_records = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".date("Ymd",strtotime($day_1))."%' ORDER BY `transaction_date` DESC");
                $trans_amount = $get_amount_per_day[0]->Total == null ? 0 : $get_amount_per_day[0]->Total;


                for ($indexex=0; $indexex < count($daily_trans_records); $indexex++) { 
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '".$daily_trans_records[$indexex]->transaction_account."'");
                    $client_name = isset($client_data[0]->client_name) ? $client_data[0]->client_name : $daily_trans_records[$indexex]->transaction_account;
                    // array_push($account_names,$client_name);
                    $daily_trans_records[$indexex]->account_names = $client_name;
                }

                $transaction_data = array("date" => date("D dS M",strtotime($day_1)),"trans_amount" => $trans_amount);
                // echo date("D dS M Y",strtotime($day_1))." Amounts".$trans_amount."<br>";
                array_push($trans_stats,$transaction_data);
                array_push($trans_records,$daily_trans_records);
                
                if (date("Ymd",strtotime($last_end_week)) == date("Ymd",strtotime($day_1))) {
                    $break = true;
                }
                $day_1 = $this->addDays($day_1,1);
            }
            $counter++;
            // echo $counter." Weeks <hr>";
            array_push($transaction_stats_weekly,$trans_stats);
            array_push($transaction_records_weekly,$trans_records);
            if ($break) {
                break;
            }
        }
        // return $transaction_records_weekly;

        // get the transaction data for monthly
         // date today
         $month_today = date("M");
        
         // get how many days we are after the week starts
         $months_index = 0;
         for ($index=0; $index < count($months); $index++) { 
 
             if ($month_today == $months[$index]) {
                 break;
             }
             $months_index++;
         }
        //  return $months_index;
         // substract today with the date index value to get when the week starts
         $last_month_start = date("YmdHis",strtotime(-$months_index." months"));
         $last_end_month = $this->addMonths($last_month_start,11);
        //  return $months_index;
 
         // get when the first client made their payment
         $first_payment = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0'  ORDER BY `transaction_date` ASC LIMIT 1");
         $first_payment_date = count($first_payment) > 0 ? $first_payment[0]->transaction_date : date("YmdHis");
         // return $first_payment_date;
 
         // get when the week started when the first payment was made
         $first_pay_month = date("M",strtotime($first_payment_date));

         $months_index = 0;
         for ($i=0; $i < count($months); $i++) { 
             if ($first_pay_month == $months[$i]) {
                 break;
             }
             $months_index++;
         }
        //  return $months_index;
 
         // get when the week start date
         $first_pay_month_start = $this->addMonths($first_payment_date,-$months_index);
         $day_1 = $first_pay_month_start;
        //  return date("D dS M Y",strtotime($day_1));
 
         $transaction_stats_monthly = [];
         $transaction_records_monthly = [];
         $break = false;
         $counter = 0;
         while (true) {
             $trans_stats = [];
             $trans_records = [];
             for ($index=0; $index < 12; $index++) {
                 $get_amount_per_day = DB::select("SELECT SUM(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".date("Ym",strtotime($day_1))."%'");
                 $daily_trans_records = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".date("Ym",strtotime($day_1))."%' ORDER BY `transaction_date` DESC");
                 $trans_amount = $get_amount_per_day[0]->Total == null ? 0 : $get_amount_per_day[0]->Total;

                for ($indexex=0; $indexex < count($daily_trans_records); $indexex++) { 
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '".$daily_trans_records[$indexex]->transaction_account."'");
                    $client_name = isset($client_data[0]->client_name) ? $client_data[0]->client_name : $daily_trans_records[$indexex]->transaction_account;
                    // array_push($account_names,$client_name);
                    $daily_trans_records[$indexex]->account_names = $client_name;
                }
 
                 $transaction_data = array("date" => date("M Y",strtotime($day_1)),"trans_amount" => $trans_amount);
                 // echo date("D dS M Y",strtotime($day_1))." Amounts".$trans_amount."<br>";
                 array_push($trans_stats,$transaction_data);
                 array_push($trans_records,$daily_trans_records);
                 
                 if (date("Ym",strtotime($last_end_month)) == date("Ym",strtotime($day_1))) {
                     $break = true;
                 }
                 $day_1 = $this->addMonths($day_1,1);
             }
             $counter++;
             // echo $counter." Weeks <hr>";
             array_push($transaction_stats_monthly,$trans_stats);
             array_push($transaction_records_monthly,$trans_records);
             if ($break) {
                 break;
             }
         }
        // return $transaction_stats_monthly;

        // get the yearly data
        $first_payment = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' ORDER BY `transaction_date` ASC LIMIT 1");
        $first_payment_year = date("YmdHis",strtotime(count($first_payment) > 0 ? $first_payment[0]->transaction_date : date("YmdHis")));

        $transaction_yearly_stats = [];
        $transaction_yearly_records = [];

        for ($index=(date("Y",strtotime($first_payment_year))*1); $index <= (date("Y")*1); $index++) {
            $get_amount_per_day = DB::select("SELECT SUM(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".$index."%'");
            $daily_trans_records = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".$index."%' ORDER BY `transaction_date` DESC");
            $trans_amount = $get_amount_per_day[0]->Total == null ? 0 : $get_amount_per_day[0]->Total;

            for ($indexex=0; $indexex < count($daily_trans_records); $indexex++) { 
                $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '".$daily_trans_records[$indexex]->transaction_account."'");
                $client_name = isset($client_data[0]->client_name) ? $client_data[0]->client_name : $daily_trans_records[$indexex]->transaction_account;
                // array_push($account_names,$client_name);
                $daily_trans_records[$indexex]->account_names = $client_name;
            }

            $transaction_data = array("date" => $index,"trans_amount" => $trans_amount);
            array_push($transaction_yearly_stats,$transaction_data);
            array_push($transaction_yearly_records,$daily_trans_records);
        }

        // return $transaction_yearly_records;

        
        // proceed to the next year
        return view("trans-stats",["transaction_stats_weekly" => $transaction_stats_weekly,"transaction_records_weekly" => $transaction_records_weekly,"transaction_stats_monthly" => $transaction_stats_monthly,"transaction_records_monthly" => $transaction_records_monthly,"transaction_yearly_stats" => $transaction_yearly_stats,"transaction_yearly_records" => $transaction_yearly_records]);
    }
    function addDays($date,$days){
        $date = date_create($date);
        date_add($date,date_interval_create_from_date_string($days." day"));
        return date_format($date,"YmdHis");
    }

    function addMonths($date,$months){
        $date = date_create($date);
        date_add($date,date_interval_create_from_date_string($months." Month"));
        return date_format($date,"YmdHis");
    }
    function addYear($date,$years){
        $date = date_create($date);
        date_add($date,date_interval_create_from_date_string($years." Year"));
        return date_format($date,"YmdHis");
    }
}
