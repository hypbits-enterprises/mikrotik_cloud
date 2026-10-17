<?php

namespace App\Http\Controllers\Hotspot;

use App\Http\Controllers\Controller;
use App\Http\Controllers\login;
use Illuminate\Support\Facades\DB;

class Dashboard extends Controller
{
    function index()
    {
        // change db
        $change_db = new login();
        $change_db->change_db();

        $today_start = now()->format('Ymd') . '000000';
        $today_end = now()->format('Ymd') . '235959';

        $todays_sales = DB::connection("mysql2")->table("hotspot_payments")
            ->where("deleted", 0)->where("status", "paid")
            ->whereBetween("paid_at", [$today_start, $today_end])
            ->sum("amount");

        $active_users = DB::connection("mysql2")->table("hotspot_tickets")
            ->where("deleted", 0)->where("status", "active")
            ->count();

        $tickets_today = DB::connection("mysql2")->table("hotspot_tickets")
            ->where("deleted", 0)
            ->whereBetween("date_created", [$today_start, $today_end])
            ->count();

        $hotspot_routers = DB::connection("mysql2")->table("remote_routers")
            ->where("deleted", 0)->where("hotspot_enabled", 1)
            ->count();

        return view("hotspot.dashboard", [
            "todays_sales" => $todays_sales,
            "active_users" => $active_users,
            "tickets_today" => $tickets_today,
            "hotspot_routers" => $hotspot_routers,
        ]);
    }
}
