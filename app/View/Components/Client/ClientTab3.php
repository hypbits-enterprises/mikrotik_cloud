<?php

namespace App\View\Components\Client;

use Illuminate\View\Component;

class ClientTab3 extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $clients_data;
    public $invoices;
    public $invoice_id;
    public $readonly_finance;
    public $readonly;
    public function __construct($clientData, $invoices, $readOnlyFinance, $readonly)
    {
        $this->clients_data = $clientData['clients_data'];
        $this->invoices = $invoices;
        $this->invoice_id = $clientData['invoice_id'];
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
        return view('components.client.client-tab3');
    }
}
