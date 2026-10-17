<?php

namespace App\Classes;

use App\Http\Controllers\login;
use Illuminate\Support\Facades\DB;

/**
 * Captive portal /hs/{hotspot_key}: finds the org and router for a key without a session (golden
 * rule 12: explicit, fails closed), the branding to show, and the router files that send customers
 * from the router's hotspot to the portal.
 */
class HotspotPortal
{
    // files the agent downloads into the router's hypbits-hs html directory
    const ROUTER_FILES = ["login.html", "alogin.html", "status.html", "logout.html", "error.html", "redirect.html", "rlogin.html"];

    // portal logos: small copies under public/hotspot/logos (phones load them before they are online)
    const LOGO_DIR = "hotspot/logos";
    const LOGO_MAX_WIDTH = 320;
    const LOGO_MAX_HEIGHT = 160;

    const DEFAULT_PRIMARY = "#1e9ff2";
    const DEFAULT_SECONDARY = "#0f2a44";

    /**
     * @return array|null ['organization' => row, 'router' => row]; the mysql2 connection then points
     *                    at the org's database. Null when the key is unknown, the org has hotspot
     *                    off or the router is not managed.
     */
    public static function resolve($key)
    {
        if (!is_string($key) || !preg_match('/^[a-f0-9]{16,40}$/', $key)) {
            return null;
        }
        $index = DB::table("hotspot_router_keys")->where("hotspot_key", $key)->first();
        if (!$index) {
            return null;
        }
        $organization = DB::table("organizations")->where("organization_id", $index->organization_id)->first();
        if (!$organization || !$organization->organization_database) {
            return null;
        }
        $enabled = DB::table("org_hotspot_settings")->where("organization_id", $organization->organization_id)->value("hotspot_enabled");
        if ((int) $enabled !== 1) {
            return null;
        }
        (new login())->change_db($organization->organization_database);
        $router = DB::connection("mysql2")->table("remote_routers")
            ->where("router_id", $index->router_id)->where("hotspot_key", $key)
            ->where("hotspot_enabled", 1)->where("deleted", 0)->first();
        if (!$router) {
            return null;
        }
        return ["organization" => $organization, "router" => $router];
    }

    /**
     * Branding for a router: its own row's fields over the org default row (router_id NULL), over
     * built-in defaults. Call with mysql2 on the org DB.
     */
    public static function branding($organization, $router_id)
    {
        $rows = DB::connection("mysql2")->table("hotspot_login_pages")->where("deleted", 0)
            ->where(function ($query) use ($router_id) {
                $query->whereNull("router_id")->orWhere("router_id", $router_id);
            })->get();
        $default = $rows->firstWhere("router_id", null);
        $own = $router_id ? $rows->firstWhere("router_id", $router_id) : null;
        $branding = self::builtIn($organization);
        foreach ([$default, $own] as $row) {
            if (!$row) {
                continue;
            }
            foreach (array_keys($branding) as $field) {
                if ($row->$field !== null && trim((string) $row->$field) !== "") {
                    $branding[$field] = $row->$field;
                }
            }
        }
        return $branding;
    }

    // what the portal shows when the ISP has set nothing
    public static function builtIn($organization)
    {
        return [
            "logo_path" => self::orgLogo($organization),
            "primary_color" => self::DEFAULT_PRIMARY,
            "secondary_color" => self::DEFAULT_SECONDARY,
            "title" => $organization->organization_name . " WiFi",
            "welcome_text" => "Choose a package and pay with M-Pesa to get online.",
            "support_phone" => $organization->organization_main_contact,
            "redirect_url" => null,
        ];
    }

    // the org's profile logo (Account and Profile), as a small copy, until the ISP uploads one
    public static function orgLogo($organization)
    {
        $source = ltrim((string) $organization->organization_logo, "/");
        if ($source === "" || str_contains($source, "..") || !is_file(public_path($source))) {
            return null;
        }
        $name = "org-" . (int) $organization->organization_id . "-" . filemtime(public_path($source)) . ".png";
        if (is_file(public_path(self::LOGO_DIR . "/" . $name))) {
            return self::LOGO_DIR . "/" . $name;
        }
        foreach (glob(public_path(self::LOGO_DIR . "/org-" . (int) $organization->organization_id . "-*.png")) ?: [] as $old) {
            @unlink($old);
        }
        return self::smallLogo(public_path($source), $name);
    }

    /**
     * Save an image as a PNG of at most LOGO_MAX_WIDTH x LOGO_MAX_HEIGHT (transparency kept) in
     * public/hotspot/logos.
     *
     * @return string|null path relative to public/, or null when the file is not a usable image
     */
    public static function smallLogo($file, $name)
    {
        $info = @getimagesize($file);
        if (!$info || !in_array($info[2], [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_WEBP], true)) {
            return null;
        }
        $image = $info[2] === IMAGETYPE_PNG ? @imagecreatefrompng($file) : ($info[2] === IMAGETYPE_JPEG ? @imagecreatefromjpeg($file) : @imagecreatefromwebp($file));
        if (!$image) {
            return null;
        }
        [$width, $height] = [$info[0], $info[1]];
        $scale = min(1, self::LOGO_MAX_WIDTH / $width, self::LOGO_MAX_HEIGHT / $height);
        $new_width = max(1, (int) round($width * $scale));
        $new_height = max(1, (int) round($height * $scale));
        $small = imagecreatetruecolor($new_width, $new_height);
        imagealphablending($small, false);
        imagesavealpha($small, true);
        imagefill($small, 0, 0, imagecolorallocatealpha($small, 0, 0, 0, 127));
        imagecopyresampled($small, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        if (!is_dir(public_path(self::LOGO_DIR))) {
            mkdir(public_path(self::LOGO_DIR), 0775, true);
        }
        $saved = imagepng($small, public_path(self::LOGO_DIR . "/" . $name), 9);
        imagedestroy($image);
        imagedestroy($small);
        return $saved ? self::LOGO_DIR . "/" . $name : null;
    }

    /**
     * The router's login URL (from $(link-login-only)) is used as the form target for the ticket
     * code, so only accept the router's own hotspot address: its login page name or a private
     * IPv4 address, path /login.
     */
    public static function loginUrlAllowed($url, $router)
    {
        $parts = parse_url((string) $url);
        if (!$parts || !isset($parts['scheme'], $parts['host']) || !in_array($parts['scheme'], ["http", "https"], true)) {
            return false;
        }
        if (($parts['path'] ?? "") !== "/login" || isset($parts['user']) || isset($parts['pass'])) {
            return false;
        }
        $host = strtolower($parts['host']);
        if ($router->hotspot_dns_name && $host === strtolower($router->hotspot_dns_name)) {
            return true;
        }
        return filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false
            && filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE) === false
            && !str_starts_with($host, "127.");
    }

    // the router's logout URL ($(link-logout)): same rules, path /logout
    public static function logoutUrlAllowed($url, $router)
    {
        return self::loginUrlAllowed(preg_replace('#/logout(\?.*)?$#', '/login', (string) $url), $router)
            && preg_match('#/logout(\?.*)?$#', (string) $url);
    }

    // where the customer may continue to after login: only plain http(s) links
    public static function safeLink($url)
    {
        $url = trim((string) $url);
        return preg_match('#^https?://[^\s"<>]+$#i', $url) ? $url : null;
    }

    /**
     * Whether a ticket code can log in on this router.
     *
     * @return array [ok, message, ticket]
     */
    public static function checkCode($code, $router)
    {
        $code = strtoupper(preg_replace('/\s+/', '', (string) $code));
        if (!preg_match('/^[A-Z0-9]{4,12}$/', $code)) {
            return [false, "Enter the ticket code exactly as you received it.", null];
        }
        $ticket = DB::connection("mysql2")->table("hotspot_tickets")->where("ticket_code", $code)->where("deleted", 0)->first();
        if (!$ticket) {
            return [false, "That ticket code was not found. Check it and try again.", null];
        }
        if ((int) $ticket->router_id !== (int) $router->router_id) {
            return [false, "That ticket is for another hotspot.", null];
        }
        if ($ticket->status === "used") {
            return [false, "That ticket has been used up. Buy a new package to continue.", null];
        }
        if ($ticket->status === "expired" || ($ticket->expires_at && $ticket->expires_at <= now()->format('YmdHis'))) {
            return [false, "That ticket has expired. Buy a new package to continue.", null];
        }
        if ($ticket->status === "failed") {
            return [false, "That ticket could not be activated. Contact support.", null];
        }
        if ($ticket->provision_status !== "done") {
            return [false, "Your ticket is being activated. Try again in a minute.", null];
        }
        return [true, "", $ticket];
    }
}
