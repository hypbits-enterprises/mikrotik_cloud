<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Classes\routeros_api;

use function PHPUnit\Framework\isJson;


date_default_timezone_set('Africa/Nairobi');
class Router_Cloud extends Controller
{
    //
    // create a new cloud router
    function save_cloud_router(Request $req){
        // return $req;
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
        $sstp_settings = DB::select("SELECT * FROM `settings` WHERE `keyword` = 'sstp_server'");
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
        $port = 8728;


        // connect to the router
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($ip_address,$user,$pass,$port)){
            $ipaddress = $API->comm("/ppp/secret/print");
            $API->comm("/ppp/secret/add",array(
                "name" => $username,
                "password" => $password,
                "profile" => "SSTP_PROFILE",
                "service" => "sstp"
            ));
        }
        

        // store the data in the database
        $today = date("YmdHis");
        $insert = DB::insert("INSERT INTO `remote_routers` (`router_name`,`sstp_username`,`sstp_password`,`router_location`,`router_coordinates`,`winbox_port`,`api_port`,`date_changed`) 
                            VALUES (?,?,?,?,?,?,?,?)",[$router_name,$username,$password,$routers_physical_address,$routers_coordinates,$winbox_port,$api_ports,$today]);

        $select = DB::select("SELECT * FROM `remote_routers` ORDER BY `router_id` DESC LIMIT 1");
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
        // change the status from unconnected to connected
        $update = DB::update("UPDATE `remote_routers` SET `activated` = '1' WHERE `router_id` = ?",[$router_id]);

        // return to the main page
        return redirect(url()->route("view_router_cloud",[$router_id]));
    }

    // view_router_details
    function view_router_details($router_id){
        // check first if the router configuration is done
        $router_data = DB::select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);

        if (count($router_data) == 0) {
            session()->flash("error_router","Invalid router");
            redirect(url()->route("view_router_cloud"));
        }

        // get all clients under that router
        $client_details = DB::select("SELECT COUNT(*) AS 'Total' FROM `client_tables` WHERE `router_name` = ?",[$router_id]);
        // return $client_details;

        // get the router details
        $router_detail = [];

        return view("router.infor",["router_data" => $router_data, "user_count" => $client_details,"router_detail" => $router_detail]);
    }

    function updateRouter(Request $request){
        // return $request;
        $router_id = $request->input("router_id");
        $router_name = $request->input("router_name");
        $physical_location = $request->input("physical_location");
        $router_coordinates = $request->input("router_coordinates");
        $winbox_ports = $request->input("winbox_ports");
        $api_ports = $request->input("api_ports");

        $update = DB::update("UPDATE `remote_routers` SET `router_name` = ?, `api_port` = ?, `winbox_port` = ?,`router_location` = ?, `router_coordinates` = ? WHERE `router_id` = ?",[$router_name,$api_ports,$winbox_ports,$physical_location,$router_coordinates,$router_id]);

        // sesssion
        session()->flash("success_router","Router details updated successfully!");
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
        // here we get the router data
        $router_data = DB::select("SELECT * FROM `remote_routers` WHERE `deleted` = '0' ORDER BY `router_id` DESC;");
        for ($index=0; $index < count($router_data); $index++) {
            $users = DB::select("SELECT * FROM `client_tables` WHERE `router_name` = ?",[$router_data[$index]->router_id]);
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
        // delete users associated to the router
        // $delete = DB::delete("DELETE FROM `client_tables` WHERE `router_name` = '".$router_id."'");
        $UPDATE = DB::update("UPDATE `client_tables` SET `date_changed` = ?, `deleted` = ? WHERE `router_name` = ?",[date("YmdHis"),"1",$router_id]);
        
        // delete the router
        DB::update("UPDATE `remote_routers` SET `date_changed` = ?, `deleted` = '1' WHERE `router_id` = ?",[date("YmdHis"),$router_id]);
        
        // DB::delete("DELETE FROM `router_tables` WHERE `router_id` = '".$router_id."'");
        session()->flash("success_router","Router deleted Successfully!");
        return redirect(url()->route("my_routers"));
    }
}
