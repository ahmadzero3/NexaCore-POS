<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\UserWarehouse;

class DropdownWarehouse extends Component
{
    /**
     * Roles array
     *
     * @var array
     */
    public $warehouses;

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
     * Show Select Option All
     *
     * @var Boolean
     */
    public $showSelectOptionAll;

    /**
     * Enable to select warehouse manually
     * @return bool
     * */
    public $enableToSelect;

    /**
     * Multiple selection
     * */
    public $multiSelect;

    /**
     * Enable to View All Warehouse
     */
    public $viewAllWarehouse;

    /**
     * Create a new component instance.
     */
    public function __construct($dropdownName, $selected = null, $showSelectOptionAll = false, $enableToSelect = false, $multiSelect=false, $viewAllWarehouse=false)
    {
        /**
         * Current User Id
         * */
        $currentUserId = auth()->id();

        $user = User::find($currentUserId);

        //$isAllowedAllWarehouses = $user->is_allowed_all_warehouses;

        // if(!$isAllowedAllWarehouses && !$viewAllWarehouse){
        //     $warehouseIds = UserWarehouse::where('user_id', $currentUserId)->pluck('warehouse_id');

        //     // Retrieve warehouse details for the assigned IDs
        //     $this->warehouses = Warehouse::whereIn('id', $warehouseIds)->get();
        // }else{
        //     $this->warehouses = Warehouse::all();
        // }
        $this->warehouses = $user->getAccessibleWarehouses($viewAllWarehouse);
        $this->selected = $selected;
        $this->dropdownName = $dropdownName;
        $this->enableToSelect = $enableToSelect;
        $this->showSelectOptionAll = $showSelectOptionAll;
        $this->multiSelect = $multiSelect;

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-warehouse');
    }
}
