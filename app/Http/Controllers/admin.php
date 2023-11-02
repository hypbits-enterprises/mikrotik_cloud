<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\admin_table;
use File;
date_default_timezone_set('Africa/Nairobi');

class admin extends Controller
{
    function isJson($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }

    function showOption($priviledges,$name){
        if ($this->isJson($priviledges)) {
            $priviledges = json_decode($priviledges);
            for ($index=0; $index < count($priviledges); $index++) { 
                if ($priviledges[$index]->option == $name) {
                    if ($priviledges[$index]->view) {
                        return "";
                    }
                }
            }
        }
        return "hide";
    }

    //all administrator activities
    function getAdmin(){
        if (session("Userids")) {
            $admin_id = session("Userids");
            $admin_data = DB::select("SELECT * FROM `admin_tables` WHERE `admin_id` = '$admin_id' AND `deleted` = '0'");
            $date = $admin_data[0]->last_time_login;

            // privileged
            $priviledges = $admin_data[0]->priviledges;
            $show_hides = $this->showOption($priviledges,"Account and Profile");
            if ($show_hides == "hide") {
                session()->flash("danger","You cannot access the Account and Profile section. Contact your administrator!");
                return redirect("/Dashboard");
            }

            $date_data = $date;
            $year = substr($date_data,0,4);
            $month = substr($date_data,4,2);
            $day = substr($date_data,6,2);
            $hour = substr($date_data,8,2);
            $minute = substr($date_data,10,2);
            $second = substr($date_data,12,2);
            $d = mktime($hour, $minute, $second, $month, $day, $year);
            $dates2 = date("D dS M-Y  h:i:sa", $d);
            $delete_sms = "";
            $delete_trans = "";
            $settings = DB::select("SELECT * FROM `settings` WHERE `keyword` = 'delete' AND `deleted` = '0'");
            if (count($settings) > 0) {
                $delete_infor = $settings[0]->value;
                $delete_infor = json_decode($delete_infor);
                for ($index=0; $index < count($delete_infor); $index++) { 
                    if ($delete_infor[$index]->name == "delete_sms") {
                        $delete_sms = $delete_infor[$index]->period;
                    }
                    if ($delete_infor[$index]->name == "delete_transaction") {
                        $delete_trans = $delete_infor[$index]->period;
                    }
                }
            }
            return view("account",["admin_data" => $admin_data , "date_time" => $dates2, "delete_trans" => $delete_trans,"delete_sms"=>$delete_sms]);
        }else{
            session()->flash("error","Please login again to proceed to view your profile");
            return redirect("/Login");
        }
    }
    function updatePassword(Request $req){
        // insert the data into the database
        $username = $req->input('username');
        $admin_id = $req->input('admin_id');
        $old_password = $req->input('old_password');
        $password = $req->input('password');
        $confirm_password = $req->input('confirm_password');
        // check if the passwords match
        if ($confirm_password == $password) {
            // proceed and query from the database
            $data = DB::select("SELECT * FROM `admin_tables` WHERE `admin_username` = '$username' AND `admin_password` = '$old_password' AND `deleted` = '0'");
            if (count($data) > 0) {
                // this means the username is present
                DB::table("admin_tables")->where("admin_id",$admin_id)->update(["admin_password" => $confirm_password,"date_changed" => date("YmdHis")]);
                session()->flash("success","You have successfully updated your password!");
        
                // log file capture error
                // read the data 
                $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                $existing_txt = fread($myfile,$file_sizes);
                // return $existing_txt;
                $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                $date = date("dS M Y (H:i:sa)");
                $txt = $date.":Admin ( ".session('Usernames')." ) successfully updated password "."!\n".$existing_txt;
                // return $txt;
                fwrite($myfile, $txt);
                fclose($myfile);
                // end of log file
                return redirect("/Accounts");
            }else {
                // the admin is not present
            session()->flash("error","You have provided wrong credentials Retry!");
            return redirect("/Accounts");
            }
        }else {
            // redirect to the accounts page with an err0r
            session()->flash("error","Your username and passwords dont match");
            return redirect("/Accounts");
        }
    }
    function addAdmin(){
        // get all the usernames present 
        $admin_data = DB::select("SELECT * FROM `admin_tables` WHERE `deleted` = '0'");
        $username = [];
        $date = [];
        foreach ($admin_data as $key => $value) {
            // get the admins username
            array_push($username, $value->admin_username);

            $date_data = $value->last_time_login;
            if (strlen($date_data) > 0) {
                $year = substr($date_data,0,4);
                $month = substr($date_data,4,2);
                $day = substr($date_data,6,2);
                $hour = substr($date_data,8,2);
                $minute = substr($date_data,10,2);
                $second = substr($date_data,12,2);
                $d = mktime($hour, $minute, $second, $month, $day, $year);
                $dates2 = date("D dS M-Y  h:i:sa", $d);
                array_push($date,$dates2);
            }else {
                $dates2 = "Not logged in before";
                array_push($date,$dates2);
            }
        }
        
        return view("addadmin",["username" => $username, "admin_data" => $admin_data, "dates" => $date]);
    }
    function addAdministrator(Request $req){
        // return $req;
        // get the values
        $admin_name = $req->input('admin_name');
        $client_address = $req->input('client_address');
        $admin_username = $req->input('admin_username');
        $admin_password = $req->input('admin_password');
        $privileges = $req->input('privileges');

        // get the username if its already used
        $admin_data = DB::select("SELECT * FROM `admin_tables` WHERE `admin_username` = '$admin_username' AND `deleted` = '0'");

        if (count($admin_data) > 0) {
            // return an error showing thet the username has been used
            session()->flash("network_presence","Username provided is already used");
            return redirect("/Accounts/add");
        }else {
            $admin_table = new admin_table();
            $admin_table->admin_fullname = $admin_name;
            $admin_table->admin_username = $admin_username;
            $admin_table->admin_password = $admin_password;
            $admin_table->contacts = $client_address;
            $admin_table->organization_id = "1";
            $admin_table->user_status = "1";
            $admin_table->priviledges = $privileges;
            $admin_table->save();
        
            // log file capture error
            // read the data 
            $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
            $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
            $existing_txt = fread($myfile,$file_sizes);
            // return $existing_txt;
            $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
            $date = date("dS M Y (H:i:sa)");
            $txt = $date.":Admin ($admin_name) has been added by ( ".session('Usernames')." )"."!\n".$existing_txt;
            // return $txt;
            fwrite($myfile, $txt);
            fclose($myfile);
            // end of log file
            session()->flash("success","The administrator has successfully been added.");
            return redirect("/Accounts/add");
        }

    }
    function upload_dp(Request $req)
    {
        $req->validate([
            'mine_dp' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:8192',
        ]);

        $client_id = $req->input('client_id');
        $imageName = $client_id."_".date("YmdHis").'.'.$req->mine_dp->extension();
     
        $client_data = DB::select("SELECT * FROM `admin_tables` WHERE `admin_id` = '$client_id' AND `deleted` = '0'");
        // return $client_data;
        if (isset($client_data[0]->dp_locale)) {
            // delete the previous file
            File::delete(public_path($client_data[0]->dp_locale));
        }
        // upload the file
        $req->mine_dp->move(public_path('theme-assets/images/dp'), $imageName);
        /* Store $imageName name in DATABASE from HERE */
        // update the clients dp locale
        $imageName = "/theme-assets/images/dp/".$imageName;
        session(['dp_locale' => $imageName]);
        $update = DB::table("admin_tables")->where("admin_id",$client_id)->update([
            "dp_locale" => $imageName,
            "date_changed" => date("YmdHis")
        ]);
        session()->flash('success',"Profile picture saved successfully!");
        return redirect("/Accounts");
    }
    function update_admin(Request $req){
        // return $req;
        $admin_id = $req->input('client_id');
        $update = DB::table("admin_tables")->where("admin_id",$admin_id)->update([
            "admin_fullname" => $req->input('fullName'),
            "CompanyName" => $req->input('company'),
            "country" => $req->input('country'),
            "contacts" => $req->input('phone'),
            "email" => $req->input('email'),
            "date_changed" => date("YmdHis")
        ]);
        session()->flash('success',"You have successfully updated your information!");
        return redirect("/Accounts");
    }
    // function update delete options
    function update_delete_option(Request $req){
        // return $req;
        $settings = DB::select("SELECT * FROM `settings` WHERE `keyword` = 'delete' AND `deleted` = '0';");
        if (count($settings) > 0) {
            // get fields and check for the two delete options
            $delete_options = json_decode($settings[0]->value);
            // option 1 delete message records
            $options = [];
            $option1 = array("name" => "delete_sms","period" => $req->input('delete_message_records'));
            // option 2 delete transaction messages
            $option2 = array("name" => "delete_transaction","period" => $req->input('delete_transactions'));
            // merge options
            array_push($options,$option1,$option2);
            // update the setting table where the keyword is delete
            $update = DB::table('settings')->where("keyword","delete")->update([
                "value" => json_encode($options),
                "date_changed" => date("YmdHis")
            ]);
            session()->flash('success',"Update has been done successfully!");
            return redirect("/Accounts");
        }else {
            // get fields and check for the two delete options
            // $delete_options = json_decode($settings[0]->value);
            // option 1 delete message records
            $options = [];
            $option1 = array("name" => "delete_sms","period" => $req->input('delete_message_records'));
            // option 2 delete transaction messages
            $option2 = array("name" => "delete_transaction","period" => $req->input('delete_transactions'));
            // merge options
            array_push($options,$option1,$option2);
            DB::table('settings')->insert([
                "keyword" => "delete",
                "value" => json_encode($options),
                "status" => "1",
                "date_changed" => date("YmdHis")
            ]);
            session()->flash('success',"Update has been done successfully!");
            return redirect("/Accounts");
        }
    }
    /**
   * Checks if a folder exist and return canonicalized absolute pathname (long version)
   * @param string $folder the path being checked.
   * @return mixed returns the canonicalized absolute pathname on success otherwise FALSE is returned
   */
  function folder_exist($folder)
  {
      // Get canonicalized absolute pathname
      $path = realpath($folder);
  
      // If it exist, check if it's a directory
      if($path !== false AND is_dir($path))
      {
          // Return canonicalized absolute pathname
          return $path;
      }
  
      // Path/folder does not exist
      return false;
  }
    function viewAdmin($admin_id){
        $admin_data = DB::select("SELECT * FROM `admin_tables` WHERE `admin_id` = '$admin_id' AND `deleted` = '0'");
        if (count($admin_data) > 0) {
            return view("viewadmin", ["admin_data" => $admin_data]);
        }else{
            session()->flash("network_presence","In-valid User!");
            return redirect("/Accounts/add");
        }
    }
    function updateAdmin(Request $req){
        // return $req;
        $admin_id = $req->input('admin_id');
        $privileges = $req->input('privileges');
        $status = $req->input('status');
        // create a model to update the data
        $update = DB::table("admin_tables")->where("admin_id",$admin_id)->update([
            "admin_fullname" => $req->input('admin_name'),
            "admin_username" => $req->input('admin_username'),
            "admin_password" => $req->input('admin_password'),
            "contacts" => $req->input('client_address'),
            "user_status" => $req->input('status'),
            "date_changed" => date("YmdHis"),
            "activated" => $status,
            "priviledges" => $privileges
        ]);
        session()->flash('success',"Administrator data updates successfully!");
        return redirect("/Admin/View/$admin_id");
    }
    function deactivateAdmin($admin_id){
        // return $admin_id;
        DB::update("UPDATE `admin_tables` SET `activated` = '0', `user_status` = '0' WHERE `admin_id` = ?",[$admin_id]);
        session()->flash("success","The administrator has successfully deactivated.");
        return redirect("/Accounts/add");

    }
    function delete_pp($admin_id){
        // return $admin_id;
        $client_data = DB::select("SELECT * FROM `admin_tables` WHERE `admin_id` = '$admin_id' AND `deleted` = '0'");
        // return $client_data;
        if (isset($client_data[0]->dp_locale)) {
            // delete the previous file
            File::delete(public_path($client_data[0]->dp_locale));
        }
        $update = DB::table("admin_tables")->where("admin_id",$admin_id)->update([
            "dp_locale" => "",
            "date_changed" => date("YmdHis")
        ]);
        session(['dp_locale' => ""]);
        session()->flash('success',"Profile picture deleted successfully!");
        return redirect("/Accounts");
    }
}
