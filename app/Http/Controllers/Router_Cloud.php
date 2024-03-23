<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Classes\routeros_api;

use function PHPUnit\Framework\isJson;


date_default_timezone_set('Africa/Nairobi');
class Router_Cloud extends Controller
{
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
                "service" => "sstp"
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
