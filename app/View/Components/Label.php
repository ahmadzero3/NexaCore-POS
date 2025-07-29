<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Label extends Component
{   
    /**
     * Name of the label
     *
     * @var string
     */
    public $name;

    /**
     * Label id
     *
     * @var string
     */
    public $for='';

    /**
     * Show Optional text if true
     *
     * @var string
     */
    public $optionalText;
    
    /**
     * Class(css) name for lable
     *
     * @var string
     */
    public $extraClass;

    public $id;
    
    public $labelDataName;

    /**
     * Create a new component instance.
     */
    public function __construct($name, $extraClass=null, $for=null, $optionalText=false, $id=null, $labelDataName = "")
    {
        $this->for = $for;
        $this->name = $name;
        $this->extraClass = $extraClass;
        $this->optionalText = $optionalText;
        $this->id = $id;
        $this->labelDataName = $labelDataName;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.label');
    }
}
