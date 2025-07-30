<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
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
    public $type;
    public $disabled;
    public $toolTip;
    public function __construct($btnText="", $btnType='primary', $btnSize="md", $otherClasses="", $btnId="", $readOnly = "", $type = "button", $disabled = "", $toolTip = null)
    {
        $this->btnText = $btnText;
        $this->btnType = $btnType;
        $this->btnSize = $btnSize;
        $this->paddingSize = $this->btnSize == "sm" ? "5px":"10px";
        $this->otherClasses = $otherClasses;
        $this->btnId = $btnId;
        $this->type = $type;
        $this->readOnly = $readOnly;
        $this->toolTip = $toolTip;
        $this->disabled = $disabled;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.button');
    }
}
