<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DropdownTimeFormat extends Component
{
    /**
     * Selected option
     *
     * @var string
     */
    public $selected;

    /**
     * Options list
     *
     * @var array
     */
    public $dropdownData;

    /**
     * Create a new component instance.
     */
    public function __construct($selected = null)
    {
        $this->dropdownData = $this->dropdownData();
        $this->selected = $selected;
    }

    public function dropdownData(){
        return  [
            '24' => '24 Hours',
            '12' => '12 Hours',
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-time-format');
    }
}
