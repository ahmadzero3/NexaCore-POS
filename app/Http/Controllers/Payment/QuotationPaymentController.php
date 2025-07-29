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

use App\Http\Controllers\Sale\SaleController;
use App\Models\Sale\Quotation;
use App\Models\PaymentTransaction;

use Mpdf\Mpdf;

class QuotationPaymentController extends Controller
{
    use FormatNumber;

    use FormatsDateInputs;

    private $paymentTransactionService;
    private $accountTransactionService;

    public function __construct(
                                PaymentTransactionService $paymentTransactionService,
                                AccountTransactionService $accountTransactionService
                            )
    {
        $this->paymentTransactionService = $paymentTransactionService;
        $this->accountTransactionService = $accountTransactionService;
    }


    public function deleteQuotationPayment($paymentId) : JsonResponse{
        try {
            DB::beginTransaction();
            $paymentTransaction = PaymentTransaction::find($paymentId);
            if(!$paymentTransaction){
                throw new \Exception(__('payment.failed_to_delete_payment_transactions'));
            }

            //Sale model id
            $quotationId = $paymentTransaction->transaction_id;

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
             * Update Sale Model
             * Total Paid Amunt
             * */
            $quotation = Quotation::find($quotationId);
            if(!$this->paymentTransactionService->updateTotalPaidAmountInModel($quotation)){
                throw new \Exception(__('payment.failed_to_update_paid_amount'));
            }

            DB::commit();
            return response()->json([
                'status'    => true,
                'message' => __('app.record_deleted_successfully'),
                'data'  => $this->getQuotationPaymentHistoryData($quotation->id),
            ]);

        } catch (\Exception $e) {
                DB::rollback();

                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 409);

        }
    }

    function getQuotationPaymentHistoryData($id){
        $model = Quotation::with('party','paymentTransaction.paymentType')->find($id);

        $data = [
            'party_id'  => $model->party->id,
            'party_name'  => $model->party->first_name.' '.$model->party->last_name,
            'balance'  => $this->formatWithPrecision($model->grand_total - $model->paid_amount),
            'invoice_id'  => $id,
            'invoice_code'  => $model->quotation_code,
            'invoice_date'  => $this->toUserDateFormat($model->quotation_date),
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

}
