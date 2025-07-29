<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Party\Party;

class DropdownCustomer extends Component
{
    /**
     * Roles array
     *
     * @var array
     */
    public $customers;

    /**
     * Selected option
     *
     * @var string
     */
    public $selected;

    /**
     * Selected option
     *
     * @var boolean
     */
    public $disabled;

    /**
     * Create a new component instance.
     */
    public function __construct($selected = null, $disabled = false)
    {
        $this->customers = Party::select('id', 'first_name', 'last_name')->where('party_type','customer')->get();
        $this->selected = $selected;
        $this->disabled = $disabled;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-customer');
    }
}
