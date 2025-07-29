<?php

namespace App\View\Components;

use App\Models\Currency;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DropdownCurrency extends Component
{
    /**
     * Selected option
     *
     * @var string
     */
    public $selected;

    /**
     * Selection Box Name or ID
     * @var string
     */
    public $name;

    public $currencies;

    /**
     * Create a new component instance.
     */
    public function __construct($selected = null, $name = 'currency_id')
    {
        $this->currencies = Currency::orderBy('is_company_currency', 'desc')->get();
        $this->selected = $selected;
        $this->name = $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-currency');
    }
}
