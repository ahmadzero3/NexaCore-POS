<?php
namespace App\Services;

use App\Models\Party\Party;
use App\Models\Sale\Sale;
use App\Models\Sale\SaleReturn;
use App\Models\Purchase\Purchase;
use App\Models\Purchase\PurchaseReturn;
use App\Models\Party\PartyTransaction;
use App\Models\Party\PartyPayment;
use App\Traits\FormatNumber;

class PartyService{
	use FormatNumber;

	/**
	 * Update Party balance in Party Model
	 *
	 * */
	public function updatePartyBalance($model) : bool{
		$itemTransactions = PartyTransaction::selectRaw('sum(to_pay) as to_pay, sum(to_receive) as to_receive')
											->where('party_id', $model->id)
											->get()
											->first();
		$model->to_pay = $itemTransactions->to_pay;
		$model->to_receive = $itemTransactions->to_receive;
		$model->save();
		return true;
	}

	/**
	 * Get Party Credit Limit
	 * */
	public function getPartyDetails($partyId)
	{
		return Party::find($partyId);
	}

	/**
	 * Get Party Credit Limit
	 * */
	public function limitThePartyCreditLimit($partyId) : bool
	{
		$party = $this->getPartyDetails($partyId);

		if($party->count()>0){

            if($party->is_set_credit_limit == 0){
				return true;
			}

			$creditLimit = $party->credit_limit;

			if($party->party_type == 'customer' ){
				//customer
				$dueSaleAmount = $this->saleTransaction($partyId);
				$dueSaleReturnAmount = $this->saleReturnTransaction($partyId);
				$totalDue = $dueSaleAmount - $dueSaleReturnAmount;
				if($totalDue > $creditLimit){
					throw new \Exception(__('party.party_credit_limit_is_exceeding', ['credit_limit' => $this->formatWithPrecision($creditLimit), 'total_due' => $this->formatWithPrecision($totalDue)]));
				}
			}else{
				//supplier
			}
		}
		return false;
	}

	public function saleTransaction($partyId)
	{
		return Sale::selectRaw('coalesce(sum(grand_total) - sum(paid_amount), 0) as due_amount')
											->where('party_id', $partyId)
											->get()
											->first()->due_amount;
	}

	public function saleReturnTransaction($partyId)
	{
		return SaleReturn::selectRaw('coalesce(sum(grand_total) - sum(paid_amount), 0) as due_amount')
											->where('party_id', $partyId)
											->get()
											->first()->due_amount;
	}

	/**
	 * Calculate Balance of the Party
	 * */
	public function getPartyBalance($partyId)
	{
	    // Retrieve the party
	    $party = Party::findOrFail($partyId);

	    // Retrieve opening balance from PartyTransaction
	    $openingBalance = PartyTransaction::where('party_id', $partyId)
					    ->selectRaw('COALESCE(SUM(to_receive) - SUM(to_pay), 0) as opening_balance')
					    ->first()
					    ->opening_balance ?? 0;

		//Get Allocation amount based on PartyPayment
		/**
		 * For Customers Sale Adjustments
		 * */
		$partyPaymentReceiveSum = PartyPayment::where('party_id', $partyId)
		->where('payment_direction', 'receive')
		->selectRaw('
			(SELECT SUM(amount) FROM party_payments
			WHERE party_id = ? AND payment_direction = \'receive\')
			-
			COALESCE(
				(SELECT SUM(payment_transactions.amount)
				FROM payment_transactions
				INNER JOIN party_payment_allocations
					ON payment_transactions.id = party_payment_allocations.payment_transaction_id
				WHERE party_payment_allocations.party_payment_id IN
					(SELECT id FROM party_payments
					WHERE party_id = ? AND payment_direction = \'receive\')
				), 0)
		AS total_amount',
		[$partyId, $partyId])
		->value('total_amount') ?? 0;

		//Get Allocation amount based on PartyPayment
	    /**
	     * For suppliers, Purchase adjustments
	     * */
		$partyPaymentPaySum = PartyPayment::where('party_id', $partyId)
		->where('payment_direction', 'pay')
		->selectRaw('
			(SELECT SUM(amount) FROM party_payments
			WHERE party_id = ? AND payment_direction = \'pay\')
			-
			COALESCE(
				(SELECT SUM(payment_transactions.amount)
				FROM payment_transactions
				INNER JOIN party_payment_allocations
					ON payment_transactions.id = party_payment_allocations.payment_transaction_id
				WHERE party_payment_allocations.party_payment_id IN
					(SELECT id FROM party_payments
					WHERE party_id = ? AND payment_direction = \'pay\')
				), 0)
		AS total_amount',
		[$partyId, $partyId])
		->value('total_amount') ?? 0;

	    // Sale & Sale Payments
		$saleBalance = Sale::where('party_id', $partyId)
				    ->selectRaw('coalesce(sum(grand_total - paid_amount), 0) as total')
				    ->value('total');

		// Sale Return & its Payments
		$saleReturnBalance = SaleReturn::where('party_id', $partyId)
		    		->selectRaw('coalesce(sum(grand_total - paid_amount), 0) as total')
				    ->value('total');

		// Purchase & its Payments
		$purchaseBalance = Purchase::where('party_id', $partyId)
						->selectRaw('coalesce(sum((grand_total - shipping_charge) -
									CASE
										WHEN paid_amount >= (grand_total - shipping_charge)
										THEN paid_amount - shipping_charge
										ELSE paid_amount
									END), 0) as total')
						->value('total');


		// Purchase Return & its Payments
		$purchaseReturnBalance = PurchaseReturn::where('party_id', $partyId)
					->selectRaw('coalesce(sum(grand_total - paid_amount), 0) as total')
				    ->value('total');


		 //Calculate as per party
	    $balance = $openingBalance - ($partyPaymentReceiveSum - $partyPaymentPaySum);

	    // Calculate balance for customers (amount to receive)
	    $balance += ($saleBalance) - ($saleReturnBalance);

	    // Calculate balance for suppliers (amount to pay)
	    $balance -= ($purchaseBalance) - ($purchaseReturnBalance);

	    // Determine if the final balance indicates an amount owed or a receivable
	    if ($balance > 0) {
	        // The company should collect this amount from the party
	        return [
	            'balance' => $balance,
	            'status' => 'you_collect',
	        ];
	    } elseif ($balance < 0) {
	        // The company owes this amount to the party
	        return [
	            'balance' => abs($balance), // Return positive amount
	            'status' => 'you_pay',
	        ];
	    } else {
	        // The balances are equal
	        return [
	            'balance' => 0,
	            'status' => 'no_balance',
	        ];
	    }
	}
}
