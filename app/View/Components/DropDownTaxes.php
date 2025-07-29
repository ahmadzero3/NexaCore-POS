<?php

namespace App\View\Components;

use App\Models\Tax;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DropDownTaxes extends Component
{
    /**
     * Roles array
     *
     * @var array
     */
    public $taxes;

    /**
     * Selected option
     *
     * @var string
     */
    public $selected;

    /**
     * Get the TaxType from CompanyServiceProvider.php
     *
     * @var string
     */
    public $taxType;

    /**
     * Create a new component instance.
     */
    public function __construct($selected = null)
    {
        $this->taxType  = app('company')['tax_type'];
        $this->taxes    = Tax::when($this->taxType == 'no-tax', function($query) {
                                    return $query->oldest()->limit(1);
                                })->get();
        $this->selected = $selected;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.drop-down-taxes');
    }
}
