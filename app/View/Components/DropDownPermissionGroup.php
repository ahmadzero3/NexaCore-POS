<?php

namespace App\View\Components;

use App\Models\PermissionGroup;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DropDownPermissionGroup extends Component
{
    /**
     * Permission Group array
     *
     * @var array
     */
    public $groups;

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
        $this->groups = PermissionGroup::pluck('id', 'name')->toArray();
        $this->selected = $selected;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.drop-down-permission-group');
    }

}
