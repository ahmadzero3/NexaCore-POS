<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Unit;

class DropdownUnits extends Component
{
    /**
     * Roles array
     *
     * @var array
     */
    public $units;

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
        $this->units = Unit::all();
        $this->selected = $selected;
        $this->dropdownName = $dropdownName;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-units');
    }
}
