<?php

namespace App\Classes;

use Illuminate\Support\Facades\DB;

/**
 * Read-only access to a cloud router (remote_routers) for the hotspot pages.
 *
 * The router is reached like Router_Cloud::checkActive(): log in to the SSTP server from
 * settings.sstp_server, find the router's tunnel address in /ppp/active, then connect to that
 * address with the router's SSTP credentials. Every connection uses one attempt and a short
 * timeout so an offline router never holds a page up. Router WRITES never happen here: they go
 * through HotspotAgent (crontab hotspot_router_agent.php).
 */
class HotspotRouterService
{
    private $router;
    private $api = null;
    public $error = null;

    public function __construct($router)
    {
        $this->router = $router;
    }

    public static function forRouter($router_id)
    {
        $router = DB::connection("mysql2")->table("remote_routers")->where("router_id", $router_id)->where("deleted", 0)->first();
        return $router ? new self($router) : null;
    }

    private function newClient()
    {
        $client = new HotspotRouterApi();
        $client->debug = false;
        $client->attempts = 1;
        $client->delay = 0;
        $client->timeout = 3;
        return $client;
    }

    public function connect()
    {
        if ($this->api) {
            return $this->api;
        }
        $sstp = DB::connection("mysql2")->table("settings")->where("keyword", "sstp_server")->value("value");
        $sstp = $sstp ? json_decode($sstp) : null;
        if (!$sstp || !isset($sstp->ip_address)) {
            $this->error = "The SSTP server is not set for this account.";
            return null;
        }

        try {
            $server = $this->newClient();
            if (!$server->connect($sstp->ip_address, $sstp->username, $sstp->password, trim($sstp->port))) {
                $this->error = "The VPN server cannot be reached.";
                return null;
            }
            $tunnel_ip = null;
            foreach ($server->comm("/ppp/active/print") as $session) {
                if (($session['name'] ?? '') == $this->router->sstp_username && ($session['service'] ?? '') == "sstp") {
                    $tunnel_ip = $session['address'] ?? null;
                    break;
                }
            }
            $server->disconnect();
            if (!$tunnel_ip) {
                $this->error = "The router is not connected to the VPN.";
                return null;
            }

            $client = $this->newClient();
            if (!$client->connect($tunnel_ip, $this->router->sstp_username, $this->router->sstp_password, $this->router->api_port)) {
                $this->error = "The router is on the VPN but its API does not answer.";
                return null;
            }
            $this->api = $client;
            return $client;
        } catch (\Throwable $e) {
            $this->error = "The connection to the router was lost.";
            return null;
        }
    }

    public function disconnect()
    {
        if ($this->api) {
            $this->api->disconnect();
            $this->api = null;
        }
    }

    private function read($command, $query = [])
    {
        $api = $this->connect();
        if (!$api) {
            return [];
        }
        $result = $api->comm($command, $query);
        return (is_array($result) && !isset($result['!trap'])) ? $result : [];
    }

    public function health()
    {
        $resource = $this->read("/system/resource/print");
        if (!$this->api || count($resource) == 0) {
            return ["online" => false, "error" => $this->error ?? "No answer from the router."];
        }
        return [
            "online" => true,
            "version" => $resource[0]['version'] ?? "",
            "uptime" => $resource[0]['uptime'] ?? "",
            "board" => $resource[0]['board-name'] ?? "",
        ];
    }

    /**
     * What the hotspot pages need to know about the router. Returns null when offline.
     */
    public function detect()
    {
        $health = $this->health();
        if (!$health['online']) {
            return null;
        }

        $interfaces = $this->read("/interface/print");
        $addresses = $this->read("/ip/address/print");
        $routes = $this->read("/ip/route/print", ["?dst-address" => "0.0.0.0/0"]);
        $nat = $this->read("/ip/firewall/nat/print");
        $dns = $this->read("/ip/dns/print");

        $default_route_interfaces = [];
        foreach ($routes as $route) {
            foreach (["immediate-gw", "gateway"] as $field) {
                if (!empty($route[$field]) && preg_match('/%(.+)$/', $route[$field], $m)) {
                    $default_route_interfaces[] = $m[1];
                }
            }
            if (!empty($route['gateway'])) {
                // gateway given as an IP: the interface holding that subnet carries the default route
                foreach ($addresses as $address) {
                    if (isset($address['address'], $address['interface']) && self::inSubnet($route['gateway'], $address['address'])) {
                        $default_route_interfaces[] = $address['interface'];
                    }
                }
            }
        }

        $masquerades = [];
        foreach ($nat as $rule) {
            if (($rule['chain'] ?? '') == "srcnat" && ($rule['action'] ?? '') == "masquerade" && ($rule['disabled'] ?? 'false') != "true") {
                $masquerades[] = ["src-address" => $rule['src-address'] ?? "", "out-interface" => $rule['out-interface'] ?? "", "comment" => $rule['comment'] ?? ""];
            }
        }

        return [
            "health" => $health,
            "interfaces" => $interfaces,
            "addresses" => $addresses,
            "hotspots" => $this->read("/ip/hotspot/print"),
            "hotspot_profiles" => $this->read("/ip/hotspot/profile/print"),
            "pppoe_servers" => $this->read("/interface/pppoe-server/server/print"),
            "bridge_ports" => $this->read("/interface/bridge/port/print"),
            "wireless" => $this->read("/interface/wireless/print"),
            "wifi" => $this->read("/interface/wifi/print"),
            "walled_garden" => $this->read("/ip/hotspot/walled-garden/print"),
            "walled_garden_ip" => $this->read("/ip/hotspot/walled-garden/ip/print"),
            "default_route_interfaces" => array_values(array_unique($default_route_interfaces)),
            "dns_remote_requests" => ($dns[0]['allow-remote-requests'] ?? 'false') == "true",
            "dns_servers" => $dns[0]['servers'] ?? "",
            "masquerades" => $masquerades,
        ];
    }

    /**
     * Interfaces the setup wizard may use: ethernet/bridge/vlan, not the WAN, not the VPN, not
     * already a hotspot or PPPoE server interface, no static address on it (even disabled) and not a bridge port.
     */
    public static function usableInterfaces(array $detect)
    {
        $blocked = $detect['default_route_interfaces'];
        $blocked[] = "ether1";
        foreach ($detect['hotspots'] as $hotspot) {
            $blocked[] = $hotspot['interface'] ?? "";
        }
        foreach ($detect['pppoe_servers'] as $server) {
            $blocked[] = $server['interface'] ?? "";
        }
        // in use already: any static address on it, even disabled (a deactivated static client's
        // gateway comes back when the client is reactivated), or a bridge port
        foreach ($detect['addresses'] as $address) {
            if (($address['dynamic'] ?? 'false') != "true") {
                $blocked[] = $address['interface'] ?? "";
            }
        }
        // a bridge port, and a bridge that already has ports (it carries something), are in use
        foreach ($detect['bridge_ports'] ?? [] as $port) {
            $blocked[] = $port['interface'] ?? "";
            $blocked[] = $port['bridge'] ?? "";
        }
        $usable = [];
        foreach ($detect['interfaces'] as $interface) {
            $name = $interface['name'] ?? "";
            $type = $interface['type'] ?? "";
            if ($name === "" || in_array($name, $blocked) || !in_array($type, ["ether", "bridge", "vlan"])) {
                continue;
            }
            $usable[] = $name;
        }
        return $usable;
    }

    /**
     * Physical Wi-Fi radios the hotspot can broadcast on. "system" is "wireless" (RouterOS v6 and
     * older v7 devices, /interface wireless) or "wifi" (newer v7 devices, /interface wifi).
     * A radio "in use" keeps its network: the hotspot adds a second (virtual) network on it.
     */
    public static function radios(array $detect)
    {
        $radios = [];
        foreach ($detect['wireless'] ?? [] as $row) {
            if (($row['interface-type'] ?? '') == "virtual" || !empty($row['master-interface'])) {
                continue;
            }
            $radios[] = ["name" => $row['name'] ?? "", "system" => "wireless", "ssid" => $row['ssid'] ?? "", "in_use" => ($row['disabled'] ?? 'false') != "true", "band" => $row['band'] ?? ""];
        }
        foreach ($detect['wifi'] ?? [] as $row) {
            if (!empty($row['master-interface'])) {
                continue;
            }
            $radios[] = ["name" => $row['name'] ?? "", "system" => "wifi", "ssid" => $row['configuration.ssid'] ?? ($row['ssid'] ?? ""), "in_use" => ($row['disabled'] ?? 'false') != "true", "band" => $row['configuration.band'] ?? ""];
        }
        return array_values(array_filter($radios, function ($radio) {
            return $radio['name'] !== "";
        }));
    }

    // first 10.5.N.0/24 that doesn't overlap an address on the router
    public static function suggestSubnet(array $addresses)
    {
        for ($n = 50; $n <= 250; $n++) {
            $candidate = "10.5.$n.0/24";
            if (self::subnetError($candidate, $addresses) === null) {
                return $candidate;
            }
        }
        return "10.5.50.0/24";
    }

    /**
     * null when $subnet is a free private /24 (x.y.z.0/24) on the router, else the reason.
     */
    public static function subnetError($subnet, array $addresses)
    {
        if (!preg_match('/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.0\/24$/', trim($subnet), $m) || $m[1] > 255 || $m[2] > 255 || $m[3] > 255) {
            return "Enter the hotspot network as a /24, for example 10.5.50.0/24.";
        }
        $start = ip2long(trim(explode("/", $subnet)[0]));
        $end = $start + 255;
        $private = [["10.0.0.0", 8], ["172.16.0.0", 12], ["192.168.0.0", 16]];
        $is_private = false;
        foreach ($private as $range) {
            if (self::inSubnet(long2ip($start), $range[0] . "/" . $range[1])) {
                $is_private = true;
            }
        }
        if (!$is_private) {
            return "Use a private network (10.x.x.0, 172.16–31.x.0 or 192.168.x.0).";
        }
        foreach ($addresses as $address) {
            if (empty($address['address']) || strpos($address['address'], "/") === false) {
                continue;
            }
            [$ip, $bits] = explode("/", $address['address']);
            $mask = $bits == 0 ? 0 : (-1 << (32 - $bits)) & 0xFFFFFFFF;
            $net_start = ip2long($ip) & $mask;
            $net_end = $net_start + (~$mask & 0xFFFFFFFF);
            if ($start <= $net_end && $end >= $net_start) {
                return "$subnet overlaps " . $address['address'] . " on " . ($address['interface'] ?? "the router") . ".";
            }
        }
        return null;
    }

    public static function inSubnet($ip, $cidr)
    {
        if (strpos($cidr, "/") === false || ip2long($ip) === false) {
            return false;
        }
        [$net, $bits] = explode("/", $cidr);
        $mask = $bits == 0 ? 0 : (-1 << (32 - $bits)) & 0xFFFFFFFF;
        return (ip2long($ip) & $mask) == (ip2long($net) & $mask);
    }
}
