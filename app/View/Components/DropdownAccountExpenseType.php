<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

use App\Enums\AccountUniqueCode;
use App\Models\Accounts\AccountGroup;

class DropdownAccountExpenseType extends Component
{

    /**
     * groups array
     *
     * @var array
     */
    public $accountGroups;

    /**
     * Selected option
     *
     * @var string
     */
    public $selected;

    public $directExpenses;

    public $indirectExpenses;

    /**
     * Create a new component instance.
     */
    public function __construct($selected = null,)
    {
        $this->directExpenses = AccountUniqueCode::DIRECT_EXPENSES->value;
        $this->indirectExpenses = AccountUniqueCode::INDIRECT_EXPENSES->value;

        $this->accountGroups = AccountGroup::whereIn('unique_code', ['DIRECT_EXPENSES', 'INDIRECT_EXPENSES'])
                                            ->get();
        $this->selected = $selected;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-account-expense-type');
    }
}
