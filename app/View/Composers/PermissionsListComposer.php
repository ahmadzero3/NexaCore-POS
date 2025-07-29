<?php
 
namespace App\View\Composers;
 
use Illuminate\View\View;
 
class PermissionsListComposer
{
    /**
     * Create a new profile composer.
     */
    public function __construct(
    ) {}
 
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        
        $permissionsList = [
                                [
                                    'name' => 'sales',
                                    'permissions' => [
                                        [
                                            'permission' => 'sales_add',
                                            'display_name' => 'Sales Add',
                                        ],
                                        [
                                            'permission' => 'sales_edit',
                                            'display_name' => 'Sales Edit',
                                        ],
                                    ],
                                ],
                                [
                                    'name' => 'purchase',
                                    'permissions' => [
                                        [
                                            'permission' => 'purchase_add',
                                            'display_name' => 'Purchase Add',
                                        ],
                                        [
                                            'permission' => 'purchase_edit',
                                            'display_name' => 'Purchase Edit',
                                        ],
                                    ],
                                ],
                            ];

        $view->with('permissionsList', $permissionsList);

    }
}