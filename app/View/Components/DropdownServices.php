<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Service;

class DropdownServices extends Component
{
    /**
     * Services array
     *
     * @var array
     */
    public $services;

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
        $this->services = Service::pluck('id', 'name')->toArray();
        $this->selected = $selected;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-services');
    }
}
