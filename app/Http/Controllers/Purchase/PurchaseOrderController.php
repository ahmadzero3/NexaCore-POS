<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Prefix;
use App\Models\Purchase\PurchaseOrder;
use App\Models\Items\Item;
use App\Traits\FormatNumber;
use App\Traits\FormatsDateInputs;
use App\Enums\App;
use App\Services\PaymentTypeService;
use App\Services\GeneralDataService;
use App\Services\PaymentTransactionService;
use App\Http\Requests\PurchaseOrderRequest;
use App\Services\AccountTransactionService;
use App\Services\ItemTransactionService;
use App\Models\Items\ItemSerialTransaction;
use App\Models\Items\ItemBatchTransaction;
use Carbon\Carbon;
use App\Services\CacheService;
use App\Enums\ItemTransactionUniqueCode;
use App\Models\Purchase\Purchase;
use App\Services\StatusHistoryService;
use App\Services\Communication\Email\PurchaseOrderEmailNotificationService;
use App\Services\Communication\Sms\PurchaseOrderSmsNotificationService;

use Mpdf\Mpdf;

class PurchaseOrderController extends Controller
{
    use FormatNumber;

    use FormatsDateInputs;

    protected $companyId;

    private $paymentTypeService;

    private $paymentTransactionService;

    private $accountTransactionService;

    private $itemTransactionService;

    private $purchaseOrderEmailNotificationService;

    private $purchaseOrderSmsNotificationService;

    public $generalDataService;

    public $statusHistoryService;

    public function __construct(PaymentTypeService $paymentTypeService,
                                PaymentTransactionService $paymentTransactionService,
                                AccountTransactionService $accountTransactionService,
                                ItemTransactionService $itemTransactionService,
                                PurchaseOrderEmailNotificationService $purchaseOrderEmailNotificationService,
                                PurchaseOrderSmsNotificationService $purchaseOrderSmsNotificationService,
                                GeneralDataService $generalDataService,
                                StatusHistoryService $statusHistoryService
                            )
    {
        $this->companyId = App::APP_SETTINGS_RECORD_ID->value;
        $this->paymentTypeService = $paymentTypeService;
        $this->paymentTransactionService = $paymentTransactionService;
        $this->accountTransactionService = $accountTransactionService;
        $this->itemTransactionService = $itemTransactionService;
        $this->purchaseOrderEmailNotificationService = $purchaseOrderEmailNotificationService;
        $this->purchaseOrderSmsNotificationService = $purchaseOrderSmsNotificationService;
        $this->generalDataService = $generalDataService;
        $this->statusHistoryService = $statusHistoryService;
    }

    /**
     * Create a new order.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View  {
        $prefix = Prefix::findOrNew($this->companyId);
        $lastCountId = $this->getLastCountId();
        $selectedPaymentTypesArray = json_encode($this->paymentTypeService->selectedPaymentTypesArray());
        $data = [
            'prefix_code' => $prefix->purchase_order,
            'count_id' => ($lastCountId+1),
        ];
        return view('purchase.order.create',compact('data', 'selectedPaymentTypesArray'));
    }

    /**
     * Get last count ID
     * */
    public function getLastCountId(){
        return PurchaseOrder::select('count_id')->orderBy('id', 'desc')->first()?->count_id ?? 0;
    }

    /**
     * List the orders
     *
     * @return \Illuminate\View\View
     */
    public function list() : View {
        return view('purchase.order.list');
    }


     /**
     * Edit a Purchase Order.
     *
     * @param int $id The ID of the expense to edit.
     * @return \Illuminate\View\View
     */
    public function edit($id) : View {
        $order = PurchaseOrder::with(['party',
                                        'itemTransaction' => [
                                            'item',
                                            'tax',
                                            'batch.itemBatchMaster',
                                            'itemSerialTransaction.itemSerialMaster'
                                        ]])->findOrFail($id);

        // Add formatted dates from ItemBatchMaster model
        $order->itemTransaction->each(function ($transaction) {
            if (!$transaction->batch?->itemBatchMaster) {
                return;
            }
            $batchMaster = $transaction->batch->itemBatchMaster;
            $batchMaster->mfg_date = $batchMaster->getFormattedMfgDateAttribute();
            $batchMaster->exp_date = $batchMaster->getFormattedExpDateAttribute();
        });

        // Item Details
        // Prepare item transactions with associated units
        $allUnits = CacheService::get('unit');

        $itemTransactions = $order->itemTransaction->map(function ($transaction) use ($allUnits ) {
            $itemData = $transaction->toArray();

            // Use the getOnlySelectedUnits helper function
            $selectedUnits = getOnlySelectedUnits(
                $allUnits,
                $transaction->item->base_unit_id,
                $transaction->item->secondary_unit_id
            );

            // Add unitList to the item data
            $itemData['unitList'] = $selectedUnits->toArray();

            // Get item serial transactions with associated item serial master data
            $itemSerialTransactions = $transaction->itemSerialTransaction->map(function ($serialTransaction) {
                return $serialTransaction->itemSerialMaster->toArray();
            })->toArray();

            // Add itemSerialTransactions to the item data
            $itemData['itemSerialTransactions'] = $itemSerialTransactions;

            return $itemData;
        })->toArray();

        $itemTransactionsJson = json_encode($itemTransactions);

        //Default Payment Details
        $selectedPaymentTypesArray = json_encode($this->paymentTypeService->selectedPaymentTypesArray());

        //Get the previous payments
        $paymentHistory = $this->paymentTransactionService->getPaymentRecordsArray($order);

        $taxList = CacheService::get('tax')->toJson();

        return view('purchase.order.edit', compact('taxList', 'order', 'itemTransactionsJson','selectedPaymentTypesArray', 'paymentHistory'));
    }

    /**
     * View Purchase Order details
     *
     * @param int $id, the ID of the order
     * @return \Illuminate\View\View
     */
    public function details($id) : View {
        $order = PurchaseOrder::with(['party',
                                        'itemTransaction' => [
                                            'item',
                                            'tax',
                                            'batch.itemBatchMaster',
                                            'itemSerialTransaction.itemSerialMaster'
                                        ]])->find($id);

        //Payment Details
        $selectedPaymentTypesArray = json_encode($this->paymentTransactionService->getPaymentRecordsArray($order));

        //Batch Tracking Row count for invoice columns setting
        $batchTrackingRowCount = (new GeneralDataService())->getBatchTranckingRowCount();

        return view('purchase.order.details', compact('order','selectedPaymentTypesArray', 'batchTrackingRowCount'));
    }

    /**
     * Print Purchase Order
     *
     * @param int $id, the ID of the order
     * @return \Illuminate\View\View
     */
    public function print($id, $isPdf = false) : View {
        $order = PurchaseOrder::with(['party',
                                        'itemTransaction' => [
                                            'item',
                                            'tax',
                                            'batch.itemBatchMaster',
                                            'itemSerialTransaction.itemSerialMaster'
                                        ]])->find($id);

        //Payment Details
        $selectedPaymentTypesArray = json_encode($this->paymentTransactionService->getPaymentRecordsArray($order));

        //Batch Tracking Row count for invoice columns setting
        $batchTrackingRowCount = (new GeneralDataService())->getBatchTranckingRowCount();

        $invoiceData = [
            'name' => __('purchase.order.order'),
        ];

        return view('print.purchase-order', compact('isPdf', 'invoiceData', 'order','selectedPaymentTypesArray','batchTrackingRowCount'));
        //return view('purchase.order.unused-print', compact('order','selectedPaymentTypesArray','batchTrackingRowCount'));
    }


    /**
     * Generate PDF using View: print() method
     * */
    public function generatePdf($id){
        $html = $this->print($id, isPdf:true);

        $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 2,
                'margin_right' => 2,
                'margin_top' => 2,
                'margin_bottom' => 2,
                'default_font' => 'dejavusans',
                //'direction' => 'rtl',
            ]);
        $mpdf->showImageErrors = true;
        $mpdf->WriteHTML($html);
        /**
         * Display in browser
         * 'I'
         * Downloadn PDF
         * 'D'
         * */
        $mpdf->Output('Purchase-Order-'.$id.'.pdf', 'D');
    }

    /**
     * Store Records
     * */
    public function store(PurchaseOrderRequest $request) : JsonResponse  {
        try {
            DB::beginTransaction();
            // Get the validated data from the expenseRequest
            $validatedData = $request->validated();

            if($request->operation == 'save'){
                // Create a new expense record using Eloquent and save it
                $newPurchaseOrder = PurchaseOrder::create($validatedData);

                $request->request->add(['purchase_order_id' => $newPurchaseOrder->id]);

            }else{
                $fillableColumns = [
                    'party_id'              => $validatedData['party_id'],
                    'order_date'            => $validatedData['order_date'],
                    'due_date'              => $validatedData['due_date'],
                    'prefix_code'           => $validatedData['prefix_code'],
                    'count_id'              => $validatedData['count_id'],
                    'order_code'            => $validatedData['order_code'],
                    'note'                  => $validatedData['note'],
                    'round_off'             => $validatedData['round_off'],
                    'grand_total'           => $validatedData['grand_total'],
                    'state_id'              => $validatedData['state_id'],
                    'currency_id'           => $validatedData['currency_id'],
                    'exchange_rate'         => $validatedData['exchange_rate'],
                    'order_status'          => $validatedData['order_status'],
                ];

                $newPurchaseOrder = PurchaseOrder::findOrFail($validatedData['purchase_order_id']);
                $newPurchaseOrder->update($fillableColumns);
                $newPurchaseOrder->itemTransaction()->delete();
                // $newPurchaseOrder->accountTransaction()->delete();
                // // Check if paymentTransactions exist
                // $paymentTransactions = $newPurchaseOrder->paymentTransaction;
                // if ($paymentTransactions->isNotEmpty()) {
                //     foreach ($paymentTransactions as $paymentTransaction) {
                //         $accountTransactions = $paymentTransaction->accountTransaction;
                //         if ($accountTransactions->isNotEmpty()) {
                //             foreach ($accountTransactions as $accountTransaction) {
                //                 // Do something with the individual accountTransaction
                //                 $accountTransaction->delete(); // Or any other operation
                //             }
                //         }
                //     }
                // }
                // $newPurchaseOrder->paymentTransaction()->delete();
            }

            /**
             * Record Status Update History
             */
            $this->statusHistoryService->RecordStatusHistory($newPurchaseOrder);

            $request->request->add(['modelName' => $newPurchaseOrder]);

            /**
             * Save Table Items in Purchase Order Items Table
             * */
            $purchaseOrderItemsArray = $this->savePurchaseOrderItems($request);
            if(!$purchaseOrderItemsArray['status']){
                throw new \Exception($purchaseOrderItemsArray['message']);
            }

            /**
             * Save Expense Payment Records
             * */
            $purchaseOrderPaymentsArray = $this->savePurchaseOrderPayments($request);
            if(!$purchaseOrderPaymentsArray['status']){
                throw new \Exception($purchaseOrderPaymentsArray['message']);
            }

            /**
            * Payment Should not be less than 0
            * */
            $paidAmount = $newPurchaseOrder->refresh('paymentTransaction')->paymentTransaction->sum('amount');
            if($paidAmount < 0){
                throw new \Exception(__('payment.paid_amount_should_not_be_less_than_zero'));
            }

            /**
             * Paid amount should not be greater than grand total
             * */
            if($paidAmount > $newPurchaseOrder->grand_total){
                throw new \Exception(__('payment.payment_should_not_be_greater_than_grand_total')."<br>Paid Amount : ". $this->formatWithPrecision($paidAmount)."<br>Grand Total : ". $this->formatWithPrecision($newPurchaseOrder->grand_total). "<br>Difference : ".$this->formatWithPrecision($paidAmount-$newPurchaseOrder->grand_total));
            }

            /**
             * Update Purchase Order Model
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
            $accountTransactionStatus = $this->accountTransactionService->purchaseOrderAccountTransaction($request->modelName);
            if(!$accountTransactionStatus){
                throw new \Exception(__('payment.failed_to_update_account'));
            }

            DB::commit();

            // Regenerate the CSRF token
            //Session::regenerateToken();

            return response()->json([
                'status'    => false,
                'message' => __('app.record_saved_successfully'),
                'id' => $request->purchase_order_id,

            ]);

        } catch (\Exception $e) {
                DB::rollback();

                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 409);

        }

    }



    public function savePurchaseOrderPayments($request)
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
                    'transaction_date'          => $request->order_date,
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
    public function savePurchaseOrderItems($request)
    {
        $itemsCount = $request->row_count;

        for ($i=0; $i < $itemsCount; $i++) {
            /**
             * If array record not exist then continue forloop
             * */
            if(!isset($request->item_id[$i])){
                continue;
            }

            /**
             * Data index start from 0
             * */
            $itemDetails = Item::find($request->item_id[$i]);
            $itemName           = $itemDetails->name;

            //validate input Quantity
            $itemQuantity       = $request->quantity[$i];
            if(empty($itemQuantity) || $itemQuantity === 0 || $itemQuantity < 0){
                    return [
                        'status' => false,
                        'message' => ($itemQuantity<0) ? __('item.item_qty_negative', ['item_name' => $itemName]) : __('item.please_enter_item_quantity', ['item_name' => $itemName]),
                    ];
            }

            /**
             *
             * Item Transaction Entry
             * */
            $transaction = $this->itemTransactionService->recordItemTransactionEntry($request->modelName, [
                'warehouse_id'              => $request->warehouse_id[$i],
                'transaction_date'          => $request->order_date,
                'item_id'                   => $request->item_id[$i],
                'description'               => $request->description[$i],

                'tracking_type'             => $itemDetails->tracking_type,

                'quantity'                  => $itemQuantity,
                'unit_id'                   => $request->unit_id[$i],
                'unit_price'                => $request->purchase_price[$i],
                'mrp'                       => $request->mrp[$i]??0,

                'discount'                  => $request->discount[$i],
                'discount_type'             => $request->discount_type[$i],
                'discount_amount'           => $request->discount_amount[$i],

                'tax_id'                    => $request->tax_id[$i],
                'tax_type'                  => $request->tax_type[$i],
                'tax_amount'                => $request->tax_amount[$i],

                'total'                     => $request->total[$i],

            ]);

            //return $transaction;
            if(!$transaction){
                throw new \Exception("Failed to record Item Transaction Entry!");
            }

            /**
             * Tracking Type:
             * regular
             * batch
             * serial
             * */
            if($itemDetails->tracking_type == 'serial'){
                //Serial validate and insert records
                if($itemQuantity > 0){
                    $jsonSerials = $request->serial_numbers[$i];
                    $jsonSerialsDecode = json_decode($jsonSerials);

                    /**
                     * Serial number count & Enter Quntity must be equal
                     * */
                    $countRecords = (!empty($jsonSerialsDecode)) ? count($jsonSerialsDecode) : 0;
                    if($countRecords != $itemQuantity){
                        throw new \Exception(__('item.opening_quantity_not_matched_with_serial_records'));
                    }

                    foreach($jsonSerialsDecode as $serialNumber){
                        $serialArray = [
                            'serial_code'       =>  $serialNumber,
                        ];

                        $serialTransaction = $this->itemTransactionService->recordItemSerials($transaction->id, $serialArray, $request->item_id[$i], $request->warehouse_id[$i], ItemTransactionUniqueCode::PURCHASE_ORDER->value);

                        if(!$serialTransaction){
                            throw new \Exception(__('item.failed_to_save_serials'));
                        }
                    }
                }
            }
            else if($itemDetails->tracking_type == 'batch'){
                //Serial validate and insert records
                if($itemQuantity > 0){
                    /**
                     * Record Batch Entry for each batch
                     * */

                    $batchArray = [
                                'batch_no'              =>  $request->batch_no[$i],
                                'mfg_date'              =>  $request->mfg_date[$i]? $this->toSystemDateFormat($request->mfg_date[$i]) : null,
                                'exp_date'              =>  $request->exp_date[$i]? $this->toSystemDateFormat($request->exp_date[$i]) : null,
                                'model_no'              =>  $request->model_no[$i],
                                'mrp'                   =>  $request->mrp[$i]??0,
                                'color'                 =>  $request->color[$i],
                                'size'                  =>  $request->size[$i],
                                'quantity'              =>  $itemQuantity,
                            ];

                        $batchTransaction = $this->itemTransactionService->recordItemBatches($transaction->id, $batchArray, $request->item_id[$i], $request->warehouse_id[$i], ItemTransactionUniqueCode::PURCHASE_ORDER->value);

                        if(!$batchTransaction){
                            throw new \Exception(__('item.failed_to_save_batch_records'));
                        }
                }
            }
            else{
                //Regular item transaction entry already done before if() condition
            }


        }//for end

        return ['status' => true];
    }


    /**
     * Datatabale
     * */
    public function datatableList(Request $request){

        $data = PurchaseOrder::with('user', 'party', 'purchase')
                        ->when($request->party_id, function ($query) use ($request) {
                            return $query->where('party_id', $request->party_id);
                        })
                        ->when($request->user_id, function ($query) use ($request) {
                            return $query->where('created_by', $request->user_id);
                        })
                        ->when($request->from_date, function ($query) use ($request) {
                            return $query->where('order_date', '>=', $this->toSystemDateFormat($request->from_date));
                        })
                        ->when($request->to_date, function ($query) use ($request) {
                            return $query->where('order_date', '<=', $this->toSystemDateFormat($request->to_date));
                        })
                        ->when(!auth()->user()->can('purchase.order.can.view.other.users.purchase.orders'), function ($query) use ($request) {
                            return $query->where('created_by', auth()->user()->id);
                        });

        return DataTables::of($data)
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && $request->search['value']) {
                            $searchTerm = $request->search['value'];
                            $query->where(function ($q) use ($searchTerm) {
                                $q->where('order_code', 'like', "%{$searchTerm}%")
                                  ->orWhere('grand_total', 'like', "%{$searchTerm}%")
                                  ->orWhereHas('party', function ($partyQuery) use ($searchTerm) {
                                      $partyQuery->where('first_name', 'like', "%{$searchTerm}%")
                                            ->orWhere('last_name', 'like', "%{$searchTerm}%");
                                  })
                                  ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                                      $userQuery->where('username', 'like', "%{$searchTerm}%");
                                  });
                            });
                        }
                    })
                    ->addIndexColumn()
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at->format(app('company')['date_format']);
                    })
                    ->addColumn('username', function ($row) {
                        return $row->user->username??'';
                    })
                    ->addColumn('order_date', function ($row) {
                        return $row->formatted_order_date;
                    })
                    ->addColumn('due_date', function ($row) {
                        return $row->formatted_order_date;
                    })
                    ->addColumn('order_code', function ($row) {
                        return $row->order_code;
                    })
                    ->addColumn('party_name', function ($row) {
                        return $row->party->first_name." ".$row->party->last_name;
                    })
                    ->addColumn('grand_total', function ($row) {
                        return $this->formatWithPrecision($row->grand_total);
                    })
                    ->addColumn('balance', function ($row) {
                        return $this->formatWithPrecision($row->grand_total - $row->paid_amount);
                    })
                    // ->addColumn('status', function ($row) {
                    //     return [
                    //         'text' => $row->purchase ? "Converted to Purchase" : "Open",
                    //         'code' => $row->purchase ? $row->purchase->purchase_code : "",
                    //     ];
                    // })
                    ->addColumn('status', function ($row) {
                        if ($row->purchase) {
                            return [
                                'text' => "Converted to Purchase",
                                'code' => $row->purchase->purchase_code,
                                'url'  => route('purchase.bill.details', ['id' => $row->purchase->id]),
                            ];
                        }
                        return [
                            'text' => "",
                            'code' => "",
                            'url'  => "",
                        ];
                    })
                    ->addColumn('color', function ($row) {
                        $purchaseOrderStatus = $this->generalDataService->getSaleOrderStatus();

                        // Find the status matching the given id
                        return collect($purchaseOrderStatus)->firstWhere('id', $row->order_status)['color'];

                    })
                    ->addColumn('action', function($row){
                            $id = $row->id;

                            $editUrl = route('purchase.order.edit', ['id' => $id]);

                            //Verify is it converted or not
                            if($row->purchase){
                                $convertToPurchase = route('purchase.bill.details', ['id' => $row->purchase->id]);
                                $convertToPurchaseText = __('app.view_bill');
                                $convertToPurchaseIcon = 'check-double';
                            }else{
                                $convertToPurchase = route('purchase.bill.convert', ['id' => $id]);
                                $convertToPurchaseText = __('purchase.convert_to_purchase');
                                $convertToPurchaseIcon = 'transfer-alt';
                            }

                            $detailsUrl = route('purchase.order.details', ['id' => $id]);
                            $printUrl = route('purchase.order.print', ['id' => $id]);
                            $pdfUrl = route('purchase.order.pdf', ['id' => $id]);

                            $actionBtn = '<div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded font-22 text-option"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="' . $editUrl . '"><i class="bx bx-edit"></i> '.__('app.edit').'</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="' . $convertToPurchase . '"><i class="bx bx-'.$convertToPurchaseIcon.'"></i> '.$convertToPurchaseText.'</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="' . $detailsUrl . '"></i><i class="bx bx-show-alt"></i> '.__('app.details').'</a>
                                </li>
                                <li>
                                    <a target="_blank" class="dropdown-item" href="' . $printUrl . '"></i><i class="bx bx-printer "></i> '.__('app.print').'</a>
                                </li>
                                <li>
                                    <a target="_blank" class="dropdown-item" href="' . $pdfUrl . '"></i><i class="bx bxs-file-pdf"></i> '.__('app.pdf').'</a>
                                </li>
                                <li>
                                    <a class="dropdown-item notify-through-email" data-model="purchase/order" data-id="' . $id . '" role="button"></i><i class="bx bx-envelope"></i> '.__('app.send_email').'</a>
                                </li>
                                <li>
                                    <a class="dropdown-item notify-through-sms" data-model="purchase/order" data-id="' . $id . '" role="button"></i><i class="bx bx-envelope"></i> '.__('app.send_sms').'</a>
                                </li>

                                <li>
                                    <a class="dropdown-item status-history" data-model="statusHistoryModal" data-id="' . $id . '" role="button"></i><i class="bx bx-book"></i> '.__('app.status_history').'</a>
                                </li>

                                <li>
                                    <button type="button" class="dropdown-item text-danger deleteRequest" data-delete-id='.$id.'><i class="bx bx-trash"></i> '.__('app.delete').'</button>
                                </li>
                            </ul>
                        </div>';
                            return $actionBtn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
    }

    /**
     * Delete Purchase Order Records
     * @return JsonResponse
     * */
    public function delete(Request $request) : JsonResponse{

        DB::beginTransaction();

        $selectedRecordIds = $request->input('record_ids');

        // Perform validation for each selected record ID
        foreach ($selectedRecordIds as $recordId) {
            $record = PurchaseOrder::find($recordId);
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
            // PurchaseOrder::whereIn('id', $selectedRecordIds)->chunk(100, function ($orders) {
            //     foreach ($orders as $order) {
            //         $order->accountTransaction()->delete();
            //         //Load Purchase Order Payment Transactions
            //         $payments = $order->paymentTransaction;
            //         foreach ($payments as $payment) {
            //             //Delete Payment Account Transactions
            //             $payment->accountTransaction()->delete();

            //             //Delete Purchase Order Payment Transactions
            //             $payment->delete();
            //         }
            //     }
            // });

            // //Delete Purchase Order
            // $deletedCount = PurchaseOrder::whereIn('id', $selectedRecordIds)->delete();

            // Attempt deletion (as in previous responses)
            PurchaseOrder::whereIn('id', $selectedRecordIds)->chunk(100, function ($orders) {
                foreach ($orders as $order) {
                    //Purchase Account Update
                    foreach($order->accountTransaction as $orderAccount){
                        //get account if of model with tax accounts
                        $orderAccountId = $orderAccount->account_id;

                        //Delete purchase and tax account
                        $orderAccount->delete();

                        //Update  account
                        $this->accountTransactionService->calculateAccounts($orderAccountId);
                    }//purchase account

                    // Check if paymentTransactions exist
                    $paymentTransactions = $order->paymentTransaction;
                    if ($paymentTransactions->isNotEmpty()) {
                        foreach ($paymentTransactions as $paymentTransaction) {
                            $accountTransactions = $paymentTransaction->accountTransaction;
                            if ($accountTransactions->isNotEmpty()) {
                                foreach ($accountTransactions as $accountTransaction) {
                                    //Purchase Account Update
                                    $accountId = $accountTransaction->account_id;
                                    // Do something with the individual accountTransaction
                                    $accountTransaction->delete(); // Or any other operation

                                    $this->accountTransactionService->calculateAccounts($accountId);
                                }
                            }

                            //delete Payment now
                            $paymentTransaction->delete();
                        }
                    }//isNotEmpty

                    //Delete order
                    $order->delete();

                    //delete item Transactions
                    $order->itemTransaction()->delete();
                }//purchases
            });

            DB::commit();

            return response()->json([
                'status'    => true,
                'message' => __('app.record_deleted_successfully'),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'status'    => false,
                'message' => __('app.cannot_delete_records'),
            ],409);
        }
    }

    /**
     * Prepare Email Content to view
     * */
    public function getEmailContent($id)
    {
        $model = PurchaseOrder::with('party')->find($id);

        $emailData = $this->purchaseOrderEmailNotificationService->purchaseOrderCreatedEmailNotification($id);

        $subject = ($emailData['status']) ? $emailData['data']['subject'] : '';
        $content = ($emailData['status']) ? $emailData['data']['content'] : '';

        $data = [
            'email'  => $model->party->email,
            'subject'  => $subject,
            'content'  => $content,
        ];
        return $data;
    }

    /**
     * Prepare SMS Content to view
     * */
    public function getSMSContent($id)
    {
        $model = PurchaseOrder::with('party')->find($id);

        $emailData = $this->purchaseOrderSmsNotificationService->purchaseOrderCreatedSmsNotification($id);

        $mobile = ($emailData['status']) ? $emailData['data']['mobile'] : '';
        $content = ($emailData['status']) ? $emailData['data']['content'] : '';

        $data = [
            'mobile'  => $mobile,
            'content'  => $content,
        ];
        return $data;
    }

    /***
     * View Status History
     *
     * */
    public function getStatusHistory($id) : JsonResponse{

        $data = $this->statusHistoryService->getStatusHistoryData(PurchaseOrder::find($id));

        return response()->json([
            'status' => true,
            'message' => '',
            'data'  => $data,
        ]);

    }

}
