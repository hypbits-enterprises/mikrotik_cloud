<?php

namespace App\Http\Controllers\Hotspot;

use App\Classes\HotspotAccess;
use App\Classes\HotspotAgent;
use App\Classes\HotspotPortal;
use App\Classes\HotspotRouterService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Hotspot Routers: which cloud routers are online and hotspot-ready, the setup wizard, adopting an
 * existing hotspot server, stop managing and remove. Reads go to the router directly with short
 * timeouts (HotspotRouterService); writes are queued for the crontab agent (HotspotAgent).
 */
class Routers extends Controller
{
    private function useOrgDb()
    {
        $change_db = new login();
        $change_db->change_db();
    }

    private function readOnly()
    {
        return HotspotAccess::readOnly(session("priviledges"), "Hotspot Routers");
    }

    function index()
    {
        $this->useOrgDb();
        $routers = DB::connection("mysql2")->table("remote_routers")->where("deleted", 0)->orderBy("router_name")->get();
        $last_activity = [];
        foreach ($routers as $router) {
            $last_activity[$router->router_id] = DB::connection("mysql2")->table("hotspot_router_activity")
                ->where("router_id", $router->router_id)->orderBy("activity_id", "DESC")->first();
        }
        return view("hotspot.routers", ["routers" => $routers, "last_activity" => $last_activity]);
    }

    // one router's live status, fetched by the list page so an offline router never blocks it
    function status($router_id)
    {
        $this->useOrgDb();
        $service = HotspotRouterService::forRouter($router_id);
        if (!$service) {
            return response()->json(["online" => false, "error" => "Router not found."], 404);
        }
        $health = $service->health();
        if ($health['online']) {
            $api = $service->connect();
            $hotspots = $api ? $api->comm("/ip/hotspot/print") : [];
            $health['hotspot_servers'] = is_array($hotspots) && !isset($hotspots['!trap']) ? array_values(array_map(function ($row) {
                return ($row['name'] ?? "") . " (" . ($row['interface'] ?? "") . ")";
            }, $hotspots)) : [];
        }
        $service->disconnect();
        return response()->json($health);
    }

    function show($router_id)
    {
        $this->useOrgDb();
        $router = DB::connection("mysql2")->table("remote_routers")->where("router_id", $router_id)->where("deleted", 0)->first();
        if (!$router) {
            session()->flash("error_hotspot", "Router not found.");
            return redirect(route("hotspot.routers"));
        }

        $service = new HotspotRouterService($router);
        $detect = $service->detect();
        $offline_reason = $detect ? null : ($service->error ?? "The router did not answer.");
        $service->disconnect();

        return view("hotspot.router", [
            "router" => $router,
            "detect" => $detect,
            "offline_reason" => $offline_reason,
            "usable_interfaces" => $detect ? HotspotRouterService::usableInterfaces($detect) : [],
            "radios" => $detect ? HotspotRouterService::radios($detect) : [],
            "suggested_subnet" => $detect ? HotspotRouterService::suggestSubnet($detect['addresses']) : "10.5.50.0/24",
            "activities" => $this->activities($router_id),
            "write_allowed" => HotspotAgent::writeAllowed(session("database_name"), $router_id),
            "readonly" => $this->readOnly(),
            "portal_host" => config("hotspot.portal_host"),
            "login_https" => (bool) config("hotspot.login_https"),
            "login_page" => $detect ? self::loginPageState($router, $detect) : null,
        ]);
    }

    // which login page the managed hotspot server's profile uses
    private static function loginPageState($router, $detect)
    {
        if (!$router->hotspot_enabled) {
            return null;
        }
        $profile = null;
        foreach ($detect['hotspots'] as $hotspot) {
            if (($hotspot['name'] ?? "") === $router->hotspot_server_name) {
                $profile = $hotspot['profile'] ?? null;
            }
        }
        if (!$profile) {
            return null;
        }
        $directory = "";
        foreach ($detect['hotspot_profiles'] as $row) {
            if (($row['name'] ?? "") === $profile) {
                $directory = $row['html-directory'] ?? "";
            }
        }
        return ["profile" => $profile, "directory" => $directory, "hypbits" => $directory === "hypbits-hs", "adopted" => $profile !== "hypbits-hs"];
    }

    // parameters of the agent task login_files (also passed to setup, which runs it at the end)
    private static function loginFileParams()
    {
        return ["portal_url" => config("hotspot.portal_url"), "files" => HotspotPortal::ROUTER_FILES];
    }

    /**
     * The Hypbits login page on the router: update = download the router files again (Hypbits
     * profile, or an adopted profile already switched); use = switch an adopted server's profile
     * to it; restore = put an adopted server's own login page back.
     */
    function loginPage(Request $request, $router_id)
    {
        $this->useOrgDb();
        if ($this->readOnly()) {
            return $this->back($router_id, "error_hotspot", "You have read-only access to hotspot routers.");
        }
        $router = DB::connection("mysql2")->table("remote_routers")->where("router_id", $router_id)->where("deleted", 0)->first();
        if (!$router || !$router->hotspot_enabled) {
            return $this->back($router_id, "error_hotspot", "Hypbits does not manage a hotspot on this router.");
        }
        $service = new HotspotRouterService($router);
        $detect = $service->detect();
        $service->disconnect();
        $page = $detect ? self::loginPageState($router, $detect) : null;
        if (!$page) {
            return $this->back($router_id, "error_hotspot", $detect ? "The managed hotspot server was not found on the router." : "The router is offline.");
        }
        $mode = $request->input("mode");
        if ($mode === "restore") {
            if (!$page['adopted'] || !$page['hypbits']) {
                return $this->back($router_id, "error_hotspot", "This hotspot server is not using a login page that can be restored.");
            }
            $params = ["target" => "own login page (" . $page['profile'] . ")", "restore" => true];
            $started = "Putting back the server's own login page.";
        } elseif ($mode === "use" || $mode === "update") {
            if ($page['adopted'] && !$page['hypbits'] && $mode !== "use") {
                return $this->back($router_id, "error_hotspot", "This adopted server uses its own login page. Use \"Use the Hypbits login page\" to switch it.");
            }
            $params = array_merge(["target" => "Hypbits login page (" . $page['profile'] . ")", "adopted" => $page['adopted']], self::loginFileParams());
            $started = "Installing the Hypbits login page on the router.";
        } else {
            return $this->back($router_id, "error_hotspot", "Unknown login page action.");
        }
        $result = HotspotAgent::queue($router_id, "login_files", $params);
        return $this->back($router_id, $result['ok'] ? "success_hotspot" : "error_hotspot", $result['ok'] ? $started . " Progress shows in the activity log below." : $result['message']);
    }

    function setup(Request $request, $router_id)
    {
        $this->useOrgDb();
        if ($this->readOnly()) {
            return $this->back($router_id, "error_hotspot", "You have read-only access to hotspot routers.");
        }
        $interface = trim((string) $request->input("interface"));
        $subnet = trim((string) $request->input("subnet"));
        $dns_name = strtolower(trim((string) $request->input("dns_name")));
        if ($dns_name !== "" && !preg_match('/^(?=.{1,100}$)([a-z0-9-]+\.)+[a-z]{2,}$/', $dns_name)) {
            return $this->back($router_id, "error_hotspot", "The login page name must look like a domain, e.g. wifi.yourisp.co.ke, or be left empty.");
        }
        $login_https = (bool) config("hotspot.login_https");
        if ($login_https && $dns_name === "") {
            return $this->back($router_id, "error_hotspot", "Enter a login page name (e.g. wifi.yourisp.co.ke): the login page runs over HTTPS and the certificate is issued for that name.");
        }

        $service = HotspotRouterService::forRouter($router_id);
        $detect = $service ? $service->detect() : null;
        if ($service) {
            $service->disconnect();
        }
        if (!$detect) {
            return $this->back($router_id, "error_hotspot", "The router is offline, so the hotspot cannot be set up now.");
        }
        // Wi-Fi: broadcast an open hotspot network on a radio; with a wired port too, both go in one
        // Hypbits bridge so wired and Wi-Fi customers share the hotspot
        $wifi = $request->boolean("wifi");
        $radio = trim((string) $request->input("radio"));
        $ssid = trim((string) $request->input("ssid"));
        $radios = HotspotRouterService::radios($detect);
        $radio_info = null;
        foreach ($radios as $candidate) {
            if ($candidate['name'] === $radio) {
                $radio_info = $candidate;
            }
        }
        if ($wifi) {
            if (!$radio_info) {
                return $this->back($router_id, "error_hotspot", "Choose the router's Wi-Fi radio for the hotspot network.");
            }
            if ($ssid === "" || mb_strlen($ssid) > 32 || !preg_match('/^[\x20-\x7E]+$/', $ssid)) {
                return $this->back($router_id, "error_hotspot", "Enter a Wi-Fi network name of 1 to 32 letters, numbers or symbols.");
            }
        }
        if ($interface === "" && !$wifi) {
            return $this->back($router_id, "error_hotspot", "Choose a wired port, a Wi-Fi network, or both.");
        }
        if ($interface !== "" && !in_array($interface, HotspotRouterService::usableInterfaces($detect), true)) {
            return $this->back($router_id, "error_hotspot", "Choose a free interface for the hotspot. Interfaces already in use (WAN, VPN, PPPoE, bridges with clients or ports, bridge ports) can't be used.");
        }
        $subnet_error = HotspotRouterService::subnetError($subnet, $detect['addresses']);
        if ($subnet_error) {
            return $this->back($router_id, "error_hotspot", $subnet_error);
        }

        $dry_run = $request->boolean("dry_run");
        $on = $wifi ? "hypbits-hs-bridge (" . trim(($interface !== "" ? $interface . " + " : "") . "Wi-Fi \"" . $ssid . "\"") . ")" : $interface;
        $result = HotspotAgent::queue($router_id, "setup", [
            "target" => "hypbits-hs on " . $on . ($dry_run ? " (dry run)" : ""),
            "dry_run" => $dry_run,
            "interface" => $interface,
            "wifi" => $wifi,
            "radio" => $wifi ? $radio : "",
            "radio_system" => $wifi ? $radio_info['system'] : "",
            "radio_in_use" => $wifi ? $radio_info['in_use'] : false,
            "ssid" => $wifi ? $ssid : "",
            "subnet" => $subnet,
            "dns_name" => $dns_name,
            "allow_dns" => $request->boolean("allow_dns"),
            "add_masquerade" => $request->boolean("add_masquerade"),
            "portal_host" => config("hotspot.portal_host"),
            "login_https" => $login_https,
        ] + self::loginFileParams());
        $started = $dry_run ? "Dry run started: the activity log below lists the commands it would run. Nothing is changed on the router." : "Hotspot setup started. Progress shows in the activity log below.";
        return $this->back($router_id, $result['ok'] ? "success_hotspot" : "error_hotspot", $result['ok'] ? $started : $result['message']);
    }

    function adopt(Request $request, $router_id)
    {
        $this->useOrgDb();
        if ($this->readOnly()) {
            return $this->back($router_id, "error_hotspot", "You have read-only access to hotspot routers.");
        }
        $server = trim((string) $request->input("server"));
        $service = HotspotRouterService::forRouter($router_id);
        $detect = $service ? $service->detect() : null;
        if ($service) {
            $service->disconnect();
        }
        $names = $detect ? array_column($detect['hotspots'], "name") : [];
        if (!in_array($server, $names, true)) {
            return $this->back($router_id, "error_hotspot", $detect ? "That hotspot server was not found on the router." : "The router is offline.");
        }
        $result = HotspotAgent::queue($router_id, "adopt", [
            "target" => $server,
            "server" => $server,
            "portal_host" => config("hotspot.portal_host"),
        ]);
        return $this->back($router_id, $result['ok'] ? "success_hotspot" : "error_hotspot", $result['ok'] ? "Adopting " . $server . ". Progress shows in the activity log below." : $result['message']);
    }

    function remove(Request $request, $router_id)
    {
        $this->useOrgDb();
        if ($this->readOnly()) {
            return $this->back($router_id, "error_hotspot", "You have read-only access to hotspot routers.");
        }
        if ($request->input("confirm") !== "REMOVE") {
            return $this->back($router_id, "error_hotspot", "Type REMOVE to confirm removing the Hypbits hotspot from the router.");
        }
        if (!HotspotRouterService::forRouter($router_id)) {
            return $this->back($router_id, "error_hotspot", "Router not found.");
        }
        $result = HotspotAgent::queue($router_id, "remove", ["target" => "Hypbits hotspot objects"]);
        return $this->back($router_id, $result['ok'] ? "success_hotspot" : "error_hotspot", $result['ok'] ? "Removing the Hypbits hotspot objects. Progress shows in the activity log below." : $result['message']);
    }

    /**
     * Delete a hotspot server that Hypbits did not create (e.g. one left by another hotspot
     * product). Only the server itself is deleted: its profile, pool, DHCP server and users may be
     * shared with other things on the router, so they stay. The Hypbits server is removed with
     * "Remove from router" instead, and a server Hypbits manages must be released first.
     */
    function deleteServer(Request $request, $router_id)
    {
        $this->useOrgDb();
        if ($this->readOnly()) {
            return $this->back($router_id, "error_hotspot", "You have read-only access to hotspot routers.");
        }
        $server = trim((string) $request->input("server"));
        if ($server === "" || $request->input("confirm") !== $server) {
            return $this->back($router_id, "error_hotspot", "Type the hotspot server's name exactly to confirm deleting it.");
        }
        if ($server === "hypbits-hs") {
            return $this->back($router_id, "error_hotspot", "The Hypbits hotspot is removed with \"Remove from router\".");
        }
        $router = DB::connection("mysql2")->table("remote_routers")->where("router_id", $router_id)->where("deleted", 0)->first();
        if (!$router) {
            return $this->back($router_id, "error_hotspot", "Router not found.");
        }
        if ($router->hotspot_enabled && $router->hotspot_server_name === $server) {
            return $this->back($router_id, "error_hotspot", "Hypbits manages this hotspot server. Stop managing it first.");
        }
        $service = new HotspotRouterService($router);
        $detect = $service->detect();
        $service->disconnect();
        if (!$detect) {
            return $this->back($router_id, "error_hotspot", "The router is offline.");
        }
        if (!in_array($server, array_column($detect['hotspots'], "name"), true)) {
            return $this->back($router_id, "error_hotspot", "That hotspot server was not found on the router.");
        }
        $result = HotspotAgent::queue($router_id, "delete_server", ["target" => $server, "server" => $server]);
        return $this->back($router_id, $result['ok'] ? "success_hotspot" : "error_hotspot", $result['ok'] ? "Deleting hotspot server " . $server . ". Progress shows in the activity log below." : $result['message']);
    }

    // stop managing: nothing changes on the router
    function stop($router_id)
    {
        $this->useOrgDb();
        if ($this->readOnly()) {
            return $this->back($router_id, "error_hotspot", "You have read-only access to hotspot routers.");
        }
        $now = now()->format('YmdHis');
        $updated = DB::connection("mysql2")->table("remote_routers")->where("router_id", $router_id)->where("deleted", 0)
            ->update(["hotspot_enabled" => 0, "date_changed" => $now]);
        if (!$updated) {
            return $this->back($router_id, "error_hotspot", "Router not found.");
        }
        DB::connection("mysql2")->table("hotspot_router_activity")->insert([
            "router_id" => $router_id,
            "action" => "stop",
            "target" => null,
            "status" => "success",
            "attempts" => 0,
            "message" => json_encode(["params" => [], "log" => [["time" => $now, "step" => "stop", "result" => "ok", "detail" => "Stopped managing the hotspot. Nothing was changed on the router."]]]),
            "triggered_by" => "admin:" . session("Userid"),
            "date_created" => $now,
        ]);
        return $this->back($router_id, "success_hotspot", "Hypbits no longer manages the hotspot on this router. Nothing was changed on the router.");
    }

    function activity(Request $request, $router_id)
    {
        $this->useOrgDb();
        if ($request->boolean("html")) {
            return view("hotspot.partials.router-activity", ["activities" => $this->activities($router_id)]);
        }
        return response()->json(["activities" => $this->activities($router_id)]);
    }

    private function activities($router_id)
    {
        $rows = DB::connection("mysql2")->table("hotspot_router_activity")->where("router_id", $router_id)
            ->orderBy("activity_id", "DESC")->limit(20)->get();
        return $rows->map(function ($row) {
            $message = json_decode($row->message, true);
            return [
                "id" => $row->activity_id,
                "action" => $row->action,
                "target" => $row->target,
                "status" => $row->status,
                "attempts" => $row->attempts,
                "when" => $row->date_created ? date("D jS M Y g:i:sA", strtotime($row->date_created)) : "",
                "log" => is_array($message) && isset($message['log']) ? $message['log'] : [],
            ];
        })->all();
    }

    private function back($router_id, $key, $message)
    {
        session()->flash($key, $message);
        return redirect(route("hotspot.router", $router_id));
    }
}
