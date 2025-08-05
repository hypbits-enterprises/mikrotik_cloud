<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    function getPPPSecrets($router_id){
        // get the IP ADDRES
        $curl_handle = curl_init();
        $url = env('CRONJOB_URL', 'https://crontab.hypbits.com')."/getIpaddress.php?db_name=" . session("database_name") . "&r_id=" . $router_id . "&r_ppoe_secrets=true";
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        $curl_data = curl_exec($curl_handle);
        curl_close($curl_handle);
        $ppp_secrets = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];
        return $ppp_secrets;
    }

    function getRouterIPAddress($router_id, $database = null){
        $database = $database ?? session("database_name");
        $router_ip_addresses = [];
        if ($database != null) {
            $curl_handle = curl_init();
            $url = env('CRONJOB_URL', 'https://crontab.hypbits.com')."/getIpaddress.php?db_name=" . $database . "&r_id=" . $router_id . "&r_ip=true";
            curl_setopt($curl_handle, CURLOPT_URL, $url);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            $curl_data = curl_exec($curl_handle);
            curl_close($curl_handle);
            $router_ip_addresses = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];
        }
        return $router_ip_addresses;
    }

    function getRouterQueues($router_id, $database = null){
        $database = $database ?? session("database_name");
        $router_simple_queues = [];
        if($database != null){
            $curl_handle = curl_init();
            $url = env('CRONJOB_URL', 'https://crontab.hypbits.com')."/getIpaddress.php?db_name=" . $database . "&r_id=" . $router_id . "&r_queues=true";
            curl_setopt($curl_handle, CURLOPT_URL, $url);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            $curl_data = curl_exec($curl_handle);
            curl_close($curl_handle);
            $router_simple_queues = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];
        }
        return $router_simple_queues;
    }

    function getRouterActiveSecrets($router_id, $database = null){
        $database = $database ?? session("database_name");
        $active_connections = [];
        if ($database != null) {
            // get the ACTIVE PPPOE CONNECTION
            $curl_handle = curl_init();
            $url = env('CRONJOB_URL', 'https://crontab.hypbits.com')."/getIpaddress.php?db_name=" . $database . "&r_id=" . $router_id . "&r_active_secrets=true";
            curl_setopt($curl_handle, CURLOPT_URL, $url);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            $curl_data = curl_exec($curl_handle);
            curl_close($curl_handle);
            $active_connections = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];
        }
        return $active_connections;
    }

    function getRouterSecrets($router_id, $database = null){
        $database = $database ?? session("database_name");
        $router_secrets = [];
        if ($database != null){
            // get the ACTIVE PPPOE CONNECTION
            $curl_handle = curl_init();
            $url = env('CRONJOB_URL', 'https://crontab.hypbits.com')."/getIpaddress.php?db_name=" . $database . "&r_id=" . $router_id . "&r_ppoe_secrets=true";
            curl_setopt($curl_handle, CURLOPT_URL, $url);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            $curl_data = curl_exec($curl_handle);
            curl_close($curl_handle);
            $router_secrets = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];
        }
        return $router_secrets;
    }
    function sortArrayByKey (array $data, string $key, string $order = 'asc'): array {
        usort($data, function ($a, $b) use ($key, $order) {
            $valA = is_object($a) ? ($a->$key ?? null) : ($a[$key] ?? null);
            $valB = is_object($b) ? ($b->$key ?? null) : ($b[$key] ?? null);

            if ($valA === $valB) return 0;

            return $order === 'asc' ? $valA <=> $valB : $valB <=> $valA;
        });

        return $data;
    }
}
