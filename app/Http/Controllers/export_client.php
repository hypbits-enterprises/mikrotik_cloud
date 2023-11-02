<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Classes\routeros_api;

class export_client extends Controller
{
    //
    function exportClients(){
        // echo "This might take a while";
        // get all the routers you do have
        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0'");
        // get the users from the database and link them to their respective router
        // for each router loop through the router and get its clients and know how many are connected to the router
        $my_router_data = [];
        foreach ($router_data as $value) {
            $router_id = $value->router_id;
            $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = '$router_id'");
            $data_in = 0;
            // get the users that their details are present
            // connect to the router and get user details

            // Initiate curl session in a variable (resource)
            $curl_handle = curl_init();

            $url = "http://localhost:81/crontab/getIpaddress.php?r_ip=true&r_id=".$router_id;

            // Set the curl URL option
            curl_setopt($curl_handle, CURLOPT_URL, $url);

            // This option will return data as a string instead of direct output
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

            // Execute curl & store data in a variable
            $curl_data = curl_exec($curl_handle);

            curl_close($curl_handle);

            // Decode JSON into PHP array
            $router_ip_addresses = json_decode($curl_data);
            // return $router_ip_addresses;
            // get the queue
            // Initiate curl session in a variable (resource)
            $curl_handle = curl_init();

            $url = "http://localhost:81/crontab/getIpaddress.php?r_queues=true&r_id=".$router_id;

            // Set the curl URL option
            curl_setopt($curl_handle, CURLOPT_URL, $url);

            // This option will return data as a string instead of direct output
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

            // Execute curl & store data in a variable
            $curl_data = curl_exec($curl_handle);

            curl_close($curl_handle);
            $router_simple_queues = json_decode($curl_data);

            $curl_handle = curl_init();
            $url = "http://localhost:81/crontab/getIpaddress.php?r_ppoe_secrets=true&r_id=".$router_id;
            // Set the curl URL option
            curl_setopt($curl_handle, CURLOPT_URL, $url);
            // This option will return data as a string instead of direct output
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            // Execute curl & store data in a variable
            $curl_data = curl_exec($curl_handle);
            curl_close($curl_handle);
            // Decode JSON into PHP array
            $router_secrets = json_decode($curl_data);
            // return $router_secrets;
            // Decode JSON into PHP array
            $connected = 0;
            if (isset($router_ip_addresses) && isset($router_simple_queues) && isset($router_secrets)) {
                $connected = 1;
            }

            foreach ($client_data as $value_client) {
                // get the clients network address and default gateway
                // if the user ip address plus their gateway is present in the address list they are present
                $network_address = $value_client->client_network;
                $gateway_address = $value_client->client_default_gw;
                $speeds = $value_client->max_upload_download;
                if ($value_client->assignment == "static") {
                    $present = 0;
                    if (isset($router_ip_addresses)) {
                        foreach ($router_ip_addresses as $value_address) {
                            $net_addr = $value_address->network;
                            $gateway_addr = $value_address->address;
                            if ($network_address == $net_addr && $gateway_addr == $gateway_address) {
                                // $data_in++;
                                $present = 1;
                                break;
                            }
                        }
                    }
                    $subnet = explode("/",$gateway_address)[1];
                    $target = $network_address."/".$subnet;
                    $speed1 = substr(explode("/",$speeds)[0],-1);
                    if ($speed1 == "M") {
                        $speed1 = substr(explode("/",$speeds)[0],0,(strlen(explode("/",$speeds)[0])-1))*1000000;
                    }elseif ($speed1 == "K") {
                        $speed1 = substr(explode("/",$speeds)[0],0,(strlen(explode("/",$speeds)[0])-1))*1000;
                    }
                    $speed2 = substr(explode("/",$speeds)[1],-1);
                    if ($speed2 == "M") {
                        $speed2 = substr(explode("/",$speeds)[1],0,(strlen(explode("/",$speeds)[1])-1))*1000000;
                    }elseif ($speed2 == "K") {
                        $speed2 = substr(explode("/",$speeds)[1],0,(strlen(explode("/",$speeds)[1])-1))*1000;
                    }
                    // return $router_simple_queues;
                    if ($present == 1) {
                        // check for the queue
                        if (isset($router_simple_queues)) {
                            foreach ($router_simple_queues as $values) {
                                $targ = $values->target;
                                if ($targ == $target) {
                                    foreach ($values as $keys => $valued) {
                                        if ($keys == "max-limit") {
                                            if ($valued == $speed1."/".$speed2) {
                                                $data_in++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }elseif ($value_client->assignment == "pppoe") {
                    // check their secret
                    $client_secret = $value_client->client_secret;
                    $profile = $value_client->client_profile;
                    // return $router_secrets;
                    for ($index=0; $index < count($router_secrets); $index++) { 
                        $data = $router_secrets[$index];
                        if ($data->name == $client_secret && $data->profile == $profile) {
                            $data_in++;
                        }
                    }
                }
            }
            $main_data = array("RouterId" => $router_id,"RouterName" => $value->router_name,"ClientCount" => count($client_data),"dataMatch" => $data_in,"router_ipaddr" => $value->router_ipaddr,"Active"=>$connected,"router_id" => $router_id);
            array_push($my_router_data,$main_data);
        }
        // return $my_router_data;
        return view("export",["export_information"=>$my_router_data]);
    }
    function router_client_information($router_id){
        // get router information
        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '$router_id'");
        // get the ip address
            // Initiate curl session in a variable (resource)
            $curl_handle = curl_init();
            $url = "http://localhost:81/crontab/getIpaddress.php?r_ip=true&r_id=".$router_id;
            // Set the curl URL option
            curl_setopt($curl_handle, CURLOPT_URL, $url);
            // This option will return data as a string instead of direct output
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            // Execute curl & store data in a variable
            $curl_data = curl_exec($curl_handle);
            curl_close($curl_handle);
            // Decode JSON into PHP array
            $router_ip_addresses = json_decode($curl_data);
            // return $router_ip_addresses;

            // get the queue
            // Initiate curl session in a variable (resource)
            $curl_handle = curl_init();

            $url = "http://localhost:81/crontab/getIpaddress.php?r_queues=true&r_id=".$router_id;

            // Set the curl URL option
            curl_setopt($curl_handle, CURLOPT_URL, $url);

            // This option will return data as a string instead of direct output
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

            // Execute curl & store data in a variable
            $curl_data = curl_exec($curl_handle);

            curl_close($curl_handle);

            // Decode JSON into PHP array
            $router_simple_queues = json_decode($curl_data);
            // return $router_simple_queues;

            // get the secrets
            // Initiate curl session in a variable (resource)
            $curl_handle = curl_init();

            $url = "http://localhost:81/crontab/getIpaddress.php?r_ppoe_secrets=true&r_id=".$router_id;

            // Set the curl URL option
            curl_setopt($curl_handle, CURLOPT_URL, $url);

            // This option will return data as a string instead of direct output
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

            // Execute curl & store data in a variable
            $curl_data = curl_exec($curl_handle);

            curl_close($curl_handle);

            // Decode JSON into PHP array
            $router_secrets = json_decode($curl_data);
            
            // get the clients information in the router and see if they are 
            $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = '$router_id'");
            // loop through each client and display their information
            $client_information_pppoe = [];
            $client_information_static = [];
            foreach ($client_data as $value) {
                if ($value->assignment == "static") {
                    $client_net_add = $value->client_network;
                    $client_gw_add = $value->client_default_gw;
                    $max_upload = $value->max_upload_download;
                    $client_name = $value->client_name;
                    $client_network = $value->client_network;
                    $client_default_gw = $value->client_default_gw;
                    $max_upload_download = $value->max_upload_download;
                    $client_id = $value->client_id;
                    $client_status = $value->client_status;
                    $client_interface =$value->client_interface;
    
    
                    $upload = explode("/",$max_upload)[0];
                    $download = explode("/",$max_upload)[1];
                    $upload_unit = substr($upload,-1);
                    $download_unit = substr($download,-1);
                    $final_upload = substr($upload,0,-1) * 1000000;
                    $final_download = substr($download,0,-1) * 1000000;
                    // check if its KBPS
                    if ($download_unit == "K") {
                        $final_download = substr($download,0,-1) * 1000;
                    }
                    if ($upload_unit == "K") {
                        $final_upload = substr($upload,0,-1)*1000;
                    }
                    $internet_speeds = $final_upload."/".$final_download;
                    // return $internet_speeds;
                    $queue_target = $client_net_add."/". explode("/",$client_gw_add)[1];
                    // return $queue_target;
    
                    // $internet_status = [];
                    $gw_match = 0;
                    $net_address_match = 0;
                    $network_interface_match = 0;
                    if (isset($router_ip_addresses)) {
                        // check if the clients ip address is present
                        foreach ($router_ip_addresses as $value) {
                            if ($value->address == $client_gw_add) {
                                $gw_match = 1;
                            }
                            if ($value->network == $client_net_add) {
                                $net_address_match = 1;
                            }
                            if ($value->interface == $client_interface) {
                                $network_interface_match = 1;
                            }
                        }
                    }
                    
                    $target_addr = 0;
                    $max_limit = 0;
                    // return $router_simple_queues;
                    if (isset($router_simple_queues)) {
                        foreach ($router_simple_queues as $values) {
                            $targ = $values->target;
                            if ($targ == $queue_target) {
                                $target_addr = 1;
                                foreach ($values as $keys => $valued) {
                                    if ($keys == "max-limit") {
                                        if ($valued == $internet_speeds) {
                                            $max_limit = 1;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $internet_status = array("gateway_match" => $gw_match, "net_address_match" => $net_address_match, "target_address"=> $target_addr, "speed_status" => $max_limit,"queue_target" => $queue_target,"interface_match" => $network_interface_match);
                    $client_data = array("client_status" => $client_status,"client_name" => $client_name,"client_network" => $client_network,"client_default_gw" => $client_default_gw,"max_upload_download" => $max_upload_download,"client_id" => $client_id,"client_interface" => $client_interface);
                    $final_data = array_merge($internet_status,$client_data);
                    // return $final_data;
                    array_push($client_information_static,$final_data);
                }elseif ($value->assignment == "pppoe") {
                    $client_secret = $value->client_secret;
                    $client_secret_password = $value->client_secret_password;
                    $client_profile = $value->client_profile;
                    $client_name = $value->client_name;
                    $client_id = $value->client_id;
                    $client_status = $value->client_status;
                    // check if the profile and the secret are present and correct
                    $client_secret_status = 0;
                    $client_profile_status = 0;
                    for ($index=0; $index < count($router_secrets); $index++) { 
                        $data = $router_secrets[$index];
                        if ($client_secret == $data->name) {
                            $client_secret_status = 1;
                            if ($client_profile == $data->profile) {
                                $client_profile_status = 1;
                            }
                        }
                    }
                    // return $client_profile;
                    $internet_status = array("secret_match" => $client_secret_status, "profile_status" => $client_profile_status);
                    $client_data = array("client_status" => $client_status,"client_name" => ucwords(strtolower($client_name)),"client_password" => $client_secret_password,"client_profile" => $client_profile,"client_id" => $client_id,"client_secret" => $client_secret);
                    $final_data = array_merge($internet_status,$client_data);
                    // return $final_data;
                    array_push($client_information_pppoe,$final_data);
                }
            }
            // return $client_information_pppoe;
            // return the view page first
            return view("routerExport",["RouterName" => $router_data[0]->router_name,"client_information_static" => $client_information_static,"client_information_pppoe" => $client_information_pppoe,"router_id" => $router_id]);
    }
    function sync_client_router($client_id){
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = '$client_id'");
        $client_name = $client_data[0]->client_name;
        $client_account = $client_data[0]->client_account;
        $client_address = $client_data[0]->client_address;
        $client_network = $client_data[0]->client_network;
        $client_gw_name = $client_data[0]->client_default_gw;
        $max_upload_download = $client_data[0]->max_upload_download;
        $router_name = $client_data[0]->router_name;
        $interface_name = $client_data[0]->client_interface;
        $location_coordinates = $client_data[0]->location_coordinates;
        $client_status = $client_data[0]->client_status;
        $client_status = ($client_status == "1")? "no":"yes";
        $assignment =$client_data[0]->assignment;
        $client_profile =$client_data[0]->client_profile;
        $client_secret =$client_data[0]->client_secret;
        $client_secret_password =$client_data[0]->client_secret_password;
        if ($assignment == "static") {
            // get the ip address and queue list above
            // get ip
            // Initiate curl session in a variable (resource)
            $curl_handle = curl_init();
    
            $url = "http://localhost:81/crontab/getIpaddress.php?r_ip=true&r_id=".$router_name;
    
            // Set the curl URL option
            curl_setopt($curl_handle, CURLOPT_URL, $url);
    
            // This option will return data as a string instead of direct output
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    
            // Execute curl & store data in a variable
            $curl_data = curl_exec($curl_handle);
    
            curl_close($curl_handle);
    
            // Decode JSON into PHP array
            $router_ip_addresses = json_decode($curl_data);
    
            // get the queue
            // Initiate curl session in a variable (resource)
            $curl_handle = curl_init();
    
            $url = "http://localhost:81/crontab/getIpaddress.php?r_queues=true&r_id=".$router_name;
    
            // Set the curl URL option
            curl_setopt($curl_handle, CURLOPT_URL, $url);
    
            // This option will return data as a string instead of direct output
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    
            // Execute curl & store data in a variable
            $curl_data = curl_exec($curl_handle);
    
            curl_close($curl_handle);
    
            // Decode JSON into PHP array
            $router_simple_queues = json_decode($curl_data);
            
            // lets get the router connection information
            $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '".$router_name."'");
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($router_data[0]->router_ipaddr,$router_data[0]->router_api_username,$router_data[0]->router_api_password,$router_data[0]->router_api_port)) {
                $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = '$client_id';");
                // return $client_data;
                $old_network = $client_network;
                $old_client_gw = $client_gw_name;
                // check if the ip is present if its not present add it if not change the data
                // return $old_client_gw;
                $ip_address = $router_ip_addresses;
                // return $ip_address;
                $present = 0;
                $myids = "";
                foreach ($ip_address as $key => $value) {
                    foreach ($value as $key1 => $value1) {
                        if ($key1 == ".id") {
                            $myids = $value1;
                        }
                        if ($key1 == "network") {
                            if ($value1 == $old_network) {
                                $present = 1;
                                break;
                            }
                        }
                    }
                    if ($present == 1) {
                        break;
                    }
                }
                // return $myids;
                // if the ip address is present change its details
                if ($present == 1) {
                    // set the ip address using its id
                    $result = $API->comm("/ip/address/set",
                    array(
                        "address"     => $client_gw_name,
                        "interface" => $interface_name,
                        "disabled" => $client_status,
                        "comment"  => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account,
                        ".id" => $myids
                    ));
                    if(count($result) > 0){
                        // this means there is an error
                        $API->comm("/ip/address/set",
                        array(
                            "interface" => $interface_name,
                            "disabled" => $client_status,
                            "comment"  => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account,
                            ".id" => $myids
                        ));
                    }
                }else {
                    // add a new ip address
                    $API->comm("/ip/address/add", 
                    array(
                        "address"     => $client_gw_name,
                        "interface" => $interface_name,
                        "network" => $client_network,
                        "disabled" => $client_status,
                        "comment"  => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account
                    ));
                }
    
                // check if there is a queue if its not present add if its present set it
                $queueList = $router_simple_queues;
                $present = 0;
                $queue_id = "";
                if (count($queueList) > 0) {
                    foreach ($queueList as $key => $value) {
                        foreach ($value as $key1 => $value1) {
                            if($key1 == ".id"){
                                $queue_id = $value1;
                            }
                            if($value1 == $old_network."/".explode("/",$old_client_gw)[1]){
                                $present = 1;
                                break;
                            }
                        }
                        if ($present == 1) {
                            break;
                        }
                    }
                }
    
                // $upload = $upload_speed.$unit1;
                // $download = $download_speed.$unit2;
    
                // return $queueList;
                if ($present == 1) {
                    // set the queue using the ip address
                    $API->comm("/queue/simple/set",
                        array(
                            "name" => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account,
                            "target" => $client_network."/".explode("/",$client_gw_name)[1],
                            "max-limit" => $max_upload_download,
                            ".id" => $queue_id
    
                        )
                    );
                }else {
                    // add the queue to the list
                    $API->comm("/queue/simple/add",
                        array(
                            "name" => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account,
                            "target" => $client_network."/".explode("/",$client_gw_name)[1],
                            "max-limit" => $max_upload_download
                        )
                    );
                }
    
                // $upload = $upload_speed.$unit1;
                // $download = $download_speed.$unit2;
    
                // // update the table
                // DB::table('client_tables')
                //         ->where('client_id', $client_id)
                //         ->update([
                //             'client_name' => $client_name,
                //             'client_network' => $client_network,
                //             'client_default_gw' => $client_gw_name,
                //             'max_upload_download' => $max_upload_download,
                //             'monthly_payment' => $client_monthly_pay,
                //             'router_name' => $router_name,
                //             'client_interface' => $interface_name,
                //             'comment' => $req->input('comments'),
                //             'clients_contacts' => $client_phone,
                //             'client_username' => $req->input('client_username'),
                //             'client_password' => $client_password,
                //             'location_coordinates' => $location_coordinates
                //         ]);
    
                // // log file capture error
                // $myfile = fopen(public_path("/logs/log.txt"), "a") or die("Unable to open file!");
                // $date = date("dS M Y (H:i:sa)");
                // $txt = $date.":Client (".$client_name.") information modified by ".session('Usernames')." on the router\n";
                // fwrite($myfile, $txt);
                // fclose($myfile);
                // // end of log file
                        
                // redirect to the client table
                session()->flash("success","Syncing has been done successfully!");
                return redirect("/Clients/Export/View/".$router_name);
                $API->disconnect();
            }else {
                session()->flash("error","Cannot connect to the router check if its active or its ip address has been changed");
                return redirect("/Clients/Export/View/".$router_name);
            }
        }elseif ($assignment == "pppoe") {
            // check if the secret is present and update changes if not add its
            // get the queue
            // Initiate curl session in a variable (resource)
            $curl_handle = curl_init();
    
            $url = "http://localhost:81/crontab/getIpaddress.php?r_ppoe_secrets=true&r_id=".$router_name;
    
            // Set the curl URL option
            curl_setopt($curl_handle, CURLOPT_URL, $url);
    
            // This option will return data as a string instead of direct output
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    
            // Execute curl & store data in a variable
            $curl_data = curl_exec($curl_handle);
    
            curl_close($curl_handle);
    
            // Decode JSON into PHP array
            $router_secrets = json_decode($curl_data);
            // return $router_secrets;
            $present = 0;
            for ($index=0; $index < count($router_secrets); $index++) {
                $secret_data = $router_secrets[$index];
                if ($secret_data->name == $client_secret) {
                    // get the id
                    $present = 1;
                    $id = "";
                    foreach ($secret_data as $key => $value) {
                        if ($key == ".id") {
                            $id = $value;
                            // return $id;
                            // connect to the router and set the necessary fields
                            $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '".$router_name."'");
                            // connect to the router
                            $API = new routeros_api();
                            $API->debug = false;
                            if ($API->connect($router_data[0]->router_ipaddr,$router_data[0]->router_api_username,$router_data[0]->router_api_password,$router_data[0]->router_api_port)) {
                                $API->comm("/ppp/secret/set",array(
                                    "name" => $client_secret,
                                    "password" => $client_secret_password,
                                    "profile" => $client_profile,
                                    "service" => "pppoe",
                                    "comment" => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account,
                                    ".id" => $id
                                ));
                                session()->flash("success","Syncing has been done successfully!");
                                return redirect("/Clients/Export/View/".$router_name);
                            }else {
                                session()->flash("error","Cannot connect to the router check if its active or its ip address has been changed");
                                return redirect("/Clients/Export/View/".$router_name);
                            }
                            break;

                        }
                    }
                }
            }
            if ($present == 0) {
                // add the interface beacause its not present
                // connect to the router and set the necessary fields
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '".$router_name."'");
                // connect to the router
                $API = new routeros_api();
                $API->debug = false;
                if ($API->connect($router_data[0]->router_ipaddr,$router_data[0]->router_api_username,$router_data[0]->router_api_password,$router_data[0]->router_api_port)) {
                    $API->comm("/ppp/secret/add",array([
                        "name" => $client_secret,
                        "password" => $client_secret_password,
                        "profile" => $client_profile,
                        "service" => "pppoe",
                        "comment" => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account
                    ]));
                    session()->flash("success","Syncing has been done successfully!");
                    return redirect("/Clients/Export/View/".$router_name);
                }else {
                    session()->flash("error","Cannot connect to the router check if its active or its ip address has been changed");
                    return redirect("/Clients/Export/View/".$router_name);
                }
            }
        }
    }
    function exportall($router_id){
        // return $router_id;
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = '$router_id'");
        // return $client_data;
        // loop through the clients data and sync their informatiom
        // get the ip address and queue list above
        // get ip
        // Initiate curl session in a variable (resource)
        $curl_handle = curl_init();

        $url = "http://localhost:81/crontab/getIpaddress.php?r_ip=true&r_id=".$router_id;

        // Set the curl URL option
        curl_setopt($curl_handle, CURLOPT_URL, $url);

        // This option will return data as a string instead of direct output
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

        // Execute curl & store data in a variable
        $curl_data = curl_exec($curl_handle);

        curl_close($curl_handle);

        // Decode JSON into PHP array
        $router_ip_addresses = json_decode($curl_data);

        // get the queue
        // Initiate curl session in a variable (resource)
        $curl_handle = curl_init();

        $url = "http://localhost:81/crontab/getIpaddress.php?r_queues=true&r_id=".$router_id;

        // Set the curl URL option
        curl_setopt($curl_handle, CURLOPT_URL, $url);

        // This option will return data as a string instead of direct output
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

        // Execute curl & store data in a variable
        $curl_data = curl_exec($curl_handle);

        curl_close($curl_handle);

        // Decode JSON into PHP array
        $router_simple_queues = json_decode($curl_data);

        // get the queue
        // Initiate curl session in a variable (resource)
        $curl_handle = curl_init();

        $url = "http://localhost:81/crontab/getIpaddress.php?r_ppoe_secrets=true&r_id=".$router_id;

        // Set the curl URL option
        curl_setopt($curl_handle, CURLOPT_URL, $url);

        // This option will return data as a string instead of direct output
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

        // Execute curl & store data in a variable
        $curl_data = curl_exec($curl_handle);

        curl_close($curl_handle);

        // Decode JSON into PHP array
        $router_secrets = json_decode($curl_data);
        
        // lets get the router connection information
        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '".$router_id."'");
        // connect to the router
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($router_data[0]->router_ipaddr,$router_data[0]->router_api_username,$router_data[0]->router_api_password,$router_data[0]->router_api_port)) {
            // $client_data = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id'");
            // loop through the clients
            for ($indexes=0; $indexes < count($client_data); $indexes++) { 
                $client_name = $client_data[$indexes]->client_name;
                $client_account = $client_data[$indexes]->client_account;
                $client_address = $client_data[$indexes]->client_address;
                $client_network = $client_data[$indexes]->client_network;
                $client_gw_name = $client_data[$indexes]->client_default_gw;
                $max_upload_download = $client_data[$indexes]->max_upload_download;
                $router_name = $client_data[$indexes]->router_name;
                $interface_name = $client_data[$indexes]->client_interface;
                $location_coordinates = $client_data[$indexes]->location_coordinates;
                $client_status = $client_data[$indexes]->client_status;
                $assignment = $client_data[$indexes]->assignment;
                $client_profile = $client_data[$indexes]->client_profile;
                $client_secret = $client_data[$indexes]->client_secret;
                $client_secret_password = $client_data[$indexes]->client_secret_password;
                // return $assignment;
                if ($assignment == "static") {
                    $old_network = $client_network;
                    $old_client_gw = $client_gw_name;
                    // check if the ip is present if its not present add it if not change the data
                    // return $old_client_gw;
                    $ip_address = $router_ip_addresses;
                    // return $ip_address;
                    $present = 0;
                    $myids = "";
                    foreach ($ip_address as $key => $value) {
                        foreach ($value as $key1 => $value1) {
                            if ($key1 == ".id") {
                                $myids = $value1;
                            }
                            if ($key1 == "network") {
                                if ($value1 == $old_network) {
                                    $present = 1;
                                    break;
                                }
                            }
                        }
                        if ($present == 1) {
                            break;
                        }
                    }
                    // return $myids;
                    // if the ip address is present change its details
                    $client_status = ($client_status == "1")?"no":"yes";
                    if ($present == 1) {
                        // set the ip address using its id
                        $result = $API->comm("/ip/address/set",
                        array(
                            "address"     => $client_gw_name,
                            "interface" => $interface_name,
                            "disabled" => $client_status,
                            "comment"  => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account,
                            ".id" => $myids
                        ));
                        if(count($result) > 0){
                            // this means there is an error
                            $API->comm("/ip/address/set",
                            array(
                                "interface" => $interface_name,
                                "disabled" => $client_status,
                                "comment"  => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account,
                                ".id" => $myids
                            ));
                        }
                    }else {
                        // add a new ip address
                        $API->comm("/ip/address/add", 
                        array(
                            "address"     => $client_gw_name,
                            "interface" => $interface_name,
                            "network" => $client_network,
                            "disabled" => $client_status,
                            "comment"  => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account
                        ));
                    }
        
                    // check if there is a queue if its not present add if its present set it
                    $queueList = $router_simple_queues;
                    $present = 0;
                    $queue_id = "";
                    foreach ($queueList as $key => $value) {
                        foreach ($value as $key1 => $value1) {
                            if($key1 == ".id"){
                                $queue_id = $value1;
                            }
                            if($value1 == $old_network."/".explode("/",$old_client_gw)[1]){
                                $present = 1;
                                break;
                            }
                        }
                        if ($present == 1) {
                            break;
                        }
                    }
        
                    // $upload = $upload_speed.$unit1;
                    // $download = $download_speed.$unit2;
        
                    // return $queueList;
                    if ($present == 1) {
                        // set the queue using the ip address
                        $API->comm("/queue/simple/set",
                            array(
                                "name" => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account,
                                "target" => $client_network."/".explode("/",$client_gw_name)[1],
                                "max-limit" => $max_upload_download,
                                ".id" => $queue_id
        
                            )
                        );
                    }else {
                        // add the queue to the list
                        $API->comm("/queue/simple/add",
                            array(
                                "name" => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account,
                                "target" => $client_network."/".explode("/",$client_gw_name)[1],
                                "max-limit" => $max_upload_download
                            )
                        );
                    }
                    // break;
                }elseif ($assignment == "pppoe") {
                    $present = 0;
                    // return $router_secrets;
                    for ($index=0; $index < count($router_secrets); $index++) {
                        $secret_data = $router_secrets[$index];
                        if ($secret_data->name == $client_secret) {
                            // get the id
                            $present = 1;
                            $id = "";
                            foreach ($secret_data as $key => $value) {
                                if ($key == ".id") {
                                    $id = $value;
                                    $API->comm("/ppp/secret/set",array(
                                        "name" => $client_secret,
                                        "password" => $client_secret_password,
                                        "profile" => $client_profile,
                                        "service" => "pppoe",
                                        "comment" => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account,
                                        ".id" => $id
                                    ));
                                    session()->flash("success","Syncing has been done successfully!");
                                    return redirect("/Clients/Export/View/".$router_name);
                                    break;
                                }
                            }
                        }
                    }
                    if ($present == 0) {
                        // add the interface beacause its not present
                        $API->comm("/ppp/secret/add",array([
                            "name" => $client_secret,
                            "password" => $client_secret_password,
                            "profile" => $client_profile,
                            "service" => "pppoe",
                            "comment" => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account
                        ]));
                    }
                }
            }
            session()->flash("success","Syncing has been done successfully!");
            return redirect("/Clients/Export/View/".$router_id);
            $API->disconnect();
        }else {
            session()->flash("error","Cannot connect to the router check if its active or its ip address has been changed");
            return redirect("/Clients/Export/View/".$router_id);
        }
    }
}
