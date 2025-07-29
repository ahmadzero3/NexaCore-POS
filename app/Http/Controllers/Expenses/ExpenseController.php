<?php

namespace App\Http\Controllers\Expenses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use App\Models\Expenses\Expense;
use App\Models\Expenses\ExpenseItemMaster;
use App\Models\Expenses\ExpenseItem;
use App\Http\Requests\ExpenseRequest;
use App\Models\Prefix;
use App\Models\PaymentTypes;
use App\Models\Expenses\ExpenseCategory;
use App\Enums\App;
use App\Services\PaymentTransactionService;
use App\Traits\FormatNumber;
use App\Services\AccountTransactionService;
use App\Services\PaymentTypeService;

class ExpenseController extends Controller
{
    use FormatNumber;

    protected $companyId;

    private $accountTransactionService;

    private $paymentTypeService;

    private $paymentTransactionService;

    private $paidPaymentTotal;

    public function __construct(AccountTransactionService $accountTransactionService,
                                    PaymentTypeService $paymentTypeService,
                                    PaymentTransactionService $paymentTransactionService)
    {
        $this->companyId = App::APP_SETTINGS_RECORD_ID->value;
        $this->accountTransactionService = $accountTransactionService;
        $this->paymentTypeService = $paymentTypeService;
        $this->paymentTransactionService = $paymentTransactionService;
    }

    /**
     * Create a new expense.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View  {
        $prefix = Prefix::findOrNew($this->companyId);
        $lastCountId = $this->getLastCountId();
        $selectedPaymentTypesArray = json_encode($this->paymentTypeService->selectedPaymentTypesArray());
        $data = [
            'prefix_code' => $prefix->expense,
            'count_id' => ($lastCountId+1),
        ];
        return view('expenses.expense.create',compact('data', 'selectedPaymentTypesArray'));

    }

    /**
     * Get last count ID
     * */
    public function getLastCountId(){
        return Expense::select('count_id')->orderBy('id', 'desc')->first()?->count_id ?? 0;
    }

    /**
     * List the Accounts
     *
     * @return \Illuminate\View\View
     */
    public function list() : View {
        return view('expenses.expense.list');
    }

    /**
     * Print expenses
     *
     * @param int $id, the ID of the order
     * @return \Illuminate\View\View
     */
    public function print($id) : View {

        $expense = Expense::with('category')->find($id);

        //Item Details
        $expenseItems = ExpenseItem::with('itemDetails')->where('expense_id',$id)->get();

        //Payment Details
        $selectedPaymentTypesArray = json_encode($this->paymentTransactionService->getPaymentRecordsArray($expense));

        return view('expenses.expense.print', compact('expense', 'expenseItems','selectedPaymentTypesArray'));
    }

     /**
     * Edit a expenses.
     *
     * @param int $id The ID of the expense to edit.
     * @return \Illuminate\View\View
     */
    public function edit($id) : View {
        $expense = Expense::find($id);

        //Item Details
        $expenseItems = ExpenseItem::with('itemDetails')->where('expense_id',$id)->get()->toArray();
        $expenseItemsJson = json_encode($expenseItems);

        //Payment Details
        $selectedPaymentTypesArray = json_encode($this->paymentTransactionService->getPaymentRecordsArray($expense));

        return view('expenses.expense.edit', compact('expense', 'expenseItemsJson','selectedPaymentTypesArray'));
    }

    /**
     * Return JsonResponse
     * */
    public function store(ExpenseRequest $request) : JsonResponse  {
        try {
            DB::beginTransaction();
            // Get the validated data from the expenseRequest
            $validatedData = $request->validated();

            if($request->operation == 'save'){
                // Create a new expense record using Eloquent and save it
                $newExpense = Expense::create($validatedData);

                $request->request->add(['expense_id' => $newExpense->id]);

            }else{
                ExpenseItem::where('expense_id', $validatedData['expense_id'])->delete();

                $fillableColumns = [
                    'expense_category_id'   => $validatedData['expense_category_id'],
                    'expense_subcategory_id'   => $validatedData['expense_subcategory_id'],
                    'expense_date'          => $validatedData['expense_date'],
                    'prefix_code'           => $validatedData['prefix_code'],
                    'count_id'              => $validatedData['count_id'],
                    'expense_code'          => $validatedData['expense_code'],
                    'note'                  => $validatedData['note'],
                    'round_off'             => $validatedData['round_off'],
                    'grand_total'           => $validatedData['grand_total'],
                ];
                // First, find the expense
                $newExpense = Expense::findOrFail($validatedData['expense_id']);

                $newExpense->accountTransaction()->delete();
                //Load Expense Payment Transactions
                $paymentTransactions = $newExpense->paymentTransaction;

                foreach ($paymentTransactions as $paymentTransaction) {
                    //Delete Account Transaction
                    $paymentTransaction->accountTransaction()->delete();

                    //Delete Expense Payment Transaction
                    $paymentTransaction->delete();
                }

                // Update the Expense records
                $newExpense->update($fillableColumns);
            }

            $request->request->add(['modelName' => $newExpense]);

            /**
             * Save Table Items in Expense Items Table
             * */
            $expenseItemsArray = $this->saveExpenseItems($request);
            if(!$expenseItemsArray['status']){
                return response()->json([
                    'status'    => false,
                    'message' => $expenseItemsArray['message'],
                ],409);
            }

            /**
             * Save Expense Payment Records
             * */
            $expensePaymentsArray = $this->saveExpensePayments($request);
            if(!$expensePaymentsArray['status']){
                return response()->json([
                    'status'    => false,
                    'message' => $expensePaymentsArray['message'],
                ],409);
            }

            /**
            * Payment Should be equal to Grand Total
            * */
            $this->paidPaymentTotal = ($request->modelName->fresh())->paymentTransaction->sum('amount');
            if($request->grand_total != $this->paidPaymentTotal){
                return response()->json([
                    'status'    => false,
                    'message' => __('payment.paid_payment_not_equal_to_grand_total'),
                ],409);
            }

            /**
             * Update Expenses Model
             * Total Paid Amunt
             * */
            if(!$this->paymentTransactionService->updateTotalPaidAmountInModel($request->modelName)){
                throw new \Exception(__('payment.failed_to_update_paid_amount'));
            }


            /**
             * Update Account Transaction entry
             * Call Services
             * @return boolean
             * */
            $accountTransactionStatus = $this->accountTransactionService->expenseAccountTransaction($request->modelName);
            if(!$accountTransactionStatus){
                return response()->json([
                    'status'    => false,
                    'message' => __('payment.failed_to_update_account'),
                ],409);
            }

            DB::commit();

            // Regenerate the CSRF token
            //Session::regenerateToken();

            return response()->json([
                'status'    => false,
                'message' => __('app.record_saved_successfully'),
                'id' => $request->expense_id,

            ]);

        } catch (\Exception $e) {
                DB::rollback();

                return response()->json([
                    'status' => true,
                    'message' => __('app.something_went_wrong').__('app.check_custom_log_file').$e->getMessage(),
                ], 409);

        }

    }



    public function saveExpensePayments($request)
    {
        $paymentCount = $request->row_count_payments;

        for ($i=0; $i <= $paymentCount; $i++) {

            /**
             * If array record not exist then continue forloop
             * */
            if(!isset($request->payment_amount[$i])){
                continue;
            }

            /**
             * Data index start from 0
             * */
            $amount           = $request->payment_amount[$i];

            if($amount > 0){
                if(!isset($request->payment_type_id[$i])){
                        return [
                            'status' => false,
                            'message' => __('payment.missed_to_select_payment_type')."#".$i,
                        ];
                }

                $paymentsArray = [
                    'transaction_date'          => $request->expense_date,
                    'amount'                    => $amount,
                    'payment_type_id'           => $request->payment_type_id[$i],
                    'note'                      => $request->payment_note[$i],
                ];

                if(!$transaction = $this->paymentTransactionService->recordPayment($request->modelName, $paymentsArray)){
                    throw new \Exception(__('payment.failed_to_record_payment_transactions'));
                }

            }//amount>0
        }//for end

        return ['status' => true];
    }

    public function saveExpenseItems($request)
    {
        $itemsCount = $request->row_count;

        for ($i=0; $i < $itemsCount; $i++) {
            /**
             * If array record not exist then continue forloop
             * */
            if(!isset($request->name[$i])){
                continue;
            }

            /**
             * Data index start from 0
             * */
            $itemName           = $request->name[$i];
            $itemQuantity       = $request->quantity[$i];

            if(empty($itemQuantity) || $itemQuantity === 0 || $itemQuantity < 0){
                    return [
                        'status' => false,
                        'message' => ($itemQuantity<0) ? __('item.item_qty_negative', ['item_name' => $itemName]) : __('item.please_enter_item_quantity', ['item_name' => $itemName]),
                    ];
            }

            $itemsArray = [
                'expense_id'                => $request->expense_id,
                'expense_item_master_id'    => $this->getExpenseItemId($request, index:$i ),
                'description'               => $request->description[$i],
                'unit_price'                => $request->unit_price[$i],
                'quantity'                  => $itemQuantity,
                'total'                     => $request->total[$i],
            ];

            if(!ExpenseItem::create($itemsArray)){
                return ['status' => false];
            }


        }//for end

        return ['status' => true];
    }

    /**
     * If record not exist then create
     * */
    protected function getExpenseItemId($request, $index)
    {
        $itemName           = $request->name[$index];
        $itemUnitPrice      = $request->unit_price[$index];

        $existingItem = ExpenseItemMaster::where('name', $itemName)->first();

        if ($existingItem) {
            return $existingItem->id;
        }

        $newItem = ExpenseItemMaster::create(['name' => $itemName, 'unit_price' => $itemUnitPrice ]);

        return $newItem->id;
    }

    public function datatableList(Request $request)
    {
        $data = Expense::with('user', 'paymentTransaction.paymentType', 'category', 'subcategory')
            ->when($request->expense_category_id, function ($query) use ($request) {
                return $query->where('expense_category_id', $request->expense_category_id);
            })
            ->when($request->from_date && $request->to_date, function ($query) use ($request) {
                return $query->whereBetween('expense_date', [$request->from_date, $request->to_date]);
            })
            ->when(!auth()->user()->can('expense.can.view.other.users.expenses'), function ($query) use ($request) {
                return $query->where('created_by', auth()->user()->id);
            });
    
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('created_at', function ($row) {
                return $row->created_at->format(app('company')['date_format']);
            })
            ->addColumn('username', function ($row) {
                return $row->user->username ?? '';
            })
            ->addColumn('expense_date', function ($row) {
                return $row->formatted_expense_date;
            })
            ->addColumn('paid_amount', function ($row) {
                return $this->formatWithPrecision($row->paid_amount);
            })
            ->addColumn('expense_number', function ($row) {
                return $row->expense_code;
            })
            ->addColumn('payment_type', function ($row) {
                return $row->paymentTransaction->pluck('paymentType.name')->implode(', ');
            })
            ->addColumn('expense_category', function ($row) {
                return $row->category->name;
            })
            ->addColumn('expense_subcategory', function ($row) {
                return $row->subcategory->name ?? '';
            })
            ->addColumn('action', function ($row) {
                $id = $row->id;
    
                $editUrl = route('expense.edit', ['id' => $id]);
                $deleteUrl = route('expense.delete', ['id' => $id]);
                $printUrl = route('expense.print', ['id' => $id]);
    
                $actionBtn = '<div class="dropdown ms-auto">
                    <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded font-22 text-option"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="' . $editUrl . '"><i class="bi bi-trash"></i><i class="bx bx-edit"></i> ' . __('app.edit') . '</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="' . $printUrl . '"></i><i class="bx bx-receipt"></i> ' . __('app.print') . '</a>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item text-danger deleteRequest" data-delete-id=' . $id . '><i class="bx bx-trash"></i> ' . __('app.delete') . '</button>
                        </li>
                    </ul>
                </div>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function delete(Request $request) : JsonResponse{

        DB::beginTransaction();

        $selectedRecordIds = $request->input('record_ids');

        // Perform validation for each selected record ID
        foreach ($selectedRecordIds as $recordId) {
            $record = Expense::find($recordId);
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
            Expense::whereIn('id', $selectedRecordIds)->chunk(100, function ($expenses) {
                foreach ($expenses as $expense) {
                    $expense->accountTransaction()->delete();
                    //Load Expense Payment Transactions
                    $paymentTransactions = $expense->paymentTransaction;
                    foreach ($paymentTransactions as $paymentTransaction) {
                        //Delete Payment Account Transactions
                        $paymentTransaction->accountTransaction()->delete();

                        //Delete Expense Payment Transactions
                        $paymentTransaction->delete();
                    }
                }
            });

            //Delete Expenses
            $deletedCount = Expense::whereIn('id', $selectedRecordIds)->delete();

            DB::commit();

            return response()->json([
                'status'    => true,
                'message' => __('app.record_deleted_successfully'),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            if ($e->getCode() == 23000) {
                return response()->json([
                    'status'    => false,
                    'message' => __('app.cannot_delete_records'),
                ],409);
            }
        }
    }


    /**
     * Ajax Response
     * Search Bar list
     * */
    function getAjaxSearchBarList(){
        $search = request('search');

        $expenseItemsMaster = ExpenseItemMaster::where('name', 'LIKE', "%{$search}%")
                                      ->select('id', 'name', 'unit_price') // Select only the required columns
                                      ->limit(10)
                                      ->get();
        $response = [
            'results' => $expenseItemsMaster->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name,
                    'unit_price' => $item->unit_price,
                ];
            })->toArray(),
        ];

        return json_encode($response);
    }

}
