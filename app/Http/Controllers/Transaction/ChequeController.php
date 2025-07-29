<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\Relation;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ChequeTransaction;
use App\Services\PaymentTypeService;
use App\Models\CashAdjustment;
use App\Enums\PaymentTypesUniqueCode;
use App\Services\PaymentTransactionService;
use App\Models\PaymentTransaction;
use App\Traits\FormatNumber;
use App\Traits\FormatsDateInputs;

class ChequeController extends Controller
{
    use FormatNumber;
    use FormatsDateInputs;

    private $paymentTypeService;

    private $paymentTransactionService;

    public function __construct(PaymentTypeService $paymentTypeService, PaymentTransactionService $paymentTransactionService)
    {
        $this->paymentTypeService = $paymentTypeService;
        $this->paymentTransactionService = $paymentTransactionService;
    }


    /**
     * List the cash transactions
     *
     * @return \Illuminate\View\View
     */
    public function list() : View {
        return view('transaction.cheque-list');
    }

    public function getChequeTransactionDetails($id) : JsonResponse{
        $model = ChequeTransaction::with('paymentTransaction')->find($id);

        $typeOfTransfer = $this->getTypeOfTransfer($model->payment_transaction_id);

        if($model->paymentTransaction->transaction->party){
            //Party Name
            $received_paid = $model->paymentTransaction->transaction->party->getFullName();
        }else{
            //expense category name
            $received_paid = $model->paymentTransaction->transaction->category->name;
        }

        $data = [
            'id'  => $model->id,
            'received_from'  => $received_paid,
            //'transaction_date'  => $this->toUserDateFormat($model->transaction_date),
            'amount'  => $this->formatWithPrecision($model->amount, comma:false),
            'note'  => '',
            'label_deposit_or_transfer' => ($typeOfTransfer =='Withdraw') ? 'Paid To' : 'Received from',//Party Name
            'label_transfer_from_or_to' => ($typeOfTransfer =='Withdraw') ? 'Withdraw From' : 'Deposit To',
        ];

        return response()->json([
            'status' => true,
            'message' => '',
            'data'  => $data,
        ]);

    }

    public function getTypeOfTransfer($paymentTransactionId){

        // Ensure morph map keys are defined
        $this->paymentTransactionService->usedTransactionTypeValue();

        $paymentTransaction = PaymentTransaction::find($paymentTransactionId);

        /**
         * @return string
         * Withdraw or Deposit
         * */
        return $this->paymentTransactionService->getChequeTransactionType($paymentTransaction->transaction_type);
    }

    public function updateChequeReopen($id) : JsonResponse{
        $model = ChequeTransaction::find($id);
        $model->transfer_to_payment_type_id = null;
        $model->transfer_date = null;
        $model->save();

        /**
         * Update it in Payment Transaction
         * */
        if(!$this->updateTransferPaymentTransactionId($model->payment_transaction_id, null)){
            throw new \Exception('Failed to update Deposit Payment Transaction ID in PaymentTransaction Model');
        }

        return response()->json([
            'status' => true,
            'message' => __('payment.cheque_reopened_successfully'),
        ]);
    }

    public function store(Request $request) : JsonResponse{
        try {

                DB::beginTransaction();
                // Validation rules
                $rules = [
                    'transfer_to_payment_type_id'    => 'required|numeric',
                    'transfer_date'                 => ['required', 'date_format:'.implode(',', $this->getDateFormats())],
                    'note'                          => 'nullable|string|max:250',
                    'cheque_transaction_id'         => 'required|numeric',
                ];

                //validation message
                $messages = [
                    'transfer_date.required'                 => 'Transfer date is required.',
                    'transfer_to_payment_type_id.required'   => 'Transfer type is required.',
                    'cheque_transaction_id.required'        => 'Cheque Transfer transaction ID is missing.',
                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                //Show validation message
                if ($validator->fails()) {
                    throw new \Exception($validator->errors()->first());
                }


                $validatedData = $validator->validated();

                $chequeTransactionId = request('cheque_transaction_id');
                $transferPaymentTypeId = request('transfer_to_payment_type_id');
                $depositDate = $this->toSystemDateFormat(request('transfer_date'));
                $note = request('note');

                /**
                 * Update check transaction model
                 * */
                $depositCheque = ChequeTransaction::find($chequeTransactionId);
                $depositCheque->transfer_to_payment_type_id = $transferPaymentTypeId;
                $depositCheque->transfer_date = $depositDate;
                $depositCheque->note = $note;
                $depositCheque->save();

                /**
                 * Update it in Payment Transaction
                 * */
                if(!$this->updateTransferPaymentTransactionId($depositCheque->payment_transaction_id, $depositCheque->transfer_to_payment_type_id)){
                    throw new \Exception('Failed to update Deposit Payment Transaction ID in PaymentTransaction Model');
                }

                DB::commit();

                return response()->json([
                    'status'    => true,
                    'message' => __('app.record_saved_successfully'),
                ]);

        } catch (\Exception $e) {
                DB::rollback();

                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 409);

        }
    }

    /**
     * Update it in Payment Transaction
     * */
    public function updateTransferPaymentTransactionId($paymentTransactionId, $transferPaymentTypeId)
    {
        try{
            $paymentTransaction = PaymentTransaction::find($paymentTransactionId);
            $paymentTransaction->transfer_to_payment_type_id = $transferPaymentTypeId;
            $paymentTransaction->save();
            return true;
        }
        catch(\Exception $e){
            return false;
        }
    }
    /**
     * Cash Transaction list
     * */
    public function datatableList(Request $request){

        // Ensure morph map keys are defined
        $this->paymentTransactionService->usedTransactionTypeValue();

        $dangerTypes = ['Expense', 'Purchase', 'Sale Return', 'Purchase Order'];

        $cashId = $this->paymentTypeService->returnPaymentTypeId(PaymentTypesUniqueCode::CASH->value);

        $paymentTransactionService = $this->paymentTransactionService;

        $data = ChequeTransaction::with('user', 'paymentTransaction')
                                    ->when($request->from_date, function ($query) use ($request) {
                                        return $query->where('transaction_date', '>=', $this->toSystemDateFormat($request->from_date));
                                    })
                                    ->when($request->to_date, function ($query) use ($request) {
                                        return $query->where('transaction_date', '<=', $this->toSystemDateFormat($request->to_date));
                                    });

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at->format(app('company')['date_format']);
                    })
                    ->addColumn('transfer_date', function ($row) {
                        return $row->transfer_date ? $this->toUserDateFormat($row->transfer_date) : '';
                    })
                    ->addColumn('username', function ($row) {
                        return $row->user->username??'';
                    })
                    ->addColumn('amount', function ($row) {
                        return $this->formatWithPrecision($row->amount);
                    })
                    ->addColumn('transaction_type', function ($row) {
                        return $row->paymentTransaction->transaction_type;
                    })
                    ->addColumn('color_class', function ($row) use ($dangerTypes) {
                        return in_array($row->paymentTransaction->transaction_type, $dangerTypes) ? "danger" : "success";
                    })
                    ->addColumn('status', function ($row) use($cashId, $paymentTransactionService) {
                        if(empty($row->transfer_to_payment_type_id)){
                            return 'Open';
                        }
                        else{
                            //If transfer_to_payment_type_id not empty
                            //If Cash or Bank
                            if($paymentTransactionService->getChequeTransactionType($row->paymentTransaction->transaction_type) == 'Withdraw'){
                                //Withdraw
                                if($row->transfer_to_payment_type_id === $cashId){
                                    return "Removed from Cash-in-Hand";
                                }else{
                                    //Should be a bank payment Type
                                    return "Withdraw from ". $row->depositToPaymentTypeName->name;
                                }
                            }else{
                                //deposit
                                if($row->transfer_to_payment_type_id === $cashId){
                                    return "Added in Cash-in-Hand";
                                }else{
                                    //Should be a bank payment Type
                                    return "Deposited to ". $row->depositToPaymentTypeName->name;
                                }
                            }

                        }

                    })
                    ->addColumn('party_name', function ($row) {
                        return $row->paymentTransaction->transaction->party? $row->paymentTransaction->transaction->party->getFullName() : '';
                    })
                    ->addColumn('action', function($row) use ($paymentTransactionService) {
                        $id = $row->id;
                        if(empty($row->transfer_to_payment_type_id)){
                            $actionBtn = "<button type='button' data-cheque-transaction-id='$id' class='btn btn-sm btn btn-outline-primary make-cheque-transfer'>". $paymentTransactionService->getChequeTransactionType($row->paymentTransaction->transaction_type)."</buton>";
                        }else{
                            $actionBtn = "<button type='button' data-cheque-transaction-id='$id' class='btn btn-sm btn btn-outline-secondary reopen-cheque-transfer'>Re-Open</buton>";
                        }
                        return $actionBtn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
    }



}
