<?php

namespace App\Http\Controllers;

use DateTime;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
    function convertBits($bits, $type = "data") {
        // Convert bits to bytes
        if($type == "data"){
            $bytes = $bits / 8;
        }else{
            $bytes = $bits;
        }

        // Helper to format with or without decimals
        $formatValue = function($value) {
            return ($value == floor($value)) ? number_format($value, 0) : number_format($value, 2);
        };

        if ($bytes < 1024) {
            return $formatValue($bytes) . ($type == "data" ? " B" : " b");
        }

        $kb = $bytes / 1024;
        if ($kb < 1024) {
            return $formatValue($kb) . ($type == "data" ? " KB" : " Kb");
        }

        $mb = $kb / 1024;
        if ($mb < 1024) {
            return $formatValue($mb) . ($type == "data" ? " MB" : " Mb");
        }

        $gb = $mb / 1024;
        if ($gb < 1024) {
            return $formatValue($gb) . ($type == "data" ? " GB" : " Gb");
        }

        $tb = $gb / 1024;
        return $formatValue($tb) . ($type == "data" ? " TB" : " Tb");
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

    // format phone number
    function formatKenyanPhone($number) {
        // Remove spaces, dashes, and plus sign
        $number = preg_replace('/[\s\-\+]/', '', $number);

        // Local format: 07XXXXXXXX or 01XXXXXXXX
        if (preg_match('/^(07|01)\d{8}$/', $number)) {
            return '254' . substr($number, 1);
        }

        // International format without plus: 2547XXXXXXXX or 2541XXXXXXXX
        if (preg_match('/^254(7|1)\d{8}$/', $number)) {
            return $number;
        }

        // Short format: 7XXXXXXXX or 1XXXXXXXX
        if (preg_match('/^(7|1)\d{8}$/', $number)) {
            return '254' . $number;
        }

        // Invalid Kenyan mobile number
        return $number;
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
    

    function sendBlessedTextsSMS($message, $phone, $apiKey, $senderId) {
        $url = "https://sms.blessedtexts.com/api/sms/v1/sendsms";
        $phone = explode(",",$phone);
        $phone = array_map(function($num) {
            return $this->formatKenyanPhone($num);
        }, $phone);
        $phone = implode(",", $phone);

        $payload = [
            "phone" => $phone,
            "sender_id" => $senderId,
            "message" => $message,
            "api_key" => $apiKey
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
        if (!$res || empty($res->responses)) {
            return null;
        }
        $values = $res->responses[0];
        foreach ($values as $key => $value) {
            if ($key == "response-code") {
                if ($value == "200") {
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
        }elseif ($smsSender == "blessedtexts") {
            return $this->sendBlessedTextsSMS($message, $phone_number, $apiKey, $shortcode);
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
                "response" => $decoded ? $decoded : $response,
            ];
            if(isset($response['response']['data']['remaining_balance'])){
                return substr($response['response']['data']['remaining_balance'], 3)." SMS";
            }
        }elseif ($smsSender == "blessedtexts") {
            $url = "https://sms.blessedtexts.com/api/sms/v1/credit-balance?api_key=" . urlencode($apikey);

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FAILONERROR    => false,
                CURLOPT_HTTPHEADER     => [
                    "Authorization: Bearer " . $apikey,
                    "Accept: application/json",
                ]
            ]);

            $response = curl_exec($ch);
            $error    = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response === false) {
                return "Can`t fetch balance now!";
            }

            $decoded = json_decode($response, true);
            if(isset($decoded['status_code']) && $decoded['status_code'] == 1000){
                return number_format($decoded['balance'])." SMS";
            }
        }
        return "0 SMS";
    }
    function getSmsSettings() {
        $sender_row     = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_sender'");
        $api_key_row    = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_api_key'");
        $partner_id_row = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_partner_id'");
        $shortcode_row  = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_shortcode'");

        if (count($api_key_row) == 0 || count($partner_id_row) == 0 || count($shortcode_row) == 0) {
            return null;
        }

        return [
            'sms_sender'     => count($sender_row) > 0 ? $sender_row[0]->value : "",
            'sms_api_key'    => $api_key_row[0]->value,
            'sms_partner_id' => $partner_id_row[0]->value,
            'sms_shortcode'  => $shortcode_row[0]->value,
        ];
    }

    function isJson($string): bool
    {
        if (!is_string($string)) {
            return false;
        }

        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    // ─── WhatsApp Helpers ─────────────────────────────────────────────────────

    function getWhatsAppSettings(): ?array
    {
        $token   = config('messaging.whatsapp.access_token');
        $phoneId = config('messaging.whatsapp.phone_number_id');

        if (!$token || !$phoneId) {
            return null;
        }

        return [
            'access_token'        => $token,
            'phone_number_id'     => $phoneId,
            'business_account_id' => config('messaging.whatsapp.business_account_id'),
            'api_version'         => config('messaging.whatsapp.api_version', 'v19.0'),
            'base_url'            => config('messaging.whatsapp.base_url', 'https://graph.facebook.com'),
        ];
    }

    function sendWhatsAppMessage(string $to, string $body, string $category = 'service'): array
    {
        $settings = $this->getWhatsAppSettings();
        if (!$settings) {
            return ['error' => 'WhatsApp not configured'];
        }

        $url     = "{$settings['base_url']}/{$settings['api_version']}/{$settings['phone_number_id']}/messages";
        $payload = json_encode([
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'text',
            'text'              => ['body' => $body],
        ]);

        return $this->whatsappApiCall($url, $settings['access_token'], $payload);
    }

    function sendWhatsAppTemplate(string $to, string $templateName, array $variables, string $category = 'utility', string $language = 'en', array $buttonVariables = []): array
    {
        $settings = $this->getWhatsAppSettings();
        if (!$settings) {
            return ['error' => 'WhatsApp not configured'];
        }

        $components = [];
        if (!empty($variables)) {
            $params = array_map(fn($v) => ['type' => 'text', 'text' => (string) $v], $variables);
            $components[] = ['type' => 'body', 'parameters' => $params];
        }

        // URL buttons require sub_type=url with type=text (not copy_code/coupon_code)
        foreach ($buttonVariables as $index => $val) {
            $components[] = [
                'type'       => 'button',
                'sub_type'   => 'url',
                'index'      => (string) $index,
                'parameters' => [['type' => 'text', 'text' => (string) $val]],
            ];
        }

        $url     = "{$settings['base_url']}/{$settings['api_version']}/{$settings['phone_number_id']}/messages";
        $payload = json_encode([
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'template',
            'template'          => [
                'name'       => $templateName,
                'language'   => ['code' => $language],
                'components' => $components,
            ],
        ]);

        $result = $this->whatsappApiCall($url, $settings['access_token'], $payload);
        if (!empty($result['error'])) {
            \Log::warning('sendWhatsAppTemplate failed', ['template' => $templateName, 'payload' => $payload, 'response' => $result]);
        }
        return $result;
    }

    function isWithin24hrWindow(int $clientId): bool
    {
        $cutoff = date('YmdHis', strtotime('-24 hours'));

        $last = DB::connection('mysql2')->select("
            SELECT wc.received_at FROM whatsapp_chats wc
            JOIN sms_tables s ON s.sms_id = wc.message_id
            WHERE s.account_id = ? AND s.channel = 'whatsapp'
              AND wc.direction = 'inbound' AND s.deleted = '0'
            ORDER BY wc.received_at DESC LIMIT 1
        ", [$clientId]);

        if (empty($last) || !$last[0]->received_at) {
            return false;
        }

        return $last[0]->received_at >= $cutoff;
    }

    function notifyClient($client, string $message, ?string $templateName = null): void
    {
        $channel = $this->getPreferredChannel();

        $phone = is_object($client) ? $client->clients_contacts : ($client['clients_contacts'] ?? null);
        if (!$phone) return;

        if ($channel === 'whatsapp') {
            if ($templateName) {
                $clientId = is_object($client) ? $client->client_id : ($client['client_id'] ?? 0);
                $withinWindow = $this->isWithin24hrWindow($clientId);

                if (!$withinWindow) {
                    // Template send for clients outside window — callers must pass template name
                    $this->sendWhatsAppMessage($this->formatKenyanPhone($phone), $message, 'utility');
                    return;
                }
            }
            $this->sendWhatsAppMessage($this->formatKenyanPhone($phone), $message, 'utility');
        } else {
            $smsSettings = $this->getSmsSettings();
            if ($smsSettings) {
                $this->GlobalSendSMS(
                    $message,
                    $this->formatKenyanPhone($phone),
                    $smsSettings['sms_api_key'],
                    $smsSettings['sms_sender'],
                    $smsSettings['sms_shortcode'],
                    $smsSettings['sms_partner_id']
                );
            }
        }
    }

    function getPreferredChannel(?int $orgId = null): string
    {
        $id = $orgId ?? session('organization_id');
        $row = DB::select(
            "SELECT `preferred_channel` FROM `organizations` WHERE `organization_id` = ? LIMIT 1",
            [$id]
        );
        return !empty($row) ? ($row[0]->preferred_channel ?: 'sms') : 'sms';
    }

    function getAutomationTemplate(string $internalName): ?array
    {
        $row = DB::select(
            "SELECT * FROM `mikrotik_cloud_manager`.`whatsapp_automation_templates`
             WHERE `internal_name` = ? AND `is_active` = 1 AND `deleted` = '0' LIMIT 1",
            [$internalName]
        );
        if (empty($row)) return null;
        $t = $row[0];
        return [
            'template_name' => $t->template_name,
            'category'      => $t->category,
            'language'      => $t->language,
            'variables'     => json_decode($t->variables ?? '[]', true) ?: [],
        ];
    }

    function buildTemplateParams(array $variableNames, object $clientData, array $extras = []): array
    {
        $expRaw  = $clientData->next_expiration_date ?? '';
        $expDate = $expRaw
            ? date('dS-M-Y', strtotime($expRaw)) . ' at ' . date('H:i:s', strtotime($expRaw))
            : '';
        $minPay = isset($clientData->monthly_payment, $clientData->min_amount)
            ? ceil($clientData->monthly_payment * ($clientData->min_amount / 100))
            : 0;

        $map = [
            'client_f_name'         => ucfirst(strtolower(explode(' ', $clientData->client_name ?? '')[0])),
            'client_name'           => $clientData->client_name ?? '',
            'acc_no'                => $clientData->client_account ?? '',
            'monthly_fees'          => 'Ksh ' . number_format($clientData->monthly_payment ?? 0),
            'exp_date'              => $expDate,
            'client_wallet'         => 'Ksh ' . number_format($clientData->wallet_amount ?? 0),
            'today'                 => date('dS-M-Y'),
            'now'                   => date('H:i:s'),
            'trans_amnt'            => 'Ksh ' . number_format($extras['trans_amount'] ?? 0),
            'min_amnt'              => 'Ksh ' . number_format($extras['min_amount'] ?? $minPay),
            'days_frozen'           => ($extras['days_frozen'] ?? '0') . ' Day(s)',
            'unfreeze_date'         => $extras['unfreeze_date'] ?? '',
            'frozen_date'           => $extras['frozen_date'] ?? '',
            'refferer_trans_amount' => 'Ksh ' . number_format($extras['refferer_trans_amount'] ?? 0),
            'refferer_name'         => $extras['refferer_name'] ?? '',
        ];

        $params = [];
        foreach ($variableNames as $varName) {
            $params[] = $map[$varName] ?? '';
        }
        return $params;
    }

    public function resolveEmailContent(string $internalName, object $clientData, array $extras = [], string $orgName = ''): ?array
    {
        $tplRow   = DB::connection('mysql2')->table('email_templates')->where('name', $internalName)->first();
        $defaults = \App\Http\Controllers\EmailTemplates::getDefaults();

        if ($tplRow) {
            $rawSubject = $tplRow->subject;
            $rawBody    = $tplRow->html_body;
        } elseif (isset($defaults[$internalName])) {
            $rawSubject = $defaults[$internalName]['subject'];
            $rawBody    = $defaults[$internalName]['body'];
        } else {
            return null;
        }

        return [
            'subject' => $this->resolveEmailVariables($rawSubject, $clientData, $extras, $orgName),
            'body'    => strip_tags($this->resolveEmailVariables($rawBody, $clientData, $extras, $orgName)),
        ];
    }

    protected function resolveEmailVariables(string $template, object $clientData, array $extras = [], string $orgName = ''): string
    {
        $expRaw  = $clientData->next_expiration_date ?? '';
        $expDate = $expRaw
            ? date('dS M Y', strtotime($expRaw)) . ' at ' . date('H:i', strtotime($expRaw))
            : '';

        $map = [
            '[client_name]'    => $clientData->client_name ?? '',
            '[client_f_name]'  => ucfirst(strtolower(explode(' ', $clientData->client_name ?? ' ')[0])),
            '[account_number]' => $clientData->client_account ?? '',
            '[monthly_fees]'   => 'Ksh ' . number_format($clientData->monthly_payment ?? 0),
            '[exp_date]'       => $expDate,
            '[wallet_balance]' => 'Ksh ' . number_format($clientData->wallet_amount ?? 0),
            '[trans_amount]'   => 'Ksh ' . number_format($extras['trans_amount'] ?? 0),
            '[min_amount]'     => 'Ksh ' . number_format($extras['min_amount'] ?? 0),
            '[days_frozen]'    => ($extras['days_frozen'] ?? '0') . ' Day(s)',
            '[unfreeze_date]'  => $extras['unfreeze_date'] ?? '',
            '[freeze_date]'    => $extras['frozen_date'] ?? $extras['unfreeze_date'] ?? '',
            '[org_name]'       => $orgName,
            '[receipt]'        => 'Please find your payment receipt attached.',
        ];

        return str_replace(array_keys($map), array_values($map), $template);
    }

    protected function buildReceiptPdf(object $clientData, array $extras): ?string
    {
        try {
            $org = $extras['org_data'] ?? session('organization');
            if (!$org) return null;

            $pdf = new \App\Classes\reports\RECEIPT("P", "mm", "A4");
            $pdf->AddFont('century_gothic', '', 'century_gothic.php');
            $pdf->AddFont('century_gothic', 'B', 'century_gothic_bold.php');
            $pdf->AddFont('century_gothic', 'I', 'Century Gothic Italic.php');
            $pdf->AddFont('century_gothic', 'BI', 'Century Gothic Bold Italic.php');
            $pdf->setCompayLogo($org->organization_logo ?? null);
            $pdf->set_company_name(strtoupper($org->organization_name ?? 'ISP'));
            $pdf->set_company_contact($org->organization_main_contact ?? '');
            $pdf->set_company_email($org->organization_email ?? '');
            $pdf->set_company_address($org->organization_address ?? '');
            $pdf->set_document_title($org->organization_name ?? 'ISP');
            $pdf->SetTextColor(25, 25, 25);

            $rawDate  = $extras['trans_date'] ?? date('YmdHis');
            $dt       = \DateTime::createFromFormat('YmdHis', $rawDate);
            $payment  = (object)[
                'transaction_date'     => $dt ? $dt->format('Y-m-d H:i:s') : date('Y-m-d H:i:s'),
                'transaction_mpesa_id' => $extras['mpesa_ref'] ?? '',
                'transacion_amount'    => $extras['trans_amount'] ?? 0,
            ];

            $pdf->set_client_data($clientData);
            $pdf->set_payment_data($payment);
            $pdf->AddPage();
            $pdf->Image(public_path("theme-assets/images/paid_stamp.png"), 80, 120, 60);

            $amount = $extras['trans_amount'] ?? 0;
            $pdf->Cell(20, 5, "Qty", 0, 0, "L");
            $pdf->Cell(100, 5, "Description Of Service", 0, 0, "L");
            $pdf->Cell(35, 5, "Transaction Code", 0, 0, "L");
            $pdf->Cell(30, 5, "Total", 0, 1, "L");

            $pdf->Cell(20, 8, "1", 1, 0, "L");
            $pdf->Cell(100, 8, "Internet Service - " . ($clientData->max_upload_download ?? ''), 1, 0, "L");
            $pdf->Cell(35, 8, ($extras['mpesa_ref'] ?? '-') . " ", 1, 0, "L");
            $pdf->Cell(30, 8, "Kes " . number_format($amount), 1, 1, "L");

            $pdf->Cell(20, 8, "", 0, 0, "L");
            $pdf->Cell(70, 8, "", 0, 0, "L");
            $pdf->Cell(65, 8, "Discount", 0, 0, "R");
            $pdf->Cell(30, 8, "Kes " . number_format(0, 2), 1, 1, "L");

            $pdf->Cell(20, 8, "", 0, 0, "L");
            $pdf->Cell(70, 8, "", 0, 0, "L");
            $pdf->Cell(65, 8, "Total", 0, 0, "R");
            $pdf->Cell(30, 8, "Kes " . number_format($amount, 2), 1, 1, "L");

            return $pdf->Output('S');
        } catch (\Throwable $e) {
            return null;
        }
    }

    function dispatchAutomationMessage(
        string  $internalName,
        string  $resolvedSms,
        string  $phone,
        object  $clientData,
        array   $extras = [],
        ?string $channelOverride = null
    ): bool {
        $channel = $channelOverride
            ?? ($clientData->preferred_channel ?: null)
            ?? $this->getPreferredChannel();

        if ($channel === 'whatsapp') {
            $template = $this->getAutomationTemplate($internalName);
            if (!$template) return false;
            $params = $this->buildTemplateParams($template['variables'], $clientData, $extras);
            $result = $this->sendWhatsAppTemplate(
                $this->formatKenyanPhone($phone),
                $template['template_name'],
                $params,
                $template['category'],
                $template['language']
            );
            return empty($result['error']);
        }

        if ($channel === 'email') {
            $email = $clientData->client_email ?? null;
            if (!$email) return false;
            try {
                $orgObj  = $extras['org_data'] ?? session('organization');
                $orgName = $orgObj ? ($orgObj->organization_name ?? 'Your ISP') : 'Your ISP';

                // Use org-configured SMTP if available, otherwise fall back to system .env
                $esSetting = DB::connection('mysql2')->table('settings')->where('keyword', 'email_settings')->first();
                $es = $esSetting ? json_decode($esSetting->value, true) : [];
                if (!empty($es['host']) && !empty($es['username']) && !empty($es['password'])) {
                    config([
                        'mail.mailers.smtp.host'       => $es['host'],
                        'mail.mailers.smtp.port'       => $es['port'] ?? 587,
                        'mail.mailers.smtp.encryption' => ($es['encryption'] ?? 'tls') === 'none' ? null : ($es['encryption'] ?? 'tls'),
                        'mail.mailers.smtp.username'   => $es['username'],
                        'mail.mailers.smtp.password'   => $es['password'],
                        'mail.from.address'            => $es['username'],
                        'mail.from.name'               => $es['from_name'] ?? $orgName,
                    ]);
                    // Force the mail manager to rebuild the transport with the new settings
                    app('mail.manager')->purge('smtp');
                } elseif (empty(env('EMAIL_HOST')) || empty(env('EMAIL_USERNAME')) || empty(env('EMAIL_PASSWORD'))) {
                    Log::warning("AutomationEmail [{$internalName}]: no SMTP config available for {$email}");
                    return false;
                }

                $tplRow   = DB::connection('mysql2')->table('email_templates')->where('name', $internalName)->first();
                $defaults = \App\Http\Controllers\EmailTemplates::getDefaults();

                if ($tplRow) {
                    $rawSubject = $tplRow->subject;
                    $rawBody    = $tplRow->html_body;
                } elseif (isset($defaults[$internalName])) {
                    $rawSubject = $defaults[$internalName]['subject'];
                    $rawBody    = $defaults[$internalName]['body'];
                } else {
                    Mail::raw($resolvedSms, function ($m) use ($email, $orgName) {
                        $m->to($email)->subject('Message from ' . $orgName);
                    });
                    return true;
                }

                $hasReceipt = str_contains($rawBody, '[receipt]');
                $pdfBytes   = $hasReceipt ? $this->buildReceiptPdf($clientData, $extras) : null;
                $pdfName    = 'receipt_' . ($clientData->client_account ?? 'unknown') . '_' . date('dMY') . '.pdf';

                $subject = $this->resolveEmailVariables($rawSubject, $clientData, $extras, $orgName);
                $body    = $this->resolveEmailVariables($rawBody, $clientData, $extras, $orgName);
                Mail::to($email)->send(new \App\Mail\AutomationEmail($subject, $body, $pdfBytes, $pdfName));
                return true;
            } catch (\Exception $e) {
                Log::error("AutomationEmail [{$internalName}] failed for {$email}: " . $e->getMessage());
                return false;
            }
        }

        $settings = $this->getSmsSettings();
        if (!$settings) return false;
        $result = $this->GlobalSendSMS(
            $resolvedSms, $phone,
            $settings['sms_api_key'], $settings['sms_sender'],
            $settings['sms_shortcode'], $settings['sms_partner_id']
        );
        return $result !== null;
    }

    protected function whatsappApiCall(string $url, string $token, ?string $payload = null, string $method = 'POST'): array
    {
        $ch   = curl_init($url);
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 30,
        ];

        if ($method === 'POST') {
            $opts[CURLOPT_POST]       = true;
            $opts[CURLOPT_POSTFIELDS] = $payload;
        } elseif ($method === 'DELETE') {
            $opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        }
        // GET: no extra opts needed

        curl_setopt_array($ch, $opts);
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            return ['error' => 'No response from WhatsApp API'];
        }

        return json_decode($response, true) ?? ['error' => 'Invalid JSON response'];
    }
}
