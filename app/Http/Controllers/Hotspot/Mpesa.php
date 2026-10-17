<?php

namespace App\Http\Controllers\Hotspot;

use App\Classes\MpesaService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\login;
use Illuminate\Support\Facades\DB;

class Mpesa extends Controller
{
    const KEYS = ['consumer_key', 'consumer_secret', 'passkey', 'paybill'];

    // "Test M-Pesa credentials": asks Daraja for an access token with the org's keys. Never echoes them.
    function test()
    {
        // change db
        $change_db = new login();
        $change_db->change_db();

        $missing = [];
        foreach (self::KEYS as $key) {
            $value = DB::connection("mysql2")->table("settings")->where("deleted", 0)->where("keyword", $key)->value("value");
            if ($value === null || trim($value) === "") {
                $missing[] = $key;
            }
        }
        if (count($missing) > 0) {
            return response()->json([
                "ok" => false,
                "message" => "These M-Pesa settings are missing: " . implode(", ", $missing) . ". Ask Hypbits to set them for your account.",
            ]);
        }

        try {
            $result = (new MpesaService())->checkCredentials();
        } catch (\Throwable $e) {
            $result = ["ok" => false, "message" => "M-Pesa could not be reached. Try again in a moment."];
        }
        return response()->json($result);
    }
}
