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
use App\Models\Purchase\Purchase;
use App\Enums\ItemTransactionUniqueCode;
use App\Models\User;

class PurchaseTransactionReportController extends Controller
{
    use FormatsDateInputs;

    use FormatNumber;

    public function getPurchaseRecords(Request $request) : JsonResponse{
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
            $partyId             = $request->input('party_id');

            $preparedData = Purchase::with('party')
                                                ->when($partyId, function ($query) use ($partyId) {
                                                    return $query->where('party_id', $partyId);
                                                })
                                                ->whereBetween('purchase_date', [$fromDate, $toDate])
                                                ->get();


            if($preparedData->count() == 0){
                throw new \Exception('No Records Found!!');
            }

            $recordsArray = [];

            foreach ($preparedData as $data) {
                $recordsArray[] = [
                                    'purchase_date'         => $this->toUserDateFormat($data->purchase_date),
                                    'invoice_or_bill_code'  => $data->purchase_code,
                                    'party_name'            => $data->party->getFullName(),
                                    'grand_total'           => $this->formatWithPrecision($data->grand_total, comma:false),
                                    'paid_amount'           => $this->formatWithPrecision($data->paid_amount, comma:false),
                                    'balance'               => $this->formatWithPrecision($data->grand_total - $data->paid_amount , comma:false),
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
     * Item Purchase Report
     * */
    function getPurchaseItemRecords(Request $request): JsonResponse{
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
            $partyId            = $request->input('party_id');
            $itemId             = $request->input('item_id');
            $brandId             = $request->input('brand_id');
            $warehouseId        = $request->input('warehouse_id');

            //If warehouseId is not provided, fetch warehouses accessible to the user
            $warehouseIds = $warehouseId ? [$warehouseId] : User::find(auth()->id())->getAccessibleWarehouses()->pluck('id');

            $preparedData = Purchase::with('party', 'itemTransaction.item.brand', 'itemTransaction.warehouse')
                                    ->with(['itemTransaction' => function($q) use ($itemId, $brandId, $warehouseIds) {
                                        $q->whereIn('warehouse_id', $warehouseIds);
                                        if ($itemId) {
                                            $q->where('item_id', $itemId);
                                        }
                                        if ($brandId) {
                                            $q->whereHas('item', function ($query) use ($brandId) {
                                                $query->where('brand_id', $brandId);
                                            });
                                        }
                                    }])
                                    ->whereBetween('purchase_date', [$fromDate, $toDate])
                                    ->when($partyId, function ($query) use ($partyId) {
                                        return $query->where('party_id', $partyId);
                                    })
                                    ->get();

            if($preparedData->count() == 0){
                throw new \Exception('No Records Found!!');
            }
            $recordsArray = [];

            foreach ($preparedData as $data) {
                foreach($data->itemTransaction as $transaction){
                    $recordsArray[] = [
                                    'purchase_date'         => $this->toUserDateFormat($data->purchase_date),
                                    'invoice_or_bill_code'  => $data->purchase_code,
                                    'party_name'            => $data->party->getFullName(),
                                    'warehouse'             => $transaction->warehouse->name,
                                    'item_name'             => $transaction->item->name,
                                    'brand_name'             => $transaction->item->brand->name??'',
                                    'unit_price'            => $this->formatWithPrecision($transaction->unit_price, comma:false),
                                    'quantity'              => $this->formatWithPrecision($transaction->quantity, comma:false),
                                    'discount_amount'       => $this->formatWithPrecision($transaction->discount_amount, comma:false),
                                    'tax_amount'            => $this->formatWithPrecision($transaction->tax_amount, comma:false),
                                    'total'                 => $this->formatWithPrecision($transaction->total , comma:false),
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

    /**
     * Item Purchase Report
     * */
    function getPurchasePaymentRecords(Request $request): JsonResponse{
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
            $partyId            = $request->input('party_id');
            $paymentTypeId      = $request->input('payment_type_id');

            $preparedData = Purchase::with('party', 'paymentTransaction')
                                                ->when($fromDate, function ($query) use ($fromDate, $toDate) {
                                                    return $query->whereHas('paymentTransaction', function ($query) use ($fromDate, $toDate) {
                                                        $query->whereBetween('transaction_date', [$fromDate, $toDate]);
                                                    });
                                                })
                                                ->when($partyId, function ($query) use ($partyId) {
                                                    return $query->where('party_id', $partyId);
                                                })
                                                ->when($paymentTypeId, function ($query) use ($paymentTypeId) {
                                                        return $query->whereHas('paymentTransaction', function ($query) use ($paymentTypeId) {
                                                            return $query->where('payment_type_id', $paymentTypeId);
                                                        });
                                                    })
                                                ->get();

            if($preparedData->count() == 0){
                throw new \Exception('No Records Found!!');
            }
            $recordsArray = [];

            foreach ($preparedData as $data) {
                foreach($data->paymentTransaction as $transaction){
                    $recordsArray[] = [
                                    'transaction_date'      => $this->toUserDateFormat($transaction->transaction_date),
                                    'invoice_or_bill_code'  => $data->purchase_code,
                                    'party_name'            => $data->party->getFullName(),
                                    'payment_type'          => $transaction->paymentType->name,
                                    'amount'                => $this->formatWithPrecision($transaction->amount, comma:false),
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
