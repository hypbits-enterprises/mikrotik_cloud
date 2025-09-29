<?php

namespace App\View\Components\Client;

use Illuminate\View\Component;

class SummaryUsageStats extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $monthly_stats;
    public $daily_stats;
    public $bandwidth_stats;
    public $client_status;
    public function __construct($monthlyStats, $bandwidthStats, $clientStatus, $dailyStats)
    {
        //
        $this->monthly_stats = $monthlyStats;
        $this->bandwidth_stats = $bandwidthStats;
        $this->client_status = $clientStatus;
        $this->daily_stats = $dailyStats;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.client.summary-usage-stats');
    }
}
