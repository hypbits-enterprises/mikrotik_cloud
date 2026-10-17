<?php

namespace App\Http\Controllers\Hotspot;

use App\Classes\HotspotAccess;
use App\Classes\HotspotAgent;
use App\Classes\HotspotPlans;
use App\Http\Controllers\Controller;
use App\Http\Controllers\login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Hotspot plans: the packages the ISP sells. Every change is pushed to the routers Hypbits manages
 * as the user profile hypbits-hs-<plan_id> by the crontab agent (task sync_profiles).
 */
class Plans extends Controller
{
    private function useOrgDb()
    {
        $change_db = new login();
        $change_db->change_db();
    }

    private function readOnly()
    {
        return HotspotAccess::readOnly(session("priviledges"), "Hotspot Plans");
    }

    private function managedRouters()
    {
        return DB::connection("mysql2")->table("remote_routers")->where("deleted", 0)->where("hotspot_enabled", 1)->orderBy("router_name")->get();
    }

    function index()
    {
        $this->useOrgDb();
        $plans = DB::connection("mysql2")->table("hotspot_plans")->where("deleted", 0)
            ->orderBy("sort_order")->orderBy("price")->orderBy("plan_id")->get();
        $routers = $this->managedRouters();

        // latest sync task per router: decides "pending" / "failed" when a plan is newer than the last good sync
        $last_sync_task = [];
        foreach ($routers as $router) {
            $last_sync_task[$router->router_id] = DB::connection("mysql2")->table("hotspot_router_activity")
                ->where("router_id", $router->router_id)->whereIn("action", ["sync_profiles", "setup", "adopt"])
                ->orderBy("activity_id", "DESC")->first();
        }

        return view("hotspot.plans", [
            "plans" => $plans,
            "routers" => $routers,
            "last_sync_task" => $last_sync_task,
            "readonly" => $this->readOnly(),
        ]);
    }

    function create()
    {
        return view("hotspot.plan-form", ["plan" => null, "readonly" => $this->readOnly()]);
    }

    function edit($plan_id)
    {
        $this->useOrgDb();
        $plan = DB::connection("mysql2")->table("hotspot_plans")->where("plan_id", $plan_id)->where("deleted", 0)->first();
        if (!$plan) {
            session()->flash("error_hotspot", "Plan not found.");
            return redirect(route("hotspot.plans"));
        }
        return view("hotspot.plan-form", ["plan" => $plan, "readonly" => $this->readOnly()]);
    }

    function store(Request $request)
    {
        return $this->save($request, null);
    }

    function update(Request $request, $plan_id)
    {
        return $this->save($request, $plan_id);
    }

    private function save(Request $request, $plan_id)
    {
        $this->useOrgDb();
        $back = $plan_id ? route("hotspot.plan_edit", $plan_id) : route("hotspot.plan_new");
        if ($this->readOnly()) {
            session()->flash("error_hotspot", "You have read-only access to hotspot plans.");
            return redirect($back);
        }
        if ($plan_id && !DB::connection("mysql2")->table("hotspot_plans")->where("plan_id", $plan_id)->where("deleted", 0)->exists()) {
            session()->flash("error_hotspot", "Plan not found.");
            return redirect(route("hotspot.plans"));
        }

        $values = $this->validatePlan($request, $plan_id);
        if (is_string($values)) {
            session()->flash("error_hotspot", $values);
            return redirect($back)->withInput();
        }

        $now = now()->format('YmdHis');
        if ($plan_id) {
            DB::connection("mysql2")->table("hotspot_plans")->where("plan_id", $plan_id)->update(array_merge($values, ["date_changed" => $now]));
            $message = "Plan \"" . $values['plan_name'] . "\" saved.";
        } else {
            DB::connection("mysql2")->table("hotspot_plans")->insert(array_merge($values, ["date_created" => $now, "date_changed" => $now, "deleted" => 0]));
            $message = "Plan \"" . $values['plan_name'] . "\" created.";
        }
        return $this->syncAndBack($message);
    }

    function toggle($plan_id)
    {
        $this->useOrgDb();
        if ($this->readOnly()) {
            return $this->backToList("error_hotspot", "You have read-only access to hotspot plans.");
        }
        $plan = DB::connection("mysql2")->table("hotspot_plans")->where("plan_id", $plan_id)->where("deleted", 0)->first();
        if (!$plan) {
            return $this->backToList("error_hotspot", "Plan not found.");
        }
        $active = $plan->is_active ? 0 : 1;
        DB::connection("mysql2")->table("hotspot_plans")->where("plan_id", $plan_id)->update(["is_active" => $active, "date_changed" => now()->format('YmdHis')]);
        return $this->syncAndBack($active
            ? "\"" . $plan->plan_name . "\" is active: customers can buy it."
            : "\"" . $plan->plan_name . "\" is inactive: it is hidden from customers. Tickets already sold keep working.");
    }

    function destroy($plan_id)
    {
        $this->useOrgDb();
        if ($this->readOnly()) {
            return $this->backToList("error_hotspot", "You have read-only access to hotspot plans.");
        }
        $plan = DB::connection("mysql2")->table("hotspot_plans")->where("plan_id", $plan_id)->where("deleted", 0)->first();
        if (!$plan) {
            return $this->backToList("error_hotspot", "Plan not found.");
        }
        DB::connection("mysql2")->table("hotspot_plans")->where("plan_id", $plan_id)->update(["deleted" => 1, "is_active" => 0, "date_changed" => now()->format('YmdHis')]);
        return $this->syncAndBack("\"" . $plan->plan_name . "\" deleted. Its router profile is removed once no ticket uses it.");
    }

    function sync()
    {
        $this->useOrgDb();
        if ($this->readOnly()) {
            return $this->backToList("error_hotspot", "You have read-only access to hotspot plans.");
        }
        return $this->syncAndBack("Sync started.");
    }

    // push the plans to every managed router; the plan change itself is already saved
    private function syncAndBack($message)
    {
        $routers = $this->managedRouters();
        if (count($routers) == 0) {
            return $this->backToList("success_hotspot", $message . " No router is managed by Hypbits yet, so nothing was sent to a router.");
        }
        $problems = [];
        foreach ($routers as $router) {
            $result = HotspotAgent::queue($router->router_id, "sync_profiles", ["target" => "plans"]);
            if (!$result['ok']) {
                $problems[] = $router->router_name . ": " . $result['message'];
            }
        }
        if (count($problems) > 0) {
            session()->flash("error_hotspot", "Not sent to every router — " . implode(" ", $problems));
        }
        return $this->backToList("success_hotspot", $message . " Updating " . (count($routers) - count($problems)) . " router(s).");
    }

    private function validatePlan(Request $request, $plan_id)
    {
        $name = trim((string) $request->input("plan_name"));
        if ($name === "" || mb_strlen($name) > 100) {
            return "Enter a plan name (up to 100 characters).";
        }
        $exists = DB::connection("mysql2")->table("hotspot_plans")->where("deleted", 0)->where("plan_name", $name)
            ->when($plan_id, function ($query) use ($plan_id) {
                return $query->where("plan_id", "!=", $plan_id);
            })->exists();
        if ($exists) {
            return "There is already a plan called \"" . $name . "\".";
        }

        $price = $request->input("price");
        if (!is_numeric($price) || $price <= 0) {
            return "The price must be more than 0.";
        }
        $duration = $request->input("duration_value");
        $unit = $request->input("duration_unit");
        if (!is_numeric($duration) || $duration <= 0 || floor($duration) != $duration || !array_key_exists($unit, HotspotPlans::DURATION_UNITS)) {
            return "The duration must be a whole number more than 0, in minutes, hours, days, weeks or months.";
        }
        $minutes = (int) $duration * HotspotPlans::DURATION_UNITS[$unit];

        $validity = $request->input("validity_type");
        if (!in_array($validity, ["paused", "elapsed"], true)) {
            return "Choose how the time is counted.";
        }

        $speeds = [];
        foreach (["upload", "download"] as $direction) {
            $value = $request->input($direction . "_speed");
            $speed_unit = $request->input($direction . "_unit", "Mbps");
            if ($value !== null && trim((string) $value) !== "" && (!is_numeric($value) || $value <= 0 || !in_array($speed_unit, ["Kbps", "Mbps"], true))) {
                return "The " . $direction . " speed must be more than 0 (in Kbps or Mbps), or empty for no limit.";
            }
            $speeds[$direction] = HotspotPlans::speed($value, $speed_unit);
        }

        $data = $request->input("data_limit");
        $data_unit = $request->input("data_unit", "MB");
        $data_mb = null;
        if ($data !== null && trim((string) $data) !== "") {
            if (!is_numeric($data) || $data <= 0 || !in_array($data_unit, ["MB", "GB"], true)) {
                return "The data limit must be more than 0 (in MB or GB), or empty for unlimited.";
            }
            $data_mb = (int) round($data_unit === "GB" ? $data * 1024 : $data);
        }

        $devices = $request->input("shared_users");
        if (!is_numeric($devices) || $devices < 1 || $devices > 10 || floor($devices) != $devices) {
            return "Devices per ticket must be between 1 and 10.";
        }
        $order = $request->input("sort_order", 0);
        if (!is_numeric($order)) {
            $order = 0;
        }

        return [
            "plan_name" => $name,
            "price" => round($price, 2),
            "duration_minutes" => $minutes,
            "validity_type" => $validity,
            "upload_speed" => $speeds['upload'],
            "download_speed" => $speeds['download'],
            "rate_limit" => HotspotPlans::rateLimit($speeds['upload'], $speeds['download']),
            "data_limit_mb" => $data_mb,
            "shared_users" => (int) $devices,
            "is_active" => $request->boolean("is_active") ? 1 : 0,
            "sort_order" => (int) $order,
        ];
    }

    private function backToList($key, $message)
    {
        session()->flash($key, $message);
        return redirect(route("hotspot.plans"));
    }
}
