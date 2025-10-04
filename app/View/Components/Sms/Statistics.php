<?php

namespace App\View\Components\Sms;

use Illuminate\View\Component;

class Statistics extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $monthlyStats = [];
    public $weeklyStats = [];
    public $dailyStats = [];
    public function __construct($monthlyStats, $weeklyStats, $dailyStats)
    {
        //monthly, weekly, daily stats
        $this->monthlyStats = $monthlyStats;
        $this->weeklyStats = $weeklyStats;
        $this->dailyStats = $dailyStats;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.sms.statistics');
    }
}
