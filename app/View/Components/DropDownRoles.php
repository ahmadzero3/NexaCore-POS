<?php

namespace App\View\Components;

use Spatie\Permission\Models\Role;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DropDownRoles extends Component
{
    /**
     * Roles array
     *
     * @var array
     */
    public $roles;

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
        $this->roles = Role::whereStatus(1)->pluck('id', 'name')->toArray();
        $this->selected = $selected;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.drop-down-roles');
    }
}
