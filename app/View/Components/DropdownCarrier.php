<?php

namespace App\View\Components;

use App\Models\Carrier;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DropdownCarrier extends Component
{
    /**
     * Carriers array
     *
     * @var array
     */
    public $carriers;

    /**
     * Selected option
     *
     * @var string
     */
    public $selected;

    /**
     * Show Select Option All
     *
     * @var Boolean
     */
    public $showSelectOptionAll;

    /**
     * Selection Box Name or ID
     * @var string
     */
    public $name;

    /**
     * Create a new component instance.
     */
    public function __construct($selected = null, $showSelectOptionAll = false, $name = 'carrier_id')
    {
        $this->carriers = Carrier::select('id','name')->get();
        $this->selected = $selected;
        $this->showSelectOptionAll = $showSelectOptionAll;
        $this->name = $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-carrier');
    }
}
