<?php

namespace App\Http\Controllers\Reports;

use App\Traits\FormatNumber; 
use App\Traits\FormatsDateInputs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Party\Party;
use App\Services\PartyService;

class SupplierReportController extends Controller
{
    use FormatsDateInputs;

    use FormatNumber;

    public $partyService;

    function __construct(PartyService $partyService)
    {
        $this->partyService = $partyService;
    }
    
    public function getDuePaymentsRecords(Request $request) : JsonResponse{
        try{
            $partyId             = $request->input('party_id');

            $preparedData = Party::when($partyId, function ($query) use ($partyId) {
                                        return $query->where('id', $partyId);
                                    })
                                    ->where('party_type', 'supplier')
                                    ->get();
            
            if($preparedData->count() == 0){
                throw new \Exception('No Records Found!!');
            }

            $recordsArray = [];

            foreach ($preparedData as $data) {
                $balanceData = $this->partyService->getPartyBalance($data->id);

                if($balanceData['balance'] != 0){

                    $status = '';
                    $className = '';
                    $balance = $balanceData['balance'];

                    if($balanceData['status']=='you_collect'){
                        $status = 'You Collect';
                        $className = 'text-danger';
                        $balance = -$balanceData['balance'];
                    }elseif($balanceData['status']=='you_pay'){
                        $status = 'You Pay';
                    }else{
                        $status = 'No Balance';
                    }

                    $recordsArray[] = [  
                                    'party_name'            => $data->getFullName(),
                                    'due_amount'            => $this->formatWithPrecision($balance, comma:false),
                                    'status'                => $status,
                                    'className'             => $className,
                                ];
                }
            }
            
            return response()->json([
                        'status'    => true,
                        'message' => "Records are retrieved!!",
                        'data' => $recordsArray,
                    ]);
        } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 409);

        }
    }

    
}
