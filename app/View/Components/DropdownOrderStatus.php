<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DropdownOrderStatus extends Component
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
     * Dropdown name or id attribute
     *
     * @var String
     */
    public $dropdownName;

    /**
     * Create a new component instance.
     */
    public function __construct($dropdownName, $selected = null)
    {
        $this->dropdownData = $this->dropdownData();
        $this->selected = $selected;
        $this->dropdownName = $dropdownName;

    }

    public function dropdownData(){
        return  [
                        [
                            'value' => 'Booked',
                            'name' => 'Booked',
                        ],
                        [
                            'value' => 'Pending',
                            'name' => 'Pending',
                        ],
                        [
                            'value' => 'Confirmed',
                            'name' => 'Confirmed',
                        ],
                        [
                            'value' => 'In Progress',
                            'name' => 'In Progress',
                        ],
                        [
                            'value' => 'On Hold',
                            'name' => 'On Hold',
                        ],
                        [
                            'value' => 'Cancelled',
                            'name' => 'Cancelled',
                        ],
                        [
                            'value' => 'Completed',
                            'name' => 'Completed',
                        ],
                ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-order-status');
    }
}
