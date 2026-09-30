<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AudienceBuilder extends Component
{
    public $routers;
    public $regions;
    public $profiles;
    public $idPrefix;
    public $readOnly;

    public function __construct($routers = [], $regions = [], $profiles = [], $idPrefix = "audience", $readOnly = "")
    {
        $this->routers = $routers;
        $this->regions = $regions;
        $this->profiles = $profiles;
        $this->idPrefix = $idPrefix;
        $this->readOnly = $readOnly;
    }

    public function render()
    {
        return view('components.audience-builder');
    }
}
