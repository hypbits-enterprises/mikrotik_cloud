<?php

namespace App\Http\Controllers\Hotspot;

use App\Classes\HotspotPayments;
use App\Http\Controllers\Controller;
use App\Http\Controllers\login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Hotspot → Payments: hotspot earnings (hotspot_transactions: money received for hotspot, never a
 * PPPoE transaction) and the portal's payment attempts.
 */
class Payments extends Controller
{
    const LIST_LIMIT = 300;

    private function useOrgDb()
    {
        $change_db = new login();
        $change_db->change_db();
    }

    private function filters(Request $request)
    {
        $from = $request->input("from", now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input("to", now()->format('Y-m-d'));
        foreach (["from", "to"] as $name) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $$name)) {
                $$name = $name === "from" ? now()->startOfMonth()->format('Y-m-d') : now()->format('Y-m-d');
            }
        }
        return [
            "from" => $from,
            "to" => $to,
            "status" => $request->input("status", ""),
            "q" => trim((string) $request->input("q", "")),
        ];
    }

    private function transactions(array $filters)
    {
        $query = DB::connection("mysql2")->table("hotspot_transactions AS t")
            ->leftJoin("hotspot_payments AS p", "p.payment_id", "=", "t.payment_id")
            ->leftJoin("hotspot_plans AS pl", "pl.plan_id", "=", "p.plan_id")
            ->leftJoin("hotspot_tickets AS k", "k.ticket_id", "=", "t.ticket_id")
            ->where("t.deleted", 0)
            ->whereBetween("t.date_created", [str_replace("-", "", $filters['from']) . "000000", str_replace("-", "", $filters['to']) . "235959"]);
        if (in_array($filters['status'], ["matched", "unmatched", "review", "rejected"], true)) {
            $query->where("t.status", $filters['status']);
        }
        if ($filters['q'] !== "") {
            $q = $filters['q'];
            $query->where(function ($inner) use ($q) {
                $inner->where("t.trans_id", "like", "%" . $q . "%")->orWhere("t.bill_ref", "like", "%" . $q . "%")
                    ->orWhere("p.phone", "like", "%" . $q . "%")->orWhere("t.phone", "like", "%" . $q . "%");
            });
        }
        return $query;
    }

    function index(Request $request)
    {
        $this->useOrgDb();
        $filters = $this->filters($request);
        $base = $this->transactions($filters);
        $totals = (clone $base)->select("t.status", DB::raw("COUNT(*) AS n"), DB::raw("SUM(t.amount) AS total"))->groupBy("t.status")->get()->keyBy("status");
        $rows = (clone $base)->select("t.*", "p.phone AS customer_phone", "p.method", "pl.plan_name", "k.ticket_code", "k.ticket_id AS ticket")
            ->orderBy("t.transaction_id", "DESC")->limit(self::LIST_LIMIT)->get();
        $attempts = DB::connection("mysql2")->table("hotspot_payments AS p")
            ->leftJoin("hotspot_plans AS pl", "pl.plan_id", "=", "p.plan_id")
            ->where("p.deleted", 0)->where("p.status", "!=", "paid")
            ->whereBetween("p.date_created", [str_replace("-", "", $filters['from']) . "000000", str_replace("-", "", $filters['to']) . "235959"])
            ->select("p.*", "pl.plan_name")->orderBy("p.payment_id", "DESC")->limit(50)->get();
        return view("hotspot.payments", [
            "filters" => $filters,
            "totals" => $totals,
            "rows" => $rows,
            "attempts" => $attempts,
            "config" => HotspotPayments::config(),
            "limit" => self::LIST_LIMIT,
        ]);
    }

    function export(Request $request)
    {
        $this->useOrgDb();
        $filters = $this->filters($request);
        $rows = $this->transactions($filters)->select("t.*", "p.phone AS customer_phone", "pl.plan_name", "k.ticket_code")->orderBy("t.transaction_id")->get();
        $name = "hotspot-payments-" . $filters['from'] . "-to-" . $filters['to'] . ".csv";
        return response()->streamDownload(function () use ($rows) {
            $out = fopen("php://output", "w");
            fputcsv($out, ["Date", "M-Pesa code", "Amount", "Phone", "Payer", "Account", "Package", "Ticket", "Source", "Status"]);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->date_created ? date("Y-m-d H:i:s", strtotime($row->date_created)) : "",
                    $row->trans_id, $row->amount, $row->customer_phone ?: $row->phone, $row->payer_name, $row->bill_ref,
                    $row->plan_name, $row->ticket_code, $row->source, $row->status,
                ]);
            }
            fclose($out);
        }, $name, ["Content-Type" => "text/csv"]);
    }
}
