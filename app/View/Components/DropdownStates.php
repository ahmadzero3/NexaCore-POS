<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\State;

class DropdownStates extends Component
{
    /**
     * Roles array
     *
     * @var array
     */
    public $states;

    /**
     * Selected option
     *
     * @var string
     */
    public $selected;

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
        $this->states = State::where('status', 1)->get();
        $this->selected = $selected;
        $this->dropdownName = $dropdownName;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-states');
    }
}
