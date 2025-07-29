<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

use App\Models\Accounts\AccountGroup;

class DropdownAccountGroups extends Component
{

    /**
     * groups array
     *
     * @var array
     */
    public $groups;

    /**
     * Selected option
     *
     * @var string
     */
    public $selected;

    /**
     * This is used while edit group avoid to show parent group to same account group
     *
     * @var string
     */
    public $currentGroupId;

    /**
     * Selection box Id name
     *
     * @var string
     */
    public $idName;

    /**
     * Show or not this : <option value="0">Main</option>
     *
     * @var Boolean
     */
    public $showMain;

    /**
     * Create a new component instance.
     */

    public function __construct($selected = null, $currentGroupId = null, $idName = 'parent_id', $showMain = true)
    {
        $allGroups = AccountGroup::when(!empty($currentGroupId), function ($query) use ($currentGroupId) {
                                          $query->whereNotIn('id', [$currentGroupId]);
                                        })
                                    ->get();

        $rootGroups = $allGroups->where('parent_id', 0);

        self::formatTree($rootGroups, $allGroups);

        $this->groups = $rootGroups;

        $this->selected = $selected;

        $this->idName = $idName;

        $this->showMain = $showMain;
    }

    private static function formatTree($groups, $allGroups)
    {

        foreach ($groups as $group) {
            $group->children = $allGroups->where('parent_id', $group->id)->values();

            if ($group->children->isNotEmpty()) {
                self::formatTree($group->children, $allGroups);
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-account-groups');
    }
}
