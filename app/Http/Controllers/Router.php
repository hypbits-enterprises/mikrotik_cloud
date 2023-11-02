<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Classes\routeros_api;
use App\Models\router_table;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;

use function PHPUnit\Framework\isJson;

date_default_timezone_set('Africa/Nairobi');

class Router extends Controller
{
    //responsible for the router
    function getRouterInfor($routerid){
        // get the router information and also connect to get all other basic information about the router
        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '$routerid'");
        // get the router information after forming the connection
        $ip_address = $router_data[0]->router_ipaddr;
        $port = $router_data[0]->router_api_port;
        $username = $router_data[0]->router_api_username;
        $password = $router_data[0]->router_api_password;

        // connect to the router
        $API = new routeros_api();
        $API->debug = false;
        $router_details = [];
        if ($API->connect($ip_address,$username,$password,$port)) {

            session()->put("router_ipaddr",$ip_address);
            session()->put("router_username",$username);
            session()->put("router_password",$password);
            session()->put("router_api_port",$port);
            // connect to the router
            $r1 = $API->comm("/system/routerboard/print");
            $model = $r1[0]['model'];
            $r2 = $API->comm("/system/identity/print");
            $identity = $r2[0]['name'];
            $r3 = $API->comm("/system/resource/print");
            $uptime = $r3[0]['uptime'];
            $free_memory = round($r3[0]['free-memory']/(1024*1024),2)." MB";
            $cpu_load = $r3[0]['cpu-load']."%";
            $total_memory =round($r3[0]['total-memory']/(1024*1024),2)." MB";
            $free_hdd_space = round($r3[0]['free-hdd-space']/(1024*1024),2)." MB";
            $total_hdd_space = round($r3[0]['total-hdd-space']/(1024*1024),2)." MB";
            $board_name = $r3[0]['board-name'];
            $API->disconnect();
            // return $r3;
            array_push($router_details,$model,$identity,$uptime,$free_memory,$total_memory,$cpu_load,$free_hdd_space,$total_hdd_space,$board_name);
        }
        // get the users available in that router
        $user_count = DB::select("SELECT  COUNT(*) AS 'Total' FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = '$routerid'");
        // return $user_count
        return view("routerinfor", ["router_data" => $router_data,"router_detail" => $router_details,"user_count" => $user_count]);
    }

    // reboot the router
    function reboot($routerid){
        // get the router information
        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '$routerid'");
        $ip_address = $router_data[0]->router_ipaddr;
        $port = $router_data[0]->router_api_port;
        $username = $router_data[0]->router_api_username;
        $password = $router_data[0]->router_api_password;

        // connect to the router
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($ip_address,$username,$password,$port)) {
            // reboot the router if it can be connected
            $reboot = $API->comm("/system/reboot");
            // return $reboot;
            $API->disconnect();
            session()->flash("success_router","Your router is being rebooted give it some time to start");

            // log file capture error
            // read the data 
            $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
            $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
            $existing_txt = fread($myfile,$file_sizes);
            // return $existing_txt;
            $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
            $date = date("dS M Y (H:i:sa)");
            $txt = $date.":Router rebooted by ".session('Usernames')."\n".$existing_txt;
            // return $txt;
            fwrite($myfile, $txt);
            fclose($myfile);
            // end of log file
        }else {
            session()->flash("error_router","Attempts to connect to the router was unsuccessfull");
        }
        return redirect("/Router/View/$routerid");
    }
    function updateRoute(Request $req){
        // connect to the router first before updating the data
        $route_ip = $req->input("ip_address");
        $api_username = $req->input("api_username");
        $router_api_password = $req->input("router_api_password");
        $router_api_port = $req->input("router_api_port");
        $router_name = $req->input("router_name");

        // create a connection
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($route_ip,$api_username,$router_api_password,$router_api_port)) {
            // disconnect and update the router data
            DB::table("router_tables")
                    ->where("router_id", $req->input("router_id"))
                    ->update([
                        "router_name" => $router_name,
                        "router_ipaddr" => $route_ip,
                        "router_api_username" => $api_username,
                        "router_api_password" => $router_api_password,
                        "router_api_port" => $router_api_port,
                        "router_status" => "1",
                        'date_changed' => date("YmdHis")
                    ]);

                    // log file capture error
                    // read the data 
                    $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                    $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                    $existing_txt = fread($myfile,$file_sizes);
                    // return $existing_txt;
                    $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                    $date = date("dS M Y (H:i:sa)");
                    $txt = $date.":Router ".$router_name." Information updated by ".session('Usernames')."\n".$existing_txt;
                    // return $txt;
                    fwrite($myfile, $txt);
                    fclose($myfile);
                    // end of log file
                    session()->flash("success_router","Your router information has been successfully been updated!");
        }else {
            session()->flash("error_router","Your router information has not been updated because connection to the router with the new changes cannot be established!");
        }
        return redirect("/Router/View/".$req->input("router_id"));
    }
    function deleteRouter($router_id){
        // delete users associated to the router
        // $delete = DB::delete("DELETE FROM `client_tables` WHERE `router_name` = '".$router_id."'");
        $UPDATE = DB::update("UPDATE `client_tables` SET `date_changed` = ?, `deleted` = ? WHERE `router_name` = ?",[date("YmdHis"),"1",$router_id]);
        // delete the router
        DB::update("UPDATE `router_tables` SET `date_changed` = ?, `deleted` = '1' WHERE `router_id` = ?",[date("YmdHis"),$router_id]);
        // DB::delete("DELETE FROM `router_tables` WHERE `router_id` = '".$router_id."'");
        session()->flash("success_router","Router deleted Successfully!");
        return redirect("/Routers");
    }
    function test_router(Request $req){
        $router_name = $req->input("router_name");
        $router_ip_address = $req->input("router_ip_address");
        $router_username = $req->input("router_username");
        $router_password = $req->input("router_password");
        $router_api_port = $req->input("router_api_port");
        
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
            // return "Connected!";
            $my_routers = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_name` = '".$router_name."'");
            if (count($my_routers) > 0) {
                return "<p class='text-danger'>The router name is already present please use another router name!</p>";
            }
            // save the router in the database
            $router = new router_table();
            $router->router_name = $router_name;
            $router->router_ipaddr = $router_ip_address;
            $router->router_api_username = $router_username;
            $router->router_api_password = $router_password;
            $router->router_api_port = $router_api_port;
            // return $router;
            // come back and save the table
            $router->save();
            session()->put("router_ipaddr",$router_ip_address);
            session()->put("router_username",$router_username);
            session()->put("router_password",$router_password);
            session()->put("router_api_port",$router_api_port);
            // $nat_in = $API->comm("/ip/firewall/nat/print");
            // return $nat_in;

            // set NAT
            $API->comm("/ip/firewall/nat/add",array(
                "chain" => "srcnat",
                "out-interface" => "ether1",
                "action" => "masquerade"
            ));
        }else {
            session()->forget("router_ipaddr");
            session()->forget("router_username");
            session()->forget("router_password");
            session()->forget("router_api_port");
            return "<li id='errors_connection' class='text-danger'>Can`t connect to the router.</li><li class='text-danger'>Check your credentials, ip address or ports provided.</li>";
        }
    }
    function get_interface_config(Request $req){
        $router_ip_address = $req->input("router_ip_address");
        $router_username = $req->input("router_username");
        $router_password = $req->input("router_password");
        $router_port = $req->input("router_port");
        // connect to the router and get the interface configuration as requested
        // connect to the router get bridges and get all ports associated with the bridges then assign them ids and classes then give the listeners
        $router_ip_address = $router_ip_address;
        $router_username = $router_username;
        $router_password = $router_password;
        $router_api_port = $router_port;
        
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
            // connect and get the bridges
            $bridges = [];
            $bridges_ids = [];
            $bridge_data = $API->comm("/interface/bridge/print");
            for ($indexes=0; $indexes < count($bridge_data); $indexes++) {
                foreach ($bridge_data[$indexes] as $key => $value) {
                    if ($key == "name") {
                        array_push($bridges,$value);
                        array_push($bridges_ids,$bridge_data[$indexes]['.id']);
                        continue;
                    }
                }
            }
            // go through the bridges and get the interfaces linked to that bridge
            $ports = $API->comm("/interface/bridge/port/print");
            // return $ports;
            $data_to_display = "<p class='text-secondary'><u>Interfaces Assigned</u></p><ul>";
            $interface_present = [];
            $hesabu = 0;
            for ($index=0; $index < count($bridges); $index++) { 
                $bridge_name = $bridges[$index];
                $bridge_id = $bridges_ids[$index];
                $data_to_display.="<li class='text-primary'> <b class = ''>".$bridge_name."</b></li>";
                $data_to_display.="<ul>";
                for ($ind=0; $ind < count($ports); $ind++) {
                    if ($ports[$ind]['bridge'] == $bridge_name) {
                        array_push($interface_present,$ports[$ind]['interface']);
                        $data_to_display.="<li>".$ports[$ind]['interface']." </li>";
                    }
                }
                $hesabu++;
                $data_to_display.="</ul>";
            }
            if ($hesabu > 0) {
                $data_to_display.="</ul><p><u>Unassigned Ports</u></p>";
            }else{
                $data_to_display = "<p class='text-secondary'><u>Interfaces Assigned</u></p><p class='text-danger'>No bridges present yet</p><p><u>Unassigned Ports</u></p>";
            }
            $interfaces = $API->comm("/interface/print");
            $display_interfaces = [];
            for ($indexed=0; $indexed < count($interfaces); $indexed++) { 
                if ($interfaces[$indexed]['type'] == "ether" || $interfaces[$indexed]['type'] == "wlan" ) {
                    $present = 0;
                    $interface_name = $interfaces[$indexed]['name'];
                    for ($i=0; $i < count($interface_present); $i++) { 
                        if ($interface_present[$i] == $interface_name) {
                            $present = 1;
                        }
                    }
                    if ($present == 0) {
                        array_push($display_interfaces,$interface_name);
                    }
                }
            }
            // creat the list of all unused ports
            $display_ports = "<ul>";
            $counter = 0;
            for ($index=0; $index < count($display_interfaces); $index++) { 
                $gateway = "";
                if ($display_interfaces[$index] == "ether1") {
                    $gateway = "<span class='ml-1 fx-lg' style='cursor:pointer;' data-toggle='tooltip' title='This interface will be used as the WAN gateway'><i class='fas fa-question-circle'></i></span>";
                }
                $display_ports.="<li class='' id=''>".$display_interfaces[$index]."".$gateway."</li>";
                $counter++;
            }
            $display_ports.="</ul>";
            if ($counter > 0) {
                $data_to_display.=$display_ports;
            }else {
                $data_to_display.="<li class='text-danger'>No interfaces un-assigned!</li>";
            }
            return $data_to_display;
        }
    }
    function process_interfaces(){
        // connect to the router get bridges and get all ports associated with the bridges then assign them ids and classes then give the listeners
        $router_ip_address = session("router_ipaddr");
        $router_username = session("router_username");
        $router_password = session("router_password");
        $router_api_port = session("router_api_port");
        
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
            // connect and get the bridges
            $bridges = [];
            $bridges_ids = [];
            $bridge_data = $API->comm("/interface/bridge/print");
            for ($indexes=0; $indexes < count($bridge_data); $indexes++) {
                foreach ($bridge_data[$indexes] as $key => $value) {
                    if ($key == "name") {
                        array_push($bridges,$value);
                        array_push($bridges_ids,$bridge_data[$indexes]['.id']);
                        continue;
                    }
                }
            }
            // go through the bridges and get the interfaces linked to that bridge
            $ports = $API->comm("/interface/bridge/port/print");
            // return $ports;
            $data_to_display = "<p class='text-secondary'><u>Interfaces Assigned</u></p><ul>";
            $interface_present = [];
            $hesabu = 0;
            for ($index=0; $index < count($bridges); $index++) { 
                $bridge_name = $bridges[$index];
                $bridge_id = $bridges_ids[$index];
                $data_to_display.="<li class='text-primary'> <b class = 'bridge-ports' id='br_name".$bridge_id."'>".$bridge_name."</b> <span class='my_funga ml-1' id='my_funga".$bridge_id."'>x</span></li>";
                $data_to_display.="<ul>";
                for ($ind=0; $ind < count($ports); $ind++) {
                    if ($ports[$ind]['bridge'] == $bridge_name) {
                        array_push($interface_present,$ports[$ind]['interface']);
                        $data_to_display.="<li>".$ports[$ind]['interface']." <span class='funga ml-1' id='funga".$ports[$ind]['.id']."'>x</span></li>";
                    }
                }
                $hesabu++;
                $data_to_display.="</ul>";
            }
            if ($hesabu > 0) {
                $data_to_display.="</ul><p><u>Unassigned Ports</u></p>";
            }else{
                $data_to_display = "<p class='text-secondary'><u>Interfaces Assigned</u></p><p class='text-danger'>No bridges present yet</p><p><u>Unassigned Ports</u></p>";
            }
            $interfaces = $API->comm("/interface/print");
            $display_interfaces = [];
            for ($indexed=0; $indexed < count($interfaces); $indexed++) { 
                if ($interfaces[$indexed]['type'] == "ether" || $interfaces[$indexed]['type'] == "wlan" ) {
                    $present = 0;
                    $interface_name = $interfaces[$indexed]['name'];
                    for ($i=0; $i < count($interface_present); $i++) { 
                        if ($interface_present[$i] == $interface_name) {
                            $present = 1;
                        }
                    }
                    if ($present == 0) {
                        array_push($display_interfaces,$interface_name);
                    }
                }
            }
            // creat the list of all unused ports
            $display_ports = "<ul>";
            $counter = 0;
            for ($index=0; $index < count($display_interfaces); $index++) { 
                $gateway = "";
                if ($display_interfaces[$index] == "ether1") {
                    $gateway = "<span class='ml-1 fx-lg' style='cursor:pointer;' data-toggle='tooltip' title='This interface will be used as the WAN gateway'><i class='fas fa-question-circle'></i></span>";
                }
                $display_ports.="<li class='un_used_ports' id='".$display_interfaces[$index]."'>".$display_interfaces[$index]."".$gateway."</li>";
                $counter++;
            }
            $display_ports.="</ul>";
            if ($counter > 0) {
                $data_to_display.=$display_ports;
            }else {
                $data_to_display.="<li class='text-danger'>No interfaces un-assigned!</li>";
            }
            return $data_to_display;
        }
    }
    function remove_interface_bridge(){
        if (isset($_POST['router_id'])) {
            $router_id = $_POST['router_id'];
            if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
                // return $router_id;
                $router_ip_address = session("router_ipaddr");
                $router_username = session("router_username");
                $router_password = session("router_password");
                $router_api_port = session("router_api_port");
                // connect to the router
                $API = new routeros_api();
                $API->debug = false;
                if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                    // remove the port from the bridge
                    $result = $API->comm("/interface/bridge/port/remove", array(
                        ".id" => $router_id
                    ));
                    return "<p class='text-success'>Interface succesfully removed from port!</p>";
                }else {
                    return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
                }
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "We cannot process any information as requested!";
        }
        return "We cannot process any information as requested!";
    }
    function add_bridge(Request $req){
        $bridge_name = $req->input("bridge_name");
        $bridge_ports = $req->input("bridge_ports");
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                // get all the bridges and find the one that has the same name as yours
                $avail_bridge = $API->comm("/interface/bridge/print");
                $available = 0;
                for ($index=0; $index < count($avail_bridge); $index++) { 
                    if ($avail_bridge[$index]['name'] == $bridge_name) {
                        $available = 1;
                        break;
                    }
                }
                if ($available == 0) {
                    // create a bridge then add the ports to the respective bridge
                    $API->comm("/interface/bridge/add", array(
                        "name" => $bridge_name
                    ));
                    // added the bridge now add the interfaces to the bridge
                    $interfaces = explode(",",$bridge_ports);
                    for ($i=0; $i < count($interfaces); $i++) {
                        $int = $interfaces[$i];
                        $API->comm("/interface/bridge/port/add",array(
                            "interface" => "".$int."",
                            "bridge" => $bridge_name
                        ));
                    }
                    // bridge and the interfaces added successfully
                    return "<p class='text-success'>Bridge and interfaces added successfully!</p>";
                }else {
                    return "<p class='text-danger'>Bridge with that interface name is already present!</p>";
                }
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function remove_bridge(Request $req){
        $remove_bridge = $req->input("remove_bridge");
        $bridge_name = $req->input("bridge_name");
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                // get all ports associated with the bridge interface and remove them from the bridge first before removing the bridge
                $ports = $API->comm("/interface/bridge/port/print");
                $interface_id = [];
                for ($index=0; $index < count($ports); $index++) { 
                    if ($ports[$index]['bridge'] == $bridge_name) {
                        array_push($interface_id,$ports[$index]['.id']);
                    }
                }
                
                // remove the interfaces associated to that port if they are present
                for ($index=0; $index < count($interface_id); $index++) { 
                    $id = $interface_id[$index];
                    $API->comm("/interface/bridge/port/remove",array(
                        ".id" => $id
                    ));
                }

                // remove the bridge
                $API->comm("/interface/bridge/remove", array(
                    ".id" => $remove_bridge
                ));
                return "<p class='text-success'>Bridge and interfaces removed successfully!</p>";
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function change_bridge(Request $req){
        // return $req->input();
        $bridge_name = $req->input("bridge_name");
        $interface_name = $req->input("interface_name");
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                // get all ports associated with the bridge interface and remove them from the bridge first before removing the bridge
                $API->comm("/interface/bridge/port/add",array(
                    "interface" => $interface_name,
                    "bridge" => $bridge_name
                ));
                return "<p class='text-success'>$interface_name has been added to $bridge_name successfully!</p>";
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function get_internet_access(Request $req){
        $router_ip_address = $req->input("router_ip_address");
        $router_username = $req->input("router_username");
        $router_password = $req->input("router_password");
        $router_port = $req->input("router_port");

        // return $router_id;
        $router_ip_address = $router_ip_address;
        $router_username = $router_username;
        $router_password = $router_password;
        $router_api_port = $router_port;

        // connect to the router
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
            // check if the dynamic assignment is set
            $dynamic = $API->comm("/ip/dhcp-client/print");
            $message = "";
            $title = "<h6 class='text-center my-1'><u></u></h6>";
            for ($index=0; $index < count($dynamic); $index++) { 
                // break;
                if ($dynamic[$index]['interface'] == "ether1") {
                    $message .= "<li class='text-secondary'>We have noticed that dynamic internet access has been set</li>";
                    $title = "<h6 class='my-1'><u>Dynamic Assignment</u></h6>";
                    break;
                }
            }
            
            // check for static assignment in ip address for ether1
            $curl_handle = curl_init();

            $url = "http://localhost:81/crontab/getIpaddress.php?router_ip_address=".$router_ip_address."&router_username=".$router_username."&router_password=".$router_password."&router_api_port=".$router_api_port."&address_lists=true";

            // Set the curl URL option
            curl_setopt($curl_handle, CURLOPT_URL, $url);

            // This option will return data as a string instead of direct output
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

            // Execute curl & store data in a variable
            $curl_data = curl_exec($curl_handle);

            curl_close($curl_handle);

            // Decode JSON into PHP array
            $static_assignment = json_decode($curl_data);
            // $static_assignment = $API->comm("/ip/address/print");
            // return $static_assignment;
            for ($index=0; $index < count($static_assignment); $index++) { 
                // break;
                if ($static_assignment[$index]->interface == "ether1") {
                    $dynamic = "and its dynamically assigned";
                    if ($static_assignment[$index]->dynamic == "true") {
                        $dynamic = "and its dynamically assigned";
                        $title = "<h6 class='my-1'><u>Dynamic Assignment</u></h6>";
                    }else {
                        $dynamic = "and its static assigned";
                        $title = "<h6 class='my-1'><u>Static Assignment</u></h6>";
                    }
                    $message .= "<li>Interface ether1 has their IP address set ".$dynamic."</li>";
                    break;
                }
            }

            // check if route is set and dns set
            // $routes = $API->comm("/ip/route/print");
            // check for static assignment in ip address for ether1
            $curl_handle = curl_init();

            $url = "http://localhost:81/crontab/getIpaddress.php?router_ip_address=".$router_ip_address."&router_username=".$router_username."&router_password=".$router_password."&router_api_port=".$router_api_port."&routes_list=true";

            // Set the curl URL option
            curl_setopt($curl_handle, CURLOPT_URL, $url);

            // This option will return data as a string instead of direct output
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

            // Execute curl & store data in a variable
            $curl_data = curl_exec($curl_handle);

            curl_close($curl_handle);

            // Decode JSON into PHP array
            $routes = json_decode($curl_data);
            for ($index=0; $index < count($routes); $index++) { 
                // break;
                if ($routes[$index]->gateway == "ether1") {
                    $dynamics = "";
                    if ($routes[$index]->dynamic == "true") {
                        $dynamics = " and its dynamic assigned!";
                    }else {
                        $dynamics = "and its static assigned!";
                    }
                    $message.="<li>Interface ether1 has been set as the gateway ".$dynamics."</li>";
                }
            }

            // check if pppoe is set. if its set then the dynamic and static wont have any power
            $pppoe_assign = $API->comm("/interface/pppoe-client/print");
            if (count($pppoe_assign) > 0) {
                $$title = "<h6 class='my-1'><u>PPPoE Assignment</u></h6>";
                $message = "<li >We have noticed that PPPoE internet access has been set</li>";
            }
            return $title.$message;
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function get_setting(){
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                // check if the dynamic assignment is set
                $dynamic = $API->comm("/ip/dhcp-client/print");
                $message = "";
                $title = "<h6 class='text-center my-1'><u></u></h6>";
                for ($index=0; $index < count($dynamic); $index++) { 
                    // break;
                    if ($dynamic[$index]['interface'] == "ether1") {
                        $message .= "<li class='text-secondary'>We have noticed that dynamic internet access has been set</li>";
                        $title = "<h6 class='text-center my-1'><u>Dynamic Assignment</u></h6>";
                        break;
                    }
                }
                
                // check for static assignment in ip address for ether1
                $static_assignment = $API->comm("/ip/address/print");
                // return $static_assignment;
                for ($index=0; $index < count($static_assignment); $index++) { 
                    // break;
                    if ($static_assignment[$index]['interface'] == "ether1") {
                        $dynamic = "and its dynamically assigned";
                        if ($static_assignment[$index]['dynamic'] == "true") {
                            $dynamic = "and its dynamically assigned";
                            $title = "<h6 class='text-center my-1'><u>Dynamic Assignment</u></h6>";
                        }else {
                            $dynamic = "and its static assigned";
                            $title = "<h6 class='text-center my-1'><u>Static Assignment</u></h6>";
                        }
                        $message .= "<li>Interface ether1 has their IP address set ".$dynamic."</li>";
                        break;
                    }
                }

                // check if route is set and dns set
                $routes = $API->comm("/ip/route/print");
                for ($index=0; $index < count($routes); $index++) { 
                    // break;
                    if ($routes[$index]['gateway'] == "ether1") {
                        $dynamics = "";
                        if ($routes[$index]['dynamic'] == "true") {
                            $dynamics = " and its dynamic assigned!";
                        }else {
                            $dynamics = "and its static assigned!";
                        }
                        $message.="<li>Interface ether1 has been set as the gateway ".$dynamics."</li>";
                    }
                }

                // check if pppoe is set. if its set then the dynamic and static wont have any power
                $pppoe_assign = $API->comm("/interface/pppoe-client/print");
                if (count($pppoe_assign) > 0) {
                    $$title = "<h6 class='text-center my-1'><u>PPPoE Assignment</u></h6>";
                    $message = "<li >We have noticed that PPPoE internet access has been set</li>";
                }
                return $title.$message;
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function set_dynamic(){
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                // add interface ether1 to the dhcp-client but first check if its present
                $dhcp_list = $API->comm("/ip/dhcp-client/print");
                $present = 0;
                for ($index=0; $index < count($dhcp_list); $index++) { 
                    if ($dhcp_list[$index]['interface'] == "ether1") {
                        $present = 1;
                        break;
                    }
                }
                $message = "No changes made because the Dynamic Assignment has already been set!";
                if ($present == 0) {
                    // add the interface to the DHCP client
                    $message = "Dynamic Assignment has been set successfully!!";
                    $API->comm("/ip/dhcp-client/add",array(
                        "disabled" => "no",
                        "interface" => "ether1"
                    ));
                }
                return "<p class='text-success'>".$message."</p>";
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function set_static_access(Request $req){
        // return $req->input();
        $ipaddress = $req->input("ipaddress");
        $gateway = $req->input("gateway");
        $dns = $req->input("dns");
        // connect to the router to set the following parameters
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                $API->comm("/ip/address/add",array(
                    "address" => $ipaddress,
                    "interface" => "ether1"
                ));
                // set the route gateway
                $API->comm("/ip/route/add",array(
                    "gateway" => $gateway
                ));
                // set the dns
                $API->comm("/ip/dns/set",array(
                    "servers" => $dns
                ));
                return "<p class='text-success'>Static assignment has been set successfully!</p>";
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function set_pppoe_assignment(Request $req){
        // return $req->input();
        $username = $req->input("username");
        $password = $req->input("password");
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                // set the pppoe-client username and password
                $API->comm("/interface/pppoe-client/add", array(
                    "disabled" => "no",
                    "interface" => "ether1",
                    "user" => $username,
                    "password" => $password,
                    "add-default-route" => "yes",
                    "use-peer-dns" => "yes"
                ));
                return "<p class='text-success'>PPPoE credentials have been set successfully!</p>";
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function set_pool(Request $req){
        // return $req->input();
        $pool_name = $req->input("pool_name");
        $pool_address = $req->input("pool_address");
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                // we check if there is a pool with that name
                $pools = $API->comm("/ip/pool/print");
                // return $pools;
                for ($index=0; $index < count($pools); $index++) { 
                    if ($pools[$index]['name'] == $pool_name) {
                        return "<p class='text-danger'>Pool (".$pool_name.") is present, use another name!</p>";
                    }
                }
                // continue and add the pool address
                $API->comm("/ip/pool/add", array(
                    "name" => $pool_name,
                    "ranges" => $pool_address
                ));
                return "<p id='added_pool_success' class='text-success'>New pool has been added successfully!</p>";
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function get_pools(){
        // we are getting pool addresses
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                // we check if there is a pool with that name
                $pools = $API->comm("/ip/pool/print");
                $data_to_display = "<select class='form-control' id='pool_name'><option value='' hidden>Select Pool</option>";
                for ($index=0; $index < count($pools); $index++) { 
                    $data_to_display.="<option value='".$pools[$index]['name']."|".$pools[$index]['ranges']."'><b>".($pools[$index]['name'])."</b>  (".$pools[$index]['ranges'].")</option>";
                }
                $data_to_display.="</select>";
                $bridges = $API->comm("/interface/bridge/print");
                $data_to_display.="<label class='form-control-label' for='bridge_pppoe_list'><b>Select bridge</b></label><select class='form-control' id='bridge_pppoe_list'><option hidden>Select bridge</option>";
                for ($index=0; $index < count($bridges); $index++) { 
                    $data_to_display.="<option value='".$bridges[$index]['name']."'>".ucwords(strtolower($bridges[$index]['name']))."</option>";
                }
                $data_to_display.="</select>";
                return $data_to_display;
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function add_pppoe_profile(Request $req){
        // return $req->input();
        $profile_name = $req->input("profile_name");
        $pool_name = $req->input("pool_name");
        $gateway_address = $req->input("gateway_address");
        $upload = $req->input("upload");
        $download = $req->input("download");
        $only_one = $req->input("only_one");
        $bridge = $req->input("bridge");
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                // get the profile
                $curl_handle = curl_init();
                $url = "http://localhost:81/crontab/getIpaddress.php?router_ip_address=".$router_ip_address."&router_username=".$router_username."&router_password=".$router_password."&router_api_port=".$router_api_port."&pppoe_server_list=true";
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
                curl_close($curl_handle);
                // Decode JSON into PHP array
                $profile = json_decode($curl_data);
                
                // return $profile;

                // get if the name is already used
                for ($index=0; $index < count($profile); $index++) { 
                    if ($profile[$index]->name == $profile_name) {
                        return "<p class='text-danger'>The profile name provided is already in use, Provide another name.</p>";
                    }
                }
                $remote_address = trim(explode("|",$pool_name)[0]);
                // create a profile
                $API->comm("/ppp/profile/add",array(
                    "name" => $profile_name,
                    "local-address" => $gateway_address,
                    "remote-address" => $remote_address,
                    "bridge" => $bridge,
                    "wins-server" => "8.8.4.4",
                    "dns-server" => "8.8.8.8",
                    "only-one" => $only_one,
                    "rate-limit" => $upload."/".$download
                ));
                return "<p class='text-success' id='profile_done'>The new profile has been successfully added!</p>";
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function get_pppoe_server(){
        // return "We are here";
        // we are getting pool addresses
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                // get the profile
                $curl_handle = curl_init();
                $url = "http://localhost:81/crontab/getIpaddress.php?router_ip_address=".$router_ip_address."&router_username=".$router_username."&router_password=".$router_password."&router_api_port=".$router_api_port."&pppoe_server_list=true";
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
                curl_close($curl_handle);
                // Decode JSON into PHP array
                $pppoe_profile = json_decode($curl_data);
                // return $pppoe_profile;
                // end of profile

                $data_to_display = "<label class='form-control-label' for='profile_pppoe_server'><b>Select Profile</b></label><select class='form-control' id='profile_pppoe_server'><option value='' hidden>Select Profile</option>";
                for ($index=0; $index < count($pppoe_profile); $index++) { 
                    $data_to_display.="<option value='".$pppoe_profile[$index]->name."'><b>".($pppoe_profile[$index]->name)."</b></option>";
                }
                $data_to_display.="</select>";
                // return $data_to_display;

                // get bridges
                $curl_handle = curl_init();
                $url = "http://localhost:81/crontab/getIpaddress.php?router_ip_address=".$router_ip_address."&router_username=".$router_username."&router_password=".$router_password."&router_api_port=".$router_api_port."&pppoe_server_bridge=true";
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
                curl_close($curl_handle);
                // Decode JSON into PHP array
                $bridges = json_decode($curl_data);
                // return $bridges;
                // end of brdiges
                $data_to_display .= "<label class='form-control-label' for='pppoe_server_bridges'><b>Select bridge</b></label><select class='form-control' id='pppoe_server_bridges'><option value='' hidden>Select bridge</option>";
                for ($index=0; $index < count($bridges); $index++) { 
                    $data_to_display.="<option value='".$bridges[$index]->name."'><b>".($bridges[$index]->name)."</b></option>";
                }
                $data_to_display.="</select>";


                return $data_to_display;
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function save_ppoe_server(Request $req){
        // return $req;
        $server_name = $req->input("server_name");
        $profile_name = $req->input("profile_name");
        $bridge_name = $req->input("bridge_name");
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                // add the server in the router
                $pppoes_server = $API->comm("/interface/pppoe-server/server/print");
                // return $pppoes_server;
                $API->comm("/interface/pppoe-server/server/add",array(
                    "service-name" => $server_name,
                    "interface" => $bridge_name,
                    "authentication" => "pap,chap",
                    "one-session-per-host" => "true",
                    "default-profile" => $profile_name,
                    "disabled" => "false"
                ));
                return "<p class='text-success'>The server has been added successfully to the router</p>";
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function add_security(Request $req){
        // return $req->input();
        $profile_name = $req->input("profile_name");
        $profile_password = $req->input("profile_password");
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                // add security profile
                $security_prof = $API->comm("/interface/wireless/security-profiles/print");
                // return $security_prof;
                for ($index=0; $index < count($security_prof); $index++) { 
                    if ($profile_name == $security_prof[$index]['name']) {
                        return "<p class='text-danger'>The profile name provided is present, Provide another one!</p>";
                    }
                }
                $API->comm("/interface/wireless/security-profiles/add",array(
                    "name" => $profile_name,
                    "authentication-types" => "wpa2-psk,wpa-psk",
                    "wpa2-pre-shared-key" => $profile_password,
                    "wpa-pre-shared-key" => $profile_password,
                    "unicast-ciphers" => "aes-ccm",
                    "group-ciphers" => "aes-ccm"
                ));
                return "<p class='text-success' id='security_prof_added'>The server has been added successfully to the router</p>";
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function get_security_profile(){
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                // add security profile
                $security_prof = $API->comm("/interface/wireless/security-profiles/print");
                $data_to_display = "<select class='form-control' id='security_profile' ><option hidden value =''>Select Security Profile</option>";
                for ($index=0; $index < count($security_prof); $index++) { 
                    $data_to_display.="<option value='".$security_prof[$index]['name']."'>".$security_prof[$index]['name']."</option>";
                }
                $data_to_display.="</select>";
                return $data_to_display;
                // return "<p class='text-success' id='security_prof_added'>The server has been added successfully to the router</p>";
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function save_ssid(Request $req){
        // return $req;
        $ssid = $req->input("ssid");
        $security_profile = $req->input("security_profile");
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                // get if there is an ssid with that name
                $id = "0";
                $wlans = $API->comm("/interface/wireless/print");
                for ($index=0; $index < count($wlans); $index++) { 
                    if ($wlans[$index]['name'] == "wlan1") {
                        $id = $wlans[$index]['.id'];
                        break;
                    }
                }
                $API->comm("/interface/wireless/set",array(
                    "ssid" => $ssid,
                    "band" => "2ghz-b/g/n",
                    "channel-width" => "20/40mhz-Ce",
                    "distance" => "indoors",
                    "mode" => "ap-bridge",
                    "wireless-protocol" => "802.11",
                    "security-profile" => $security_profile,
                    "frequency-mode" => "regulatory-domain",
                    ".id" => $id
                ));
                return "<p class='text-success'>Wi-Fi successfully set!</p>";
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function wireless_settings(Request $req){
        
        $router_ip_address = $req->input("router_ip_address");
        $router_username = $req->input("router_username");
        $router_password = $req->input("router_password");
        $router_port = $req->input("router_port");

        $router_ip_address = $router_ip_address;
        $router_username = $router_username;
        $router_password = $router_password;
        $router_api_port = $router_port;
        // connect to the router
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
            // get type of ways of distributing internet
            // if pppoe check the server
            $wireless = $API->comm("/interface/wireless/print");
            $message = "";
            if (count($wireless) > 0) {
                for ($index=0; $index < count($wireless); $index++) { 
                    if ($wireless[$index]["name"] == "wlan1") {
                        $message.="<br><b>Wi-Fi Name</b>: ".$wireless[$index]['ssid']."<br>";
                        $message.="<b>Security Profile </b>: ".$wireless[$index]['security-profile']."<br>";
                        $message.="<b>Status</b>: ".($wireless[$index]['disabled'] == "true" ? "Disabled":"Enabled")."<br>";
                        $message.="<b>Running</b>: ".$wireless[$index]['running']."<br>";
                    }
                }
                return $message;
            }else{
                return "<p class='text-danger'>We cannot detect any wireless interface!</p>";
            }

        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function get_supply_method(Request $req){
        $router_ip_address = $req->input("router_ip_address");
        $router_username = $req->input("router_username");
        $router_password = $req->input("router_password");
        $router_port = $req->input("router_port");

        $router_ip_address = $router_ip_address;
        $router_username = $router_username;
        $router_password = $router_password;
        $router_api_port = $router_port;

        // connect to the router
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
            // get type of ways of distributing internet
            // if pppoe check the server
            $pppoes_server = $API->comm("/interface/pppoe-server/server/print");
            $message = "";
            // return $pppoes_server;
            if (count($pppoes_server) > 0) {
                $message.="<li>PPPoE server is set to distribute internet from (";
                for ($index=0; $index < count($pppoes_server); $index++) { 
                    $message.=$pppoes_server[$index]['interface'].",";
                }
                $message = substr($message,0,-1).") interfaces</li>";
            }
            $message.="<li>Static assignment is set!</li>";
            $message.="<li>Dynamic distribution of internet is not set by default</li>";
            return $message;
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function get_interface_supply(){
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                // get type of ways of distributing internet
                // if pppoe check the server
                $pppoes_server = $API->comm("/interface/pppoe-server/server/print");
                $message = "";
                // return $pppoes_server;
                if (count($pppoes_server) > 0) {
                    $message.="<li>PPPoE server is set to distribute internet from (";
                    for ($index=0; $index < count($pppoes_server); $index++) { 
                        $message.=$pppoes_server[$index]['interface'].",";
                    }
                    $message = substr($message,0,-1).") interfaces</li>";
                }
                $message.="<li>Static assignment is set!</li>";
                $message.="<li>Dynamic distribution of internet is not set by default</li>";
                return $message;
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function get_wireless(){
        // get the wireless setting
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                // get type of ways of distributing internet
                // if pppoe check the server
                $wireless = $API->comm("/interface/wireless/print");
                $message = "";
                if (count($wireless) > 0) {
                    for ($index=0; $index < count($wireless); $index++) { 
                        if ($wireless[$index]["name"] == "wlan1") {
                            $message.="<b>Wi-Fi Name</b>: ".$wireless[$index]['ssid']."<br>";
                            $message.="<b>Security Profile </b>: ".$wireless[$index]['security-profile']."<br>";
                            $message.="<b>Status</b>: ".($wireless[$index]['disabled'] == "true" ? "Disabled":"Enabled")."<br>";
                            $message.="<b>Running</b>: ".$wireless[$index]['running']."<br>";
                        }
                    }
                    return $message;
                }else{
                    return "<p class='text-danger'>We cannot detect any wireless interface!</p>";
                }

            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }
    function getconnection(){
        if (Session::has("router_ipaddr") || Session::has("router_username") || Session::has("router_password") || Session::has("router_api_port")) {
            // return $router_id;
            $router_ip_address = session("router_ipaddr");
            $router_username = session("router_username");
            $router_password = session("router_password");
            $router_api_port = session("router_api_port");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_ip_address,$router_username,$router_password,$router_api_port)) {
                return "<li class='text-secondary'>Connection to the router has been successfull</li>";
            }else {
                return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
            }
        }else {
            return "<p class='text-danger'>We cannot connect to your router try restarting the process again!</p>";
        }
    }

    function writeRouterLogs($router_id){
        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '".$router_id."'");
        if (count($router_data)>0) {
            // get the router logs and edit the file
            $curl_handle = curl_init();

            $url = "http://localhost:81/crontab/getIpaddress.php?router_logs=true&router_ids=".$router_id;

            // Set the curl URL option
            curl_setopt($curl_handle, CURLOPT_URL, $url);

            // This option will return data as a string instead of direct output
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

            // Execute curl & store data in a variable
            $curl_data = curl_exec($curl_handle);

            curl_close($curl_handle);

            if (isJson($curl_data)) {
                $router_logs = json_decode($curl_data);
                $days = [];
                // get the number of days that have passed since the first log
                for ($index=0; $index < count($router_logs); $index++) { 
                    $time = $router_logs[$index]->time;
                    if (str_contains($time,"/")) {
                        $date = explode(" ",$time);
                        if (!$this->isPresent($days,$date[0])) {
                            array_push($days,$date[0]);
                        }
                    }
                }

                // return $days;
                // get the days in full
                $dates = [];
                $today = date("YmdHis");
                for ($index=0; $index < count($days); $index++) { 
                    $date = $this->addDays($today,(-1 * (count($days) - $index)));
                    array_push($dates,$date);
                }

                // replace the time in the returned array so that its easy for the system interpretation
                for ($index=0; $index < count($router_logs); $index++) {
                    $time = $router_logs[$index]->time;
                    if (str_contains($time,"/")) {
                        $date = explode(" ",$time);
                        $log_date = $this->getFullDate($dates,$days,$date[0]);

                        // get the time and replace the : with nothing
                        $log_time = str_replace(":","",$date[1]);
                        $router_logs[$index]->time = date("Ymd",strtotime($log_date)).$log_time;
                    }else{
                        $log_date = date("Ymd");
                        $log_time = str_replace(":","",$time);
                        $router_logs[$index]->time = $log_date.$log_time;
                    }
                    $topics = explode(",",$router_logs[$index]->topics);
                    $router_logs[$index]->topics = $topics;
                }

                // return $router_logs;

                // get the records
                $router_logs_storage = [];
                for ($index=0; $index < count($router_logs); $index++) { 
                    $r_logs = [];
                    $r_logs['time'] = $router_logs[$index]->time;
                    $r_logs['topics'] = $router_logs[$index]->topics;
                    $r_logs['message'] = $router_logs[$index]->message;
                    // return $r_logs;
                    array_push($router_logs_storage,$r_logs);
                }
                // return $router_logs_storage;
                // check if the file is present.
                if(File::exists(public_path("/logs/".$router_id.".json"))){

                    // read file of the router logs
                    $myfile = fopen(public_path("/logs/$router_id.json"), "r") or die("Unable to open file!");
                    $file_sizes = filesize(public_path("/logs/$router_id.json")) > 0?filesize(public_path("/logs/$router_id.json")):8190;
                    $existing_txt = fread($myfile,$file_sizes);

                    // read and get the latest time
                    $router_log = json_decode($existing_txt);
                    $latest_data = $router_log[count($router_log)-1];

                    // return $router_log[count($router_log)-1];
                    // read the new logs read and add only the new records
                    $start_adding = false;
                    $indexex = 0;
                    for ($index=0; $index < count($router_logs_storage); $index++) { 
                        if ($start_adding) {
                            array_push($router_log,$router_logs_storage[$index]);
                        }
                        if ($router_logs_storage[$index]['time'] == $latest_data->time) {
                            $start_adding = true;
                            $indexex = $index;
                        }
                    }

                    // rewrite the logs in the file
                    $rlogs = json_encode($router_log);
                    $myfile = fopen(public_path("/logs/$router_id.json"), "w") or die("Unable to open file!");
                    fwrite($myfile, $rlogs);
                    fclose($myfile);

                    return "New record written successfully!";
                }else{
                    // create a file and out content
                    $content = json_encode($router_logs_storage);
                    File::put(public_path("/logs/".$router_id.".json"), $content);
                    return "New file created successfully <br>New record written successfully!";
                }
            }else{
                return "Invalid Data!";
            }
        }else{
            // return redirect("/Routers");
            return "INvalid Operation!";
        }
    }
    function isJson($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
    function isPresent($array,$string){
        if (count($array) > 0 ) {
            for ($indexes=0; $indexes <count($array) ; $indexes++) { 
                if ($string == $array[$indexes]) {
                    return true;
                    break;
                }
            }
        }
        return false;
    }
    function addDays($date,$days){
        $date = date_create($date);
        date_add($date,date_interval_create_from_date_string($days." day"));
        return date_format($date,"YmdHis");
    }
    
    function getFullDate($dates,$date,$date_str){
        for ($index=0; $index < count($date); $index++) { 
            if ($date_str == $date[$index]) {
                return $dates[$index];
            }
        }
        return date("YmdHis");
    }

    function readLogs($router_id){
        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '".$router_id."'");
        if (count($router_data) > 0) {
            // return $router_id;
            // read data from the file of the logs of the user
            $router_logs_sorted_by_day = [];
            $topics = [];
            if(File::exists(public_path("/logs/".$router_id.".json"))){
                $myfile = fopen(public_path("/logs/$router_id.json"), "r") or die("Unable to open file!");
                $file_sizes = filesize(public_path("/logs/$router_id.json")) > 0?filesize(public_path("/logs/$router_id.json")):8190;
                $existing_txt = fread($myfile,$file_sizes);
                
                // get the data and record it in days format only from the latest to earliest
                if (isJson($existing_txt)) {
                    $router_logs = json_decode($existing_txt);
                    // echo count($router_logs);
    
                    // get all dates
                    $all_dates = [];
                    for ($index=0; $index < count($router_logs); $index++) {
                        $date = date("Ymd",strtotime($router_logs[$index]->time));
                        if (!$this->isPresent($all_dates,$date)) {
                            array_push($all_dates,$date);
                        }
                    }
                    // return $all_dates;
    
                    // loop through the days to get the data for each day
                    $daily_router_logs = [];
                    for ($index=0; $index < count($all_dates); $index++) {
                        $daily_logs = [];
                        $modify_logs = $router_logs;
                        for ($index_1=(count($router_logs)-1); $index_1 >=0; $index_1--) {
                            $date = date("Ymd",strtotime($router_logs[$index_1]->time));
                            if ($date == $all_dates[$index]) {
                                $r_logs = [];
                                $r_logs['time'] = date("H:i:s",strtotime($modify_logs[$index_1]->time));
                                $r_logs['topics'] = $modify_logs[$index_1]->topics;
                                $r_logs['message'] = $modify_logs[$index_1]->message;
                                // $r_logs['time_long'] = $modify_logs[$index_1]->time;
                                $top = $r_logs['topics'];
                                for ($ind=0; $ind < count($top); $ind++) {
                                    if (!$this->isPresent($topics,$top[$ind])) {
                                        array_push($topics,$top[$ind]);
                                    }
                                }
                                // echo $date." == ".$all_dates[$index]."<br>";
                                // $modify_logs[$index_1]->time = date("H:i:s",strtotime($router_logs[$index_1]->time));
                                array_push($daily_logs,$r_logs);
                            }
                        }
                        // echo "<hr>";
                        $daily_array = array("date" => date("D dS M Y",strtotime($all_dates[$index])),"daily_data" => $daily_logs,"date_long" => $all_dates[$index]);
                        array_push($daily_router_logs,$daily_array);
                        // break;
                    }
    
                    $router_logs_sorted_by_day = $daily_router_logs;
                }
            }
            // return $router_logs_sorted_by_day;
            return view("router-logs",["router_logs_sorted_by_day" => $router_logs_sorted_by_day,"router_data" => $router_data,"topics" => $topics]);
        }else{
            session()->flash("error_router","Invalid router select another router to view its logs!");
            return redirect("/Routers");
        }
    }
}
