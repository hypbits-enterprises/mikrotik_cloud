<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Classes\routeros_api;

use function PHPUnit\Framework\isJson;


date_default_timezone_set('Africa/Nairobi');
class Router_Cloud extends Controller
{

    function update_profile(Request $req){
        // return request
        // return $req;

        // change db
        $change_db = new login();
        $change_db->change_db();

        // check first if the router configuration is done
        $router_id = $req->input("router_id");
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);
        if (count($router_data) == 0) {
            session()->flash("error_router","Invalid router");
            redirect(url()->route("view_router_cloud"));
        }
        
        // create a SSTP secret on the SSTP server
        // get the server details
        $sstp_settings = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sstp_server'");
        if (count($sstp_settings) == 0) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }

        // connect to the server
        $sstp_value = $this->isJson($sstp_settings[0]->value) ? json_decode($sstp_settings[0]->value) : null;

        if ($sstp_value == null) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }

        // connect to the router and set the sstp client
        $server_ip_address = $sstp_value->ip_address;
        $user = $sstp_value->username;
        $pass = $sstp_value->password;
        $port = $sstp_value->port;

        // check if the router is actively connected
        $client_router_ip = $this->checkActive($server_ip_address, $user, $pass, $port, $router_data[0]->sstp_username);

        // check if the bridge is present on the router
        $API = new routeros_api();
        $API->debug = false;
        $profile = null;
        if ($API->connect($client_router_ip, $user, $pass, $port)){
            // check if its a new pool so that its added
            $local_address = $req->input("local_address");
            $remote_address = $req->input("remote_address");
            $pool_range_start = $req->input("pool_range_start");
            $pool_range_end = $req->input("pool_range_end");
            if($req->has("new_pool")){
                if($req->input("new_pool") == "on"){
                    $new_pool_name = $req->input("new_pool_name");
                    $profile = $API->comm("/ip/pool/print", [
                        "?name" => $new_pool_name
                    ]);
                    if (count($profile) > 0) {
                        $API->disconnect();
                        session()->flash("error_router", "IP Pool name is present in your router, create a new name!");
                        return redirect(url()->previous());
                    }

                    // proceed and register the new pool
                    $API->comm("/ip/pool/add", [
                        "name" => $new_pool_name,
                        "ranges" => $pool_range_start."-".$pool_range_end,
                        "comment" => "Added by Hypbits Billing System!"
                    ]);
                    $local_address = $new_pool_name;
                    $remote_address = $new_pool_name;
                }
            }else{
                $local_address = $local_address == "ip_address" ? $req->input("local_ip_address") : $local_address;
            }

            // register the profile and assign it the new pool name
            $check_profile = $API->comm("/ppp/profile/print", [
                "?name" => $req->input("edit_profile_name_2")
            ]);
            if(count($check_profile) > 0){
                $API->comm("/ppp/profile/set", [
                    ".id" => $check_profile[0]['.id'],
                    "name" => $req->input("edit_profile_name"),
                    "local-address" => $local_address,
                    "remote-address" => $remote_address,
                    "rate-limit" => $req->input("upload_speed_value").$req->input("upload_speed_unit")."/".$req->input("download_speed_value").$req->input("download_speed_unit"),
                    "comment" => "Modified by Hypbits Billing System!"
                ]);
            }else{
                $API->comm("/ppp/profile/add", [
                    "name" => $req->input("edit_profile_name"),
                    "local-address" => $local_address,
                    "remote-address" => $remote_address,
                    "rate-limit" => $req->input("upload_speed_value").$req->input("upload_speed_unit")."/".$req->input("download_speed_value").$req->input("download_speed_unit"),
                    "comment" => "Added by Hypbits Billing System!"
                ]);
            }
            
            // status
            $status = 1;
            $profile_details = DB::connection("mysql2")->select("SELECT * FROM `router_profile` WHERE profile_name = ? AND router_id = ?", [$req->input("edit_profile_name"), $router_id]);
            if(count($profile_details) > 0){
                DB::connection("mysql2")->update("UPDATE router_profile SET profile_name = ? WHERE profile_id = ?", [$req->input("edit_profile_name"), $profile_details[0]->profile_id]);
            }else{
                DB::connection("mysql2")->insert("INSERT INTO router_profile (profile_name, profile_status, router_id) VALUES (?,?,?)", [$req->input("edit_profile_name"), $status, $router_id]);
            }
            $API->disconnect();

            session()->flash("success_router", "Profile has been updated successfully!");
            return redirect(url()->previous());
        }else{
            session()->flash("error_router", "Cannot connect to your router try again later!");
            return redirect(url()->previous());
        }
    }

    function display_router_pool($router_id, $profile_name){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // get the bridge ports
        $curl_handle = curl_init();
        $url = env('CRONJOB_URL', 'https://crontab.hypbits.com')."/getIpaddress.php?db_name=" . session("database_name") . "&r_id=" . $router_id . "&r_ip_pool=true";
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        $curl_data = curl_exec($curl_handle);
        curl_close($curl_handle);
        $bridge_ports = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];
        // return $bridge_ports;

        // check first if the router configuration is done
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);
        if (count($router_data) == 0) {
            session()->flash("error_router","Invalid router");
            redirect(url()->route("view_router_cloud"));
        }
        
        // create a SSTP secret on the SSTP server
        // get the server details
        $sstp_settings = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sstp_server'");
        if (count($sstp_settings) == 0) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }

        // connect to the server
        $sstp_value = $this->isJson($sstp_settings[0]->value) ? json_decode($sstp_settings[0]->value) : null;

        if ($sstp_value == null) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }

        // connect to the router and set the sstp client
        $server_ip_address = $sstp_value->ip_address;
        $user = $sstp_value->username;
        $pass = $sstp_value->password;
        $port = $sstp_value->port;

        // check if the router is actively connected
        $client_router_ip = $this->checkActive($server_ip_address, $user, $pass, $port, $router_data[0]->sstp_username);

        // check if the bridge is present on the router
        $API = new routeros_api();
        $API->debug = false;
        $profile = null;
        if ($API->connect($client_router_ip, $user, $pass, $port)){
            $profile = $API->comm("/ppp/profile/print", [
                "?name" => $profile_name
            ]);
            $API->disconnect();
        }
        return array("profile_details" => $profile, "bridge_port" => $bridge_ports);
    }

    function delete_router_profile($router_id, $profile_name, Request $request){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // check first if the router configuration is done
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);
        if (count($router_data) == 0) {
            session()->flash("error_router","Invalid router");
            redirect(url()->route("view_router_cloud"));
        }
        
        // create a SSTP secret on the SSTP server
        // get the server details
        $sstp_settings = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sstp_server'");
        if (count($sstp_settings) == 0) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }

        // connect to the server
        $sstp_value = $this->isJson($sstp_settings[0]->value) ? json_decode($sstp_settings[0]->value) : null;

        if ($sstp_value == null) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }

        // connect to the router and set the sstp client
        $server_ip_address = $sstp_value->ip_address;
        $user = $sstp_value->username;
        $pass = $sstp_value->password;
        $port = $sstp_value->port;

        // check if the router is actively connected
        $client_router_ip = $this->checkActive($server_ip_address, $user, $pass, $port, $router_data[0]->sstp_username);
        $delete_pool = $request->has("delete_pool") ? $request->input("delete_pool") : "false";

        // check if the bridge is present on the router
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($client_router_ip, $user, $pass, $port)){
            // delete the bridge and unslave all the interfaces connected to that bridge
            $profile_data = $API->comm("/ppp/profile/print", [
                "?name" => $profile_name
            ]);
            if(count($profile_data) > 0){
                $active_pool = $profile_data[0]['remote-address'] ?? "";
                $API->comm("/ppp/profile/remove", [
                    ".id" => $profile_data[0]['.id']
                ]);
                if($delete_pool == "true"){
                    $pool_list = $API->comm("/ip/pool/print", [
                        "?name" => $active_pool
                    ]);
                    if(count($pool_list) > 0){
                        $API->comm("/ip/pool/remove", [
                            ".id" => $pool_list[0]['.id']
                        ]);
                    }
                }

                $API->disconnect();
            }

            // delete the profile
            DB::connection("mysql2")->delete("DELETE FROM router_profile WHERE profile_name = ?",[$profile_name]);

            // success messgae
            session()->flash("success_router", "Router profile has been deleted successfully!");
            return redirect(url()->previous());
        }else {
            // delete the profile
            DB::connection("mysql2")->delete("DELETE FROM router_profile WHERE profile_name = ?",[$profile_name]);
            // success messgae
            session()->flash("success_router", "Router profile has been deleted successfully!");
            session()->flash("error_router","Cannot connect to router, ensure you have configured the router correctly!");
            return redirect(url()->previous());
        }
    }

    function delete_router_bridge($router_id, $bridge_name){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // check first if the router configuration is done
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);
        if (count($router_data) == 0) {
            session()->flash("error_router","Invalid router");
            redirect(url()->route("view_router_cloud"));
        }
        
        // create a SSTP secret on the SSTP server
        // get the server details
        $sstp_settings = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sstp_server'");
        if (count($sstp_settings) == 0) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }

        // connect to the server
        $sstp_value = $this->isJson($sstp_settings[0]->value) ? json_decode($sstp_settings[0]->value) : null;

        if ($sstp_value == null) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }

        // connect to the router and set the sstp client
        $server_ip_address = $sstp_value->ip_address;
        $user = $sstp_value->username;
        $pass = $sstp_value->password;
        $port = $sstp_value->port;

        // check if the router is actively connected
        $client_router_ip = $this->checkActive($server_ip_address, $user, $pass, $port, $router_data[0]->sstp_username);

        // check if the bridge is present on the router
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($client_router_ip, $user, $pass, $port)){
            // delete the bridge and unslave all the interfaces connected to that bridge
            $bridge_ports = $API->comm("/interface/bridge/port/print", [
                "?bridge" => $bridge_name
            ]);
            if(count($bridge_ports) > 0){
                $API->comm("/interface/bridge/port/remove", [
                    ".id" => $bridge_ports[0]['.id']
                ]);
            }

            // delete the bridge
            $bridge = $API->comm("/interface/bridge/print", [
                "?name" => $bridge_name
            ]);
            if(count($bridge) > 0){
                $API->comm("/interface/bridge/remove", [
                    ".id" => $bridge[0]['.id']
                ]);
            }

            // delete the bridge
            DB::connection("mysql2")->delete("DELETE FROM router_bridge WHERE bridge_name = ?",[$bridge_name]);

            // success messgae
            session()->flash("success_router", "Router bridge has been deleted successfully!");
            return redirect(url()->previous());
        }else {
            // delete the bridge
            DB::connection("mysql2")->delete("DELETE FROM router_bridge WHERE bridge_name = ?",[$bridge_name]);

            // success messgae
            session()->flash("success_router", "Router bridge has been deleted successfully!");
            session()->flash("error_router","Cannot connect to router, ensure you have configured the router correctly!");
            return redirect(url()->previous());
        }
    }

    function update_bridge(Request $req){
        // change db
        $change_db = new login();
        $change_db->change_db();
        
        $bridges = [];
        $new_bridge_name = $req->input("edit_bridge_name");
        $old_bridge_name = $req->input("edit_bridge_name_2");
        $router_id = $req->input("router_id");

        foreach($req->all() as $key => $value){
            if(str_contains($key, "select_interface_checkbox_")){
                $bridge = $req->input($key);
                if($bridge == "on"){
                    array_push($bridges, $req->input("interface_index_bridge_".substr($key,strlen("select_interface_checkbox_"))));
                }
            }
        }

        // check if the bridge name is already used
        $check_bridge = DB::connection("mysql2")->select("SELECT * FROM `router_bridge` WHERE `bridge_name` = ? AND `router_id` = ?",[$new_bridge_name, $router_id]);
        if(count($check_bridge) > 0 && $old_bridge_name != $new_bridge_name){
            session()->flash("error_router","Bridge name already exists!");
            return redirect(url()->previous());
        }

        // return $bridges;

        // get the bridge ports
        $curl_handle = curl_init();
        $url = env('CRONJOB_URL', 'https://crontab.hypbits.com')."/getIpaddress.php?db_name=" . session("database_name") . "&r_id=" . $router_id . "&r_bridge_ports=true";
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        $curl_data = curl_exec($curl_handle);
        curl_close($curl_handle);
        $bridge_ports = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];
        // return $bridge_ports;

        // get the router interfaces
        $interfaces = [];
        $curl_handle = curl_init();
        $url = env('CRONJOB_URL', 'https://crontab.hypbits.com')."/getIpaddress.php?db_name=" . session("database_name") . "&r_id=" . $router_id . "&r_interfaces=true";
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        $curl_data = curl_exec($curl_handle);
        curl_close($curl_handle);
        $interfaces = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];

        // check those to remove, those to add and those retained
        $to_add = [];
        foreach ($bridges as $key => $bridge_port) {
            $found = false;
            foreach ($bridge_ports as $bp) {
                if ($bp['bridge'] == $old_bridge_name && $bp['interface'] == $bridge_port) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                array_push($to_add, $bridge_port);
            }
        }

        $to_remove = [];
        foreach ($bridge_ports as $bp) {
            if ($bp['bridge'] == $old_bridge_name && !str_contains($bp['bridge'],"*")) {
                $found = false;
                foreach ($bridges as $bridge_port) {
                    if ($bp['interface'] == $bridge_port) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    array_push($to_remove, $bp['interface']);
                }
            }
        }

        // from the interface list check to see if to_remove list is type ether
        foreach ($to_remove as $key => $remove_interface) {
            for ($index=0; $index < count($interfaces); $index++) { 
                if ($interfaces[$index]['name'] == $remove_interface && $interfaces[$index]['type'] != "ether") {
                    // remove from to_remove list
                    unset($to_remove[$key]);
                }
            }
        }

        // check first if the router configuration is done
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);
        if (count($router_data) == 0) {
            session()->flash("error_router","Invalid router");
            redirect(url()->route("view_router_cloud"));
        }
        
        // create a SSTP secret on the SSTP server
        // get the server details
        $sstp_settings = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sstp_server'");
        if (count($sstp_settings) == 0) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }

        // connect to the server
        $sstp_value = $this->isJson($sstp_settings[0]->value) ? json_decode($sstp_settings[0]->value) : null;

        if ($sstp_value == null) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }

        // connect to the router and set the sstp client
        $server_ip_address = $sstp_value->ip_address;
        $user = $sstp_value->username;
        $pass = $sstp_value->password;
        $port = $sstp_value->port;

        // check if the router is actively connected
        $client_router_ip = $this->checkActive($server_ip_address, $user, $pass, $port, $router_data[0]->sstp_username);

        // check if the bridge is present on the router
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($client_router_ip, $user, $pass, $port)){
            // get all bridges
            $all_bridges = $API->comm("/interface/bridge/print");
            $bridge_found = false;
            foreach ($all_bridges as $bridge) {
                if ($bridge['name'] == $old_bridge_name) {
                    $bridge_found = true;
                    break;
                }
            }
            if($bridge_found){
                // update the bridge name if they don`t match
                if($old_bridge_name != $new_bridge_name){
                    $API->comm("/interface/bridge/set", [
                        ".id" => $bridge['.id'],
                        "name" => $new_bridge_name,
                        "comment" => "Modified by HBS Cloud System"
                    ]);

                    // update the bridge name
                    DB::connection("mysql2")->update("UPDATE router_bridge SET bridge_name = ? WHERE bridge_name = ? AND router_id = ?",[$new_bridge_name, $old_bridge_name, $router_id]);
                }
            }else{
                // add the bridge if its not present
                $API->comm("/interface/bridge/add", [
                    "name" => $new_bridge_name,
                    "comment" => "Added by HBS Cloud System"
                ]);

                // insert the bridge
                $bridge_status = "1";
                DB::connection("mysql2")->insert("INSERT INTO router_bridge (bridge_name, bridge_status, router_id) VALUES (?,?,?)",[$new_bridge_name, $bridge_status, $router_id]);
            }
            
            // add multiple bridge ports
            foreach ($to_add as $add_interface) {
                $API->comm("/interface/bridge/port/add", [
                    "bridge" => $new_bridge_name,
                    "interface" => $add_interface,
                    "comment" => "Added by HBS Cloud System"
                ]);
            }

            // remove multiple bridge ports
            foreach ($to_remove as $remove_interface) {
                // get the bridge port id
                $bridge_ports_list = $API->comm("/interface/bridge/port/print", [
                    "?interface" => $remove_interface,
                    "?bridge" => $old_bridge_name
                ]);
                if (count($bridge_ports_list) > 0) {
                    $API->comm("/interface/bridge/port/remove", [
                        ".id" => $bridge_ports_list[0]['.id']
                    ]);
                }
            }
            
            $API->disconnect();
            session()->flash("success_router","Bridge updated successfully!");
            return redirect(url()->previous());
        }else{
            session()->flash("error_router","Cannot connect to router, ensure you have configured the router correctly!");
            return redirect(url()->previous());
        }
    }
    function sync_bridge_modal(Request $req){
        // change db
        $change_db = new login();
        $change_db->change_db();
        
        $bridges = [];
        foreach ($req->all() as $key => $value) {
            if (str_contains($key, "select_bridge_checkbox_")) {
                $bridge = $req->input($key);
                if ($bridge == "on") {
                    array_push($bridges, $req->input("new_selected_bridge_".substr($key,strlen("select_bridge_checkbox_"))));
                    $one_bridge = $req->input("new_selected_bridge_".substr($key,strlen("select_bridge_checkbox_")));
                    $bridge_status = 1;
                    DB::connection("mysql2")->insert("INSERT INTO router_bridge (bridge_name, bridge_status, router_id) VALUES (?,?,?)",[$one_bridge, $bridge_status, $req->input("router_id")]);
                }
            }
        }
        
        session()->flash("success_router","Selected bridges synchronized successfully!");
        return redirect(url()->previous());
    }

    function sync_profile_modal(Request $req){
        // change db
        $change_db = new login();
        $change_db->change_db();
        
        $bridges = [];
        foreach ($req->all() as $key => $value) {
            if (str_contains($key, "select_profile_checkbox_")) {
                $profile = $req->input($key);
                if ($profile == "on") {
                    array_push($bridges, $req->input("new_selected_profile_".substr($key,strlen("select_profile_checkbox_"))));
                    $one_profile = $req->input("new_selected_profile_".substr($key,strlen("select_profile_checkbox_")));
                    $profile_status = 1;
                    DB::connection("mysql2")->insert("INSERT INTO router_profile (profile_name, profile_status,router_id) VALUES (?,?,?)",[$one_profile, $profile_status, $req->input("router_id")]);
                }
            }
        }
        session()->flash("success_router","Selected profiles synchronized successfully!");
        return redirect(url()->previous());
    }

    function get_router_secret_information(Request $request, $router_id){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // get the bridge information
        $router_profile = [];
        $missing_account = $request->has("missing_account") ? $request->input("missing_account") : "false";

        // get the server details
        $sstp_settings = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sstp_server'");
        if (count($sstp_settings) == 0) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }
        
        // get the IP ADDRES
        $curl_handle = curl_init();
        $url = env('CRONJOB_URL', 'https://crontab.hypbits.com')."/getIpaddress.php?db_name=" . session("database_name") . "&r_id=" . $router_id . "&r_ppoe_profiles=true";
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        $curl_data = curl_exec($curl_handle);
        curl_close($curl_handle);
        $router_profile = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];

        // CHECK IF THE DB PROFILES EXIST
        $db_profiles = DB::connection("mysql2")->select("SELECT * FROM `router_profile` WHERE router_id = ?",[$router_id]);
        for ($index=0; $index < count($db_profiles); $index++) {
            $db_profiles[$index]->exists = 0;
            for ($j=0; $j < count($router_profile); $j++) {
                if ($db_profiles[$index]->profile_name == $router_profile[$j]['name']) {
                    $db_profiles[$index]->exists = 1;
                    break;
                }
            }
        }

        // CHECK IF THERE IS ANY NEW PROFILES AND ADD THEM TO THE DB BRIDGE WITH THE STATUS EXIST TO 0
        for ($j=0; $j < count($router_profile); $j++) {
            $found = 0;
            for ($index=0; $index < count($db_profiles); $index++) {
                if ($db_profiles[$index]->profile_name == $router_profile[$j]['name']) {
                    $found = 1;
                    break;
                }
            }
            if ($found == 0) {
                array_push($db_profiles, (object)[
                    "profile_id" => null,
                    "profile_name" => $router_profile[$j]['name'],
                    "exists" => 0
                ]);
            }
        }
        if($missing_account == "true"){
            $temp_profiles = [];
            for ($i=0; $i < count($db_profiles); $i++) { 
                if ($db_profiles[$i]->exists == 0 && $db_profiles[$i]->profile_id == null) {
                    array_push($temp_profiles, $db_profiles[$i]);
                }
            }
            $db_profiles = $temp_profiles;
        }

        // data list
        $data = [];
        $start = 0;
        foreach ($db_profiles as $index => $db_profile) {
            $button = "<button type='button' class='btn btn-info btn-sm profile_edit_btn' style='padding: 3px; background-color: rgb(40, 175, 208); transition: background-color 0.3s;' data-profile-name='".$db_profile->profile_name."' id='profile_edit_btn_".$db_profile->profile_id."'><span class='d-inline-block border border-white w-100 ' style='border-radius: 2px; padding: 5px; background-color: rgba(0, 0, 0, 0); color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); transition: color 0.3s, background-color 0.3s, border-color 0.3s;'><i class='fa fa-pen-fancy'></i> Edit</span></button>";
            $button .= "<button type='button' class='btn btn-danger btn-sm profile_del_btn ml-1' style='padding: 3px; background-color: rgb(250, 98, 107); transition: background-color 0.3s;' data-profile-name='".$db_profile->profile_name."' id='profile_del_btn_".$db_profile->profile_id."' ><span class='d-inline-block border border-white w-100 ' style='border-radius: 2px; padding: 5px; background-color: rgba(0, 0, 0, 0); color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); transition: color 0.3s, background-color 0.3s, border-color 0.3s;'><i class='fa fa-trash'></i> Del</span></button>";
            $data[] = [
                "rownum" => ($start + $index + 1). ($missing_account == "true" ? " <input type='checkbox' name='select_profile_checkbox_".$index."' class='form-check-input select_profile_checkbox ml-1' id='select_profile_checkbox_".$index."'> <input type='hidden' name='new_selected_profile_".$index."' value='".$db_profile->profile_name."'>" : ""),
                "profile_name" => $db_profile->profile_name,
                "profile_status" => ($db_profile->exists == 1  ? " <span class='badge bg-success'>Configured</span>" : " <small data-toggle='tooltip' title='' class='badge ".($db_profile->profile_id ? "bg-danger" : "bg-warning")." missing_profiles'>".($db_profile->profile_id ? "\"Missing in your router!\"" : "\"Missing in your account!\"")."</small>"),
                "actions" =>  $db_profile->profile_id ? $button : "<span class='badge bg-danger'>Not in DB</span>"
            ];
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => count($data),
            "recordsFiltered" => count($data),
            "data"            => $data
        ];
        return $json_data;
    }

    function get_router_bridge_interfaces(Request $request, $router_id){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // get the bridge information
        $bridge_ports = [];
        $bridge_name = $request->input("bridge_name");

        // get the bridge ports
        $curl_handle = curl_init();
        $url = env('CRONJOB_URL', 'https://crontab.hypbits.com')."/getIpaddress.php?db_name=" . session("database_name") . "&r_id=" . $router_id . "&r_bridge_ports=true";
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        $curl_data = curl_exec($curl_handle);
        curl_close($curl_handle);
        $bridge_ports = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];

        // get the router interfaces
        $interfaces = [];
        $curl_handle = curl_init();
        $url = env('CRONJOB_URL', 'https://crontab.hypbits.com')."/getIpaddress.php?db_name=" . session("database_name") . "&r_id=" . $router_id . "&r_interfaces=true";
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        $curl_data = curl_exec($curl_handle);
        curl_close($curl_handle);
        $interfaces = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];
        
        $final_interfaces = [];
        for ($index=0; $index < count($interfaces); $index++) { 
            if ($interfaces[$index]['type'] == "ether") {
                // check if the interface is part of any bridge
                $bridge = null;
                foreach ($bridge_ports as $key => $bridge_port) {
                    if ($bridge_port['interface'] == $interfaces[$index]['name'] && !str_contains($bridge_port['bridge'],"*")) {
                        // add to final interfaces
                        $bridge = $bridge_port['bridge'];
                        break;
                    }
                }
                $interfaces[$index]['bridge'] = $bridge;
                array_push($final_interfaces, $interfaces[$index]);
            }
        }
        // return $final_interfaces;


        // CHECK IF THE DB BRIDGES EXIST
        $start = 0;
        $data = [];
        foreach ($final_interfaces as $index => $interface) {
            $status = $interface['bridge'] ? ($interface['bridge'] != $bridge_name ? "disabled" : "") : "";
            $checked = $interface['bridge'] ? "checked" : "";
            $button = "<input type='checkbox' ".($status != "disabled" ? "name='select_interface_checkbox_".$index."' " : "")." ".$checked." class='form-check-input ml-1' $status id='select_interface_checkbox_".$index."'> <input type='hidden' ".($status != "disabled" ? "name='interface_index_bridge_".$index."' " : "")." value='".$interface['name']."'>";
            $data[] = [
                // add checkbox on first column to select
                "rownum" => ($start + $index + 1),
                "interface_name" => $interface['name'],
                "interface_status" => $interface['bridge'] ? "<span class='badge bg-success'>".$interface['bridge']."</span>" : "<span class='badge bg-danger'>Not Assigned</span>",
                "actions" =>  $button
            ];
        }

         $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => count($data),
            "recordsFiltered" => count($data),
            "data"            => $data
        ];
        return $json_data;
    }

    function get_router_bridge_information(Request $request, $router_id){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // get the bridge information
        $bridge_info = [];

        $only_misconfigured = $request->has("only_misconfigured") ? $request->input("only_misconfigured") : "false";

        // get the server details
        $sstp_settings = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sstp_server'");
        if (count($sstp_settings) == 0) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }
        // get the IP ADDRES
        $curl_handle = curl_init();
        $url = env('CRONJOB_URL', 'https://crontab.hypbits.com')."/getIpaddress.php?db_name=" . session("database_name") . "&r_id=" . $router_id . "&r_interfaces=true";
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        $curl_data = curl_exec($curl_handle);
        curl_close($curl_handle);
        $interfaces = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];

        foreach ($interfaces as $key => $interface) {
            if ($interface['type'] == "bridge") {
                array_push($bridge_info, $interface);
            }
        }

        // CHECK IF THE DB BRIDGES EXIST
        $db_bridges = DB::connection("mysql2")->select("SELECT * FROM `router_bridge` WHERE router_id = ?",[$router_id]);
        for ($index=0; $index < count($db_bridges); $index++) {
            $db_bridges[$index]->exists = 0;
            for ($j=0; $j < count($bridge_info); $j++) { 
                if ($db_bridges[$index]->bridge_name == $bridge_info[$j]['name']) {
                    $db_bridges[$index]->exists = 1;
                    break;
                }
            }
        }

        // CHECK IF THERE IS ANY NEW BRIDGES AND ADD THEM TO THE DB BRIDGE WITH THE STATUS EXIST TO 0
        for ($j=0; $j < count($bridge_info); $j++) { 
            $found = 0;
            for ($index=0; $index < count($db_bridges); $index++) {
                if ($db_bridges[$index]->bridge_name == $bridge_info[$j]['name']) {
                    $found = 1;
                    break;
                }
            }
            if ($found == 0) {
                array_push($db_bridges, (object)[
                    "bridge_id" => null,
                    "bridge_name" => $bridge_info[$j]['name'],
                    "exists" => 0
                ]);
            }
        }
        
        // handle requests for interfaces that are only misconfigured
        if($only_misconfigured == "true"){
            $filtered_bridges = [];
            for ($index=0; $index < count($db_bridges); $index++) {
                if ($db_bridges[$index]->exists == 0 && $db_bridges[$index]->bridge_id == null) {
                    array_push($filtered_bridges, $db_bridges[$index]);
                }
            }
            $db_bridges = $filtered_bridges;
        }

        $data = [];
        $start = 0;
        foreach ($db_bridges as $index => $db_bridge) {
            $button = "<button type='button' class='btn btn-info btn-sm bridge_view_btn' style='padding: 3px; background-color: rgb(40, 175, 208); transition: background-color 0.3s;' id='bridge_view_btn_".$db_bridge->bridge_id."' data-bridge-name='".$db_bridge->bridge_name."'><span class='d-inline-block border border-white w-100 ' style='border-radius: 2px; padding: 5px; background-color: rgba(0, 0, 0, 0); color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); transition: color 0.3s, background-color 0.3s, border-color 0.3s;'><i class='fa fa-pen-fancy'></i> Edit</span></button>";
            $button .= "<button type='button' class='btn btn-danger btn-sm bridge_del_btn ml-1' style='padding: 3px; background-color: rgb(250, 98, 107); transition: background-color 0.3s;' id='bridge_del_btn_".$db_bridge->bridge_id."' data-bridge-name='".$db_bridge->bridge_name."'><span class='d-inline-block border border-white w-100 ' style='border-radius: 2px; padding: 5px; background-color: rgba(0, 0, 0, 0); color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); transition: color 0.3s, background-color 0.3s, border-color 0.3s;'><i class='fa fa-trash'></i> Del</span></button>";
            $data[] = [
                // add checkbox on first column to select
                "rownum" => ($start + $index + 1) . ($only_misconfigured == "true" ? " <input type='checkbox' name='select_bridge_checkbox_".$index."' class='form-check-input select_bridge_checkbox ml-1' id='select_bridge_checkbox_".$index."'> <input type='hidden' name='new_selected_bridge_".$index."' value='".$db_bridge->bridge_name."'>" : ""),
                "bridge_name" => $db_bridge->bridge_name,
                "bridge_status" => ($db_bridge->exists == 1  ? " <span class='badge bg-success'>Configured</span>" : " <small data-toggle='tooltip' title='' class='badge ".($db_bridge->bridge_id ? "bg-danger" : "bg-warning")." missing_bridge'>".($db_bridge->bridge_id ? "\"Missing in your router!\"" : "\"Missing in your account!\"")."</small>"),
                "actions" =>  $db_bridge->bridge_id ? $button : "<span class='badge bg-danger'>Not in DB</span>"
            ];
        }

         $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => count($data),
            "recordsFiltered" => count($data),
            "data"            => $data
        ];
        return $json_data;
    }
    // create a new cloud router
    function save_cloud_router(Request $req){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // get the data
        $router_name = $req->input("router_name");
        $routers_physical_address = $req->input("routers_physical_address");
        $routers_coordinates = $req->input("routers_coordinates");
        $winbox_port = $req->input("winbox_port");
        $api_ports = $req->input("api_ports");

        // set them as flash values
        session()->flash("router_name",$router_name);
        session()->flash("routers_physical_address",$routers_physical_address);
        session()->flash("routers_coordinates",$routers_coordinates);
        session()->flash("winbox_port",$winbox_port);
        session()->flash("api_ports",$api_ports);

        // create the username and password
        $username  = $this->randomWords(9);
        $password = $this->randomWords(18);
        
        // create a SSTP secret on the SSTP server
        // get the server details
        $sstp_settings = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sstp_server'");
        if (count($sstp_settings) == 0) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }

        // connect to the server
        $sstp_value = $this->isJson($sstp_settings[0]->value) ? json_decode($sstp_settings[0]->value) : null;

        if ($sstp_value == null) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }

        // connect to the router and set the sstp client
        $ip_address = $sstp_value->ip_address;
        $user = $sstp_value->username;
        $pass = $sstp_value->password;
        $port = $sstp_value->port;

        $organization_data = DB::select("SELECT * FROM `organizations` WHERE organization_id = ?", [session("organization_id")]);
        $organization_name = count($organization_data) > 0 ? $organization_data[0]->organization_name : "N/A";
        $organization_account_no = count($organization_data) > 0 ? $organization_data[0]->account_no : "N/A";

        // connect to the router
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($ip_address,$user,$pass,$port)){
            // return $API->comm("/ppp/secret/print");
            // create a ppp profile
            $add_pp_secret = $API->comm("/ppp/secret/add",
            array(
                "name" => $username,
                "password" => $password,
                "profile" => "SSTP_PROFILE",
                "service" => "sstp",
                "comment" => "Router Name ($router_name) of ($organization_name - $organization_account_no)"
            ));
            $API->disconnect();
        }else{
            session()->flash("error_router","The SSTP server cannot be reached, Contact your administrator!");
            return redirect(url()->previous());
        }
        

        // store the data in the database
        $today = date("YmdHis");
        $insert = DB::connection("mysql2")->insert("INSERT INTO `remote_routers` (`router_name`,`sstp_username`,`sstp_password`,`router_location`,`router_coordinates`,`winbox_port`,`api_port`,`date_changed`) 
                            VALUES (?,?,?,?,?,?,?,?)",[$router_name,$username,$password,$routers_physical_address,$routers_coordinates,$winbox_port,$api_ports,$today]);

        $select = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` ORDER BY `router_id` DESC LIMIT 1");
        if (count($select) == 0) {
            session()->flash("error_router","An error has occured");
            return redirect(url()->route("my_routers"));
        }
        $router_id = $select[0]->router_id;
        // return to the view page of the router
        return redirect(url()->route("view_router_cloud",[$router_id]));
    }

    // connect router
    function connect_router($router_id){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // check if the router is active
        // check first if the router configuration is done
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);

        if (count($router_data) == 0) {
            session()->flash("error_router","Invalid router");
            redirect(url()->route("view_router_cloud"));
        }

        // get all clients under that router
        $client_details = DB::connection("mysql2")->select("SELECT COUNT(*) AS 'Total' FROM `client_tables` WHERE `router_name` = ?",[$router_id]);

        // get the router details
        $router_detail = [];

        // connect to the router and get its details

        // connect to the router and set the sstp client
        $sstp_value = $this->getSSTPAddress();
        if ($sstp_value == null) {
            $error = "The SSTP server is not set, Contact your administrator!";
            session()->flash("error_router",$error);
            return redirect(url()->route("view_router_cloud"));
        }

        // connect to the router and set the sstp client
        $ip_address = $sstp_value->ip_address;
        $user = $sstp_value->username;
        $pass = $sstp_value->password;
        $port = $sstp_value->port;

        // check if the router is actively connected
        $client_router_ip = $this->checkActive($ip_address,$user,$pass,$port,$router_data[0]->sstp_username);

        $router_stats = [];
        if ($client_router_ip) {
            // get the router details
            $API = new routeros_api();
            $API->debug = false;
            
            $ip_address = $client_router_ip;
            $user = $router_data[0]->sstp_username;
            $pass = $router_data[0]->sstp_password;
            $port = $router_data[0]->api_port;
            if ($API->connect($ip_address, $user, $pass, $port)){
                $router_stats = $API->comm("/system/resource/print");
            }else{
                session()->flash("error_router","Cannot connect to router, ensure you have configured the router correctly!");
                return redirect(url()->route("view_router_cloud",[$router_id]));
            }
        }else{
            session()->flash("error_router","Cannot connect to router, ensure you have configured the router correctly!");
            return redirect(url()->route("view_router_cloud",[$router_id]));
        }
        
        // change the status from unconnected to connected
        $update = DB::connection("mysql2")->update("UPDATE `remote_routers` SET `activated` = '1' WHERE `router_id` = ?",[$router_id]);

        // return to the main page
        return redirect(url()->route("view_router_cloud",[$router_id]));
    }

    // view_router_details
    function view_router_details($router_id){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // check first if the router configuration is done
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);

        if (count($router_data) == 0) {
            session()->flash("error_router","Invalid router");
            redirect(url()->route("view_router_cloud"));
        }

        // get all clients under that router
        $client_details = DB::connection("mysql2")->select("SELECT COUNT(*) AS 'Total' FROM `client_tables` WHERE `router_name` = ?",[$router_id]);

        // get the router details
        $router_detail = [];

        // connect to the router and get its details

        // connect to the router and set the sstp client
        $sstp_value = $this->getSSTPAddress();
        if ($sstp_value == null) {
            $error = "The SSTP server is not set, Contact your administrator!";
            session()->flash("error_router",$error);
            return redirect(url()->route("view_router_cloud"));
        }

        // connect to the router and set the sstp client
        $ip_address = $sstp_value->ip_address;
        $user = $sstp_value->username;
        $pass = $sstp_value->password;
        $port = $sstp_value->port;

        // check if the router is actively connected
        $client_router_ip = $this->checkActive($ip_address,$user,$pass,$port,$router_data[0]->sstp_username);

        $router_stats = [];
        if ($client_router_ip) {
            // get the router details
            $API = new routeros_api();
            $API->debug = false;
            
            $ip_address = $client_router_ip;
            $user = $router_data[0]->sstp_username;
            $pass = $router_data[0]->sstp_password;
            $port = $router_data[0]->api_port;
            if ($API->connect($ip_address, $user, $pass, $port)){
                $router_stats = $API->comm("/system/resource/print");
            }
        }
        // return $router_stats;

        return view("router.infor",["router_data" => $router_data, "router_stats" => $router_stats, "user_count" => $client_details,"router_detail" => $router_detail, "ip_address" => $ip_address]);
    }

    function reboot($router_id){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // get the router data
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);
        if (count($router_data) == 0) {
            $error = "The router is invalid!";
            session()->flash("error_router",$error);
            return redirect(url()->previous());
        }

        // connect to the router and set the sstp client
        $sstp_value = $this->getSSTPAddress();
        if ($sstp_value == null) {
            $error = "The SSTP server is not set, Contact your administrator!";
            session()->flash("error_router",$error);
            return redirect(url()->previous());
        }

        // connect to the router and set the sstp client
        $ip_address = $sstp_value->ip_address;
        $user = $sstp_value->username;
        $pass = $sstp_value->password;
        $port = $sstp_value->port;

        // check if the router is actively connected
        $client_router_ip = $this->checkActive($ip_address,$user,$pass,$port,$router_data[0]->sstp_username);
        if ($client_router_ip) {
            // connect and reboot the router
            $API = new routeros_api();
            $API->debug = false;

            $ip_address = $client_router_ip;
            $user = $router_data[0]->sstp_username;
            $pass = $router_data[0]->sstp_password;
            $port = $router_data[0]->api_port;
            if ($API->connect($ip_address, $user, $pass, $port)){
                // reboot
                $API->comm("/system/reboot");

                // skip disconnect
                $API->disconnect();

                // return
                return redirect(url()->route("view_router_cloud",$router_data[0]->router_id));
            }else {
                // return
                return redirect(url()->route("view_router_cloud",$router_data[0]->router_id));
            }
        }else{
            // return
            return redirect(url()->route("view_router_cloud",$router_data[0]->router_id));
        }
    }

    function checkActive($ip_address,$user,$pass,$port,$sstp_username){
        $API = new routeros_api();
        $API->debug = false;

        if ($API->connect($ip_address, $user, $pass, $port)){
            // connect and get the 
            $active = $API->comm("/ppp/active/print");
            // return $active;

            // loop through the active routers to get if the router is active or not so that we connect
            $found = 0;
            $ip_address_remote_client = false;
            for ($index=0; $index < count($active); $index++) { 
                if ($active[$index]['name'] == $sstp_username && $active[$index]['service'] == "sstp") {
                    $found = 1;
                    $ip_address_remote_client = $active[$index]['address'];
                    break;
                }
            }

            // if found the router is actively connected
            if ($found == 1) {
                $API->disconnect();
                return $ip_address_remote_client;
            }
            $API->disconnect();
        }
        return false;
    }

    function getSSTPAddress(){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // get the server details
        $sstp_settings = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sstp_server'");
        if (count($sstp_settings) == 0) {
            return null;
        }

        // connect to the server
        $sstp_value = $this->isJson($sstp_settings[0]->value) ? json_decode($sstp_settings[0]->value) : null;

        if ($sstp_value == null) {
            return null;
        }
        return $sstp_value;
    }

    function updateRouter(Request $request){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // return $request;
        $router_id = $request->input("router_id");
        $router_name = $request->input("router_name");
        $physical_location = $request->input("physical_location");
        $router_coordinates = $request->input("router_coordinates");
        $winbox_ports = $request->input("winbox_ports");
        $api_ports = $request->input("api_ports");

        $update = DB::connection("mysql2")->update("UPDATE `remote_routers` SET `router_name` = ?, `api_port` = ?, `winbox_port` = ?,`router_location` = ?, `router_coordinates` = ? WHERE `router_id` = ?",[$router_name,$api_ports,$winbox_ports,$physical_location,$router_coordinates,$router_id]);

        // sesssion
        session()->flash("success_router","Router details updated successfully!");
        
        // get the router details
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);
        $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";

        // log routers
        $new_client = new Clients();
        $txt = ":Router (".$router_name.") details updated successfully!";
        $new_client->log($txt);
        return redirect(url()->route("view_router_cloud",[$router_id]));
    }

    // view cloud router details

    function randomWords($word_length){
        // check if the word length is string

        // letters
        $letters = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
        $word = "";

        // create the word
        for ($index=0; $index < $word_length; $index++) { 
            $random_no = rand(0,51);
            $word.=$letters[$random_no];
        }

        return $word;
    }

    function getRouterData(){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // here we get the router data
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `deleted` = '0' ORDER BY `router_id` DESC;");
        for ($index=0; $index < count($router_data); $index++) {
            $users = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `router_name` = ?",[$router_data[$index]->router_id]);
            $router_data[$index]->user_count = count($users);
        }
        return view("router.myRouter",['router_data'=>$router_data]);
    }
    function isJson($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
    
    // delete router
    function deleteRouter($router_id){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // get the router details
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);
        if (count($router_data) == 0) {
            session()->flash("error_router","The you are deleting is invalid!");
            return redirect(url()->route("my_routers"));
        }
        
        // create a SSTP secret on the SSTP server
        // get the server details
        $sstp_settings = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sstp_server'");
        if (count($sstp_settings) == 0) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }

        // connect to the server
        $sstp_value = $this->isJson($sstp_settings[0]->value) ? json_decode($sstp_settings[0]->value) : null;

        if ($sstp_value == null) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }

        // connect to the router and set the sstp client
        $ip_address = $sstp_value->ip_address;
        $user = $sstp_value->username;
        $pass = $sstp_value->password;
        $port = $sstp_value->port;


        // connect to the router
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($ip_address,$user,$pass,$port)){
            // get the router username and password and delete it from the list of profiles in the system
            // return $API->comm("/ppp/secret/print");
            // create a ppp profile
            $ppp_secrets = $API->comm("/ppp/secret/print");
            // return $ppp_secrets;
            $id = false;
            for ($index=0; $index < count($ppp_secrets); $index++) {
                if ($ppp_secrets[$index]['name'] == $router_data[0]->sstp_username && $ppp_secrets[$index]['password'] == $router_data[0]->sstp_password) {
                    $id = $ppp_secrets[$index]['.id'];
                    break;
                }
            }

            // delete the profile if the id is found
            if ($id) {
                $API->comm("/ppp/secret/remove",array(
                    ".id" => $id
                ));
            }
            $API->disconnect();
        }

        // delete users associated to the router
        // $delete = DB::connection("mysql2")->delete("DELETE FROM `client_tables` WHERE `router_name` = '".$router_id."'");
        $UPDATE = DB::connection("mysql2")->update("UPDATE `client_tables` SET `date_changed` = ?, `deleted` = ? WHERE `router_name` = ?",[date("YmdHis"),"1",$router_id]);
        
        // delete the router
        DB::connection("mysql2")->update("UPDATE `remote_routers` SET `date_changed` = ?, `deleted` = '1' WHERE `router_id` = ?",[date("YmdHis"),$router_id]);
        
        // get the router details
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);
        $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";

        // delete router
        DB::connection("mysql2")->delete("DELETE FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);
        
        // DB::connection("mysql2")->delete("DELETE FROM `router_tables` WHERE `router_id` = '".$router_id."'");
        session()->flash("success_router","Router deleted Successfully!");

        // log routers
        $new_client = new Clients();
        $txt = ":Router (".$router_name.") deleted successfully!";
        $new_client->log($txt);

        // redirect url
        return redirect(url()->route("my_routers"));
    }
}
