<?php

namespace App\Classes;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Queues a router write for the crontab hotspot router agent (hotspot_router_agent.php) and starts
 * it, fire-and-forget like the SMS agent. The task and its parameters are a
 * hotspot_router_activity row (status "queued"); the agent claims it, runs it under a per-router
 * lock and writes the result back to the same row.
 */
class HotspotAgent
{
    /**
     * Golden rule 5: outside production, router writes only on allow-listed routers
     * (HOTSPOT_ROUTER_WRITE_ALLOWLIST=<db>:<router_id>,...).
     */
    public static function writeAllowed($database, $router_id)
    {
        if (app()->environment('production')) {
            return true;
        }
        $allowed = array_filter(array_map('trim', explode(",", (string) config('hotspot.router_write_allowlist'))));
        return in_array($database . ":" . $router_id, $allowed, true);
    }

    /**
     * $database / $triggered_by: for callers without a session (portal, M-Pesa callbacks); the
     * mysql2 connection must already point at that org DB.
     *
     * @return array ['ok' => bool, 'message' => string, 'activity_id' => int|null]
     */
    public static function queue($router_id, $task, array $params = [], $database = null, $triggered_by = null)
    {
        $database = $database ?: session("database_name");
        if (!self::writeAllowed($database, $router_id)) {
            return ["ok" => false, "activity_id" => null, "message" => "This router is not on the hotspot write allowlist for this environment (HOTSPOT_ROUTER_WRITE_ALLOWLIST), so nothing was sent to it."];
        }

        $activity_id = DB::connection("mysql2")->table("hotspot_router_activity")->insertGetId([
            "router_id" => $router_id,
            "action" => $task,
            "target" => $params['target'] ?? null,
            "status" => "queued",
            "attempts" => 0,
            "message" => json_encode(["params" => $params, "log" => []]),
            "triggered_by" => $triggered_by ?: "admin:" . session("Userid"),
            "date_created" => now()->format('YmdHis'),
        ]);

        $agent_url = config('hotspot.agent_url');
        if (!$agent_url) {
            self::fail($activity_id, "The hotspot agent link (HOTSPOT_AGENT_URL) is not configured.");
            return ["ok" => false, "activity_id" => $activity_id, "message" => "The hotspot agent link is not configured, so the task could not start."];
        }

        $link = $agent_url . (str_contains($agent_url, "?") ? "&" : "?") . http_build_query(["db" => $database, "activity" => $activity_id, "background" => 1]);
        try {
            $response = Http::withOptions(["connect_timeout" => 5, "verify" => false])->timeout(10)->get($link);
            $reply = $response->json();
            if ($response->successful() && is_array($reply) && !empty($reply['success'])) {
                return ["ok" => true, "activity_id" => $activity_id, "message" => "The task has started on the router."];
            }
            $text = trim(strip_tags((string) $response->body()));
            $reason = is_array($reply) && isset($reply['message']) ? $reply['message'] : ("HTTP " . $response->status() . ($text !== "" ? ": " . mb_substr($text, 0, 200) : ", empty reply"));
        } catch (\Throwable $e) {
            $reason = $e->getMessage();
        }

        Log::error("Hotspot router agent could not be started", ["link" => $link, "reason" => $reason]);
        self::fail($activity_id, "The agent could not be started: " . $reason);
        return ["ok" => false, "activity_id" => $activity_id, "message" => "The hotspot agent could not be reached (" . $reason . ")."];
    }

    private static function fail($activity_id, $reason)
    {
        $row = DB::connection("mysql2")->table("hotspot_router_activity")->where("activity_id", $activity_id)->first();
        $message = $row ? json_decode($row->message, true) : [];
        $message['log'][] = ["time" => now()->format('YmdHis'), "step" => "start", "result" => "failed", "detail" => $reason];
        DB::connection("mysql2")->table("hotspot_router_activity")->where("activity_id", $activity_id)
            ->where("status", "queued")
            ->update(["status" => "failed", "message" => json_encode($message)]);
    }
}
