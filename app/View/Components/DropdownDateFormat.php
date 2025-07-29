<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DropdownDateFormat extends Component
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
            'Y-m-d',           // Example: 2023-11-30
            'd/m/Y',           // Example: 30/11/2023
            'd-m-Y',           // Example: 30-11-2023
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-date-format');
    }
}
