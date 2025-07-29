<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\GeneralDataService;

class DropdownGeneral extends Component
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
     * Dropdown option naming - Active/Inactive or Enable/Disable
     *
     * @var String
     */
    public $optionNaming;

    /**
     * Show Select Option All
     *
     * @var Boolean
     */
    public $showSelectOptionAll;

    /**
     * Create a new component instance.
     */
    public function __construct($dropdownName, $selected = null, $showSelectOptionAll = false, $optionNaming = null)
    {
        //Declare var optionNaming before this dropdownData() mthod
        $this->optionNaming = $optionNaming;
        $this->dropdownData = $this->dropdownData();
        $this->selected = $selected;
        $this->dropdownName = $dropdownName;
        $this->showSelectOptionAll = $showSelectOptionAll;

    }

    public function dropdownData(){
        if($this->optionNaming == 'StaffJobStatus'){
            $jobStatusArray = new GeneralDataService;
            return $jobStatusArray->getStaffStatus();
        }
        elseif($this->optionNaming == 'saleOrderStatus'){
            $saleOrderStatus = new GeneralDataService;
            return $saleOrderStatus->getSaleOrderStatus();
        }
        elseif($this->optionNaming == 'purchaseOrderStatus'){
            $purchaseOrderStatus = new GeneralDataService;
            return $purchaseOrderStatus->getPurchaseOrderStatus();
        }
        elseif($this->optionNaming == 'quotationStatus'){
            $quotationStatus = new GeneralDataService;
            return $quotationStatus->getQuotationStatus();
        }
        elseif($this->optionNaming == 'appDirection'){
            return [
                    [
                        'id'    => 'ltr',
                        'name'  =>  'LTR',
                    ],
                    [
                        'id'    => 'rtl',
                        'name'  =>  'RTL',
                    ],
            ];
        }
        elseif($this->optionNaming == 'withOrWithoutTax'){
            return [
                    [
                        'id'    => 0,
                        'name'  =>  'Without Tax',
                    ],
                    [
                        'id'    => 1,
                        'name'  =>  'With Tax',
                    ],
            ];
        }
        elseif($this->optionNaming == 'amountOrPercentage'){
            return [
                    [
                        'id'    => 'percentage',
                        'name'  =>  'Percentage',
                    ],
                    [
                        'id'    => 'fixed',
                        'name'  =>  'Fixed',
                    ],
            ];
        }
        elseif($this->optionNaming == 'creditLimit'){
            return [
                    [
                        'id'    => 0,
                        'name'  =>  'No Limit',
                    ],
                    [
                        'id'    => 1,
                        'name'  =>  'Set Limit',
                    ],
            ];
        }
        else{
            return [];
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-general');
    }
}
