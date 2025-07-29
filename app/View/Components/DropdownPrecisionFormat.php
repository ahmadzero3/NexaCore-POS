<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DropdownPrecisionFormat extends Component
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
     * For Number Decimals formats
     * @return string
     * number or qunatity
     * */
    public $precisionFor;

    public $selectionBoxName;

    /**
     * Create a new component instance.
     */
    public function __construct($selectionBoxName, $selected = null, $precisionFor = 'number')
    {
        $this->dropdownData = $this->dropdownData();
        $this->selected = $selected;
        $this->precisionFor = $precisionFor;
        $this->selectionBoxName = $selectionBoxName;
    }

    public function dropdownData(){
        if($this->precisionFor == 'number'){
            //Number Precision
            return [0,1,2,3,4];
        }
        else{
            //Quantity Precision
            return [0,1,2,3,4];
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.dropdown-precision-format');
    }
}
