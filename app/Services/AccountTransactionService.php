<?php
namespace App\Services;

use App\Models\Accounts\AccountTransaction;
use App\Models\Expenses\Expense;
use App\Enums\AccountUniqueCode;
use App\Enums\PaymentTypesUniqueCode;
use App\Models\Accounts\Account;
use App\Models\Expenses\ExpenseCategory;
use App\Models\Purchase\PurchaseOrder;
use App\Models\Purchase\Purchase;
use Illuminate\Support\Facades\DB;
use App\Models\Accounts\AccountGroup;



class AccountTransactionService{

	private $allAccounts;

	public function __construct()
	{
		//$this->allAccounts = Account::all();
	}

	public function expenseAccountTransaction(Expense $modelName) : bool
	{
		/**
		 * Disabled
		 * */

		return true;
		$payments = $modelName->paymentTransaction()->with('paymentType')->get();

		if($payments->count()>0){
			foreach($payments as $payment){

				$paymentTypeName = strtoupper($payment->paymentType->name);



				if(PaymentTypesUniqueCode::CASH->value == $paymentTypeName){
					//get cash account id
					//credit
					$accountId = $this->getAccountId(uniqueCode: AccountUniqueCode::CASH_IN_HAND->value);
				}
				else if(PaymentTypesUniqueCode::CHEQUE->value == $paymentTypeName){
					//get cheque account id
					//credit
					$accountId = $this->getAccountId(uniqueCode: AccountUniqueCode::UNWITHDRAWN_CHEQUES->value);
				}else{
					//else payment type acccount id
					//credit
					$accountId = $this->getAccountId(paymentTypeBankId: $payment->payment_type_id);
				}
				//$debitAccountId = $this->getAccountId(expenseCategoryId: $expenseCategory->id);
				$transaction = [
					'transaction_date' 		=> $payment->transaction_date,
					'account_id' 			=> $accountId,
					'credit_amount' 		=> $payment->amount,

				];

				if(!$payment->accountTransaction()->create($transaction)){
					return false;
				}

				$this->calculateAccounts($transaction['account_id']);
			}
		}


		//Record Invoice Total
		$transaction = [
			'transaction_date' 		=> $modelName->expense_date,
			'account_id' 			=> $this->getAccountId(expenseCategoryId: $modelName->expense_category_id),
			'debit_amount' 			=> $modelName->grand_total,
		];


		if(!$modelName->accountTransaction()->create($transaction)){
			return false;
		}

		$this->calculateAccounts($transaction['account_id']);

		return true;

	}

    public function purchaseAccountTransaction(Purchase $modelName) : bool
	{
		/**
		 * Disabled
		 * */

		return true;

		$payments = $modelName->paymentTransaction()->with('paymentType')->get();

		if($payments->count()>0){
			foreach($payments as $payment){

				$paymentTypeName = strtoupper($payment->paymentType->name);

				if(PaymentTypesUniqueCode::CASH->value == $paymentTypeName){
					//get cash account id
					//Credit
					$accountId = $this->getAccountId(uniqueCode: AccountUniqueCode::CASH_IN_HAND->value);
				}
				else if(PaymentTypesUniqueCode::CHEQUE->value == $paymentTypeName){
					//get cheque account id
					//Credit
					$accountId = $this->getAccountId(uniqueCode: AccountUniqueCode::UNWITHDRAWN_CHEQUES->value);
				}else{
					//else payment type acccount id
					//Credit
					$accountId = $this->getAccountId(paymentTypeBankId: $payment->payment_type_id);
				}

				//$debitAccountId = $this->getAccountId(uniqueCode: AccountUniqueCode::PURCHASES->value);
				$transaction = [
					'transaction_date' 		=> $payment->transaction_date,
					'account_id' 			=> $accountId,
					'credit_amount' 		=> $payment->amount,

				];

				if(!$payment->accountTransaction()->create($transaction)){
					return false;
				}

				$this->calculateAccounts($transaction['account_id']);
			}
		}

		$itemTransaction = $modelName->itemTransaction;
		$itemSumWithoutTax = $itemTransaction->sum('total')-$itemTransaction->sum('tax_amount');

		//Record Invoice Total
		$transaction = [
			'transaction_date' 		=> $modelName->purchase_date,
			'account_id' 			=> $this->getAccountId(uniqueCode: AccountUniqueCode::PURCHASES->value),
			'debit_amount' 			=> $itemSumWithoutTax,
		];


		if(!$modelName->accountTransaction()->create($transaction)){
			return false;
		}

		$this->calculateAccounts($transaction['account_id']);

		//Record Tax Accounts
		$sumOfTaxAmount = $modelName->itemTransaction->sum('tax_amount');

		if($sumOfTaxAmount){

			$transaction = [
				'transaction_date' 		=> $modelName->purchase_date,
				'account_id' 			=> $this->getAccountId(uniqueCode: AccountUniqueCode::INPUT_TAX_ALL->value),
				'debit_amount' 			=> $sumOfTaxAmount,
			];
			if(!$modelName->accountTransaction()->create($transaction)){
				return false;
			}
			$this->calculateAccounts($transaction['account_id']);
		}

		return true;

	}

	public function purchaseOrderAccountTransaction(PurchaseOrder $modelName) : bool
	{
		/**
		 * Disabled
		 * */

		return true;

		$payments = $modelName->paymentTransaction()->with('paymentType')->get();

		if($payments->count()>0){
			foreach($payments as $payment){

				$paymentTypeName = strtoupper($payment->paymentType->name);

				if(PaymentTypesUniqueCode::CASH->value == $paymentTypeName){
					//get cash account id
					//credit
					$accountId = $this->getAccountId(uniqueCode: AccountUniqueCode::CASH_IN_HAND->value);
				}
				else if(PaymentTypesUniqueCode::CHEQUE->value == $paymentTypeName){
					//get cheque account id
					//credit
					$accountId = $this->getAccountId(uniqueCode: AccountUniqueCode::UNWITHDRAWN_CHEQUES->value);
				}else{
					//else payment type acccount id
					//credit
					$accountId = $this->getAccountId(paymentTypeBankId: $payment->payment_type_id);
				}

				$transaction = [
					'transaction_date' 		=> $payment->transaction_date,
					'account_id' 			=> $accountId,
					'credit_amount' 		=> $payment->amount,
				];

				if(!$payment->accountTransaction()->create($transaction)){
					return false;
				}

				$this->calculateAccounts($transaction['account_id']);
			}


			//Record Invoice Total only if payment exist
			$transaction = [
				'transaction_date' 		=> $modelName->order_date,
				'account_id' 			=> $this->getAccountId(uniqueCode: AccountUniqueCode::ADVANCE_PAID_FOR_PURCHASE_ORDER->value),
				'debit_amount' 			=> $modelName->grand_total,
			];


			if(!$modelName->accountTransaction()->create($transaction)){
				return false;
			}

			$this->calculateAccounts($transaction['account_id']);
		}

		return true;

	}

	public function itemOpeningStockTransaction($itemModel) : bool
	{
		/**
		 * Disabled
		 * */
		return true;

		$itemTransaction = $itemModel->itemTransaction()->get()->first();

		if($itemTransaction->count()>0){

			//Debit info
			$transaction = [
				'transaction_date' 		=> $itemTransaction->transaction_date,
				'account_id' 			=> $this->getAccountId(uniqueCode: AccountUniqueCode::STOCK_IN_HAND->value),
				'debit_amount' 			=> ($itemTransaction->unit_price * $itemTransaction->quantity),
			];


			if(!$itemTransaction->accountTransaction()->create($transaction)){
				return false;
			}

			$this->calculateAccounts($transaction['account_id']);

			//credit info
			$transaction = [
				'transaction_date' 		=> $itemTransaction->transaction_date,
				'account_id' 			=> $this->getAccountId(uniqueCode: AccountUniqueCode::OPENING_STOCK_BALANCE->value),
				'credit_amount' 		=> ($itemTransaction->unit_price * $itemTransaction->quantity),
			];


			if(!$itemTransaction->accountTransaction()->create($transaction)){
				return false;
			}

			$this->calculateAccounts($transaction['account_id']);

		}

		return true;

	}

	public function partyOpeningBalanceTransaction($partyModel) : bool
	{
		/**
		 * Disabled
		 * */

		return true;
		$partyTransaction = $partyModel->transaction()->get()->first();

		if($partyTransaction->count() == 0){
			return true;
		}
		/**
		 * Sundry Debitor or Sundray Creditors
		 * */
		$transaction = [
			'transaction_date' 		=> $partyTransaction->transaction_date,
		];
		if($partyTransaction->to_pay>0){
			//to pay
			$transaction['account_id'] = $this->getAccountId(uniqueCode: AccountUniqueCode::SUNDRY_CREDITORS_LIST->value);
			$transaction['credit_amount'] = $partyTransaction->to_pay;
		}else{
			//to receive
			$transaction['account_id'] = $this->getAccountId(uniqueCode: AccountUniqueCode::SUNDRY_DEBTORS_LIST->value);
			$transaction['debit_amount'] = $partyTransaction->to_receive;
		}

		if(!$partyTransaction->accountTransaction()->create($transaction)){
			return false;
		}

		$this->calculateAccounts($transaction['account_id']);

		/**
		 * Opening Balance Account Entry
		 * */
		$transaction = [
			'transaction_date' 		=> $partyTransaction->transaction_date,
			'account_id' 			=> $this->getAccountId(uniqueCode: AccountUniqueCode::PARTY_OPENING_BALANCE->value),
		];

		if($partyTransaction->to_pay>0){
			//to pay
			$transaction['debit_amount'] = $partyTransaction->to_pay;
		}else{
			//to receive
			$transaction['credit_amount'] = $partyTransaction->to_receive;
		}

		if(!$partyTransaction->accountTransaction()->create($transaction)){
			return false;
		}

		$this->calculateAccounts($transaction['account_id']);

		return true;

	}

	public function getAccountId($uniqueCode = null, $expenseCategoryId = null, $paymentTypeBankId = null)
	{
		if($uniqueCode){
			//uniqueCode
			$account = $this->allAccounts->firstWhere('unique_code', $uniqueCode);
		}
		else if($paymentTypeBankId){
			//paymentTypeBankId
			$account = $this->allAccounts->firstWhere('payment_type_bank_id', $paymentTypeBankId);
		}
		else{
			//expenseCategoryId
			$account = $this->allAccounts->firstWhere('expense_category_id', $expenseCategoryId);
		}

		return $account->id;
	}

	public function calculateAccounts($accountId)
	{
		$totaDebits = AccountTransaction::where('account_id', $accountId)->sum('debit_amount');
		$totalCredits = AccountTransaction::where('account_id', $accountId)->sum('credit_amount');

		$updated = Account::where('id', $accountId)->update(['debit_amt' => $totaDebits, 'credit_amt' => $totalCredits]);


		if(!$updated){
			return false;
		}
		return true;
	}

	/**
     * Create or update Party Account
     *
     * */
    public function createOrUpdateAccountOfParty($partyId, $partyName, $partyType)
    {
    	/**
    	 * Disabled account
    	 * */
    	return true;

    	DB::beginTransaction();
    	try{
	    		$account = Account::where('party_id', $partyId)->first();
		        if($account){
		            $account->name = $partyName;
		            $account->save();
		        }else{
		            $uniqueCode = (strtoupper($partyType) == 'CUSTOMER') ? AccountUniqueCode::SUNDRY_DEBTORS->value : AccountUniqueCode::SUNDRY_CREDITORS->value;
		            Account::create([
		                'name'  => $partyName,
		                'group_id'  => AccountGroup::where('unique_code', $uniqueCode)->first()->id,
		                'description'  => 'Children of Sundry Debtors',
		                'party_id'  => $partyId,
		                'is_deletable'  => 0,
		            ]);
		        }
    	} catch (\Exception $e) {
    			DB::rollback();
                return false;
        }
        DB::commit();
        return true;
    }
}
