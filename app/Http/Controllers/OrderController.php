<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrderReportRequest;
use App\Http\Requests\OrderPaymentReportRequest;

use App\Traits\FormatsDateInputs;

use App\Models\Tax;
use App\Models\Order;
use App\Models\OrderedProduct;
use App\Models\OrderPayment;
use App\Models\Prefix;
use App\Enums\App;
use App\Events\OrderPaymentsEvent;
use App\Services\OrderNotificationService;
use App\Models\SmtpSettings;
use App\Mail\SendEmail;

class OrderController extends Controller
{
    use FormatsDateInputs;

    protected $companyId;

    private $orderNotificationService;

    public function __construct(OrderNotificationService $orderNotificationService)
    {
        $this->companyId = App::APP_SETTINGS_RECORD_ID->value;
        $this->orderNotificationService = $orderNotificationService;
    }
    /**
     * Get last count ID
     * */
    public function getLastCountId(){
        return Order::select('count_id')->orderBy('id', 'desc')->first()?->count_id ?? 0;
    }
    /**
     * Create a new order.
     *
     * @return \Illuminate\View\View
     */
    public function create()  {
        $prefix = Prefix::findOrNew($this->companyId);
        $lastCountId = $this->getLastCountId();
        $data = [
            'prefix_code' => $prefix->order,
            'count_id' => ($lastCountId+1),
        ];
        return view('order.create', compact('data'));
    }

    /**
     * Edit a order.
     *
     * @param int $id The ID of the order to edit.
     * @return \Illuminate\View\View
     */
    public function edit($id) : View {
        $order = Order::find($id);
        $payments = OrderPayment::with('paymentType')->where('order_id', $id)->get();
        return view('order.edit', compact('order', 'payments'));
    }

    /**
     * Print receipt
     *
     * @param int $id, the ID of the order
     * @return \Illuminate\View\View
     */
    public function receipt($id) : View {
        $order = Order::with([ 'orderedProducts', 'party' ])->find($id);
        $payments = OrderPayment::with('paymentType')->where('order_id', $id)->get();
        return view('order.receipt', compact('order', 'payments'));
    }

    /**
     * Order Timeline
     *
     * @param int $id, the ID of the order
     * @return \Illuminate\View\View
     */
    public function timeline($id) : View {
        $order = Order::with([ 'orderedProducts', 'party' ])->find($id);

        $payments = OrderPayment::with('paymentType')->where('order_id', $id)->get();
        return view('order.timeline', compact('order', 'payments'));
    }

    /**
     * Update Job code
     * @return boolean
     * */
    public function updateJobCode($orderId) 
    {
        $prefix = Prefix::findOrNew($this->companyId);

        $orderedProducts = OrderedProduct::select(['id','job_code'])
                            ->where('job_code','=',null)
                            ->where('order_id','=',$orderId)->get();
        if ($orderedProducts->isNotEmpty()) {
            foreach($orderedProducts as $record){
                $job_code = $prefix->job_code.$record->id;
                OrderedProduct::whereId($record->id)->update(['job_code'=>$job_code]);
            }
            return true;
        }else{
            return true;
        }
    }
    
    /**
     * Return JsonResponse
     * */
    public function store(OrderRequest $request) {
        try {
                DB::beginTransaction();

                $insertOrderedProducts = [];

                $timestamp = now();

                // Get the validated data from the OrderRequest
                $validatedData = $request->validated();

                //Start: Order Code related
                $countId = $this->makeOrderCode($request['prefix_code'],$request['count_id']);
                $validatedData['prefix_code'] = $request['prefix_code'];
                $validatedData['count_id'] = $countId;
                $validatedData['order_code'] = $request['prefix_code'].$countId;
                //End: Order Code related
                $orderModel = Order::create($validatedData);

                /**
                 * Code for table records
                 * */
                $rowCount = $request['row_count'];
                if($rowCount == 0){
                    return response()->json([
                        'status' => false,
                        'message' => __('service.please_select_services'),
                    ],409);
                }
                //$lastId = OrderedProduct::latest()->first()->id ?? 0;
                for ($i=0; $i < $rowCount ; $i++) {
                    $quantity = $request->input('quantity.'.$i, 0);

                    if(!$request->input('service_id.'.$i)){
                        continue;
                    }

                    if(empty($quantity) || $quantity === 0){
                        return response()->json([
                            'message' => __('service.please_select_service_quantity'),
                        ], 409);
                    }

                    $insertOrderedProducts[] = [
                        //'job_code'              => $prefix->job_code. ++$lastId,
                        'order_id'              => $orderModel->id,
                        'service_id'            => $request->input('service_id.'.$i),
                        'description'           => $request->input('description.'.$i, null),
                        'unit_price'             => $request->input('unit_price.'.$i, 0),
                        'quantity'              => $request->input('quantity.'.$i, 0),
                        'total_price'           => $request->input('total_unit_price.'.$i, 0),

                        'discount'              => $request->input('discount.'.$i, 0),
                        'discount_type'         => $request->input('discount_type.'.$i, null),
                        'discount_amount'       => $request->input('discount_amount.'.$i, 0),
                        'total_price_after_discount'  => $request->input('total_price_after_discount.'.$i, 0),

                        'start_date'            => $this->toSystemDateFormat($request->input('start_date.'.$i, null)),
                        'start_time'            => $request->input('start_time.'.$i, null),

                        'end_date'              => $this->toSystemDateFormat($request->input('end_date.'.$i, null)),
                        'end_time'              => $request->input('end_time.'.$i, null),

                        'tax_id'                => $request->input('tax_id.'.$i),
                        'tax_type'              => $request->input('tax_type.'.$i),
                        'tax_amount'            => $request->input('tax_amount.'.$i),

                        'total_price_with_tax'  => $request->input('total.'.$i, 0),
                        'created_at'            => $timestamp,
                        'updated_at'            => $timestamp,
                    ];
                }//end: forloop

                //Ordered Products Bulk insert
                OrderedProduct::insert($insertOrderedProducts);

                /**
                 * Update Job Code
                 * */
                $this->updateJobCode($orderModel->id);

                /**
                 * Record Payment Details
                 * */
                $paidAmount     = $request->input('amount');
                $paymentTypeId    = $request->input('payment_type_id');
                if($paidAmount && $paidAmount > 0){
                    if(empty($paymentTypeId)){
                        return response()->json([
                            'message' => __('payment.please_select_payment_type'),
                        ], 409);
                    }
                    if($paidAmount > $request->input('total_amount')){
                        return response()->json([
                            'message' => __('payment.payment_should_not_be_greater_than_grand_total'),
                        ], 409);
                    }
                    $paymentNote    = $request->input('payment_note');

                    OrderPayment::create([
                        'payment_date'      => $request['order_date'],
                        'order_id'          => $orderModel->id,
                        'payment_type_id'   => $paymentTypeId,
                        'amount'            => $paidAmount,
                        'note'              => $request->input('payment_note'),
                    ]);

                }

                /**
                 * Call Event to update Orders Model toupdate Payment status
                 * @OrderPaymentsEvent
                 * */
                event(new OrderPaymentsEvent(['order_id' => $orderModel->id ]));

                /**
                 * Notfify Customer By SMS
                 * And Setup the Session
                 * @return [bool, message]
                 * */
                $sendSmsFlag = $request->input('send_sms');
                $smsResponseMessage = null;
                if ($sendSmsFlag === 'on') {
                    $notificationResponse = $this->orderNotificationService->orderCreatedSmsNotification($orderModel->id);
                    $smsResponseMessage = $notificationResponse['message'];
                }

                /**
                 * Notfify Customer By Email
                 * And Setup the Session
                 * @return [bool, message]
                 * */
                $sendEmailFlag = $request->input('send_email');
                $emailResponseMessage = null;
                if($sendEmailFlag === 'on'){
                    $notificationResponse = $this->orderNotificationService->orderCreatedEmailNotification($orderModel->id);
                    $emailResponseMessage = $notificationResponse['message'];
                }
                
                session(['record' => [
                                    'type' => 'success',
                                    'status' => null, //Save or update
                                    'sms' => null,
                                    'email' => $emailResponseMessage,
                                ]]);

                DB::commit();

                // Retrieve the 'record' array
                $session = session('record');

                // Update the 'save' key
                $session['status'] = __('app.record_saved_successfully');

                // Store the modified array back in the session
                session(['record' => $session]);

                return response()->json([
                    'status'    => false,
                    'message' => __('app.record_saved_successfully'),
                    'id' => $orderModel->id,
                ]);

        } catch (\Exception $e) {
                DB::rollback();

                Log::channel('custom')->critical($e->getMessage());

                return response()->json([
                    'message' => __('app.something_went_wrong').__('app.check_custom_log_file').$e->getMessage(),
                ], 409);

        }
    }
    /**
     * Update the model
     * @return JsonResponse
     * */
    public function update(OrderRequest $request): JsonResponse {
        try {
                DB::beginTransaction();
                $insertOrderedProducts = [];

                $timestamp = now();

                $orderId = $request->input('order_id');

                // Get the validated data from the OrderRequest
                $validatedData = $request->validated();

                //Start: Order Code related
                $validatedData['prefix_code'] = $request['prefix_code'];
                $validatedData['count_id'] = $request['count_id'];
                $validatedData['order_code'] = $request['prefix_code'].$request['count_id'];
                //End: Order Code related
                $orderModel = Order::whereId($orderId)->update($validatedData);

                
                /**
                 * Code for table records
                 * */
                $rowCount = $request['row_count'];
                if($rowCount == 0){
                    return response()->json([
                        'status' => false,
                        'message' => __('service.please_select_services'),
                    ],409);
                }

                $orderedProducts = OrderedProduct::where('order_id', '=', $orderId)->get();

                for ($i=0; $i < $rowCount ; $i++) {
                    if(!$request->input('service_id.'.$i)){
                        continue;
                    }

                    $quantity = $request->input('quantity.'.$i, 0);
                    if(empty($quantity) || $quantity === 0){
                        return response()->json([
                            'message' => __('service.please_select_service_quantity'),
                        ], 409);
                    }
                    $orderedProductId = $request->input('ordered_product_id.'.$i);
                    $orderRecord = $orderedProducts->where('id', '=', $orderedProductId)->first();
                    
                    $insertOrderedProducts[] = [
                        'order_id'              => $orderId,
                        'service_id'            => $request->input('service_id.'.$i),
                        'description'           => $request->input('description.'.$i, null),
                        'unit_price'             => $request->input('unit_price.'.$i, 0),
                        'quantity'              => $request->input('quantity.'.$i, 0),
                        'total_price'           => $request->input('total_unit_price.'.$i, 0),

                        'discount'              => $request->input('discount.'.$i, 0),
                        'discount_type'         => $request->input('discount_type.'.$i, null),
                        'discount_amount'       => $request->input('discount_amount.'.$i, 0),
                        'total_price_after_discount'  => $request->input('total_price_after_discount.'.$i, 0),

                        'start_date'            => $this->toSystemDateFormat($request->input('start_date.'.$i, null)),
                        'start_time'            => $request->input('start_time.'.$i, null),

                        'end_date'              => $this->toSystemDateFormat($request->input('end_date.'.$i, null)),
                        'end_time'              => $request->input('end_time.'.$i, null),

                        'tax_id'                => $request->input('tax_id.'.$i),
                        'tax_type'              => $request->input('tax_type.'.$i),
                        'tax_amount'            => $request->input('tax_amount.'.$i),

                        'total_price_with_tax'  => $request->input('total.'.$i, 0),

                        'job_code'              => $orderRecord->job_code??null,
                        'assigned_user_id'      => $orderRecord->assigned_user_id??null,
                        'assigned_user_note'    => $orderRecord->assigned_user_note??null,

                        'staff_status'          => $orderRecord->staff_status??null,
                        'staff_status_note'     => $orderRecord->staff_status_note??null,

                        'updated_at'            => $timestamp,
                    ];
                }//end: forloop


                //Delete records before inserting
                OrderedProduct::where('order_id', $orderId)->delete();

                //Ordered Products Bulk insert
                $query = OrderedProduct::insert($insertOrderedProducts);

                /**
                 * Update Job Code
                 * @return true
                 * */
                $this->updateJobCode($orderId);

                /**
                 * Record Payment Details
                 * */
                $paidAmount     = $request->input('amount');
                $paymentTypeId    = $request->input('payment_type_id');
                if($paidAmount && $paidAmount > 0){
                    if(empty($paymentTypeId)){
                        return response()->json([
                            'message' => __('payment.please_select_payment_type'),
                        ], 409);
                    }
                    if($paidAmount > $request->input('total_amount')){
                        return response()->json([
                            'message' => __('payment.payment_should_not_be_greater_than_grand_total'),
                        ], 409);
                    }
                    $paymentNote    = $request->input('payment_note');

                    OrderPayment::create([
                        'payment_date'      => $request['order_date'],
                        'order_id'          => $orderId,
                        'payment_type_id'   => $paymentTypeId,
                        'amount'            => $paidAmount,
                        'note'              => $request->input('payment_note'),
                    ]);

                }

                /**
                 * Call Event to update Orders Model toupdate Payment status
                 * @OrderPaymentsEvent
                 * */
                event(new OrderPaymentsEvent(['order_id' => $orderId ]));

                session(['record' => [
                                    'type' => 'success',
                                    'status' => __('app.record_updated_successfully'), //Save or update
                                    'sms' => null,
                                    'email' => null,
                                ]]);

                DB::commit();

                return response()->json([
                    'status'    => false,
                    'message' => __('app.record_updated_successfully'),
                    'id' => $orderId,
                ]);

        } catch (\Exception $e) {
                DB::rollback();

                Log::channel('custom')->critical($e->getMessage());

                return response()->json([
                    'message' => __('app.something_went_wrong').__('app.check_custom_log_file').$e->getMessage(),
                ], 409);

        }
    }
    /**
     * Get the corrected or incremented order code if given 
     * order_code already exist
     * */
    public function makeOrderCode($prefixCode=null, $countId=null): ?int {
        if(trim($countId)!=''){
            $orderCode = $prefixCode.$countId;
            if($this->isCodeExist($orderCode)){
                $lastCountId = $this->getLastCountId();
                $lastCountId++;
                return $lastCountId;
            }
            return $countId;
        }
        return null;
    }

    /**
     * Check is Code exist
     * @return boolean
     * */
    public function isCodeExist($orderCode): bool {
        return Order::where('order_code', $orderCode)->exists();
    }

    public function list() : View {
        return view('order.list');
    }

    public function datatableList(Request $request){
        
        $data = Order::query();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('order_date', function ($row) {
                        return $row->formatted_order_date;
                    })
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at->format(app('company')['date_format']);
                    })
                    ->addColumn('customer_name', function ($row) {
                        return $row->party->first_name??'';
                    })
                    ->addColumn('username', function ($row) {
                        return $row->user->username;
                    })
                    ->addColumn('mobile', function ($row) {
                        return $row->party->mobile;
                    })
                    ->addColumn('action', function($row){
                            $id = $row->id;

                            $editUrl = route('order.edit', ['id' => $id]);
                            $deleteUrl = route('order.delete', ['id' => $id]);
                            $receiptUrl = route('order.receipt', ['id' => $id]);
                            $timelineUrl = route('order.timeline', ['id' => $id]);


                            $actionBtn = '<div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded font-22 text-option"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="' . $editUrl . '"></i><i class="bx bx-edit"></i> '.__('app.edit').'</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="' . $receiptUrl . '"></i><i class="bx bx-receipt"></i> '.__('order.receipt').'</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="' . $timelineUrl . '"></i><i class="bx bx-time"></i> '.__('order.timeline').'</a>
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

    public function delete(Request $request) : JsonResponse{

        $selectedRecordIds = $request->input('record_ids');

        // Perform validation for each selected record ID
        foreach ($selectedRecordIds as $recordId) {
            $record = Order::find($recordId);
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
        Order::whereIn('id', $selectedRecordIds)->delete();

        return response()->json([
            'status'    => true,
            'message' => __('app.record_deleted_successfully'),
        ]);
    }

    
     /**
      * Delete Payaments
      * @return JsonResponse
      * */
     public function deletePayment(Request $request) : JsonResponse{

        $paymentId = $request->input('payment_id');

        // Perform validation for each selected record ID
        $record = OrderPayment::find($paymentId);
        if (!$record) {
            // Invalid record ID, handle the error (e.g., show a message, log, etc.)
            return response()->json([
                'status'    => false,
                'message' => __('app.invalid_record_id'),
            ]);
        }

        /**
         * All selected record IDs are valid, proceed with the deletion
         * Delete all records with the selected IDs in one query
         * */
        OrderPayment::whereId($paymentId)->delete();

        /**
         * Call Event to update Orders Model toupdate Payment status
         * @OrderPaymentsEvent
         * */
        event(new OrderPaymentsEvent(['order_id' => $record->order_id ]));

        return response()->json([
            'status'    => true,
            'message' => __('app.record_deleted_successfully'),
        ]);
    }

    function getOrderRecords(Request $request): JsonResponse{
        $preparedData = [];

        $orderId = $request->input('order_id');

        $order = Order::find($orderId);
        $orderedProducts = $order->orderedProducts;

        /**
         * If no records
         * @return JsonResponse 
         * */
        if($orderedProducts->count() == 0){
            return response()->json([
                    'status'    => false,
                    'message' => __('app.record_not_found'),
                ]);
        }
        /**
         * Return JsonResponse with Actual Records
         * */
        foreach ($orderedProducts as $record) {
            $preparedData[] = [
                'ordered_product_id' => $record->id,
                'id'                => $record->service->id,
                'name'              => $record->service->name,
                'description'       => $record->description??'',
                'start_date'        => $record->formatted_start_date,
                'start_time'        => $record->start_time,
                'end_date'          => $record->formatted_end_date,
                'end_time'          => $record->end_time,
                'unit_price'        => $record->unit_price,
                'quantity'          => $record->quantity,
                'total_price'       => $record->total_price,
                'discount'          => $record->discount,
                'discount_type'     => $record->discount_type,
                'discount_amount'   => $record->discount_amount,
                'total_price_after_discount'   => $record->total_price_after_discount,
                'tax_id'            => $record->tax_id,
                'tax_type'          => $record->tax_type,
                'tax_amount'        => $record->tax_amount,
                'status'            => $record->status,
                'taxList'           => Tax::all(),
            ];
        }
        
        
        return response()->json([
                    'status'    => true,
                    'message' => null,
                    'data' => $preparedData,
                ]);
     }

    /**
     * Get Order Service Records
     * @return JsonResponse
     * */
     function getOrderRecordsForReport(OrderReportRequest $request): JsonResponse{
        $customerId = $request->input('customer_id');
        $fromDate   = $request->input('from_date');
        $toDate     = $request->input('to_date');

        $preparedData = Order::with("party")
                            ->whereBetween('order_date', [$fromDate, $toDate])
                            ->when($customerId, function ($query) use ($customerId) {
                                return $query->where('party_id', $customerId);
                            })
                            ->get();
        $recordsArray = [];

        foreach ($preparedData as $order) {
            $recordsArray[] = [  
                                'order_date' => $order->order_date,
                                'customer_name' => $order->party->first_name . ' ' . $order->party->last_name,
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
     * Get Order Payment Records
     * @return JsonResponse
     * */
     function getOrderPaymentRecordsForReport(OrderPaymentReportRequest $request): JsonResponse{

        $customerId         = $request->input('party_id');
        $fromDate           = $request->input('from_date');
        $toDate             = $request->input('to_date');
        $paymentTypeId      = $request->input('payment_type_id');

        $preparedData = OrderPayment::whereBetween('payment_date', [$fromDate, $toDate])
                            ->when($paymentTypeId, function ($query) use ($paymentTypeId) {
                                return $query->where('payment_type_id', $paymentTypeId);
                            })
                            ->whereHas('order', function($q) use ($customerId) {
                                if($customerId){
                                    $q->where('party_id', $customerId);
                                }
                            })
                            ->get();
        $recordsArray = [];

        foreach ($preparedData as $payment) {
            $recordsArray[] = [  
                            'order_date' => $payment->payment_date,
                            'order_code' => $payment->order->order_code,
                            'customer_name' => $payment->order->party->first_name .' '. $payment->order->party->last_name,
                            'payment_type' => $payment->paymentType->name,
                            'amount' => $payment->amount,
                            'note' => $payment->note??'',
                        ];
        }
        
        return response()->json([
                    'status'    => true,
                    'message' => null,
                    'data' => $recordsArray,
                ]);
     }
}
