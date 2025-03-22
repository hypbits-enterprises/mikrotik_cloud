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
        // change db
        $change_db = new login();
        $change_db->change_db();

        if (session("Userids")) {
            $admin_id = session("Userids");
            $admin_data = DB::select("SELECT * FROM `admin_tables` WHERE `admin_id` = '$admin_id' AND `organization_id` = '".session('organization_id')."' AND `deleted` = '0'");
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
            $settings = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'delete' AND `deleted` = '0'");
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


            // get the data for the organization
            $organization = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[session("organization_id")]);
            
            if (count($organization) == 0) {
                // logout the user
                session()->flash("error","You`ve been logged out because we have discovered some suspicious activity, Login and try again!");
                return redirect("/");
            }
            // return $organization;

            // GET THE SMS API LINK
            $select = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_sender'");
            $sms_sender = count($select) > 0 ? $select[0]->value : "";
            $organization[0]->sms_sender = $sms_sender;
            // GET THE SMS API LINK
            $select = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_api_key'");
            $sms_api_key = count($select) > 0 ? $select[0]->value : "";
            $organization[0]->sms_api_key = $sms_api_key;
            // GET THE SMS API LINK
            $select = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_partner_id'");
            $sms_partner_id = count($select) > 0 ? $select[0]->value : "";
            $organization[0]->sms_partner_id = $sms_partner_id;
            // GET THE SMS API LINK
            $select = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_shortcode'");
            $sms_shortcode = count($select) > 0 ? $select[0]->value : "";
            $organization[0]->sms_shortcode = $sms_shortcode;

            return view("account",["admin_data" => $admin_data , "date_time" => $dates2, "delete_trans" => $delete_trans,"delete_sms"=>$delete_sms, "organization" => $organization[0]]);
        }else{
            session()->flash("error","Please login again to proceed to view your profile");
            return redirect("/Login");
        }
    }
    function updatePassword(Request $req){
        // change db
        $change_db = new login();
        $change_db->change_db();

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
                
                $new_client = new Clients();
                $txt = ":Admin ( ".session('Usernames')." ) successfully updated password"."!";
                $new_client->log($txt);
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
        // change db
        $change_db = new login();
        $change_db->change_db();

        // get all the usernames present 
        $admin_data = DB::select("SELECT * FROM `admin_tables` WHERE `deleted` = '0' AND `organization_id` = '".session('organization_id')."'");
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
        // return json_decode($req->input("privileges"));
        // change db
        $change_db = new login();
        $change_db->change_db();

        // return session("organization_id");
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
            $admin_table->organization_id = session("organization_id");
            $admin_table->user_status = "1";
            $admin_table->activated = "1";
            $admin_table->priviledges = $privileges;
            $admin_table->save();
                
            $new_client = new Clients();
            $txt = ":Admin ($admin_name) has been added by ( ".session('Usernames')." )"."!";
            $new_client->log($txt);
            session()->flash("success","The administrator has successfully been added.");
            return redirect("/Accounts/add");
        }

    }
    function upload_dp(Request $req)
    {
        // change db
        $change_db = new login();
        $change_db->change_db();

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
    function update_company_dp(Request $req)
    {
        // change db
        $change_db = new login();
        $change_db->change_db();

        $req->validate([
            'mine_dp' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:8192',
        ]);

        $organization_id = session('organization_id');
        $imageName = $organization_id."_".date("YmdHis").'.'.$req->mine_dp->extension();

        // get the old organization id
        $select = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if(isset($select[0]->organization_logo)){
            File::delete(public_path($select[0]->organization_logo));
        }

        // move the dp to the new dp location
        $req->mine_dp->move(public_path('theme-assets/images/dp'), $imageName);
        $imageName = "/theme-assets/images/dp/".$imageName;
        $update = DB::table("organizations")->where("organization_id", session('organization_id'))->update([
            "organization_logo" => $imageName
        ]);

        // reset the organization logo location
        session()->put("organization_logo", $imageName);

        session()->flash('success',"Profile picture saved successfully!");
        return redirect("/Accounts");
    }
    function update_admin(Request $req){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // return $req;
        $admin_id = $req->input('client_id');
        $update = DB::table("admin_tables")->where("admin_id",$admin_id)->update([
            "admin_fullname" => $req->input('fullName'),
            "contacts" => $req->input('phone'),
            "email" => $req->input('email')
        ]);
        session()->flash('success',"You have successfully updated your information!");
        return redirect("/Accounts");
    }
    // function update delete options
    function update_delete_option(Request $req){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // return $req;
        $settings = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'delete' AND `deleted` = '0';");
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
            $update = DB::connection("mysql2")->table('settings')->where("keyword","delete")->update([
                "value" => json_encode($options),
                "date_changed" => date("YmdHis")
            ]);
            session()->flash('success',"Update has been done successfully!");
            return redirect("/Accounts");
        }else{
            // get fields and check for the two delete options
            // $delete_options = json_decode($settings[0]->value);
            // option 1 delete message records
            $options = [];
            $option1 = array("name" => "delete_sms","period" => $req->input('delete_message_records'));
            // option 2 delete transaction messages
            $option2 = array("name" => "delete_transaction","period" => $req->input('delete_transactions'));
            // merge options
            array_push($options,$option1,$option2);
            DB::connection("mysql2")->table('settings')->insert([
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
        // change db
        $change_db = new login();
        $change_db->change_db();

        $admin_data = DB::select("SELECT * FROM `admin_tables` WHERE `admin_id` = '$admin_id' AND `deleted` = '0'");
        if (count($admin_data) > 0) {
            return view("viewadmin", ["admin_data" => $admin_data]);
        }else{
            session()->flash("network_presence","In-valid User!");
            return redirect("/Accounts/add");
        }
    }

    function updateAdmin(Request $req){
        // return json_decode($req->input("privileges"));
        // change db
        $change_db = new login();
        $change_db->change_db();

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

    function delete_admin($admin_id){
        // get the administrator`s name
        $administrator_detail = DB::select("SELECT * FROM `admin_tables` WHERE `admin_id` = '".$admin_id."'");
        $admin_name = count($administrator_detail) > 0 ? $administrator_detail[0]->admin_fullname : "NULL";
        // delete the user admin and record that as a log
        DB::delete("DELETE FROM `admin_tables` WHERE `admin_id` = '".$admin_id."'");
        session()->flash("success","The administrator \"".$admin_name."\" has been deleted successfully!");

        // 
        $new_client = new Clients();
        $txt = ":The administrator \"".$admin_name."\" has been deleted successfully! by ".session('Usernames')."!";
        $new_client->log($txt);
        return redirect("/Accounts/add");
    }

    function deactivateAdmin($admin_id){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // return $admin_id;
        DB::update("UPDATE `admin_tables` SET `activated` = '0', `user_status` = '0' WHERE `admin_id` = ?",[$admin_id]);
        session()->flash("success","The administrator has successfully deactivated.");
        return redirect("/Accounts/add");

    }

    function delete_pp($admin_id){
        // change db
        $change_db = new login();
        $change_db->change_db();

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

    function delete_pp_organization(){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // return $admin_id;
        $organization = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[session("organization_id")]);
        // return $organization;
        if (isset($organization[0]->organization_logo)){
            // delete the previous file
            File::delete(public_path($organization[0]->organization_logo));
        }
        $update = DB::table("organizations")->where("organization_id",session("organization_id"))->update([
            "organization_logo" => ""
        ]);
        session(['organization_logo' => ""]);
        session()->flash('success',"Organization Profile picture deleted successfully!");
        return redirect("/Accounts");
    }
    function update_organization_profile(Request $req){
        // change_db
        $change_db = new login();
        $change_db->change_db();

        // update
        $update = DB::table("organizations")->where("organization_id",session("organization_id"))->update([
            "organization_name" => $req->input("organization_name"),
            "organization_address" => $req->input("organization_address"),
            "organization_main_contact" => $req->input("organization_main_contact"),
            "organization_email" => $req->input("organization_email"),
            "BusinessShortCode" => $req->input("BusinessShortCode")
        ]);

        // insert or update the API SENDER
        $api_link = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_sender'");
        if (count($api_link) > 0) {
            $api_link_id = $api_link[0]->id;
            DB::connection("mysql2")->update("UPDATE `settings` SET `value` = '".$req->input("sms_sender")."' WHERE `id` = '".$api_link_id."'");
        }else{
            $insert = DB::connection("mysql2")->insert("INSERT INTO `settings` (`keyword`, `value`,`status`) VALUES ('sms_sender','".$req->input("sms_sender")."','1')");
        }

        // insert or update the PATNER ID
        $api_link = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_partner_id'");
        if (count($api_link) > 0) {
            $api_link_id = $api_link[0]->id;
            DB::connection("mysql2")->update("UPDATE `settings` SET `value` = '".$req->input("sms_partner_id")."' WHERE `id` = '".$api_link_id."'");
        }else{
            $insert = DB::connection("mysql2")->insert("INSERT INTO `settings` (`keyword`, `value`,`status`) VALUES ('sms_partner_id','".$req->input("sms_partner_id")."','1')");
        }

        // insert or update the SHORT CODE
        $api_link = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_shortcode'");
        if (count($api_link) > 0) {
            $api_link_id = $api_link[0]->id;
            DB::connection("mysql2")->update("UPDATE `settings` SET `value` = '".$req->input("sms_short_code")."' WHERE `id` = '".$api_link_id."'");
        }else{
            $insert = DB::connection("mysql2")->insert("INSERT INTO `settings` (`keyword`, `value`,`status`) VALUES ('sms_shortcode','".$req->input("sms_short_code")."','1')");
        }

        // insert or update the API KEY
        $api_link = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_api_key'");
        if (count($api_link) > 0) {
            $api_link_id = $api_link[0]->id;
            DB::connection("mysql2")->update("UPDATE `settings` SET `value` = '".$req->input("sms_api_key")."' WHERE `id` = '".$api_link_id."'");
        }else{
            $insert = DB::connection("mysql2")->insert("INSERT INTO `settings` (`keyword`, `value`,`status`) VALUES ('sms_api_key','".$req->input("sms_api_key")."','1')");
        }

        $organization = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[session("organization_id")]);
        session()->put("organization",$organization[0]);

        session()->flash("success","Organization details have been successfully updated!");
        return redirect("/Accounts");
    }
}
