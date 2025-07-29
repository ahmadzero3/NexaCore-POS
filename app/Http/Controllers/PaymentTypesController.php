<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Http\Requests\PaymentTypesRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\QueryException;

use App\Enums\AccountUniqueCode;
use App\Models\Accounts\AccountGroup;
use App\Models\Accounts\Account;
use App\Models\PaymentTypes;
use App\Enums\PaymentTypesUniqueCode;
use App\Services\PaymentTransactionService;
use App\Services\PaymentTypeService;

class PaymentTypesController extends Controller
{
    private $paymentTypeService;

    private $paymentTransactionService;

    public function __construct(PaymentTypeService $paymentTypeService, PaymentTransactionService $paymentTransactionService)
    {
        $this->paymentTypeService = $paymentTypeService;
        $this->paymentTransactionService = $paymentTransactionService;
    }

    /**
     * Create a new payment-types.
     *
     * @return \Illuminate\View\View
     */
    public function create() : View {
        return view('payment-types.create');
    }

    /**
     * Edit a payment-types.
     *
     * @param int $id The ID of the payment-types to edit.
     * @return \Illuminate\View\View
     */
    public function edit($id) : View {
        // Ensure morph map keys are defined
        $this->paymentTransactionService->usedTransactionTypeValue();

        $cashId = $this->paymentTypeService->returnPaymentTypeId(PaymentTypesUniqueCode::CASH->value);
        $chequeId = $this->paymentTypeService->returnPaymentTypeId(PaymentTypesUniqueCode::CHEQUE->value);

        if($id == $cashId || $id == $chequeId){
            abort(403, 'Unauthorized action.');
        }
        $paymentType = PaymentTypes::find($id);

        return view('payment-types.edit', compact('paymentType'));
    }
    /**
     * Return JsonResponse
     * */
    public function store(PaymentTypesRequest $request) : JsonResponse {

        $filename = null;

        // Get the validated data from the PaymentTypesRequest
        $validatedData = $request->validated();
        $validatedData['unique_code'] = PaymentTypesUniqueCode::BANK->value;
        $validatedData['print_bit'] = $request->has('print_bit') ? 1 : 0;
        // Create a new tax record using Eloquent and save it
        $newPaymentType = PaymentTypes::create($validatedData);

        if($validatedData['print_bit']){
            //Update bit to 0 to remaining payment types
            PaymentTypes::whereNotIn('id', [$newPaymentType->id])->update(['print_bit' => 0]);
        }
        /**
         * Create Account
         * */
        if($newPaymentType){
            Account::create([
                'name'  => $validatedData['name'],
                'group_id'  => AccountGroup::where('unique_code', AccountUniqueCode::BANK_ACCOUNT->value)->first()->id,
                'description'  => $validatedData['description'],
                'payment_type_bank_id'  => $newPaymentType->id,
                'is_deletable'  => 0,
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => __('app.record_saved_successfully'),
            'data'  => [
                'id' => $newPaymentType->id,
                'name' => $newPaymentType->name,
            ]
        ]);
    }

    /**
     * Return JsonResponse
     * */
    public function update(PaymentTypesRequest $request) : JsonResponse {
        $validatedData = $request->validated();
        $validatedData['print_bit'] = $request->has('print_bit') ? 1 : 0;
        // Save the tax details
        $updatePaymentType = PaymentTypes::where('id', $validatedData['id'])->update($validatedData);

        if($validatedData['print_bit']){
            //Update bit to 0 to remaining payment types
            PaymentTypes::whereNotIn('id', [$validatedData['id']])->update(['print_bit' => 0]);

        }
        /**
         * Create Account
         * */
        if($updatePaymentType){
            Account::where('payment_type_bank_id', $validatedData['id'])
                        ->update([
                                'name'  => $validatedData['name'],
                                'description'  => $validatedData['description'],
                            ]);
        }

        return response()->json([
            'message' => __('app.record_updated_successfully'),
        ]);
    }

    public function list() : View {
        return view('payment-types.list');
    }

    public function datatableList(Request $request){
        
        // Ensure morph map keys are defined
        $this->paymentTransactionService->usedTransactionTypeValue();

        $cashId = $this->paymentTypeService->returnPaymentTypeId(PaymentTypesUniqueCode::CASH->value);
        $chequeId = $this->paymentTypeService->returnPaymentTypeId(PaymentTypesUniqueCode::CHEQUE->value);

        $data = PaymentTypes::whereNotIn('id', [$cashId, $chequeId]);

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at->format(app('company')['date_format']);
                    })
                    ->addColumn('username', function ($row) {
                        return $row->user->username??'';
                    })
                    ->addColumn('action', function($row){
                            $id = $row->id;

                            $editUrl = route('payment.type.edit', ['id' => $id]);
                            $deleteUrl = route('payment.type.delete', ['id' => $id]);


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
            $record = PaymentTypes::find($recordId);
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
        try{
            PaymentTypes::whereIn('id', $selectedRecordIds)->where('is_deletable', 1)->delete();
        }catch (QueryException $e){
            return response()->json(['message' => __('app.cannot_delete_records')], 422);
        }


        return response()->json([
            'status'    => true,
            'message' => __('app.record_deleted_successfully'),
        ]);
    }
    /**
     * Ajax Response
     * Search Bar list 
     * */
    function getAjaxSearchBarList(){
        $search = request('search');

        $paymentTypes = PaymentTypes::where('name', 'LIKE', "%{$search}%")
                                      ->select('id', 'name') // Select only the required columns
                                      ->get();

        $response = [
            'results' => $paymentTypes->map(function ($paymentType) {
                return [
                    'id' => $paymentType->id,
                    'text' => $paymentType->name,
                ];
            })->toArray(),
        ];

        return json_encode($response);
       
    }

}
