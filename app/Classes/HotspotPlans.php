<?php

namespace App\Classes;

use Illuminate\Support\Facades\DB;

/**
 * Hotspot plans (packages the ISP sells, e.g. KES 20 for 1 hour): unit conversions, the text shown
 * for a plan, and the list the captive portal shows. Each plan is the router user profile
 * "hypbits-hs-<plan_id>" (created by the crontab agent's sync_profiles task).
 */
class HotspotPlans
{
    // minutes per duration unit (a month is 30 days)
    const DURATION_UNITS = ["minutes" => 1, "hours" => 60, "days" => 1440, "weeks" => 10080, "months" => 43200];

    public static function profileName($plan_id)
    {
        return "hypbits-hs-" . $plan_id;
    }

    // active, non-deleted plans in the order the ISP chose: what the captive portal offers
    public static function forPortal()
    {
        return DB::connection("mysql2")->table("hotspot_plans")
            ->where("deleted", 0)->where("is_active", 1)
            ->orderBy("sort_order")->orderBy("price")->orderBy("plan_id")
            ->get();
    }

    // "2M" from 2 + "Mbps", "512k" from 512 + "Kbps"; null when empty (unlimited)
    public static function speed($value, $unit)
    {
        if ($value === null || trim((string) $value) === "") {
            return null;
        }
        $number = rtrim(rtrim(number_format((float) $value, 2, ".", ""), "0"), ".");
        return $number . ($unit === "Kbps" ? "k" : "M");
    }

    // RouterOS rate-limit "rx/tx" from the router's side: rx = what the customer uploads
    public static function rateLimit($upload, $download)
    {
        if ($upload === null && $download === null) {
            return null;
        }
        return ($upload ?? "0") . "/" . ($download ?? "0");
    }

    public static function speedText($speed)
    {
        if (!$speed) {
            return "unlimited";
        }
        return preg_match('/^([\d.]+)k$/i', $speed, $m) ? $m[1] . " Kbps" : rtrim($speed, "Mm") . " Mbps";
    }

    // split a stored speed back into value + unit for the edit form
    public static function speedParts($speed)
    {
        if (!$speed) {
            return ["", "Mbps"];
        }
        return preg_match('/^([\d.]+)k$/i', $speed, $m) ? [$m[1], "Kbps"] : [rtrim($speed, "Mm"), "Mbps"];
    }

    // biggest unit that divides the minutes exactly: 360 -> [6, "hours"]
    public static function durationParts($minutes)
    {
        foreach (array_reverse(self::DURATION_UNITS, true) as $unit => $size) {
            if ($minutes >= $size && $minutes % $size == 0) {
                return [$minutes / $size, $unit];
            }
        }
        return [$minutes, "minutes"];
    }

    public static function durationText($minutes)
    {
        [$value, $unit] = self::durationParts((int) $minutes);
        return $value . " " . ($value == 1 ? rtrim($unit, "s") : $unit);
    }

    public static function dataText($mb)
    {
        if (!$mb) {
            return "Unlimited";
        }
        return $mb % 1024 == 0 ? ($mb / 1024) . " GB" : $mb . " MB";
    }
}
