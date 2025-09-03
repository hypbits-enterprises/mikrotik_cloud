<?php

namespace App\Classes;

use App\Http\Controllers\login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MpesaService
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $shortCode;
    protected $passKey;
    protected $callbackUrl;

    public function __construct()
    {
        // $consumerKey = "cCAEjjhm1LSEFUqqlHBOjMG8mjBey25DpkZi8sRrL2SQIkgA";
        // $consumerSecret = "LVO4bJTvAi1fDFsFki3MYVdh9DFQCkXz4KovJJf3WfuxUnJcuJg1zenJmX5vFaMD";
        // $shortCode = "174379";
        // $passKey = "aw7SvztWI34yPh2D/MzHHUE09Kb2BY7ZgS7aEjW9VaOgsvN/TWoyt4Shbpex0boJSStIYvfKJKWWwjoTuqarQTWFIQhgwptFnOfynqVyZKqaOfnjSmbTbvj2b2ilrEj67+mdcK4U/GY2CzQzfchSLVWMrBstiTFMdm1u3MhTfyx3mWqFvVvfluNGQlThWwoBX79Cq8yOjWZa8KeZgo8RvWkeFxiz5d2z+2c/cfPtlVhUzF9Pmf3QRl3m6/pcjRNf9W9WrWJyWab/3OzTPsbmFZtDpUNLRMlEddpoZebLmZqTnwJO3bGR5XNHMZyPwMZs5k9Stztu7oYhzTA1SHMCqQ==";
        // $callbackUrl = "https://mydomain.com/path";

        // initiate_stk
        // $consumerKey = "6yYoxkjeAPGbkxjfEgUZ14KT52IOPhWM";
        // $consumerSecret = "L4w8Ls49ZfLxurde";
        // $shortCode = "4061913";
        // $passKey = "3f9f489b44c2bbbc9cf3cf04672825994ce39ded35c8cc24304f42ffa76397ae";
        $callbackUrl = "https://mydomain.com/path";

        // get the consumer key
        $update_database = new login();
        $update_database->change_db();
        $key = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'consumer_key'");
        $consumer_key = $key[0]->value;

        $key = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'consumer_secret'");
        $consumer_secret = $key[0]->value;

        $key = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'passkey'");
        $passkey = $key[0]->value;

        $key = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'paybill'");
        $paybillno = $key[0]->value;

        $this->consumerKey   = $consumer_key;
        $this->consumerSecret= $consumer_secret;
        $this->shortCode     = $paybillno;
        $this->passKey       = $passkey;
        $this->callbackUrl   = $callbackUrl;
    }

    /**
     * Generate Access Token
     */
    private function getAccessToken()
    {
        // send stk push
		$password = base64_encode($this->consumerKey.':'.$this->consumerSecret);
		$headers = [
			'Authorization: Basic '.$password,
			'Content-Type:application/json; charset=utf8'
		];
        
        $headers = ['Content-Type:application/json; charset=utf8'];
        $url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_USERPWD, $this->consumerKey.':'.$this->consumerSecret);
        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = json_decode($result);
        curl_close($curl);
        $access_token = isset($result->access_token)? $result->access_token:"0";
        return $result;
    }

    /**
     * Initiate STK Push
     */
    public function stkPush($phone, $amount, $accountReference = "TestPayment", $transactionDesc = "Payment")
    {
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->shortCode . $this->passKey . $timestamp);

        $payload = [
            "BusinessShortCode" => $this->shortCode,
            "Password"          => $password,
            "Timestamp"         => $timestamp,
            "TransactionType"   => "CustomerPayBillOnline",
            "Amount"            => $amount,
            "PartyA"            => $phone, // customer phone number in format 2547xxxxxxxx
            "PartyB"            => $this->shortCode,
            "PhoneNumber"       => $phone,
            "CallBackURL"       => $this->callbackUrl,
            "AccountReference"  => $accountReference,
            "TransactionDesc"   => $transactionDesc
        ];

        $token = $this->getAccessToken();
        if(isset($token->access_token)){
            $token = $token->access_token;

            $response = Http::withToken($token)
                ->post('https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest', $payload);

            return $response->json();
        }else{
            return ["CustomerMessage"=>"Could not generate access token"];
        }
    }
}
