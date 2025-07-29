<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\PaymentTypes;

class BankDetailsComponent extends Component
{
    public $bankDetails;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->bankDetails = PaymentTypes::where('print_bit', 1)->get()->first();
        
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.bank-details-component');
    }
}
