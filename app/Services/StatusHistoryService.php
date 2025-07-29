<?php
namespace App\Services;

use App\Models\Purchase\Purchase;
use App\Models\Purchase\PurchaseOrder;
use App\Models\Sale\Quotation;
use App\Models\Sale\Sale;
use App\Models\Sale\SaleOrder;

class StatusHistoryService
{

    public function RecordStatusHistory(SaleOrder|PurchaseOrder|Sale|Purchase|Quotation $model) : bool {

        if($model instanceof SaleOrder || $model instanceof PurchaseOrder){
            $status = $model->order_status;
            $date = $model->order_date;
        } else if($model instanceof Quotation){
            $status = $model->quotation_status;
            $date = $model->quotation_date;
        }else{
            //
        }

        // Check if a status history with the same status and date already exists
        $existingHistory = $model->statusHistory()->latest()->first();

        if ($existingHistory) {
            if($existingHistory->status == $status && $existingHistory->status_date == $date){
                // Update the existing history record
                $existingHistory->updated_by = auth()->id();
                $existingHistory->touch(); // Update updated_at timestamp
                $existingHistory->save();
                return true;
            }
        }

        // Create a new status history record
        $update = $model->statusHistory()->create(['status' => $status, 'status_date' => $date]);

        return (bool) $update ?? false;
    }

    public function getStatusHistoryData(SaleOrder|PurchaseOrder|Sale|Purchase|Quotation $model)  {
        $data = [
            'statusHistory' => $model->statusHistory->map(function ($history) {
                                return [
                                    'id' => $history->id,
                                    'status_date' => $history->formated_status_date,
                                    'status' => $history->status,
                                    'note' => $history->note??'',
                                    'created_by' => $history->createdBy->username,
                                    'updated_by' => $history->updatedBy->username,
                                ];
                            })->toArray(),
        ];
        return $data;
    }
}
