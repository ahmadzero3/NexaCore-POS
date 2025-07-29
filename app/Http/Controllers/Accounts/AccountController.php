<?php

namespace App\Http\Controllers\Accounts;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Accounts\AccountGroup;
use App\Models\Accounts\Account;
use App\Http\Requests\AccountRequest;
use App\Http\Requests\AccountBalanceSheetReportRequest;
use App\Enums\AccountUniqueCode;
use App\Traits\FormatNumber; 

class AccountController extends Controller
{
    use FormatNumber;

    /**
     * Create a new service.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View  {

        return view('accounts.account.create');

    }

    /**
     * List the Accounts
     *
     * @return \Illuminate\View\View
     */
    public function list() : View {
        return view('accounts.account.list');
    }

     /**
     * Edit a accounts.
     *
     * @param int $id The ID of the account to edit.
     * @return \Illuminate\View\View
     */
    public function edit($id) : View {

        $account = Account::find($id);

        return view('accounts.account.edit', compact('account'));
    }

    /**
     * Return JsonResponse
     * */
    public function store(AccountRequest $request) : JsonResponse  {

        $filename = null;

        // Get the validated data from the ServiceRequest
        $validatedData = $request->validated();

        // Create a new service record using Eloquent and save it
        $newAccount = Account::create($validatedData);

        return response()->json([
            'status'    => true,
            'message' => __('app.record_saved_successfully'),
            'data'  => [
                'id' => $newAccount->id,
                'name' => $newAccount->name,
            ]
        ]);
    }

    /**
     * Return JsonResponse
     * */
    public function update(AccountRequest $request) : JsonResponse {
        $validatedData = $request->validated();
        
        // Save the service details
        Account::where('id', $validatedData['id'])->update($validatedData);

        return response()->json([
            'message' => __('app.record_updated_successfully'),
        ]);
    }

    public function datatableList(Request $request){

        $data = Account::with('user');

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at->format(app('company')['date_format']);
                    })
                    ->addColumn('group_name', function ($row) {
                        return $row->group->name;
                    })
                    ->addColumn('balance', function ($row) {
                        return $this->formatWithPrecision($row->debit_amt - $row->credit_amt);
                    })
                    ->addColumn('username', function ($row) {
                        return $row->user->username??'';
                    })
                    ->addColumn('action', function($row){
                            $id = $row->id;

                            $editUrl = route('account.edit', ['id' => $id]);
                            $deleteUrl = route('account.delete', ['id' => $id]);


                            $actionBtn = '<div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded font-22 text-option"></i>
                            </a>
                            <ul class="dropdown-menu">';
                                $actionBtn .= '<li>
                                    <a class="dropdown-item" href="' . $editUrl . '"><i class="bi bi-trash"></i><i class="bx bx-edit"></i> '.__('app.edit').'</a>
                                </li>';
                                $actionBtn .= ($row->is_deletable==0)? '' : '<li>
                                    <button type="button" class="dropdown-item text-danger deleteRequest " data-delete-id='.$id.'><i class="bx bx-trash"></i> '.__('app.delete').'</button>
                                </li>';
                            $actionBtn .= '</ul>
                        </div>';
                            return $actionBtn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
    }

    public function delete(Request $request) : JsonResponse{

        $selectedRecordIds = $request->input('record_ids');

        // Perform validation for each selected record ID
        foreach ($selectedRecordIds as $recordId) {
            $record = Account::find($recordId);
            if (!$record) {
                // Invalid record ID, handle the error (e.g., show a message, log, etc.)
                return response()->json([
                    'status'    => false,
                    'message' => __('app.invalid_record_id',['record_id' => $recordId]),
                ]);

            }
            // You can perform additional validation checks here if needed before deletion
        }

        /**
         * All selected record IDs are valid, proceed with the deletion
         * Delete all records with the selected IDs in one query
         * */
        

        try {
            // Attempt deletion (as in previous responses)
            Account::whereIn('id', $selectedRecordIds)->where('is_deletable', 1)->delete();
            return response()->json([
                'status'    => true,
                'message' => __('app.record_deleted_successfully'),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json([
                    'status'    => false,
                    'message' => __('app.cannot_delete_records'),
                ],409);
            } 
        }
    }

    /**
     * Create a new service.
     *
     * @return \Illuminate\View\View
     */
    public function viewBalanceSheetReport()  {
        $allGroups = AccountGroup::all();
        $allAccounts = Account::all();
        $mains = $allGroups->where('parent_id', 0 );

        //$rootGroups = $allGroups->where('parent_id', 0);
        $assetGroups = $allGroups->where('unique_code', AccountUniqueCode::ASSETS->value);
        $equityLiabilityGroups = $allGroups->where('unique_code', AccountUniqueCode::EQUITIES_AND_LIABILITIES->value);

        self::formatTree($assetGroups, $allGroups);
        self::formatTree($equityLiabilityGroups, $allGroups);

        return view('report.accounts.balance-sheet', compact('mains', 'assetGroups', 'equityLiabilityGroups','allAccounts'));

    }

    /*function renderOptions($group, $indent = 0) {
            //echo '<br>';
            //echo ''.str_repeat('&nbsp;', $indent * 4) . '--'. $group->name;
            if ($group->children->isNotEmpty()) {
                foreach ($group->children as $child) {
                    $this->renderOptions($child, $indent + 1);
                }
            }
        }*/

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
     * Get Balance Sheet Records
     * @return JsonResponse
     * */
     function getAccountBalanceSheetRecordsForReport(AccountBalanceSheetReportRequest $request): JsonResponse{

        $fromDate   = $request->input('from_date');
        $toDate     = $request->input('to_date');

        $preparedData = Order::with("customer")
                            ->whereBetween('order_date', [$fromDate, $toDate])
                            ->when($customerId, function ($query) use ($customerId) {
                                return $query->where('customer_id', $customerId);
                            })
                            ->get();
        $recordsArray = [];

        foreach ($preparedData as $order) {
            $recordsArray[] = [  
                                'order_date' => $order->order_date,
                                'customer_name' => $order->customer->first_name . ' ' . $order->customer->last_name,
                                'order_status' => $order->order_status,
                                'total_amount' => $order->total_amount,
                                'paid_amount' => $order->paid_amount,
                                'balance' => $order->total_amount - $order->paid_amount,
                            ];
        }
        
        return response()->json([
                    'status'    => true,
                    'message' => null,
                    'data' => $recordsArray,
                ]);
     }

     /**
     * Trial Balance Report
     *
     * @return \Illuminate\View\View
     */
    public function viewTrialSheetReport()  {
        $allGroups = AccountGroup::all();
        $allAccounts = Account::all();
        $mains = $allGroups->where('parent_id', 0 );

        //$rootGroups = $allGroups->where('parent_id', 0);
        $assetGroups = $allGroups->where('unique_code', AccountUniqueCode::ASSETS->value);
        $equityLiabilityGroups = $allGroups->where('unique_code', AccountUniqueCode::EQUITIES_AND_LIABILITIES->value);

        self::formatTree($assetGroups, $allGroups);
        self::formatTree($equityLiabilityGroups, $allGroups);

        $topLevelGroups = AccountGroup::where('parent_id', 0)->get();
        $trees = [];

        foreach ($topLevelGroups as $topGroup) {
            $trees[$topGroup->id] = [
                'group' => $topGroup,

            ];
        }

        //dd($tree);exit;

        return view('report.accounts.trial-balance', compact('mains', 'assetGroups', 'equityLiabilityGroups','allAccounts', 'trees'));

    }
public function getTreeData()
{
    return response()->json($this->buildTreeData(0));
}

/*private function buildTreeData($parentId = null)
{
    $treeData = [];
    $groups = AccountGroup::where('parent_id', $parentId)->get();

    foreach ($groups as $group) {
        $node = [
            'id' => 'g' . $group->id,
            'text' => $group->name,
            'type' => 'group',
            'debit' => '--',
            'credit' => '--',
            'children' => []
        ];

        $childGroups = $this->buildTreeData($group->id);
        $node['children'] = array_merge($node['children'], $childGroups);

        $accounts = Account::where('group_id', $group->id)->get();
        foreach ($accounts as $account) {
            $node['children'][] = [
                'id' => 'a' . $account->id,
                'text' => $account->name,
                'type' => 'account',
                'debit' => $account->debit_amt,
                'credit' => $account->credit_amt,
            ];
        }

        $treeData[] = $node;
    }

    return $treeData;
}*/

private function buildTreeData($parentId = null)
{
    $treeData = [];
    $groups = AccountGroup::where('parent_id', $parentId)->get();
    foreach ($groups as $group) {
        $node = [
            'id' => 'g' . $group->id,
            'text' => $group->name,
            'type' => 'group',
            'debit' => 0,
            'credit' => 0,
            'children' => []
        ];
        
        $childGroups = $this->buildTreeData($group->id);
        $node['children'] = array_merge($node['children'], $childGroups);
        
        $accounts = Account::where('group_id', $group->id)->get();
        foreach ($accounts as $account) {
            $childNode = [
                'id' => 'a' . $account->id,
                'text' => $account->name,
                'type' => 'account',
                'debit' => $account->debit_amt,
                'credit' => $account->credit_amt,
            ];
            $node['children'][] = $childNode;
            
            // Add account totals to group totals
            $node['debit'] += $childNode['debit'];
            $node['credit'] += $childNode['credit'];
        }
        
        // Add child group totals to this group's totals
        foreach ($childGroups as $childGroup) {
            $node['debit'] += $childGroup['debit'];
            $node['credit'] += $childGroup['credit'];
        }
        
        $treeData[] = $node;
    }
    return $treeData;
}
}
