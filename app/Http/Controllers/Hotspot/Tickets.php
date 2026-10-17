<?php

namespace App\Http\Controllers\Hotspot;

use App\Classes\HotspotAccess;
use App\Classes\HotspotAgent;
use App\Classes\HotspotTickets;
use App\Http\Controllers\Controller;
use App\Http\Controllers\login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Hotspot tickets: the tickets list (bought and manual), manual (cash) tickets, resending a code by
 * SMS and ending a ticket early.
 */
class Tickets extends Controller
{
    const LIST_LIMIT = 300;

    private function useOrgDb()
    {
        $change_db = new login();
        $change_db->change_db();
    }

    private function readOnly()
    {
        return HotspotAccess::readOnly(session("priviledges"), "Hotspot Tickets");
    }

    function index(Request $request)
    {
        $this->useOrgDb();
        $date = $request->input("date", now()->format('Y-m-d'));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date)) {
            $date = now()->format('Y-m-d');
        }
        $day = str_replace("-", "", $date);
        $filters = [
            "date" => $date,
            "status" => $request->input("status", ""),
            "router_id" => $request->input("router_id", ""),
            "plan_id" => $request->input("plan_id", ""),
            "source" => $request->input("source", ""),
            "q" => trim((string) $request->input("q", "")),
        ];

        $base = DB::connection("mysql2")->table("hotspot_tickets AS t")
            ->leftJoin("hotspot_plans AS p", "p.plan_id", "=", "t.plan_id")
            ->leftJoin("remote_routers AS r", "r.router_id", "=", "t.router_id")
            ->where("t.deleted", 0)
            ->whereBetween("t.date_created", [$day . "000000", $day . "235959"]);
        foreach (["router_id" => "t.router_id", "plan_id" => "t.plan_id", "source" => "t.source"] as $key => $column) {
            if ($filters[$key] !== "") {
                $base->where($column, $filters[$key]);
            }
        }
        if ($filters['q'] !== "") {
            $q = $filters['q'];
            $base->where(function ($query) use ($q) {
                $query->where("t.ticket_code", "like", "%" . $q . "%")->orWhere("t.phone", "like", "%" . $q . "%");
            });
        }

        $counts = (clone $base)->select("t.status", DB::raw("COUNT(*) AS n"), DB::raw("SUM(t.amount) AS total"))->groupBy("t.status")->get()->keyBy("status");
        $list = clone $base;
        if (in_array($filters['status'], HotspotTickets::STATUSES, true)) {
            $list->where("t.status", $filters['status']);
        }
        $tickets = $list->select("t.*", "p.plan_name", "p.duration_minutes", "p.validity_type", "p.data_limit_mb", "r.router_name")
            ->orderBy("t.ticket_id", "DESC")->limit(self::LIST_LIMIT)->get();

        return view("hotspot.tickets", [
            "tickets" => $tickets,
            "counts" => $counts,
            "filters" => $filters,
            "routers" => DB::connection("mysql2")->table("remote_routers")->where("deleted", 0)->orderBy("router_name")->get(),
            "plans" => DB::connection("mysql2")->table("hotspot_plans")->where("deleted", 0)->orderBy("sort_order")->get(),
            "limit" => self::LIST_LIMIT,
            "readonly" => $this->readOnly(),
        ]);
    }

    function create()
    {
        $this->useOrgDb();
        return view("hotspot.ticket-form", [
            "plans" => DB::connection("mysql2")->table("hotspot_plans")->where("deleted", 0)->orderBy("sort_order")->orderBy("price")->get(),
            "routers" => DB::connection("mysql2")->table("remote_routers")->where("deleted", 0)->where("hotspot_enabled", 1)->orderBy("router_name")->get(),
            "readonly" => $this->readOnly(),
        ]);
    }

    // manual (cash) tickets
    function store(Request $request)
    {
        $this->useOrgDb();
        $back = redirect(route("hotspot.ticket_new"))->withInput();
        if ($this->readOnly()) {
            session()->flash("error_hotspot", "You have read-only access to hotspot tickets.");
            return $back;
        }
        $plan = DB::connection("mysql2")->table("hotspot_plans")->where("plan_id", $request->input("plan_id"))->where("deleted", 0)->first();
        if (!$plan) {
            session()->flash("error_hotspot", "Choose a plan.");
            return $back;
        }
        $router = DB::connection("mysql2")->table("remote_routers")->where("router_id", $request->input("router_id"))->where("deleted", 0)->where("hotspot_enabled", 1)->first();
        if (!$router) {
            session()->flash("error_hotspot", "Choose a router that Hypbits manages the hotspot on.");
            return $back;
        }
        $quantity = $request->input("quantity", 1);
        if (!is_numeric($quantity) || $quantity < 1 || $quantity > 20 || floor($quantity) != $quantity) {
            session()->flash("error_hotspot", "You can create 1 to 20 tickets at a time.");
            return $back;
        }
        $quantity = (int) $quantity;
        $phone = trim((string) $request->input("phone"));
        if ($phone !== "") {
            if ($quantity > 1) {
                session()->flash("error_hotspot", "A phone number can only be given for a single ticket.");
                return $back;
            }
            // formatKenyanPhone() returns invalid numbers unchanged, so check the result
            $phone = $this->formatKenyanPhone($phone);
            if (!preg_match('/^254(7|1)\d{8}$/', (string) $phone)) {
                session()->flash("error_hotspot", "Enter a valid Kenyan phone number, e.g. 0712345678.");
                return $back;
            }
        }

        $ids = [];
        for ($i = 0; $i < $quantity; $i++) {
            $ids[] = HotspotTickets::issue($plan, $router, $phone ?: null, null, "manual", null, session("Userid"));
        }
        $result = HotspotTickets::provision($router->router_id, $ids);
        $codes = DB::connection("mysql2")->table("hotspot_tickets")->whereIn("ticket_id", $ids)->orderBy("ticket_id")->pluck("ticket_code")->all();

        $notes = [];
        if (!$result['ok']) {
            $notes[] = "They are not on the router yet (" . $result['message'] . "); they are added automatically when the router can be reached.";
        }
        if ($phone && $request->boolean("send_sms")) {
            $ticket = DB::connection("mysql2")->table("hotspot_tickets")->where("ticket_id", $ids[0])->first();
            [$sent, $sms_message] = $this->sendCode($ticket, $plan);
            $notes[] = $sms_message;
        }
        session()->flash("created_codes", ["plan" => $plan->plan_name, "router" => $router->router_name, "codes" => $codes]);
        session()->flash($result['ok'] ? "success_hotspot" : "error_hotspot", count($codes) . " ticket(s) created for " . $plan->plan_name . ". " . implode(" ", $notes));
        return redirect(route("hotspot.tickets"));
    }

    function show($ticket_id)
    {
        $this->useOrgDb();
        $ticket = DB::connection("mysql2")->table("hotspot_tickets AS t")
            ->leftJoin("remote_routers AS r", "r.router_id", "=", "t.router_id")
            ->where("t.ticket_id", $ticket_id)->where("t.deleted", 0)
            ->select("t.*", "r.router_name")->first();
        if (!$ticket) {
            session()->flash("error_hotspot", "Ticket not found.");
            return redirect(route("hotspot.tickets"));
        }
        $plan = DB::connection("mysql2")->table("hotspot_plans")->where("plan_id", $ticket->plan_id)->first();
        return view("hotspot.ticket", [
            "ticket" => $ticket,
            "plan" => $plan,
            "usage" => HotspotTickets::usage($ticket, $plan),
            "snapshots" => DB::connection("mysql2")->table("hotspot_usage_snapshots")->where("ticket_id", $ticket_id)->orderBy("snapshot_id", "DESC")->limit(48)->get(),
            "created_by" => $ticket->created_by ? DB::table("admin_tables")->where("admin_id", $ticket->created_by)->value("admin_fullname") : null,
            "readonly" => $this->readOnly(),
        ]);
    }

    function sms($ticket_id)
    {
        $this->useOrgDb();
        if ($this->readOnly()) {
            return $this->backToTicket($ticket_id, "error_hotspot", "You have read-only access to hotspot tickets.");
        }
        $ticket = DB::connection("mysql2")->table("hotspot_tickets")->where("ticket_id", $ticket_id)->where("deleted", 0)->first();
        if (!$ticket || !$ticket->phone) {
            return $this->backToTicket($ticket_id, "error_hotspot", "This ticket has no phone number.");
        }
        $plan = DB::connection("mysql2")->table("hotspot_plans")->where("plan_id", $ticket->plan_id)->first();
        [$sent, $message] = $this->sendCode($ticket, $plan);
        return $this->backToTicket($ticket_id, $sent ? "success_hotspot" : "error_hotspot", $message);
    }

    // end a ticket now: disconnect it and remove it from the router (crontab agent)
    function expire($ticket_id)
    {
        $this->useOrgDb();
        if ($this->readOnly()) {
            return $this->backToTicket($ticket_id, "error_hotspot", "You have read-only access to hotspot tickets.");
        }
        $ticket = DB::connection("mysql2")->table("hotspot_tickets")->where("ticket_id", $ticket_id)->where("deleted", 0)->first();
        if (!$ticket || !in_array($ticket->status, ["pending", "active"])) {
            return $this->backToTicket($ticket_id, "error_hotspot", "Only a ticket that is not used yet or still running can be ended.");
        }
        $admin = session("Usernames") ?: ("admin " . session("Userid"));
        $result = HotspotAgent::queue($ticket->router_id, "revoke_ticket", [
            "target" => $ticket->ticket_code,
            "ticket_id" => (int) $ticket->ticket_id,
            "reason" => "Ended by " . $admin . " on " . now()->format('D jS M Y g:i:sA') . ".",
        ]);
        return $this->backToTicket($ticket_id, $result['ok'] ? "success_hotspot" : "error_hotspot", $result['ok'] ? "Ending ticket " . $ticket->ticket_code . ": it is disconnected and removed from the router in a moment." : $result['message']);
    }

    private function sendCode($ticket, $plan)
    {
        return HotspotTickets::sendCode($ticket, $plan, DB::table("organizations")->where("organization_id", session("organization_id"))->first());
    }

    private function backToTicket($ticket_id, $key, $message)
    {
        session()->flash($key, $message);
        return redirect(route("hotspot.ticket", $ticket_id));
    }
}
