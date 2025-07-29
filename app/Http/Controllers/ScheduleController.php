<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\OrderedProduct;
use App\Models\Order;
use App\Models\Tax;
use App\Models\User;
use App\Models\JobOrder;
use App\Services\GeneralDataService;
use App\Http\Requests\JobStatusReport;

class ScheduleController extends Controller
{
    private $jobStatusArray;

    public function __construct(GeneralDataService $jobStatusArray){
        $this->jobStatusArray = $jobStatusArray;
    }

    /**
     * Edit a order.
     *
     * @param int $id The ID of the order to edit.
     * @return \Illuminate\View\View
     */
    public function edit($id) : View {
        $order = Order::find($id);
        return view('schedule.edit', compact('order'));
    }

    public function list() : View {
        return view('schedule.list');
    }

    /**
     * Update the model
     * @return JsonResponse
     * */
    public function update(Request $request): JsonResponse {
        try {
                DB::beginTransaction();

                $timestamp = now();

                $orderId = $request->input('order_id');

                $validatedData = [];
                $validatedData['order_status'] = $request['order_status'];
                $validatedData['schedule_note'] = $request['schedule_note'];
                $validatedData['schedule_note'] = $request['schedule_note'];
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

                for ($i=0; $i < $rowCount ; $i++) {
                    if(!$request->input('service_id.'.$i)){
                        continue;
                    }

                    $insertOrderedProducts = [
                        'assigned_user_id'          => $request->input('user_id.'.$i, null),
                        'assigned_user_note'        => $request->input('assigned_user_note.'.$i, null),
                        'staff_status'              => $request->input('staff_status.'.$i, null),
                        'staff_status_note'        => $request->input('staff_status_note.'.$i, null),
                        'updated_at'                => $timestamp,
                    ];

                    //Ordered Products update
                    OrderedProduct::whereId($request->input('ordered_product_id.'.$i))->update($insertOrderedProducts);

                }//end: forloop

                DB::commit();

                return response()->json([
                    'status'    => false,
                    'message' => __('app.record_saved_successfully'),
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

    public function datatableList(Request $request){

        $data = Order::with('party');


        return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('order_date', function ($row) {
                        return $row->formatted_order_date;
                    })
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at->format(app('company')['date_format']);
                    })
                    ->addColumn('customer_name', function ($row) {
                        return $row->party->first_name;
                    })
                    ->addColumn('mobile', function ($row) {
                        return $row->party->mobile;
                    })
                    ->addColumn('action', function($row){
                            $id = $row->id;

                            $editUrl = route('schedule.edit', ['id' => $id]);
                            $timelineUrl = route('order.timeline', ['id' => $id]);


                            $actionBtn = '<div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded font-22 text-option"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="' . $editUrl . '"></i><i class="bx bx-alarm-add"></i> '.__('schedule.schedule').'</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="' . $timelineUrl . '"></i><i class="bx bx-time"></i> '.__('order.timeline').'</a>
                                </li>
                            </ul>
                        </div>';
                            return $actionBtn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
    }
    /**
     * Get Order Service Records
     * @return JsonResponse
     * */
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
                'ordered_product_id'  => $record->id,
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
                'assigned_user_id'  => $record->assigned_user_id??'',
                'assigned_user_note'  => $record->assigned_user_note??'',
                'status'            => $record->status,
                'taxList'           => Tax::all(),
                'userList'          => User::all(),
                'staff_status_list' => $this->jobStatusArray->getStaffStatus(),
                'staff_status'      => $record->staff_status??'',
                'staff_status_note' => $record->staff_status_note??'',
            ];
        }
        
        
        return response()->json([
                    'status'    => true,
                    'message' => null,
                    'data' => $preparedData,
                ]);
     }

     public function getJobStatusRecords(JobStatusReport $request): JsonResponse{
        $customerId         = $request->input('party_id');
        $fromDate           = $request->input('from_date');
        $toDate             = $request->input('to_date');
        $assignedUserId     = $request->input('user_id');
        $staffStatus        = $request->input('staff_status');

        $preparedData = Order::with([
                                'orderedProducts' => function ($query) use ($assignedUserId, $staffStatus) {
                                    if ($assignedUserId) {
                                        $query->where('assigned_user_id', $assignedUserId);
                                    }
                                    if ($staffStatus) {
                                        $query->where('staff_status', $staffStatus);
                                    }
                                },
                            ])
                            ->whereBetween('order_date', [$fromDate, $toDate])
                            ->when($customerId, function ($query) use ($customerId) {
                                return $query->where('party_id', $customerId);
                            })
                            ->get();
        $recordsArray = [];

        foreach ($preparedData as $order) {
            foreach ($order->orderedProducts as $product) {
                $recordsArray[] = [  
                                'order_date' => $order->order_date,
                                'order_code' => $order->order_code,
                                'job_code' => $product->job_code,
                                'customer_name' => $order->party->first_name . ' ' . $order->party->last_name,
                                'service_name' => $product->service->name,
                                'start_date' => $product->start_date??'',
                                'start_time' => $product->start_time??'',
                                'end_date' => $product->end_date??'',
                                'end_time' => $product->end_time??'',
                                'assigned_user' => $product->user->first_name??'',
                                'user_status' => $product->staff_status??'',
                            ];
            }
        }
        
        return response()->json([
                    'status'    => true,
                    'message' => null,
                    'data' => $recordsArray,
                ]);
     }
}
