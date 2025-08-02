<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ButtonLink extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $btnText;
    public $btnType;
    public $btnSize;
    public $otherClasses;
    public $btnId;
    public $paddingSize;
    public $readOnly;
    public $btnLink;
    public $toolTip;
    public $target;
    public $otherAttributes;
    public function __construct($otherAttributes = "", $btnText="", $btnType='primary', $btnSize="md", $otherClasses="", $btnId="", $readOnly = "", $btnLink = "#", $toolTip = null, $target = null)
    {
        $this->btnText = $btnText;
        $this->btnType = $btnType;
        $this->btnSize = $btnSize;
        $this->paddingSize = $this->btnSize == "sm" ? "5px":"10px";
        $this->otherClasses = $otherClasses;
        $this->btnId = $btnId;
        $this->readOnly = $readOnly;
        $this->toolTip = $toolTip;
        $this->btnLink = $btnLink;
        $this->target = $target;
        $this->otherAttributes = $otherAttributes;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.button-link');
    }
}
