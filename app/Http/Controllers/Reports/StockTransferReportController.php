<?php

namespace App\Http\Controllers\Reports;

use App\Traits\FormatNumber;
use App\Traits\FormatsDateInputs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

use App\Models\Items\ItemTransaction;
// use App\Models\Items\ItemBatchTransaction;
// use App\Models\Items\ItemSerialTransaction;
use App\Models\StockTransfer;
use App\Models\Items\ItemStockTransfer;
use App\Enums\ItemTransactionUniqueCode;

class StockTransferReportController extends Controller
{
    use FormatsDateInputs;

    use FormatNumber;

    public function getStockTransferRecords(Request $request) : JsonResponse{
        try{
            // Validation rules
            $rules = [
                'from_date'         => ['required', 'date_format:'.implode(',', $this->getDateFormats())],
                'to_date'           => ['required', 'date_format:'.implode(',', $this->getDateFormats())],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $fromDate           = $request->input('from_date');
            $fromDate           = $this->toSystemDateFormat($fromDate);
            $toDate             = $request->input('to_date');
            $toDate             = $this->toSystemDateFormat($toDate);

            $preparedData = StockTransfer::with('user')->whereBetween('transfer_date', [$fromDate, $toDate])->get();

            if($preparedData->count() == 0){
                throw new \Exception('No Records Found!!');
            }

            $recordsArray = [];

            foreach ($preparedData as $data) {
                $recordsArray[] = [
                                    'transfer_code'         => $data->transfer_code,
                                    'transfer_date'         => $this->toUserDateFormat($data->transfer_date),
                                    'created_by'           => $data->user->username??'',
                                ];
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

    /**
     * Item Sale Report
     * */
    function getStockTransferItemRecords(Request $request): JsonResponse{
        try{

            // Validation rules
            $rules = [
                'from_date'         => ['required', 'date_format:'.implode(',', $this->getDateFormats())],
                'to_date'           => ['required', 'date_format:'.implode(',', $this->getDateFormats())],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $fromDate           = $request->input('from_date');
            $fromDate           = $this->toSystemDateFormat($fromDate);
            $toDate             = $request->input('to_date');
            $toDate             = $this->toSystemDateFormat($toDate);
            $itemId             = $request->input('item_id');
            $brandId             = $request->input('brand_id');
            $fromWarehouseId    = $request->input('from_warehouse_id');
            $toWarehouseId      = $request->input('to_warehouse_id');

            $preparedData = StockTransfer::with(['itemTransaction' => function($query) {
                            $query->where('unique_code', 'STOCK_TRANSFER')
                                  ->with([  'item',
                                            'tax',
                                            'unit',
                                            'batch.itemBatchMaster',
                                            'itemSerialTransaction.itemSerialMaster',
                                            'itemStockTransfer',
                                            'item.itemGeneralQuantities',
                                        ]);
                        }])
                        ->whereBetween('transfer_date', [$fromDate, $toDate])
                        ->when($fromWarehouseId, function ($query) use ($fromWarehouseId) {
                            // Filter by fromWarehouseId
                            return $query->whereHas('itemTransaction.itemStockTransfer', function ($query) use ($fromWarehouseId) {
                                return $query->where('from_warehouse_id', $fromWarehouseId);
                            });
                        })
                        ->when($toWarehouseId, function ($query) use ($toWarehouseId) {
                            // Filter by toWarehouseId
                            return $query->whereHas('itemTransaction.itemStockTransfer', function ($query) use ($toWarehouseId) {
                                return $query->where('to_warehouse_id', $toWarehouseId);
                            });
                        })
                        ->when($brandId, function ($query) use ($brandId) {
                            return $query->whereHas('itemTransaction.item', function ($query) use ($brandId) {
                                $query->where('brand_id', $brandId); // Corrected to `brand_id`
                            });
                        })
                        ->when($itemId, function ($query) use ($itemId) {
                            return $query->whereHas('itemTransaction', function ($query) use ($itemId) {
                                return $query->where('item_id', $itemId);
                            });
                        })
                        ->get();

            if($preparedData->count() == 0){
                throw new \Exception('No Records Found!!');
            }
            $recordsArray = [];

            foreach ($preparedData as $data) {
                foreach($data->itemTransaction as $transaction){
                    $itemSerialTransactions = $transaction->itemSerialTransaction->map(function ($serialTransaction) {
                        return $serialTransaction->itemSerialMaster->serial_code; // Assuming 'serial_number' is the field you want
                    })->implode(','); // Converts the array to a comma-separated string

                    $recordsArray[] = [
                                    'transfer_code'         => $data->transfer_code,
                                    'transfer_date'         => $this->toUserDateFormat($data->transfer_date),
                                    'from_warehouse'        => $transaction->itemStockTransfer->fromWarehouse->name,
                                    'to_warehouse'          => $transaction->itemStockTransfer->toWarehouse->name,
                                    'item_name'             => $transaction->item->name,
                                    'brand_name'             => $transaction->item->brand->name??'',
                                    'serial_code'           => $itemSerialTransactions,
                                    'batch_no'              => $transaction->batch->itemBatchMaster->batch_no??'',
                                    'quantity'              => $this->formatWithPrecision($transaction->quantity, comma:false),
                                    'unit_name'             => $transaction->unit->name,
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
