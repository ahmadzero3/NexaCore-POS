<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Party\Party;

class OptionDefaultPartySelected extends Component
{
    /**
     * Roles array
     *
     * @var array
     */
    public $defaultParty;

    /**
     * Attribute
     *
     * @var string
     */
    public $partyType;

    /**
     * Create a new component instance.
     */
    public function __construct($partyType)
    {
        $this->defaultParty = Party::select('id', 'first_name', 'last_name')
                                ->where('party_type',$partyType)
                                ->where('default_party',1)
                                ->first();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.option-default-party-selected');
    }
}
