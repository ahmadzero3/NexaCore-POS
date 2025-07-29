<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AnchorTag extends Component
{
    /**
     * Name of the label
     *
     * @var string
     */
    public $href;

    /**
     * Name of the label
     *
     * @var string
     */
    public $text;

    /**
     * Button class
     * 
     * @var string
     */
    public $class;

    /**
     * Create a new component instance.
     */
    public function __construct($href,$text,$class=null)
    {
        $this->href = $href;
        $this->text = $text;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.anchor-tag');
    }
}
