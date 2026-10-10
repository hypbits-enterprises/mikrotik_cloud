<?php

namespace App\View\Components\Client;

use Illuminate\View\Component;

class Dash extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $clients_data;
    public $readonly;
    public $registration_date;
    public $expire_date;
    public $router_data;
    public $last_client_details;
    public $client_refferal;
    public $subregion_groups;
    public function __construct($clientData, $readonly, $registrationDate, $expireDate, $routerData, $lastClientDetails, $clientRefferal, $subregionGroups = [])
    {
        $this->clients_data = $clientData;
        $this->readonly = $readonly;
        $this->registration_date = $registrationDate;
        $this->expire_date = $expireDate;
        $this->router_data = $routerData;
        $this->last_client_details = $lastClientDetails;
        $this->client_refferal =$clientRefferal;
        $this->subregion_groups = $subregionGroups;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.client.dash');
    }
}
