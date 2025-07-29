<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    /**
     * Button name
     * 
     * @var string
     */
    public $text;

    /**
     * Button type
     * 
     * @var string
     */
    public $type;

    /**
     * Button class
     * 
     * @var string
     */
    public $class;

    /**
     * Button ID
     * 
     * @var string
     */
    public $buttonId;

    /**
     * Create a new component instance.
     */
    public function __construct($type,$text,$class,$buttonId = null)
    {
        $this->type = $type;
        $this->text = $text;
        $this->class = $class;
        $this->buttonId = $buttonId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.button');
    }
}
