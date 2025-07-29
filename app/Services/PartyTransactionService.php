<?php
namespace App\Services;

use App\Traits\FormatNumber; 
use App\Traits\FormatsDateInputs;
use App\Models\Party\PartyTransaction;
use App\Models\Party\Party;
use App\Services\PartyService;

class PartyTransactionService{

	use FormatNumber;

	use FormatsDateInputs;


	/**
	 * Record Item Transactions
	 * 
	 * */
	public function recordPartyTransactionEntry(Party $partyModel, array $data) 
    {
        $modelId 			= $partyModel->id;

        $transaction = $partyModel->transaction()->create(
                [
                    'transaction_date'      =>  $this->toSystemDateFormat($data['transaction_date']),
                    'party_id'              =>  $partyModel->id,
                    'to_pay'                =>  $data['to_pay'],
                    'to_receive'            =>  $data['to_receive'],
                ]
            );

        $partyService = new PartyService();

        //Update Balance of Party
        $partyService->updatePartyBalance($partyModel);
        
        return $transaction;
    }

}