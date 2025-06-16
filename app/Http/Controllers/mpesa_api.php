<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

date_default_timezone_set('Africa/Nairobi');
class mpesa_api extends Controller
{
    // register url
    function register_url(Request $request){
        $consumerKey = $request->input("consumerKey");
        $consumerSecret = $request->input("consumerSecret");
        $access_token = $this->access_token($consumerKey, $consumerSecret);
        if ($access_token) {
            $registerurl = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v2/registerurl';
            $BusinessShortCode = $request->input("BusinessShortCode");
            $confirmationUrl = 'https://192.168.86.16:8000/Transact';
            $validationUrl = "https://192.168.86.16:8000/Validate";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $registerurl);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Authorization:Bearer ' . $access_token
            ));
            $data = array(
            'ShortCode' => $BusinessShortCode,
            'ResponseType' => 'Completed',
            'ConfirmationURL' => $confirmationUrl,
            'ValidationURL' => $validationUrl
            );
            $data_string = json_encode($data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            return $curl_response = curl_exec($curl);
        }else{
            return array("success" => false, "desc" => "Invalid access token!", "access_token" => $access_token);
        }
    }

    function access_token($consumerKey, $consumerSecret){
        //ACCESS TOKEN URL
        $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v2/generate?grant_type=client_credentials';
        $headers = ['Content-Type:application/json; charset=utf8'];
        $curl = curl_init($access_token_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = json_decode($result);
        $access_token = $result->access_token ?? null;
        curl_close($curl);
        return $access_token;
    }
}
