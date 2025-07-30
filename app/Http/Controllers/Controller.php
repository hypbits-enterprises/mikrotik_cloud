<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function getIpaddress($router_id){
        // get the IP ADDRES
        $curl_handle = curl_init();
        $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=" . session("database_name") . "&r_id=" . $router_id . "&r_ip=true";
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        $curl_data = curl_exec($curl_handle);
        curl_close($curl_handle);
        $router_ip_addresses = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];
        // save the router ip address
        return $router_ip_addresses;
    }

    function getQueues($router_id){
        // get the SIMPLE QUEUES
        $curl_handle = curl_init();
        $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=" . session("database_name") . "&r_id=" . $router_id . "&r_queues=true";
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        $curl_data = curl_exec($curl_handle);
        curl_close($curl_handle);
        $router_simple_queues = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];
        return $router_simple_queues;
    }

    function getPPPSecrets($router_id){
        // get the IP ADDRES
        $curl_handle = curl_init();
        $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=" . session("database_name") . "&r_id=" . $router_id . "&r_ppoe_secrets=true";
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        $curl_data = curl_exec($curl_handle);
        curl_close($curl_handle);
        $ppp_secrets = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];
        return $ppp_secrets;
    }
}
