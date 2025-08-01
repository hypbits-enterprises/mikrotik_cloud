<?php

namespace App\View\Components\Client;

use Illuminate\View\Component;

class ClientTab2 extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $clients_data;
    public $client_issues;
    public $readonly_finance;
    public $readonly;
    public function __construct($clientData, $clientIssues, $readOnlyFinance, $readonly)
    {
        $this->clients_data = $clientData;
        $this->client_issues = $clientIssues;
        $this->readonly_finance = $readOnlyFinance;
        $this->readonly = $readonly;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.client.client-tab2');
    }
}
