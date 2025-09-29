<?php

namespace App\View\Components\Client;

use Illuminate\View\Component;

class ClientUsageStats extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $clients_data;
    public $readonly;
    public function __construct($clientsData, $readonly)
    {
        //
        $this->clients_data = $clientsData;
        $this->readonly = $readonly;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.client.client-usage-stats');
    }
}
