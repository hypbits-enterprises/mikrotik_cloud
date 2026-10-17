<?php

namespace App\Classes;

use App\Http\Controllers\login;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MpesaService
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $shortCode;
    protected $passKey;
    protected $callbackUrl;
    protected $cacheToken = false;
    public $configError = null;

    /**
     * No argument: the logged-in org's keys from the session DB (PPPoE, unchanged).
     * Hotspot (no session): ['database' => org DB, 'callback_url' => ..., 'keys' => 'billing'|'hotspot'].
     * 'hotspot' reads the separate hotspot Paybill keys (hotspot_consumer_key, hotspot_consumer_secret,
     * hotspot_passkey, hotspot_shortcode). Missing keys set $configError instead of failing.
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->loadKeys($options);
            return;
        }
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

    private function loadKeys(array $options)
    {
        (new login())->change_db($options['database']);
        $names = ($options['keys'] ?? 'billing') === 'hotspot'
            ? ['consumer_key' => 'hotspot_consumer_key', 'consumer_secret' => 'hotspot_consumer_secret', 'passkey' => 'hotspot_passkey', 'paybill' => 'hotspot_shortcode']
            : ['consumer_key' => 'consumer_key', 'consumer_secret' => 'consumer_secret', 'passkey' => 'passkey', 'paybill' => 'paybill'];
        $rows = DB::connection("mysql2")->table("settings")->where("deleted", "0")->whereIn("keyword", array_values($names))->pluck("value", "keyword");
        $missing = [];
        foreach ($names as $name) {
            if (!isset($rows[$name]) || trim((string) $rows[$name]) === "") {
                $missing[] = $name;
            }
        }
        if (count($missing) > 0) {
            $this->configError = "M-Pesa settings missing: " . implode(", ", $missing) . ".";
        }
        $this->consumerKey    = trim((string) ($rows[$names['consumer_key']] ?? ""));
        $this->consumerSecret = trim((string) ($rows[$names['consumer_secret']] ?? ""));
        $this->passKey        = trim((string) ($rows[$names['passkey']] ?? ""));
        $this->shortCode      = trim((string) ($rows[$names['paybill']] ?? ""));
        $this->callbackUrl    = $options['callback_url'] ?? "https://mydomain.com/path";
        $this->cacheToken     = true;
    }

    public function shortCode()
    {
        return $this->shortCode;
    }

    // Daraja token cached until shortly before it expires (hotspot mode only; never logged)
    private function cachedToken($version)
    {
        $key = "mpesa_token_" . $version . "_" . sha1($this->consumerKey . ":" . $this->consumerSecret);
        $token = Cache::get($key);
        if ($token) {
            return (object) ["access_token" => $token];
        }
        $result = $this->getAccessToken($version);
        if (isset($result->access_token)) {
            $seconds = max(60, (int) ($result->expires_in ?? 3599) - 120);
            Cache::put($key, $result->access_token, $seconds);
        }
        return $result;
    }

    /**
     * Daraja STK push query: the result of a push when its callback did not reach us.
     *
     * @return array Daraja reply (ResultCode "0" = paid, "1032" = cancelled, errorCode
     *               "500.001.1001" = still being processed)
     */
    public function stkQuery($checkoutRequestId, $version = 'v1')
    {
        $timestamp = now()->format('YmdHis');
        $token = $this->cachedToken($version);
        if (!isset($token->access_token)) {
            return ["errorMessage" => "Could not generate access token"];
        }
        $response = Http::withToken($token->access_token)->timeout(20)
            ->post('https://api.safaricom.co.ke/mpesa/stkpushquery/' . $version . '/query', [
                "BusinessShortCode" => $this->shortCode,
                "Password"          => base64_encode($this->shortCode . $this->passKey . $timestamp),
                "Timestamp"         => $timestamp,
                "CheckoutRequestID" => $checkoutRequestId,
            ]);
        return $response->json() ?: ["errorMessage" => "HTTP " . $response->status()];
    }

    /**
     * Register the C2B confirmation and validation links for this shortcode (hotspot mode), like
     * mpesa_api::register_url() does for /Transact.
     *
     * @return array Daraja reply
     */
    public function registerC2B($confirmationUrl, $validationUrl)
    {
        $token = $this->cachedToken('v1');
        if (!isset($token->access_token)) {
            return ["errorMessage" => "M-Pesa did not accept the consumer key and secret."];
        }
        $response = Http::withToken($token->access_token)->timeout(30)
            ->post('https://api.safaricom.co.ke/mpesa/c2b/v2/registerurl', [
                "ShortCode"       => $this->shortCode,
                "ResponseType"    => "Completed",
                "ConfirmationURL" => $confirmationUrl,
                "ValidationURL"   => $validationUrl,
            ]);
        return $response->json() ?: ["errorMessage" => "HTTP " . $response->status()];
    }

    /**
     * Generate Access Token
     */
    private function getAccessToken($version = 'v1')
    {
        // send stk push
		$password = base64_encode($this->consumerKey.':'.$this->consumerSecret);
		$headers = [
			'Authorization: Basic '.$password,
			'Content-Type:application/json; charset=utf8'
		];
        
        $headers = ['Content-Type:application/json; charset=utf8'];
        $url = 'https://api.safaricom.co.ke/oauth/'.$version.'/generate?grant_type=client_credentials';
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
     * Check that the organization's consumer key and secret can get a Daraja access token
     * (tries v1 then v2, like initiate_stk). Never returns the token.
     */
    public function checkCredentials()
    {
        foreach (['v1', 'v2'] as $version) {
            $result = $this->getAccessToken($version);
            if (isset($result->access_token)) {
                return ['ok' => true, 'message' => "M-Pesa accepted the consumer key and secret (Daraja $version)."];
            }
        }
        $reason = isset($result->errorMessage) ? $result->errorMessage : (isset($result->error_description) ? $result->error_description : "No access token returned");
        return ['ok' => false, 'message' => "M-Pesa rejected the consumer key and secret: " . $reason];
    }

    /**
     * Initiate STK Push
     */
    public function stkPush($phone, $amount, $accountReference = "TestPayment", $transactionDesc = "Payment", $version = 'v1')
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

        $token = $this->cacheToken ? $this->cachedToken($version) : $this->getAccessToken($version);
        if(isset($token->access_token)){
            $token = $token->access_token;

            $response = Http::withToken($token)
                ->post('https://api.safaricom.co.ke/mpesa/stkpush/'.$version.'/processrequest', $payload);

            return $response->json();
        }else{
            return ["CustomerMessage"=>"Could not generate access token"];
        }
    }
}
