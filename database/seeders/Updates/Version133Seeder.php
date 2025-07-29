<?php

namespace Database\Seeders\Updates;

use App\Enums\General;
use App\Enums\PaymentTypesUniqueCode;
use App\Models\ChequeTransaction;
use App\Models\Party\PartyPayment;
use App\Models\Party\PartyPaymentAllocation;
use App\Models\PartyBalanceAfterAdjustment;
use App\Models\PaymentTransaction;
use App\Models\PaymentTypes;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Version133Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\Version133Seeder Running...";

        $this->updatePaymentTransaction();

        echo "\Version133Seeder Completed!!\n";
    }

    public function updatePaymentTransaction(): bool
    {

        $partyPayments = PartyPayment::all();

        if ($partyPayments->isEmpty()) {
            return true;
        }

        try {
            foreach ($partyPayments as $partyPayment) {

                $paymentTransactionAmount = PartyPaymentAllocation::where('party_payment_id', $partyPayment->id)
                    ->join('payment_transactions', 'party_payment_allocations.payment_transaction_id', '=', 'payment_transactions.id')
                    ->sum('payment_transactions.amount');

                //balance after adjustment
                $partyBalanceAfterAdjustment = $partyPayment->amount - $paymentTransactionAmount;

                if($partyBalanceAfterAdjustment != 0){

                    $paymentsArray = [
                        'party_payment_id'          => $partyPayment->id,
                        'transaction_date'          => $partyPayment->transaction_date,
                        'amount'                    => $partyBalanceAfterAdjustment,
                        'payment_type_id'           => $partyPayment->payment_type_id,
                        'reference_no'              => null,
                        'note'                      => null,
                        'payment_from_unique_code'  => General::PARTY_BALANCE_AFTER_ADJUSTMENT->value,
                        'created_by'            => $partyPayment->created_by,
                        'updated_by'            => $partyPayment->updated_by,
                    ];


                    if (!$transaction = $this->recordPayment($paymentsArray)) {
                        throw new \Exception(__('payment.failed_to_record_payment_transactions'));
                    }

                    PartyBalanceAfterAdjustment::create([
                        'party_payment_id' => $partyPayment->id,
                        'payment_transaction_id' => $transaction->id,
                    ]);


                    if(!$this->recordChequePaymentTransaction($transaction)){
                        throw new \Exception(__('payment.failed_to_record_cheque_payment_transactions'));
                    }


                }//if


            }//foreach


            return true;
        } catch (\Exception $e) {

            echo "\n" . $e->getMessage();
            return false;
        }
    }

    public function recordPayment(array $data)
    {

        $inserted = DB::table('payment_transactions')->insertGetId([
            'transaction_date'          => $data['transaction_date'],
            'payment_type_id'           => $data['payment_type_id'],
            'amount'                    => $data['amount'],
            'note'                      => $data['note'],
            'reference_no'              => $data['reference_no'] ?? null,
            'payment_from_unique_code'  => $data['payment_from_unique_code'] ?? null,
            'transaction_type'          => 'Party Payment',
            'transaction_id'            => $data['party_payment_id'],
            'created_by'                => $data['created_by'],
            'updated_by'                => $data['updated_by'],
            'created_at'                => now(),
            'updated_at'                => now(),
        ]);

        return PaymentTransaction::find($inserted);

    }

    public function recordChequePaymentTransaction($paymentTransaction)
    {

        //Validate is it cheque entry?
        $chequeId = $this->returnPaymentTypeId(PaymentTypesUniqueCode::CHEQUE->value);

        if($paymentTransaction->payment_type_id != $chequeId){
            return true;
        }

        $chequeEntry = [
            'transaction_date'          =>  $paymentTransaction->transaction_date,
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

    public function returnPaymentTypeId($paymentTypeUniqueName){
        $paymentType = PaymentTypes::where('unique_code', $paymentTypeUniqueName)->select('id', 'name')->first();
        return $paymentType ? $paymentType->id : null;
    }
}
