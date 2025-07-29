<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Jackiedo\Timezonelist\Timezonelist;

class DropdownTimezone extends Component
{
    /**
     * Timezone object
     *
     * @var array
     */
    public $timezoneList;

    /**
     * Timezone array
     *
     * @var array
     */
    public $timezones;

    /**
     * Selected option
     *
     * @var string
     */
    public $selected;

    /**
     * Create a new component instance.
     */
    public function __construct($selected = null)
    {
        $this->timezoneList = new Timezonelist;
        
        $this->timezones = $this->timezoneList->toArray(); 

        $this->selected = $selected;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-timezone');
    }
}
