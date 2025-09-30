<?php

namespace App\View\Components\Client;

use Illuminate\View\Component;

class AllClientsUsageStats extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $client_status = array(
        "online" => 0,
        "offline" => 0,
        "never_online" => 0
    );
    public $monthly_stats = array(
        "this_month_usage" => 0,
        "last_month_usage" => 0,
        "increase" => false,
        "percentage" => 0
    );
    public $bandwidth_stats = array(
        "this_week_band" => 0,
        "last_week_band" => 0,
        "increase" => false,
        "percentage" => 0
    );
    public $daily_stats = array(
        "today_usage" => 0,
        "yesterday_usage" => 0,
        "increase" => false,
        "percentage" => 0
    );
    public function __construct($clientStatus, $monthlyStats, $bandwidthStats, $dailyStats)
    {
        //
        $this->client_status = $clientStatus;
        $this->monthly_stats = $monthlyStats;
        $this->bandwidth_stats = $bandwidthStats;
        $this->daily_stats = $dailyStats;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.client.all-clients-usage-stats');
    }
}
