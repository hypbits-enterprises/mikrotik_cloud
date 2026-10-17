<?php

namespace App\Console\Commands;

use App\Classes\HotspotPayments;
use App\Http\Controllers\login;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Hotspot payments nobody finished (customer closed the portal, STK callback lost): asks Daraja
 * about STK pushes still pending after 2 minutes and expires payments after 10 minutes.
 * System crontab, every minute: * * * * * cd <app> && php artisan hotspot:reconcile-payments
 */
class HotspotReconcilePayments extends Command
{
    protected $signature = 'hotspot:reconcile-payments {--org= : only this organization_id}';

    protected $description = 'Check pending hotspot M-Pesa payments with Daraja and expire abandoned ones';

    public function handle()
    {
        $organizations = DB::table("organizations AS o")
            ->join("org_hotspot_settings AS s", "s.organization_id", "=", "o.organization_id")
            ->where("s.hotspot_enabled", 1)
            ->when($this->option("org"), function ($query) {
                $query->where("o.organization_id", $this->option("org"));
            })
            ->select("o.*")->get();
        foreach ($organizations as $organization) {
            if (!$organization->organization_database) {
                continue;
            }
            try {
                (new login())->change_db($organization->organization_database);
                $counts = HotspotPayments::reconcile($organization);
                if ($counts["checked"] > 0) {
                    $this->line($organization->organization_database . ": " . json_encode($counts));
                }
            } catch (\Throwable $e) {
                $this->error($organization->organization_database . ": " . $e->getMessage());
            }
        }
        return 0;
    }
}
