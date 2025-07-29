<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\PaymentTypes;
use App\Services\PaymentTypeService;
use App\Enums\PaymentTypesUniqueCode;

class DropdownPaymentType extends Component
{
    /**
     * Roles array
     *
     * @var array
     */
    public $paymentTypes;

    /**
     * Selected option
     *
     * @var string
     */
    public $selected;

    public $paymentTypeName;

    /**
     * Ignote the ids
     * comma delimited
     * */
    public $dontShowCheque;

    /**
     * Create a new component instance.
     */
    public function __construct($paymentTypeName = 'payment_type_id', $dontShowCheque = false, $selected = null)
    {
        $chequeId = '';

        if($dontShowCheque){
            $paymentTypeService = new PaymentTypeService();

            $chequeId = $paymentTypeService->returnPaymentTypeId(PaymentTypesUniqueCode::CHEQUE->value);
        }

        $this->paymentTypes = PaymentTypes::select('id', 'name')
                                          ->when($dontShowCheque, function($query) use ($chequeId) {
                                              return $query->whereNotIn('id', [$chequeId]);
                                          })
                                          ->get();
        $this->selected = $selected;
        $this->paymentTypeName = $paymentTypeName;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-payment-type');
    }
}
