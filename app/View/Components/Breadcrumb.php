<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    /**
     * Array lang
     * 
     * @var array
     */
    public $langArray;

    /**
     * Create a new component instance.
     */
    public function __construct($langArray)
    {
        $this->langArray = $langArray;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.breadcrumb');
    }
}
