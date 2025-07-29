<?php
namespace App\Services;

use App\Traits\FormatNumber;
use App\Traits\FormatsDateInputs;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Expenses\Expense;
use App\Models\Purchase\PurchaseOrder;
use App\Models\Purchase\Purchase;
use App\Models\Purchase\PurchaseReturn;
use App\Models\Sale\SaleOrder;
use App\Models\Sale\Sale;
use App\Models\Sale\SaleReturn;
use App\Models\CashAdjustment;
use App\Models\ChequeTransaction;
use App\Enums\PaymentTypesUniqueCode;
use App\Models\Party\PartyPayment;
use App\Models\Sale\Quotation;
use App\Services\PaymentTypeService;

class PaymentTransactionService{

	use FormatNumber;

	use FormatsDateInputs;

    private $paymentTypeService;

    function __construct(PaymentTypeService $paymentTypeService)
    {
        $this->paymentTypeService = $paymentTypeService;
    }

	/**
	 * Record Expense Payment Transactions
	 *
	 * */
	public function recordPayment(Expense|PurchaseOrder|Purchase|PurchaseReturn|SaleOrder|Sale|SaleReturn|CashAdjustment|PartyPayment|Quotation $modelName, array $data)
    {
        $transaction = $modelName->paymentTransaction()->create(
                [
                    'transaction_date'          =>  $this->toSystemDateFormat($data['transaction_date']),
                    'payment_type_id'           =>  $data['payment_type_id'],
                    'amount'                    =>  $data['amount'],
                    'note'                      =>  $data['note'],
                    'reference_no'              =>  $data['reference_no']??null,
                    'payment_from_unique_code'  =>  $data['payment_from_unique_code']??null,
                ]
            );
        if(!$this->recordChequePaymentTransaction($transaction)){
            throw new \Exception(__('payment.failed_to_record_cheque_payment_transactions'));
        }

        return $transaction;
    }

    public function recordChequePaymentTransaction($paymentTransaction)
    {
        //Validate is it cheque entry?
        $chequeId = $this->paymentTypeService->returnPaymentTypeId(PaymentTypesUniqueCode::CHEQUE->value);

        if($paymentTransaction->payment_type_id != $chequeId){
            return true;
        }

        $chequeEntry = [
            'transaction_date'          =>  $this->toSystemDateFormat($paymentTransaction->transaction_date),
            'cheque_no'                 =>  null,
            'payment_transaction_id'    =>  $paymentTransaction->id,
            'payment_type_id'           =>  $paymentTransaction->payment_type_id,
            'amount'                    =>  $paymentTransaction->amount,
            'note'                      =>  null,
        ];

        if(!ChequeTransaction::create($chequeEntry)){
            return false;
        }

        return true;
    }

    /**
     * Return Payment Transactions
     * @return array
     * */
    public function getPaymentRecordsArray(Expense|PurchaseOrder|Purchase|PurchaseReturn|SaleOrder|Sale|SaleReturn|Quotation $model): array
    {
        $transactions = $model->paymentTransaction()
            ->with('paymentType')
            ->get()
            ->map(function ($payment) {
                return [
                    'id'                        => $payment->id,
                    'payment_type_id'           => $payment->paymentType->id,
                    'type'                      => $payment->paymentType->name,
                    'transaction_date'          => $this->toUserDateFormat($payment->transaction_date),
                    'amount'                    => $payment->amount,
                    'note'                      => $payment->note,
                    'reference_no'              => $payment->reference_no,
                    'payment_from_unique_code'  => $payment->payment_from_unique_code,
                ];
            })
            ->toArray();

        return $transactions;
    }

    /**
     * Update Invoice Paid Amount
     * Purchase Model
     * */
    public function updateTotalPaidAmountInModel(Expense|PurchaseOrder|Purchase|PurchaseReturn|SaleOrder|Sale|SaleReturn|Quotation $model) : bool {
        $paidAmount = $model->refresh('paymentTransaction')->paymentTransaction->sum('amount');
        $updateStatus = $model->update(['paid_amount' => $paidAmount]);
        if(!$updateStatus){
            return false;
        }
        return true;
    }

    /**
     * Get Hardcode transaction_type for validation
     * Ensure morph map keys are defined
     * related AppServiceProvider method: boot
     *
     * Used in CashController, ChequeController
     * */
    public function usedTransactionTypeValue(){
        $morphMap = Relation::morphMap();
        $keys = [
                    'Cash Adjustment',
                    'Sale',
                    'Sale Return',
                    'Sale Order',
                    'Purchase',
                    'Purchase Return',
                    'Purchase Order',
                    'Expense',
                ];
        foreach ($keys as $key) {
            if (!isset($morphMap[$key])) {
                throw new \Exception(__('Mis-matched ' . $key . ' class Key name in AppServiceProvider'));
            }
        }
    }

    public function getChequeTransactionType($TransactionType){

        // Ensure morph map keys are defined
        $this->usedTransactionTypeValue();

        switch ($TransactionType) {
            case 'Expense':
            case 'Purchase Order':
            case 'Purchase':
            case 'Purchase Return':
                return "Withdraw";
            default:
                return "Deposit";
        }
    }

}
