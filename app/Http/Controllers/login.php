<?php

namespace App\Http\Controllers;

use App\Classes\phpmailer\src\PHPMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\sms_table;
use App\Models\verification_code;
use routeros_api;

date_default_timezone_set('Africa/Nairobi');
class login extends Controller
{
    //
    function processLogin(Request $req){
        $data = $req->input();
        // create a connection and find the data in the database if its true
        if ($data['authority'] == "admin") {
            // redirect to the admin dashboard
            $username = $data['emails'];
            $password = $data['password'];
            $send_code = $data['send_code'];

            // send money
            $result = DB::select("SELECT * FROM `admin_tables` WHERE `deleted` = '0' AND `admin_username` = '$username' AND `admin_password` = '$password'");
            if (count($result) > 0) {
                if ($result[0]->activated == 0) {
                    session()->flash('error',"You account has been deactivated by the administrator! Contact them to be allowed back in!");
                    return redirect("/Login");
                }

                // if the username email is null redirect and show error
                if ($result[0]->email == null || strlen($result[0]->email) < 1) {
                    session()->flash('error',"Your email has not been set up! Contact your administrator to set it up for you!");
                    return redirect("/Login");
                }
                // $req->session()->put("Usernames",$result[0]->admin_fullname);
                // $req->session()->put("Userids",$result[0]->admin_id);
                $req->session()->put("Userid",$result[0]->admin_id);
                $req->session()->put("auth","admin");
                $req->session()->put("dp_locale",$result[0]->dp_locale);
                $contacts = $result[0]->contacts;
                $admin_id = $result[0]->admin_id;
                $contact = substr($contacts,0,4)."XXXX".substr($contacts,8);
                $req->session()->put("priviledges",$result[0]->priviledges);

                // GET THE SMS KEYS FROM THE DATABASE
                $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_api_key'");
                $sms_api_key = $sms_keys[0]->value;
                $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_partner_id'");
                $sms_partner_id = $sms_keys[0]->value;
                $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_shortcode'");
                $sms_shortcode = $sms_keys[0]->value;

                // return $contact;
                $this->deleteSMSnTRANS();

                $message_status = 0;
                // if send sms is 1 we send  the sms
                $partnerID = $sms_partner_id;
                $apikey = $sms_api_key;
                $shortcode = $sms_shortcode;
                $random_no = rand(1000,9999);
                $mobile = $contacts; // Bulk messages can be comma separated
                $message = "Your verification code is ".$random_no.". It will expire in 5 minutes";
                // return $partnerID."  &  ".$apikey." & ".$shortcode;

                if ($send_code == "SMS") {
                
                    $finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
                    $ch = \curl_init();
                    \curl_setopt($ch, CURLOPT_URL, $finalURL);
                    \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = \curl_exec($ch);
                    \curl_close($ch);
                    $res = json_decode($response);
                    // return $res;
                    $values = $res->responses[0] ? $res->responses[0] : [];
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
                }elseif ($send_code == "EMAILS") {
                    $sender_name = "HypBits";
                    $email_username = "hypbits@gmail.com";
                    $sender_address = $result[0]->email;
                    $mobile = $sender_address;

                    // USE PHP MAILER
                    $mail = new PHPMailer(true);
            
                    $mail->isSMTP();
                    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                    $mail->Host = 'smtp.gmail.com';
                    // $mail->Host = $email_host_addr;
                    $mail->SMTPAuth = true;
                    $mail->Username = "hypbits@gmail.com";
                    $mail->Password = "rdvafdolnhxaxnxg";
                    // $mail->Username = $email_username;
                    // $mail->Password = $email_password;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    
                    
                    $mail->setFrom($email_username,$sender_name);
                    $mail->addAddress($sender_address);
                    $mail->isHTML(true);
                    $mail->Subject = "Hypbits Login Code";
                    $mail->Body = $message;
            
                    $mail->send();
                    $message_status = 1;
                }


                // save the sms in the database
                $sms_table = new sms_table();
                $sms_table->sms_content = $message;
                $sms_table->date_sent = date("YmdHis");
                $sms_table->recipient_phone = $mobile;
                $sms_table->sms_status = $message_status;
                $sms_table->account_id = "0";
                $sms_table->sms_type = "2";
                $sms_table->save();

                // save the verifcation code in the database
                $verification_code = new verification_code();
                $verification_code->code = $random_no;
                $verification_code->phone_sent = $mobile;
                $verification_code->date_generated = date("YmdHis",strtotime("5 Minutes"));
                $verification_code->status = "0";
                $verification_code->save();
                

                if ($send_code == "SMS"){
                    $req->session()->flash("contacts",$contact);
                }elseif ($send_code == "EMAILS") {
                    $contact = "".$result[0]->email."";
                    $req->session()->flash("contacts",$contact);
                }
                // update the last time they logged in;
                DB::table("admin_tables")->where("admin_id",$result[0]->admin_id)->update(["last_time_login" => date("YmdHis"),"date_changed" => date("YmdHis")]);
                return redirect("/verify");
            }else {
                // log file capture error
                // read the data 
                $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                $existing_txt = fread($myfile,$file_sizes);
                // return $existing_txt;
                $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                $date = date("dS M Y (H:i:sa)");
                $txt = "{$date}: Admin failed attempt to login  on ip ".$_SERVER['REMOTE_ADDR']."\n".$existing_txt;
                // return $txt;
                fwrite($myfile, $txt);
                fclose($myfile);
                // end of log file

                session()->flash('error',"Invalid username and password provided!");
                return redirect("/Login");
            }
        }elseif ($data['authority'] == "client") {
            // redirect to the client dash
            $username = $data['emails'];
            $password = $data['password'];
            $send_code = $data['send_code'];
            if ($send_code == "EMAILS") {
                session()->flash('error',"Email Set-Up has not been completed, Kindly use SMS!");
                return redirect("/Login");
            }

            $result = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_username` = '$username' AND `client_password` = '$password'");
            // return $result;
            if (count($result) > 0) {
                // get the student data 
                // set session for the user id
                // $req->session()->put("client_id",$result[0]->client_id);
                // $req->session()->put("fullname",$result[0]->client_name);
                $req->session()->put("Userid",$result[0]->client_id);
                $req->session()->put("auth","client");
                $contacts = $result[0]->clients_contacts;
                $admin_id = $result[0]->client_id;
                $contact = substr($contacts,0,4)."XXXX".substr($contacts,8);
                // GET THE SMS KEYS FROM THE DATABASE
                $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_api_key'");
                $sms_api_key = $sms_keys[0]->value;
                $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_partner_id'");
                $sms_partner_id = $sms_keys[0]->value;
                $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_shortcode'");
                $sms_shortcode = $sms_keys[0]->value;

                $message_status = 0;
                // if send sms is 1 we send  the sms
                $partnerID = $sms_partner_id;
                $apikey = $sms_api_key;
                $shortcode = $sms_shortcode;
                $random_no = rand(1000,9999);
                $mobile = $contacts; // Bulk messages can be comma separated
                $message = "Your verification code is ".$random_no.". It will expire in 5 minutes";
                
                $finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
                $ch = \curl_init();
                \curl_setopt($ch, CURLOPT_URL, $finalURL);
                \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = \curl_exec($ch);
                \curl_close($ch);
                $res = json_decode($response);
                // return $res;
                $values = $res->responses[0] ? $res->responses[0] : [];
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


                // save the sms in the database
                $sms_table = new sms_table();
                $sms_table->sms_content = $message;
                $sms_table->date_sent = date("YmdHis");
                $sms_table->recipient_phone = $mobile;
                $sms_table->sms_status = $message_status;
                $sms_table->account_id = "0";
                $sms_table->sms_type = "2";
                $sms_table->save();

                // save the verifcation code in the database
                $verification_code = new verification_code();
                $verification_code->code = $random_no;
                $verification_code->phone_sent = $mobile;
                $verification_code->date_generated = date("YmdHis",strtotime("5 Minutes"));
                $verification_code->status = "0";
                $verification_code->save();
                

                $req->session()->flash("contacts",$contact);
                return redirect("/verify");
                // return redirect("/ClientDashboard");
            }else {
                // log file capture error
                // read the data 
                $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                $existing_txt = fread($myfile,$file_sizes);
                // return $existing_txt;
                $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                $date = date("dS M Y (H:i:sa)");
                $txt = "{$date}: Client failed attempt to login username:".$username." on ip ".$_SERVER['REMOTE_ADDR']."\n".$existing_txt;
                // return $txt;
                fwrite($myfile, $txt);
                fclose($myfile);
                // end of log file

                session()->flash('error',"Invalid username and password provided!");
                return redirect("/Login");
            }
        }
    }

    function deleteSMSnTRANS(){
        // get the period of deleting sms
        $delete_data = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'delete'");
        if (count($delete_data) > 0) {
            $values = $delete_data[0]->value;
            $json_del = json_decode($values);
            $period_sms = $json_del[0]->period;
            $real_time = "";
            if ($period_sms == "daily") {
                $real_time = "-1 day";
            }elseif ($period_sms == "weekly") {
                $real_time = "-1 week";
            }elseif ($period_sms == "monthly") {
                $real_time = "-1 month";
            }elseif ($period_sms == "yearly") {
                $real_time = "-1 year";
            }elseif ($period_sms == "2years") {
                $real_time = "-2 years";
            }elseif ($period_sms == "5years") {
                $real_time = "-5 years";
            }elseif ($period_sms == "never") {
                $real_time = "0";
            }
            if ($real_time != "0") {
                $delete_dates = date("YmdHis",strtotime($real_time));
                // DB::delete("DELETE FROM `sms_tables` WHERE `deleted` = '0' AND `date_sent` < '".$delete_dates."'");
                DB::update("UPDATE `sms_tables` SET `deleted` = '1', `date_changed` = '".date("YmdHis")."' WHERE `date_sent` < '".$delete_dates."'");
            }
            $period_trans = $json_del[1]->period;
            $real_time = "";
            if ($period_trans == "daily") {
                $real_time = "-1 day";
            }elseif ($period_trans == "weekly") {
                $real_time = "-1 week";
            }elseif ($period_trans == "monthly") {
                $real_time = "-1 month";
            }elseif ($period_trans == "yearly") {
                $real_time = "-1 year";
            }elseif ($period_trans == "2years") {
                $real_time = "-2 years";
            }elseif ($period_trans == "5years") {
                $real_time = "-5 years";
            }elseif ($period_trans == "never") {
                $real_time = "0";
            }
            if ($real_time != "0") {
                $delete_dates = date("YmdHis",strtotime($real_time));
                // DB::delete("DELETE FROM `sms_tables` WHERE `deleted` = '0' AND `date_sent` < '".$delete_dates."'");
                DB::update("UPDATE `sms_tables` SET `deleted` = '1' , `date_changed` = '".date("YmdHis")."' WHERE `date_sent` < '".$delete_dates."'");
            }
        }
    }

    function processVerification(Request $req){
        // get the verifaction code
        // return $req->input('verification_code');
        // get the user id
        $code = $req->input('verification_code');
        $user_id = session('Userid');
        $dates = date("YmdHis");
        // get the user data
        $verify = DB::select("SELECT * FROM `verification_codes` WHERE `deleted` = '0' AND `code` = '$code' AND `date_generated` > '$dates'  AND `status` = '0'");
        if (count($verify) > 0) {
            // this means that the code is valid
            // get the user data
            if (session('auth') == "admin") {
                $user_data = DB::select("SELECT * FROM `admin_tables` WHERE `deleted` = '0' AND `admin_id` = '$user_id'");
                DB::table("verification_codes")->where("code",$code)->update(["status" => "1", 'date_changed' => date("YmdHis")]);
                if (count($user_data) > 0) {
                    // log file capture error
                    // read the data 
                    $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                    $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                    $existing_txt = fread($myfile,$file_sizes);
                    // return $existing_txt;
                    $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                    $date = date("dS M Y (H:i:sa)");
                    $txt = "{$date}: ".$user_data[0]->admin_fullname." successfully login as admin on ip ".$_SERVER['REMOTE_ADDR']."\n".$existing_txt;
                    // return $txt;
                    fwrite($myfile, $txt);
                    fclose($myfile);
                    // end of log file

                    $req->session()->put("Usernames",$user_data[0]->admin_fullname);
                    $req->session()->put("Userids",$user_data[0]->admin_id);
                    // session_unset('Userid');
                    // redirect the page to the dashbord of the administrator, update the last time they logged in;
                    DB::table("admin_tables")->where("admin_id",$user_id)->update(["last_time_login" => date("YmdHis"),'date_changed' => date("YmdHis")]);
                    return redirect("/Dashboard");
                }else {
                    session()->flash('error',"Invalid User!");
                    return redirect("/Login");
                }
            }elseif (session('auth') == 'client') {
                $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = '$user_id'");
                DB::table("verification_codes")->where("code",$code)->update(["status" => "1",'date_changed' => date("YmdHis")]);
                if (count($user_data) > 0) {
                    $req->session()->put("fullname",$user_data[0]->client_name);
                    $req->session()->put("Usernames",$user_data[0]->client_name);
                    $req->session()->put("client_id",$user_data[0]->client_id);

                    // log file capture error
                    // read the data 
                    $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                    $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                    $existing_txt = fread($myfile,$file_sizes);
                    // return $existing_txt;
                    $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                    $date = date("dS M Y (H:i:sa)");
                    $txt = "{$date}: ".$user_data[0]->client_name." successfully login as client on ip ".$_SERVER['REMOTE_ADDR']."\n".$existing_txt;
                    // return $txt;
                    fwrite($myfile, $txt);
                    fclose($myfile);
                    // end of log file

                    // session_unset('Userid');
                    // redirect the page to the dashbord of the administrator, update the last time they logged in;
                    return redirect("/ClientDashboard");
                }else {
                    session()->flash('error',"Invalid User!");
                    return redirect("/Login");
                }
            }else {
                session()->flash('error',"Invalid User!");
                return redirect("/Login");
            }
        }else {
            // the code is invalid
            session()->flash('error',"Invalid verification code!");
            return redirect("/verify");
        }
    }
}
