<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\PermissionGroup;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $records = [
            /*[
            'groupName' => 'Services',
            'permissionName' => [
                                    [
                                        'name' => 'service.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'service.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'service.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'service.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],*/

            [
            'groupName' => 'Customers',
            'permissionName' => [
                                    [
                                        'name' => 'customer.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'customer.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'customer.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'customer.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],

            [
            'groupName' => 'Tax',
            'permissionName' => [
                                    [
                                        'name' => 'tax.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'tax.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'tax.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'tax.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],
            [
            'groupName' => 'Users',
            'permissionName' => [
                                    [
                                        'name' => 'user.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'user.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'user.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'user.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],
            [
            'groupName' => 'Roles',
            'permissionName' => [
                                    [
                                        'name' => 'role.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'role.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'role.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'role.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],
            /*[
            'groupName' => 'Permissions',
            'permissionName' => [
                                    [
                                        'name' => 'permission.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'permission.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'permission.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'permission.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],*/
            /*[
            'groupName' => 'Permission Groups',
            'permissionName' => [
                                    [
                                        'name' => 'permission.group.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'permission.group.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'permission.group.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'permission.group.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],*/
            [
            'groupName' => 'Profile',
            'permissionName' => [
                                    [
                                        'name' => 'profile.edit',
                                        'displayName' => 'Edit',
                                    ],

                                ],
            ],
            [
            'groupName' => 'App Settings',
            'permissionName' => [
                                    [
                                        'name' => 'app.settings.edit',
                                        'displayName' => 'Edit',
                                    ],

                                ],
            ],
            [
            'groupName' => 'Bank Account',
            'permissionName' => [
                                    [
                                        'name' => 'payment.type.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'payment.type.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'payment.type.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'payment.type.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],
            [
            'groupName' => 'Company Details',
            'permissionName' => [
                                    [
                                        'name' => 'company.edit',
                                        'displayName' => 'Edit',
                                    ],
                                ],
            ],
            [
            'groupName' => 'Create & Send Manual SMS',
            'permissionName' => [
                                    [
                                        'name' => 'sms.create',
                                        'displayName' => 'Create',
                                    ],
                                ],
            ],
            [
            'groupName' => 'SMS Template',
            'permissionName' => [
                                    [
                                        'name' => 'sms.template.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'sms.template.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'sms.template.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'sms.template.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],
            [
                'groupName' => 'Create & Send Manual Email',
                'permissionName' => [
                    [
                        'name' => 'email.create',
                        'displayName' => 'Create',
                    ],
                ],
            ],
            [
            'groupName' => 'Email Template',
            'permissionName' => [
                                    [
                                        'name' => 'email.template.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'email.template.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'email.template.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'email.template.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],
            // [
            // 'groupName' => __('order.orders'),
            // 'permissionName' => [
            //                         [
            //                             'name' => 'order.create',
            //                             'displayName' => 'Create',
            //                         ],
            //                         [
            //                             'name' => 'order.edit',
            //                             'displayName' => 'Edit',
            //                         ],
            //                         [
            //                             'name' => 'order.view',
            //                             'displayName' => 'View',
            //                         ],
            //                         [
            //                             'name' => 'order.delete',
            //                             'displayName' => 'Delete',
            //                         ]

            //                     ],
            // ],
            // [
            // 'groupName' => 'Shedule',
            // 'permissionName' => [
            //                         [
            //                             'name' => 'shedule.create',
            //                             'displayName' => 'Create',
            //                         ],
            //                         [
            //                             'name' => 'shedule.edit',
            //                             'displayName' => 'Edit',
            //                         ],
            //                         [
            //                             'name' => 'shedule.view',
            //                             'displayName' => 'View',
            //                         ],
            //                         [
            //                             'name' => 'shedule.delete',
            //                             'displayName' => 'Delete',
            //                         ]

            //                     ],
            // ],
            // [
            // 'groupName' => 'Assigned Jobs',
            // 'permissionName' => [
            //                         [
            //                             'name' => 'assigned_jobs.create',
            //                             'displayName' => 'Create',
            //                         ],
            //                         [
            //                             'name' => 'assigned_jobs.edit',
            //                             'displayName' => 'Edit',
            //                         ],
            //                         [
            //                             'name' => 'assigned_jobs.view',
            //                             'displayName' => 'View',
            //                         ],
            //                         [
            //                             'name' => 'assigned_jobs.delete',
            //                             'displayName' => 'Delete',
            //                         ]

            //                     ],
            // ],
            [
            'groupName' => __('language.languages'),
            'permissionName' => [
                                    [
                                        'name' => 'language.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'language.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'language.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'language.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],
            [
            'groupName' => __('app.reports'),
            'permissionName' => [
                                    [
                                        'name' => 'report.profit_and_loss',
                                        'displayName' => 'Profit and Loss',
                                    ],
                                    /*[
                                        'name' => 'report.balance_sheet',
                                        'displayName' => 'Balance Sheet',
                                    ],
                                    [
                                        'name' => 'report.trial_balance',
                                        'displayName' => 'Trial Balance',
                                    ],*/
                                   /* [
                                        'name' => 'report.order',
                                        'displayName' => 'Orders',
                                    ],
                                    [
                                        'name' => 'report.order.payment',
                                        'displayName' => 'Orders Payment',
                                    ],
                                    [
                                        'name' => 'report.job.status',
                                        'displayName' => 'Job Status',
                                    ],*/
                                    [
                                        'name' => 'report.item.transaction.batch',
                                        'displayName' => 'Batch Wise Item Transaction Report',
                                    ],
                                    [
                                        'name' => 'report.item.transaction.serial',
                                        'displayName' => 'Serial/IMEI Item Transaction Report',
                                    ],
                                    [
                                        'name' => 'report.item.transaction.general',
                                        'displayName' => 'General Item Transaction Report',
                                    ],
                                    [
                                        'name' => 'report.purchase',
                                        'displayName' => 'Purchase Report',
                                    ],
                                    [
                                        'name' => 'report.purchase.item',
                                        'displayName' => 'Item Purchase Report',
                                    ],
                                    [
                                        'name' => 'report.purchase.payment',
                                        'displayName' => 'Purchase Payment Report',
                                    ],
                                    [
                                        'name' => 'report.sale',
                                        'displayName' => 'Sale Report',
                                    ],
                                    [
                                        'name' => 'report.sale.item',
                                        'displayName' => 'Item Sale Report',
                                    ],
                                    [
                                        'name' => 'report.sale.payment',
                                        'displayName' => 'Sale Payment Report',
                                    ],
                                    [
                                        'name' => 'report.expired.item',
                                        'displayName' => 'Expired Item Report',
                                    ],
                                    [
                                        'name' => 'report.reorder.item',
                                        'displayName' => 'Reorder Item Report',
                                    ],
                                    [
                                        'name' => 'report.expense',
                                        'displayName' => 'Expense Report',
                                    ],
                                    [
                                        'name' => 'report.expense.item',
                                        'displayName' => 'Item Expense Report',
                                    ],
                                    [
                                        'name' => 'report.expense.payment',
                                        'displayName' => 'Expense Payment Report',
                                    ],
                                    [
                                        'name' => 'report.gstr-1',
                                        'displayName' => 'GSTR-1',
                                    ],
                                    [
                                        'name' => 'report.gstr-2',
                                        'displayName' => 'GSTR-2',
                                    ],
                                    [
                                        'name' => 'report.stock_transfer',
                                        'displayName' => 'Stock Transfer Report',
                                    ],
                                    [
                                        'name' => 'report.stock_transfer.item',
                                        'displayName' => 'Item Stock Transfer Report',
                                    ],

                                ],
            ],
            /*[
            'groupName' => __('account.accounting'),
            'permissionName' => [
                                    [
                                        'name' => 'account.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'account.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'account.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'account.delete',
                                        'displayName' => 'Delete',
                                    ],
                                    [
                                        'name' => 'account.group.create',
                                        'displayName' => 'Group Create',
                                    ],
                                    [
                                        'name' => 'account.group.edit',
                                        'displayName' => 'Group Edit',
                                    ],
                                    [
                                        'name' => 'account.group.view',
                                        'displayName' => 'Group View',
                                    ],
                                    [
                                        'name' => 'account.group.delete',
                                        'displayName' => 'Group Delete',
                                    ],
                                ],
            ],*/
            [
            'groupName' => __('expense.expense'),
            'permissionName' => [
                                    [
                                        'name' => 'expense.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'expense.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'expense.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'expense.delete',
                                        'displayName' => 'Delete',
                                    ],
                                    [
                                        'name' => 'expense.category.create',
                                        'displayName' => 'Category Create',
                                    ],
                                    [
                                        'name' => 'expense.category.edit',
                                        'displayName' => 'Category Edit',
                                    ],
                                    [
                                        'name' => 'expense.category.view',
                                        'displayName' => 'Category View',
                                    ],
                                    [
                                        'name' => 'expense.category.delete',
                                        'displayName' => 'Category Delete',
                                    ],
                                ],
            ],
            [
            'groupName' => __('warehouse.warehouses'),
            'permissionName' => [
                                    [
                                        'name' => 'warehouse.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'warehouse.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'warehouse.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'warehouse.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],
            [
            'groupName' => __('warehouse.stock_transfer'),
            'permissionName' => [
                                    [
                                        'name' => 'stock_transfer.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'stock_transfer.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'stock_transfer.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'stock_transfer.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],
            [
            'groupName' => __('item.items'),
            'permissionName' => [
                                    [
                                        'name' => 'item.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'item.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'item.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'item.delete',
                                        'displayName' => 'Delete',
                                    ],
                                    [
                                        'name' => 'item.category.create',
                                        'displayName' => 'Category Create',
                                    ],
                                    [
                                        'name' => 'item.category.edit',
                                        'displayName' => 'Category Edit',
                                    ],
                                    [
                                        'name' => 'item.category.view',
                                        'displayName' => 'Category View',
                                    ],
                                    [
                                        'name' => 'item.category.delete',
                                        'displayName' => 'Category Delete',
                                    ],
                                ],
            ],
            [
            'groupName' => __('unit.units'),
            'permissionName' => [
                                    [
                                        'name' => 'unit.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'unit.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'unit.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'unit.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],
            [
            'groupName' => 'Suppliers',
            'permissionName' => [
                                    [
                                        'name' => 'supplier.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'supplier.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'supplier.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'supplier.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],
            [
            'groupName' => 'Utilities',
            'permissionName' => [
                                    [
                                        'name' => 'import.item',
                                        'displayName' => 'Import Items & Services',
                                    ],
                                    [
                                        'name' => 'import.party',
                                        'displayName' => 'Import Customers & Suppliers',
                                    ],
                                    [
                                        'name' => 'generate.barcode',
                                        'displayName' => 'Generate Barcode',
                                    ],

                                ],
            ],
            [
            'groupName' => 'Purchase Order',
            'permissionName' => [
                                    [
                                        'name' => 'purchase.order.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'purchase.order.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'purchase.order.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'purchase.order.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],
            [
            'groupName' => 'Purchase Bill',
            'permissionName' => [
                                    [
                                        'name' => 'purchase.bill.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'purchase.bill.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'purchase.bill.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'purchase.bill.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],
            [
            'groupName' => 'Purchase Return',
            'permissionName' => [
                                    [
                                        'name' => 'purchase.return.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'purchase.return.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'purchase.return.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'purchase.return.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],

            [
            'groupName' => 'Sale Order',
            'permissionName' => [
                                    [
                                        'name' => 'sale.order.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'sale.order.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'sale.order.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'sale.order.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],
            [
            'groupName' => 'Sale Bill',
            'permissionName' => [
                                    [
                                        'name' => 'sale.invoice.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'sale.invoice.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'sale.invoice.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'sale.invoice.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],
            [
            'groupName' => 'Sale Return',
            'permissionName' => [
                                    [
                                        'name' => 'sale.return.create',
                                        'displayName' => 'Create',
                                    ],
                                    [
                                        'name' => 'sale.return.edit',
                                        'displayName' => 'Edit',
                                    ],
                                    [
                                        'name' => 'sale.return.view',
                                        'displayName' => 'View',
                                    ],
                                    [
                                        'name' => 'sale.return.delete',
                                        'displayName' => 'Delete',
                                    ]

                                ],
            ],
            [
            'groupName' => 'Cash & Bank Transaction',
            'permissionName' => [
                                    [
                                        'name' => 'transaction.cash.add',
                                        'displayName' => 'Cash Transaction Create',
                                    ],
                                    [
                                        'name' => 'transaction.cash.edit',
                                        'displayName' => 'Cash Transaction Edit',
                                    ],
                                    [
                                        'name' => 'transaction.cash.view',
                                        'displayName' => 'Cash Transaction View',
                                    ],
                                    [
                                        'name' => 'transaction.cash.delete',
                                        'displayName' => 'Cash Transaction Delete',
                                    ],
                                    [
                                        'name' => 'transaction.bank.view',
                                        'displayName' => 'Bank Transaction View',
                                    ],
                                    [
                                        'name' => 'transaction.cheque.view',
                                        'displayName' => 'Cheque Transaction View',
                                    ],

                                ],
            ],


        ];


        foreach ($records as $record) {

            $group = PermissionGroup::firstOrCreate(['name' => $record['groupName']]);

            foreach ($record['permissionName'] as $permission) {

                Permission::create([
                                    'name' => $permission['name'],
                                    'display_name' => $permission['displayName'],
                                    'permission_group_id' => $group->id,
                                    'status' => 1,
                                ]);

            }


        }




    }
}
