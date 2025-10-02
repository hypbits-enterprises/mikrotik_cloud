<?php

namespace App\Http\Controllers;

use DateTime;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

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
    
    // convert bits
    function convertBits($bits) {
        // Convert bits to bytes
        $bytes = $bits / 8;

        // Helper to format with or without decimals
        $formatValue = function($value) {
            return ($value == floor($value)) ? number_format($value, 0) : number_format($value, 2);
        };

        if ($bytes < 1024) {
            return $formatValue($bytes) . " B";
        }

        $kb = $bytes / 1024;
        if ($kb < 1024) {
            return $formatValue($kb) . " KB";
        }

        $mb = $kb / 1024;
        if ($mb < 1024) {
            return $formatValue($mb) . " MB";
        }

        $gb = $mb / 1024;
        if ($gb < 1024) {
            return $formatValue($gb) . " GB";
        }

        $tb = $gb / 1024;
        return $formatValue($tb) . " TB";
    }

    function getLast5MinuteInterval() {
        $now = new DateTime(); // current time

        // Get current minutes and floor to nearest 5
        $minutes = (int)$now->format('i');
        $roundedMinutes = $minutes - ($minutes % 5);

        // Set new time with seconds = 0
        $now->setTime((int)$now->format('H'), $roundedMinutes, 0);

        return $now->format('YmdHis');
    }

    function getLast30MinuteInterval() {
        $now = new DateTime(); // current time

        // Get minutes and floor to 30 min blocks
        $minutes = (int)$now->format('i');
        $roundedMinutes = $minutes - ($minutes % 30);

        // Set the minutes & seconds
        $now->setTime((int)$now->format('H'), $roundedMinutes, 0);

        return $now->format('YmdHis');
    }

    function dateDiffSingleUnit($date1, $date2, $unit = "minutes") {
        $d1 = new DateTime($date1);
        $d2 = new DateTime($date2);

        // Absolute difference in seconds
        $diffInSeconds = abs($d1->getTimestamp() - $d2->getTimestamp());

        // Conversion factors
        $conversions = [
            "seconds" => 1,
            "minutes" => 60,
            "hours"   => 3600,
            "days"    => 86400,
            "weeks"   => 604800,
            "months"  => 2629800, // Approx (30.44 days)
            "years"   => 31557600 // Approx (365.25 days)
        ];

        if (!array_key_exists($unit, $conversions)) {
            throw new Exception("Invalid unit provided. Use seconds, minutes, hours, days, weeks, months, years.");
        }

        $round = 2;
        if ($unit == "days" || $unit == "months" || $unit == "years") {
            $round = 4; // more precision for larger units
        }

        return round($diffInSeconds / $conversions[$unit], $round); // round to 2 decimals
    }

    function getLast6AM() {
        $now = new DateTime();  
        
        // Clone to avoid modifying original
        $last6am = clone $now;  
        $last6am->setTime(6, 0, 0);

        // If current time is before today's 6 AM → go back to yesterday 6 AM
        if ($now < $last6am) {
            $last6am->modify('-1 day');
        }

        return $last6am->format('YmdHis');
    }

    function getLast2HourInterval() {
        $now = new DateTime(); // current time

        // Get the current hour and floor to nearest multiple of 2
        $hour = (int)$now->format('H');
        $roundedHour = $hour - ($hour % 2);

        // Set hours, reset minutes and seconds
        $now->setTime($roundedHour, 0, 0);

        return $now->format('YmdHis');
    }

    function parseQueueSpeed($speedStr) {
        // Split upload/download
        list($uploadStr, $downloadStr) = explode('/', strtolower(trim($speedStr)));

        // Helper closure to convert string to bits
        $toBits = function($val) {
            if (preg_match('/^([\d\.]+)([kmg]?bps)$/', $val, $matches)) {
                $num = (float)$matches[1];
                $unit = $matches[2];

                switch ($unit) {
                    case 'bps':
                        return (int)$num;
                    case 'kbps':
                        return (int)($num * 1000);
                    case 'mbps':
                        return (int)($num * 1000 * 1000);
                    case 'gbps':
                        return (int)($num * 1000 * 1000 * 1000);
                    default:
                        return 0;
                }
            }
            return 0;
        };

        return [
            'upload' => $toBits($uploadStr),
            'download' => $toBits($downloadStr)
        ];
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
    function formatKenyanPhone($number) {
        // Remove spaces, dashes, and plus sign
        $number = preg_replace('/[\s\-\+]/', '', $number);

        // If it starts with "07", replace with "2547"
        if (preg_match('/^07\d{8}$/', $number)) {
            return '254' . substr($number, 1);
        }

        // If it starts with "+2547" (after plus removal)
        if (preg_match('/^2547\d{8}$/', $number)) {
            return $number;
        }

        // If it starts with "7" only, add "254"
        if (preg_match('/^7\d{8}$/', $number)) {
            return '254' . $number;
        }

        // Invalid number
        return null;
    }

    function sendHostPinnacleSMS($message, $mobile, $apikey, $partnerID, $shortcode) {
        // API URL
        $url = "https://smsportal.hostpinnacle.co.ke/SMSApi/send";
        
        // Prepare POST fields
        $postData = [
            "userid"     => $apikey,
            "password"     => $partnerID,
            "senderid"   => urlencode($shortcode),
            "msg"        => urlencode($message),
            "mobile"   => $this->formatKenyanPhone($mobile),
            "sendMethod" => "quick",
            "msgType"    => "text",  // or 'unicode' if sending special characters
            "output"     => "json"   // Response format: json, xml, plain
        ];
        // return $postData;
        
        // Initialize cURL
        $ch = \curl_init();
        \curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        $response = \curl_exec($ch);
        \curl_close($ch);

        return $response;
    }
    

    function sendTalkSasaSms($message, $phone, $apiKey, $senderId)
    {
        $url = "https://bulksms.talksasa.com/api/v3/sms/send";
        $phone = explode(",",$phone);
        $phone = array_map(function($num) {
            return $this->formatKenyanPhone($num);
        }, $phone);
        $phone = implode(",", $phone);

        $payload = [
            "recipient" => $phone,
            "sender_id" => $senderId,
            "message" => $message,
            "type" => "plain", // or 'unicode' if sending special characters
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR    => false,
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer " . $apiKey,
                "Accept: application/json",
                "Content-Type: application/json",
            ],
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            return ["success" => false, "response" => "cURL error: {$error}"];
        }

        $decoded = json_decode($response, true);

        return [
            "success"  => $httpCode >= 200 && $httpCode < 300,
            "status"   => $httpCode,
            "response" => $decoded ?: $response,
        ];
    }


    function sendAfrokattSMS($message, $phone_number, $apikey, $shortcode) {
        $client_phone = explode(",",$phone_number);
        $message_status = 0;
        foreach ($client_phone as $key => $phone) {
            $phone = $this->formatKenyanPhone($phone);
            $finalURL = "https://account.afrokatt.com/sms/api?action=send-sms&api_key=".urlencode($apikey)."&to=".$phone."&from=".$shortcode."&sms=".urlencode($message)."&unicode=1";
            $ch = \curl_init();
            \curl_setopt($ch, CURLOPT_URL, $finalURL);
            \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = \curl_exec($ch);
            \curl_close($ch);
            $res = json_decode($response);
            $values = $res->code;
            if (isset($res->code)) {
                if($res->code == "200"){
                    $message_status = 1;
                }
            }
        }
        return $message_status;
    }

    function sendCelcomSMS($message, $mobile, $apikey, $shortcode, $partnerID) {
        $finalURL = "https://isms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL, $finalURL);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = \curl_exec($ch);
        \curl_close($ch);
        $res = json_decode($response);
        // return $res;
        $values = $res->responses[0];
        foreach ($values as  $key => $value) {
            // echo $key;
            if ($key == "response-code") {
                if ($value == "200") {
                    // if its 200 the message is sent delete the
                    $message_status = 1;
                }
            }
        }
        return $values;
    }

    function GlobalSendSMS($message, $phone_number, $apiKey, $smsSender, $shortcode, $partnerID) {
        if ((session()->has("organization") && session("organization")->send_sms == 0)) {
            return null;
        }

        if ($smsSender == "hostpinnacle") {
            return $this->sendHostPinnacleSMS($message, $phone_number, $apiKey, $partnerID, $shortcode);
        } elseif ($smsSender == "afrokatt") {
            return $this->sendAfrokattSMS($message, $phone_number, $apiKey, $shortcode);
        } elseif ($smsSender == "celcom") {
            return $this->sendCelcomSMS($message, $phone_number, $apiKey, $shortcode, $partnerID);
        } elseif ($smsSender == "talksasa") {
            return $this->sendTalkSasaSms($message, $phone_number, $apiKey, $shortcode);
        }
        return null;
    }

    function getSMSBalance($apikey, $smsSender, $shortcode, $partnerID){
        if ($smsSender == "celcom") {
            // if send sms is 1 we send  the sms
            // get the sms balance
            $finalURL = "https://isms.celcomafrica.com/api/services/getbalance/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID);
            $ch = \curl_init();
            \curl_setopt($ch, CURLOPT_URL, $finalURL);
            \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = \curl_exec($ch);
            \curl_close($ch);
            $res = json_decode($response);
            $credit_balance = $res->credit;
            return round($credit_balance)." SMS";
        } elseif($smsSender == "afrokatt") {
            // get the sms balance
            $finalURL = "https://account.afrokatt.com/sms/api?action=check-balance&api_key=".urlencode($apikey)."&response=json";
            $ch = \curl_init();
            \curl_setopt($ch, CURLOPT_URL, $finalURL);
            \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = \curl_exec($ch);
            \curl_close($ch);
            $res = json_decode($response);
            if(isset($res->balance)){
                $credit_balance = $res->balance;
            }else{
                $credit_balance = 0;
            }
            return round($credit_balance)." SMS";
        }elseif ($smsSender == "hostpinnacle") {
            // API URL
            $url = "https://smsportal.hostpinnacle.co.ke/SMSApi/account/readstatus";
            
            // Prepare POST fields
            $postData = [
                "userid"     => $apikey,
                "password"     => $partnerID,
                "senderid"   => urlencode($shortcode),
                "sendMethod" => "quick",
                "msgType"    => "text",  // or 'unicode' if sending special characters
                "output"     => "json"   // Response format: json, xml, plain
            ];
            
            // Initialize cURL
            $ch = \curl_init();
            \curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $postData,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            $response = \curl_exec($ch);
            \curl_close($ch);

            $response = json_decode($response, true);
            if (isset($response['response']['code']) && $response['response']['code'] == 200) {
                return round($response['response']['account']['smsBalance'])." SMS";
            }
        }elseif ($smsSender == "talksasa") {
            $url = "https://bulksms.talksasa.com/api/v3/balance";
            $payload = [];

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FAILONERROR    => false,
                CURLOPT_HTTPHEADER     => [
                    "Authorization: Bearer " . $apikey,
                    "Accept: application/json",
                ],
            ]);

            $response = curl_exec($ch);
            $error    = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response === false) {
                return "Can`t fetch balance now!";
            }

            $decoded = json_decode($response, true);

            $response = [
                "success"  => $httpCode >= 200 && $httpCode < 300,
                "status"   => $httpCode,
                "response" => $decoded ?: $response,
            ];
            if(isset($response['response']['data']['remaining_balance'])){
                return number_format(substr($response['response']['data']['remaining_balance'], 3))." SMS";
            }
        }
        return "0 SMS";
    }
}
