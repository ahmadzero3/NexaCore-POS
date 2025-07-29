<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Prefix;
use App\Models\Purchase\PurchaseOrder;
use App\Models\Purchase\Purchase;
use App\Models\Items\Item;
use App\Traits\FormatNumber;
use App\Traits\FormatsDateInputs;
use App\Enums\App;
use App\Enums\General;
use App\Services\PaymentTypeService;
use App\Services\GeneralDataService;
use App\Services\PaymentTransactionService;
use App\Http\Requests\PurchaseRequest;
use App\Services\AccountTransactionService;
use App\Services\ItemTransactionService;
use App\Models\Items\ItemSerial;
use App\Models\Items\ItemBatchTransaction;
use Carbon\Carbon;
use App\Services\CacheService;
use App\Services\ItemService;
use App\Enums\ItemTransactionUniqueCode;
use App\Services\Communication\Email\PurchaseBillEmailNotificationService;
use App\Services\Communication\Sms\PurchaseBillSmsNotificationService;

use Mpdf\Mpdf;

class PurchaseController extends Controller
{
    use FormatNumber;

    use FormatsDateInputs;

    protected $companyId;

    private $paymentTypeService;

    private $paymentTransactionService;

    private $accountTransactionService;

    private $itemTransactionService;

    private $itemService;

    public $previousHistoryOfItems;

    public $purchaseBillEmailNotificationService;

    public $purchaseBillSmsNotificationService;

    public function __construct(PaymentTypeService $paymentTypeService,
                                PaymentTransactionService $paymentTransactionService,
                                AccountTransactionService $accountTransactionService,
                                ItemTransactionService $itemTransactionService,
                                ItemService $itemService,
                                PurchaseBillEmailNotificationService $purchaseBillEmailNotificationService,
                                PurchaseBillSmsNotificationService $purchaseBillSmsNotificationService
                            )
    {
        $this->companyId = App::APP_SETTINGS_RECORD_ID->value;
        $this->paymentTypeService = $paymentTypeService;
        $this->paymentTransactionService = $paymentTransactionService;
        $this->accountTransactionService = $accountTransactionService;
        $this->itemTransactionService = $itemTransactionService;
        $this->itemService = $itemService;
        $this->purchaseBillEmailNotificationService = $purchaseBillEmailNotificationService;
        $this->purchaseBillSmsNotificationService = $purchaseBillSmsNotificationService;
        $this->previousHistoryOfItems = [];
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
            'prefix_code' => $prefix->purchase_bill,
            'count_id' => ($lastCountId+1),
        ];
        return view('purchase.bill.create',compact('data', 'selectedPaymentTypesArray'));
    }

    /**
     * Get last count ID
     * */
    public function getLastCountId(){
        return Purchase::select('count_id')->orderBy('id', 'desc')->first()?->count_id ?? 0;
    }

    /**
     * List the orders
     *
     * @return \Illuminate\View\View
     */
    public function list() : View {
        return view('purchase.bill.list');
    }


    /**
     * Edit a Purchase Order.
     *
     * @param int $id The ID of the expense to edit.
     * @return \Illuminate\View\View
     */
    public function convertToPurchase($id) : View | RedirectResponse {

        //Validate Existance of Converted Purchase Orders
        $convertedBill = Purchase::where('purchase_order_id', $id)->first();
        if($convertedBill){
            session(['record' => [
                                    'type' => 'success',
                                    'status' => __('purchase.already_converted'), //Save or update
                                ]]);
            //Already Converted, Redirect it.
            return redirect()->route('purchase.bill.details', ['id' => $convertedBill->id]);
        }

        $purchase = PurchaseOrder::with(['party',
                                        'itemTransaction' => [
                                            'item',
                                            'tax',
                                            'batch.itemBatchMaster',
                                            'itemSerialTransaction.itemSerialMaster'
                                        ]])->findOrFail($id);

        // Add formatted dates from ItemBatchMaster model
        $purchase->itemTransaction->each(function ($transaction) {
            if (!$transaction->batch?->itemBatchMaster) {
                return;
            }
            $batchMaster = $transaction->batch->itemBatchMaster;
            $batchMaster->mfg_date = $batchMaster->getFormattedMfgDateAttribute();
            $batchMaster->exp_date = $batchMaster->getFormattedExpDateAttribute();
        });

        //Convert Code adjustment - start
        $purchase->operation = 'convert';
        $purchase->formatted_purchase_date = $this->toSystemDateFormat($purchase->order_date);
        $purchase->reference_no = '';
        //Convert Code adjustment - end

        $prefix = Prefix::findOrNew($this->companyId);
        $lastCountId = $this->getLastCountId();
        $purchase->prefix_code = $prefix->purchase_bill;
        $purchase->count_id = ($lastCountId+1);

        $purchase->formatted_purchase_date = $this->toUserDateFormat(date('Y-m-d'));

        // Item Details
        // Prepare item transactions with associated units
        $allUnits = CacheService::get('unit');

        $itemTransactions = $purchase->itemTransaction->map(function ($transaction) use ($allUnits ) {
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

        //Payment Details
        $selectedPaymentTypesArray = json_encode($this->paymentTransactionService->getPaymentRecordsArray($purchase));

        $taxList = CacheService::get('tax')->toJson();

        $paymentHistory = [];

        return view('purchase.bill.edit', compact('taxList', 'purchase', 'itemTransactionsJson','selectedPaymentTypesArray', 'paymentHistory'));
    }

     /**
     * Edit a Purchase Order.
     *
     * @param int $id The ID of the expense to edit.
     * @return \Illuminate\View\View
     */
    public function edit($id) : View {
        $purchase = Purchase::with(['party',
                                        'itemTransaction' => [
                                            'item',
                                            'tax',
                                            'batch.itemBatchMaster',
                                            'itemSerialTransaction.itemSerialMaster'
                                        ]])->findOrFail($id);

        $purchase->operation = 'update';

        // Add formatted dates from ItemBatchMaster model
        $purchase->itemTransaction->each(function ($transaction) {
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

        $itemTransactions = $purchase->itemTransaction->map(function ($transaction) use ($allUnits ) {
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

        $paymentHistory = $this->paymentTransactionService->getPaymentRecordsArray($purchase);

        $taxList = CacheService::get('tax')->toJson();

        return view('purchase.bill.edit', compact('taxList', 'purchase', 'itemTransactionsJson','selectedPaymentTypesArray', 'paymentHistory'));
    }

    /**
     * View Purchase Order details
     *
     * @param int $id, the ID of the order
     * @return \Illuminate\View\View
     */
    public function details($id) : View {
        $purchase = Purchase::with(['party',
                                        'itemTransaction' => [
                                            'item',
                                            'tax',
                                            'batch.itemBatchMaster',
                                            'itemSerialTransaction.itemSerialMaster'
                                        ]])->findOrFail($id);

        //Payment Details
        $selectedPaymentTypesArray = json_encode($this->paymentTransactionService->getPaymentRecordsArray($purchase));

        //Batch Tracking Row count for invoice columns setting
        $batchTrackingRowCount = (new GeneralDataService())->getBatchTranckingRowCount();


        return view('purchase.bill.details', compact('purchase','selectedPaymentTypesArray', 'batchTrackingRowCount'));
    }

    /**
     * Print Purchase
     *
     * @param int $id, the ID of the purchase
     * @return \Illuminate\View\View
     */
    public function print($id, $isPdf = false, $thermalPrint = false) : View {

        $purchase = Purchase::with(['party',
                                        'itemTransaction' => [
                                            'item',
                                            'tax',
                                            'batch.itemBatchMaster',
                                            'itemSerialTransaction.itemSerialMaster'
                                        ]])->findOrFail($id);

        //Payment Details
        $selectedPaymentTypesArray = json_encode($this->paymentTransactionService->getPaymentRecordsArray($purchase));

        //Batch Tracking Row count for invoice columns setting
        $batchTrackingRowCount = (new GeneralDataService())->getBatchTranckingRowCount();

        $invoiceData = [
            'name' => __('purchase.bill'),
        ];

        if($thermalPrint){
            return view('print.purchase.thermal', compact('isPdf', 'invoiceData', 'purchase','selectedPaymentTypesArray','batchTrackingRowCount'));
        }
        else{
            return view('print.purchase.purchase', compact('isPdf', 'invoiceData', 'purchase','selectedPaymentTypesArray','batchTrackingRowCount'));
        }

    }

    /**
     * Thermal Print Purchase
     *
     * @param int $id, the ID of the purchase
     * @return \Illuminate\View\View
     */
    public function thermalPrint($id) : View {
        return $this->print($id, isPdf:false, thermalPrint:true);
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
        $mpdf->Output('Purchase-Bill-'.$id.'.pdf', 'D');
    }

    /**
     * Store Records
     * */
    public function store(PurchaseRequest $request) : JsonResponse  {
        try {

            DB::beginTransaction();
            // Get the validated data from the expenseRequest
            $validatedData = $request->validated();

            if($request->operation == 'save' || $request->operation == 'convert'){
                // Create a new purchase record using Eloquent and save it
                $newPurchase = Purchase::create($validatedData);

                $request->request->add(['purchase_id' => $newPurchase->id]);
            }
            else{
                $fillableColumns = [
                    'party_id'              => $validatedData['party_id'],
                    'purchase_date'         => $validatedData['purchase_date'],
                    'reference_no'          => $validatedData['reference_no'],
                    'prefix_code'           => $validatedData['prefix_code'],
                    'count_id'              => $validatedData['count_id'],
                    'purchase_code'         => $validatedData['purchase_code'],
                    'note'                  => $validatedData['note'],
                    'round_off'             => $validatedData['round_off'],
                    'grand_total'           => $validatedData['grand_total'],
                    'state_id'              => $validatedData['state_id'],
                    'currency_id'           => $validatedData['currency_id'],
                    'exchange_rate'         => $validatedData['exchange_rate'],
                    'is_shipping_charge_distributed' => $validatedData['is_shipping_charge_distributed'],
                    'shipping_charge'       => $validatedData['shipping_charge'],
                ];

                $newPurchase = Purchase::findOrFail($validatedData['purchase_id']);
                $newPurchase->update($fillableColumns);

                /**
                * Before deleting ItemTransaction data take the
                * old data of the item_serial_master_id
                * to update the item_serial_quantity
                * */
               $this->previousHistoryOfItems = $this->itemTransactionService->getHistoryOfItems($newPurchase);

                $newPurchase->itemTransaction()->delete();
                //$newPurchase->accountTransaction()->delete();

                //Purchase Account Update
                foreach($newPurchase->accountTransaction as $purchaseAccount){
                    //get account if of model with tax accounts
                    $purchaseAccountId = $purchaseAccount->account_id;

                    //Delete purchase and tax account
                    $purchaseAccount->delete();

                    //Update  account
                    $this->accountTransactionService->calculateAccounts($purchaseAccountId);
                }//purchase account


                // Check if paymentTransactions exist
                // $paymentTransactions = $newPurchase->paymentTransaction;
                // if ($paymentTransactions->isNotEmpty()) {
                //     foreach ($paymentTransactions as $paymentTransaction) {
                //         $accountTransactions = $paymentTransaction->accountTransaction;
                //         if ($accountTransactions->isNotEmpty()) {
                //             foreach ($accountTransactions as $accountTransaction) {
                //                 //Purchase Account Update
                //                 $accountId = $accountTransaction->account_id;
                //                 // Do something with the individual accountTransaction
                //                 $accountTransaction->delete(); // Or any other operation

                //                 $this->accountTransactionService->calculateAccounts($accountId);
                //             }
                //         }
                //     }
                // }

                // $newPurchase->paymentTransaction()->delete();
            }

            $request->request->add(['modelName' => $newPurchase]);

            /**
             * Save Table Items in Purchase Items Table
             * */
            $PurchaseItemsArray = $this->savePurchaseItems($request);
            if(!$PurchaseItemsArray['status']){
                throw new \Exception($PurchaseItemsArray['message']);
            }

            /**
             * Save Expense Payment Records
             * */
            $purchasePaymentsArray = $this->savePurchasePayments($request);
            if(!$purchasePaymentsArray['status']){
                throw new \Exception($purchasePaymentsArray['message']);
            }

            /**
            * Payment Should not be less than 0
            * */
            $paidAmount = $newPurchase->refresh('paymentTransaction')->paymentTransaction->sum('amount');
            if($paidAmount < 0){
                throw new \Exception(__('payment.paid_amount_should_not_be_less_than_zero'));
            }

            /**
             * Paid amount should not be greater than grand total
             * */
            if($paidAmount > $newPurchase->grand_total){
                throw new \Exception(__('payment.payment_should_not_be_greater_than_grand_total')."<br>Paid Amount : ". $this->formatWithPrecision($paidAmount)."<br>Grand Total : ". $this->formatWithPrecision($newPurchase->grand_total). "<br>Difference : ".$this->formatWithPrecision($paidAmount-$newPurchase->grand_total));
            }

            /**
             * Update Purchase Model
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
            $accountTransactionStatus = $this->accountTransactionService->purchaseAccountTransaction($request->modelName);
            if(!$accountTransactionStatus){
                throw new \Exception(__('payment.failed_to_update_account'));
            }


            /**
             * UPDATE HISTORY DATA
             * LIKE: ITEM SERIAL NUMBER QUNATITY, BATCH NUMBER QUANTITY, GENERAL DATA QUANTITY
             * */
            $previousItemStockUpdate = $this->itemTransactionService->updatePreviousHistoryOfItems($request->modelName, $this->previousHistoryOfItems);
            if(!$previousItemStockUpdate){
                throw new \Exception("Failed to update Previous Item Stock!");
            }


            //Update Item Master Average Purchase Price
            $this->itemTransactionService->updatePurchasedItemsPurchasePrice($request->purchase_id);

            DB::commit();

            // Regenerate the CSRF token
            //Session::regenerateToken();

            return response()->json([
                'status'    => true,
                'message' => __('app.record_saved_successfully'),
                'id' => $request->purchase_id,

            ]);

        } catch (\Exception $e) {
                DB::rollback();

                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 409);

        }

    }


    public function savePurchasePayments($request)
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
                    'transaction_date'          => $request->purchase_date,
                    'amount'                    => $amount,
                    'payment_type_id'           => $request->payment_type_id[$i],
                    'note'                      => $request->payment_note[$i],
                    'payment_from_unique_code'  => General::INVOICE->value,
                ];
                if(!$transaction = $this->paymentTransactionService->recordPayment($request->modelName, $paymentsArray)){
                    throw new \Exception(__('payment.failed_to_record_payment_transactions'));
                }

            }//amount>0
        }//for end

        return ['status' => true];
    }

    public function updateItemMasterPurchasePrice($request, $i){
        //If auto update purchase price is disabled then return
        if(!app('company')['auto_update_purchase_price']){
            return;
        }

        //If enabled average purchase price then return
        if(app('company')['auto_update_average_purchase_price']){
            return;
        }
        /**
         * Update Item Master
         * Purchase Price
         * */
        $updateItemMaster = Item::find($request->item_id[$i]);
        if(!empty($request->purchase_price[$i]) && $request->purchase_price[$i]>0){
            if($updateItemMaster->base_unit_id != $request->unit_id[$i]){
                $purchasePrice = $request->purchase_price[$i] * $updateItemMaster->conversion_rate;
            }
            else{
                $purchasePrice = $request->purchase_price[$i];
            }
            $updateItemMaster->purchase_price = $purchasePrice;
            $updateItemMaster->is_purchase_price_with_tax = ($request->tax_type[$i] == 'inclusive') ? 1 : 0;
        }
        $updateItemMaster->save();
        return true;
    }

    /**
     * Calculate Shipping Cost in Each Item
     *
     * Formula:
     * (ShippingCharge/TotalInvoiceAmount) * TotalItemAmount
     *
     * where,
     * TotalInvoiceAmount is without tax & discount
     * TotalItemAmount is without tax & discount
     *
     */
    public function updateShippingCost($model){
        $itemTransactions = $model->refresh()->itemTransaction()->with('tax')->get();
        if($itemTransactions->isNotEmpty() && $model->shipping_charge > 0 && $model->is_shipping_charge_distributed == 1){

            //Calculate itemTransaction unit_price * quantity, give me sum of it
            //Use foreach
            $totalItemAmount = $itemTransactions->map(function($itemTransaction) {
                return $itemTransaction->unit_price * $itemTransaction->quantity;
            })->sum();

            //Update charge_amount in itemTransaction model for each entry
            $itemTransactions->map(function($itemTransaction) use ($model, $totalItemAmount) {
                $itemTransaction->charge_amount = ($model->shipping_charge / $totalItemAmount) * $itemTransaction->unit_price * $itemTransaction->quantity;
                /**
                 * Calculate Charge Tax Amount
                 * get the tax value from tax model, where tax is morph with itemTransaction as well
                 * Tax model has the taxrate column
                 */
                $itemTransaction->charge_tax_amount = ($itemTransaction->charge_amount * $itemTransaction->tax->rate)/100;


                $itemTransaction->save();
            });

        }
    }

    public function savePurchaseItems($request)
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

            //Auto-Update Item Master Purchase Price
            $this->updateItemMasterPurchasePrice($request, $i);



            /**
             *
             * Item Transaction Entry
             * */
            $transaction = $this->itemTransactionService->recordItemTransactionEntry($request->modelName, [
                'warehouse_id'              => $request->warehouse_id[$i],
                'transaction_date'          => $request->purchase_date,
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


                'charge_type'               => 'shipping',
                'charge_amount'             => 0,

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
             * Update Shipping Cost
             * */
            $this->updateShippingCost($request->modelName);

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

                        $serialTransaction = $this->itemTransactionService->recordItemSerials($transaction->id, $serialArray, $request->item_id[$i], $request->warehouse_id[$i], ItemTransactionUniqueCode::PURCHASE->value);

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

                    $batchTransaction = $this->itemTransactionService->recordItemBatches($transaction->id, $batchArray, $request->item_id[$i], $request->warehouse_id[$i], ItemTransactionUniqueCode::PURCHASE->value);

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

        $data = Purchase::with('user', 'party')
                        ->when($request->party_id, function ($query) use ($request) {
                            return $query->where('party_id', $request->party_id);
                        })
                        ->when($request->user_id, function ($query) use ($request) {
                            return $query->where('created_by', $request->user_id);
                        })
                        ->when($request->from_date, function ($query) use ($request) {
                            return $query->where('purchase_date', '>=', $this->toSystemDateFormat($request->from_date));
                        })
                        ->when($request->to_date, function ($query) use ($request) {
                            return $query->where('purchase_date', '<=', $this->toSystemDateFormat($request->to_date));
                        })
                        ->when(!auth()->user()->can('purchase.bill.can.view.other.users.purchase.bills'), function ($query) use ($request) {
                            return $query->where('created_by', auth()->user()->id);
                        });

        return DataTables::of($data)
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && $request->search['value']) {
                            $searchTerm = $request->search['value'];
                            $query->where(function ($q) use ($searchTerm) {
                                $q->where('purchase_code', 'like', "%{$searchTerm}%")
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
                    ->addColumn('purchase_date', function ($row) {
                        return $row->formatted_purchase_date;
                    })

                    ->addColumn('purchase_code', function ($row) {
                        return $row->purchase_code;
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


                    ->addColumn('status', function ($row) {
                        if ($row->purchaseOrder) {
                            return [
                                'text' => "Converted from Purchase Order",
                                'code' => $row->purchaseOrder->order_code,
                                'url'  => route('purchase.order.details', ['id' => $row->purchaseOrder->id]),
                            ];
                        }

                        return [
                            'text' => "",
                            'code' => "",
                            'url'  => "",
                        ];
                    })

                    ->addColumn('is_return_raised', function ($row) {
                        $returns = $row->purchaseReturn()->get(); // Get all return records

                        if ($returns->isNotEmpty()) {
                            $returnCodes = $returns->pluck('return_code')->toArray(); // Get return codes
                            $returnIds = $returns->pluck('id')->toArray(); // Get return IDs

                            return [
                                'status' => "Return Raised",
                                'codes'  => implode(', ', $returnCodes), // Convert codes to comma-separated string
                                'urls'   => array_map(function ($id) {
                                    return route('purchase.return.details', ['id' => $id]);
                                }, $returnIds), // Generate URLs for each return ID
                            ];
                        }
                        return [
                            'status' => "",
                            'codes'  => "",
                            'urls'   => [],
                        ];
                    })

                    ->addColumn('action', function($row){
                            $id = $row->id;

                            $editUrl = route('purchase.bill.edit', ['id' => $id]);
                            $deleteUrl = route('purchase.bill.delete', ['id' => $id]);
                            $detailsUrl = route('purchase.bill.details', ['id' => $id]);
                            $printUrl = route('purchase.bill.print', ['id' => $id]);
                            $pdfUrl = route('purchase.bill.pdf', ['id' => $id]);

                            //Verify is it converted or not
                            /*if($row->purchaseReturn){
                                $convertToPurchase = route('purchase.return.details', ['id' => $row->purchaseReturn->id]);
                                $convertToPurchaseText = __('app.view_bill');
                                $convertToPurchaseIcon = 'check-double';
                            }else{*/
                                $convertToPurchase = route('purchase.return.convert', ['id' => $id]);
                                $convertToPurchaseText = __('purchase.convert_to_return');
                                $convertToPurchaseIcon = 'transfer-alt';
                            //}

                            $actionBtn = '<div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded font-22 text-option"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="' . $editUrl . '"><i class="bi bi-trash"></i><i class="bx bx-edit"></i> '.__('app.edit').'</a>
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
                                    <a class="dropdown-item make-payment" data-invoice-id="' . $id . '" role="button"></i><i class="bx bx-money"></i> '.__('payment.make_payment').'</a>
                                </li>
                                <li>
                                    <a class="dropdown-item payment-history" data-invoice-id="' . $id . '" role="button"></i><i class="bx bx-table"></i> '.__('payment.history').'</a>
                                </li>
                                <li>
                                    <a class="dropdown-item notify-through-email" data-model="purchase/bill" data-id="' . $id . '" role="button"></i><i class="bx bx-envelope"></i> '.__('app.send_email').'</a>
                                </li>
                                <li>
                                    <a class="dropdown-item notify-through-sms" data-model="purchase/bill" data-id="' . $id . '" role="button"></i><i class="bx bx-envelope"></i> '.__('app.send_sms').'</a>
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
     * Delete Purchase Records
     * @return JsonResponse
     * */
    public function delete(Request $request) : JsonResponse{

        DB::beginTransaction();

        $selectedRecordIds = $request->input('record_ids');

        // Perform validation for each selected record ID
        foreach ($selectedRecordIds as $recordId) {
            $record = Purchase::find($recordId);
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
            Purchase::whereIn('id', $selectedRecordIds)->chunk(100, function ($purchases) {
                foreach ($purchases as $purchase) {
                    /**
                    * Before deleting ItemTransaction data take the
                    * old data of the item_serial_master_id
                    * to update the item_serial_quantity
                    * */
                   $this->previousHistoryOfItems = $this->itemTransactionService->getHistoryOfItems($purchase);
                    //Purchase Account Update
                    // foreach($purchase->accountTransaction as $purchaseAccount){
                    //     //get account if of model with tax accounts
                    //     $purchaseAccountId = $purchaseAccount->account_id;

                    //     //Delete purchase and tax account
                    //     $purchaseAccount->delete();

                    //     //Update  account
                    //     $this->accountTransactionService->calculateAccounts($purchaseAccountId);
                    // }//purchase account

                    // Check if paymentTransactions exist
                    $paymentTransactions = $purchase->paymentTransaction;
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

                    $itemIdArray = [];
                    //Purchasr Item delete and update the stock
                    foreach($purchase->itemTransaction as $itemTransaction){
                        //get item id
                        $itemId = $itemTransaction->item_id;

                        //delete item Transactions
                        $itemTransaction->delete();

                        $itemIdArray[] = $itemId;
                    }//purchase account





                    //Delete Purchase
                    $purchase->delete();


                    /**
                     * UPDATE HISTORY DATA
                     * LIKE: ITEM SERIAL NUMBER QUNATITY, BATCH NUMBER QUANTITY, GENERAL DATA QUANTITY
                     * */

                     $this->itemTransactionService->updatePreviousHistoryOfItems($purchase, $this->previousHistoryOfItems);

                     //Update Average Purchase Price of the item

                     $this->itemTransactionService->updateItemMasterAveragePurchasePrice($itemIdArray);


                    //Update stock update in master
                    if(count($itemIdArray) > 0){
                        foreach($itemIdArray as $itemId){
                            $this->itemService->updateItemStock($itemId);


                        }
                    }

                }//purchases

            });//chunk

            //Delete Purchase
            $deletedCount = Purchase::whereIn('id', $selectedRecordIds)->delete();

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
        $model = Purchase::with('party')->find($id);

        $emailData = $this->purchaseBillEmailNotificationService->purchaseCreatedEmailNotification($id);

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
        $model = Purchase::with('party')->find($id);

        $emailData = $this->purchaseBillSmsNotificationService->purchaseCreatedSmsNotification($id);

        $mobile = ($emailData['status']) ? $emailData['data']['mobile'] : '';
        $content = ($emailData['status']) ? $emailData['data']['content'] : '';

        $data = [
            'mobile'  => $mobile,
            'content'  => $content,
        ];
        return $data;
    }

    /**
     *
     * Load Purchased Items Data
     */
    function getPurchasedItemsData($partyId, $itemId = null)
    {
        try {
            $purchases = Purchase::with([
                                'party',
                                'itemTransaction' => fn($query) => $query->when($itemId, fn($q) => $q->where('item_id', $itemId)),
                                'itemTransaction.item.brand',
                                'itemTransaction.item.tax',
                                'itemTransaction.warehouse'])
                        ->where('party_id', $partyId)
                        ->get();

            if ($purchases->isEmpty()) {
                throw new \Exception('No Records found!!');
            }

            // Extract the first party name for display (assuming all purchases belong to the same party)
            $partyName = $purchases->first()->party->getFullName();

            $data = $purchases->map(function ($purchase) {
                return [
                    'purchased_items' => $purchase->itemTransaction->map(function ($transaction) use ($purchase) {
                        return [
                            'id' => $transaction->id,
                            'purchase_code' => $purchase->purchase_code,
                            'purchase_date' => $this->toUserDateFormat($purchase->purchase_date),
                            'warehouse_name' => $transaction->warehouse->name,

                            'item_id' => $transaction->item_id,
                            'item_name' => "<span class='text-primary'>{$transaction->item->name}</span><br><i>[<b>Code: </b>{$transaction->item->item_code}]</i>",

                            'brand_name' => $transaction->brand->name??'',

                            'unit_price' => $this->formatWithPrecision($transaction->unit_price),
                            'quantity' => $this->formatQuantity($transaction->quantity),
                            'discount_amount' => $this->formatQuantity($transaction->discount_amount),
                            'tax_id' => $transaction->tax_id,
                            'tax_name' => $transaction->item->tax->name,
                            'tax_amount' => $this->formatQuantity($transaction->tax_amount),
                            'total' => $this->formatQuantity($transaction->total),
                        ];
                    })->toArray(),
                ];
            });

            // Include the party name in the response
            return [
                'party_name' => $partyName,
                'purchased_items' => $data->flatMap(function ($purchase) {
                    return $purchase['purchased_items'];
                })->toArray(),
            ];
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 409);
        }
    }

}
