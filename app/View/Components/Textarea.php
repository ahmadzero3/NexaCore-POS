<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Textarea extends Component
{
    /**
     * Name of the input box
     * 
     * @var string
     */
    public $placeholder;

    /**
     * Attribute name
     * 
     * @var string
     */
    public $name;

    /**
     * Id of the input box
     * 
     * @var string
     */
    public $id;

    /**
     * Attribute name
     * 
     * @var string
     */
    public $value;

    /**
     * Selected option
     *
     * @var boolean
     */
    public $disabled;

    public $textRows;

    /**
     * Create a new component instance.
     */
    public function __construct($placeholder=null,$name=null,$id=null,$value=null, $disabled=false, $textRows=2)
    {
        $this->placeholder = $placeholder;
        $this->name = $name;
        $this->id = $id;
        $this->value = $value;
        $this->disabled = $disabled;
        $this->textRows = $textRows;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.textarea');
    }
}
