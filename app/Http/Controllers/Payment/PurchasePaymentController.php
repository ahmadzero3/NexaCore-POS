<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;

use App\Traits\FormatNumber;
use App\Traits\FormatsDateInputs;
use App\Enums\General;

use App\Services\PaymentTransactionService;
use App\Services\AccountTransactionService;

use App\Http\Controllers\Purchase\PurchaseController;
use App\Models\Purchase\Purchase;
use App\Models\PaymentTransaction;
use App\Services\PartyService;

use Mpdf\Mpdf;

class PurchasePaymentController extends Controller
{
    use FormatNumber;

    use FormatsDateInputs;

    private $paymentTransactionService;
    private $accountTransactionService;
    private $partyService;

    public function __construct(
                                PaymentTransactionService $paymentTransactionService,
                                AccountTransactionService $accountTransactionService,
                                PartyService $partyService
                            )
    {
        $this->paymentTransactionService = $paymentTransactionService;
        $this->accountTransactionService = $accountTransactionService;
        $this->partyService = $partyService;
    }

    /***
     * View Payment History
     *
     * */
    public function getPurchaseBillPaymentHistory($id) : JsonResponse{

        $data = $this->getPurchaseBillPaymentHistoryData($id);

        return response()->json([
            'status' => true,
            'message' => '',
            'data'  => $data,
        ]);

    }

    /**
     * Print Purchase
     *
     * @param int $id, the ID of the purchase
     * @return \Illuminate\View\View
     */
    public function printPurchaseBillPayment($id, $isPdf = false) : View {
        $payment = PaymentTransaction::with('paymentType')->find($id);

        $purchaseId = $payment->transaction_id;

        $purchase = Purchase::with('party')->find($purchaseId);

        $balanceData = $this->partyService->getPartyBalance($purchase->party->id);

        return view('print.bill-payment-receipt', compact('isPdf', 'purchase', 'payment', 'balanceData'));
    }

    /**
     * Generate PDF using View: print() method
     * */
    public function pdfPurchaseBillPayment($id){
        $html = $this->printPurchaseBillPayment($id, isPdf:true);

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
        $mpdf->Output('Purchase-Bill-Payment-'.$id.'.pdf', 'D');
    }

    function getPurchaseBillPaymentHistoryData($id){
        $model = Purchase::with('party','paymentTransaction.paymentType')->find($id);

        $data = [
            'party_id'  => $model->party->id,
            'party_name'  => $model->party->first_name.' '.$model->party->last_name,
            'balance'  => $this->formatWithPrecision($model->grand_total - $model->paid_amount),
            'invoice_id'  => $id,
            'invoice_code'  => $model->purchase_code,
            'invoice_date'  => $this->toUserDateFormat($model->purchase_date),
            'balance_amount'  => $this->formatWithPrecision($model->grand_total - $model->paid_amount),
            'paid_amount'  => $this->formatWithPrecision($model->paid_amount),
            'paid_amount_without_format'  => $model->paid_amount,
            'paymentTransactions' => $model->paymentTransaction->map(function ($transaction) {
                                        return [
                                            'payment_id' => $transaction->id,
                                            'transaction_date' => $this->toUserDateFormat($transaction->transaction_date),
                                            'reference_no' => $transaction->reference_no??'',
                                            'payment_type' => $transaction->paymentType->name,
                                            'amount' => $this->formatWithPrecision($transaction->amount),
                                        ];
                                    })->toArray(),
        ];
        return $data;
    }
    public function getPurchaseBillPayment($id) : JsonResponse{
        $model = Purchase::with('party')->find($id);

        $data = [
            'party_id'  => $model->party->id,
            'party_name'  => $model->party->first_name.' '.$model->party->last_name,
            'balance'  => ($model->grand_total - $model->paid_amount),
            'invoice_id'  => $id,
            'form_heading' => __('payment.make_payment'),
        ];

        return response()->json([
            'status' => true,
            'message' => '',
            'data'  => $data,
        ]);

    }

    public function deletePurchaseBillPayment($paymentId) : JsonResponse{
        try {
            DB::beginTransaction();
            $paymentTransaction = PaymentTransaction::find($paymentId);
            if(!$paymentTransaction){
                throw new \Exception(__('payment.failed_to_delete_payment_transactions'));
            }

            //Purchase model id
            $purchaseId = $paymentTransaction->transaction_id;

            // Find the related account transaction
            $accountTransactions = $paymentTransaction->accountTransaction;
            if ($accountTransactions->isNotEmpty()) {
                foreach ($accountTransactions as $accountTransaction) {
                    $accountId = $accountTransaction->account_id;
                    // Do something with the individual accountTransaction
                    $accountTransaction->delete(); // Or any other operation
                    //Update  account
                    $this->accountTransactionService->calculateAccounts($accountId);
                }
            }

            $paymentTransaction->delete();

            /**
             * Update Purchase Model
             * Total Paid Amunt
             * */
            $purchase = Purchase::find($purchaseId);
            if(!$this->paymentTransactionService->updateTotalPaidAmountInModel($purchase)){
                throw new \Exception(__('payment.failed_to_update_paid_amount'));
            }

            DB::commit();

            return response()->json([
                'status'    => true,
                'message' => __('app.record_deleted_successfully'),
                'data'  => $this->getPurchaseBillPaymentHistoryData($purchase->id),
            ]);

        } catch (\Exception $e) {
                DB::rollback();

                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 409);

        }
    }

    public function storePurchaseBillPayment(Request $request)
    {
        try {
            DB::beginTransaction();

            $invoiceId          = $request->input('invoice_id');
            $transactionDate    = $request->input('transaction_date');
            $receiptNo          = $request->input('receipt_no');
            $paymentTypeId      = $request->input('payment_type_id');
            $payment            = $request->input('payment');
            $paymentNote        = $request->input('payment_note');

            $purchase = Purchase::find($invoiceId);

            if (!$purchase) {
                throw new \Exception('Invoice not found');
            }

             // Validation rules
            $rules = [
                'transaction_date'  => 'required|date_format:'.implode(',', $this->getDateFormats()),
                'receipt_no'        => 'nullable|string|max:255',
                'payment_type_id'   => 'required|integer',
                'payment'           => 'required|numeric|gt:0',
            ];

            //validation message
            $messages = [
                'transaction_date.required' => 'Payment date is required.',
                'payment_type_id.required'  => 'Payment type is required.',
                'payment.required'          => 'Payment amount is required.',
                'payment.gt'                => 'Payment amount must be greater than zero.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            //Show validation message
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $paymentsArray = [
                'transaction_date'          => $transactionDate,
                'amount'                    => $payment,
                'payment_type_id'           => $paymentTypeId,
                'reference_no'              => $receiptNo,
                'note'                      => $paymentNote,
                'payment_from_unique_code'  => General::INVOICE_LIST->value,//Saving Purchase-list page
            ];

            if(!$transaction = $this->paymentTransactionService->recordPayment($purchase, $paymentsArray)){
                throw new \Exception(__('payment.failed_to_record_payment_transactions'));
            }

            /**
             * Update Purchase Model
             * Total Paid Amunt
             * */
            if(!$this->paymentTransactionService->updateTotalPaidAmountInModel($purchase)){
                throw new \Exception(__('payment.failed_to_update_paid_amount'));
            }

            /**
             * Update Account Transaction entry
             * Call Services
             * @return boolean
             * */
            $accountTransactionStatus = $this->accountTransactionService->purchaseAccountTransaction($purchase);
            if(!$accountTransactionStatus){
                throw new \Exception(__('payment.failed_to_update_account'));
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
     * Datatabale
     * */
    public function datatablePurchaseBillPayment(Request $request){
        $data = PaymentTransaction::whereHasMorph(
            'transaction',
            [Purchase::class],
            function (Builder $query, string $type) use($request) {
                //Class wise Apply filter
                if($type === Purchase::class){
                     $query->when($request->party_id, function ($query) use ($request) {
                        $query->where('party_id', $request->party_id);
                    })
                     ->when($request->user_id, function ($query) use ($request) {
                        return $query->where('created_by', $request->user_id);
                    })
                     ->when($request->from_date, function ($query) use ($request) {
                        return $query->where('transaction_date', '>=', $this->toSystemDateFormat($request->from_date));
                    })
                    ->when($request->to_date, function ($query) use ($request) {
                        return $query->where('transaction_date', '<=', $this->toSystemDateFormat($request->to_date));
                    });
                }

            }
        )->with('transaction.party');

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at->format(app('company')['date_format']);
                    })
                    ->addColumn('transaction_date', function ($row) {
                        return $row->formatted_transaction_date;
                    })
                    ->addColumn('username', function ($row) {
                        return $row->user->username??'';
                    })
                    ->addColumn('purchase_code', function ($row) {
                        return $row->transaction->purchase_code??'';
                    })
                    ->addColumn('party_name', function ($row) {
                        return $row->transaction->party->first_name." ".$row->transaction->party->last_name;
                    })
                    ->addColumn('payment', function ($row) {
                        return $this->formatWithPrecision($row->amount);
                    })
                    ->addColumn('action', function($row){
                            $id = $row->id;
                            $deleteUrl = route('purchase.bill.delete', ['id' => $id]);
                            $printUrl = route('purchase.bill.payment.print', ['id' => $id]);
                            $pdfUrl = route('purchase.bill.payment.pdf', ['id' => $id]);

                            $actionBtn = '<div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded font-22 text-option"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a target="_blank" class="dropdown-item" href="' . $printUrl . '"></i><i class="bx bx-printer "></i> '.__('app.print').'</a>
                                </li>
                                <li>
                                    <a target="_blank" class="dropdown-item" href="' . $pdfUrl . '"></i><i class="bx bxs-file-pdf"></i> '.__('app.pdf').'</a>
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
}
