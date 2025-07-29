<?php

namespace App\Http\Controllers\Reports;

use App\Traits\FormatNumber;
use App\Traits\FormatsDateInputs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

use App\Models\Items\ItemTransaction;
use App\Models\Sale\Sale;
use App\Models\Sale\SaleReturn;
use App\Models\Purchase\Purchase;
use App\Models\Purchase\PurchaseReturn;
use App\Enums\ItemTransactionUniqueCode;

class GstReportController extends Controller
{
    use FormatsDateInputs;

    use FormatNumber;

    public function getGstr1Records(Request $request) : JsonResponse{
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



            $preparedData = ItemTransaction::with(['transaction'])
                                                ->whereHasMorph('transaction', [Sale::class, SaleReturn::class], function ($query) use ($fromDate, $toDate) {
                                                    $query->whereBetween('transaction_date', [$fromDate, $toDate]);
                                                })
                                                ->selectRaw('transaction_id, transaction_type, tax_id, sum(tax_amount) as tax_amount, sum(unit_price) - sum(discount_amount) as taxable_value')
                                                ->groupBy('tax_id', 'transaction_id', 'transaction_type')
                                                ->get();

            if($preparedData->count() == 0){
                throw new \Exception('No Records Found!!');
            }

            $recordsArray = [];

            foreach ($preparedData as $data) {
                $transactionDate = $data->transaction instanceof Sale ? $data->transaction->sale_date : $data->transaction->return_date;
                $transactionType = $data->transaction instanceof Sale ? 'Sale' : 'Sale Return';
                $invoiceNumber = $data->transaction instanceof Sale ? $data->transaction->sale_code : $data->transaction->return_code;
                $stateName = $data->transaction->state? $data->transaction->state->name : '';

                $companyStateID = app('company')['state_id'];
                $invoiceStateID = $data->transaction->party->state_id;

                //GST Calculation
                $taxAmount = $data->tax_amount;
                $cs_gst_amt = $i_gst_amt = 0;
                if(empty($invoiceStateID) || $companyStateID == $invoiceStateID){
                    $cs_gst_amt = $this->formatWithPrecision($taxAmount/2, comma:false);
                }else{
                    $i_gst_amt = $this->formatWithPrecision($taxAmount, comma:false);
                }
                //end

                $recordsArray[] = [
                                    'tax_number'            => $data->transaction->party->tax_number??'',
                                    'party_name'            => $data->transaction->party->getFullName(),
                                    'transaction_type'      => $transactionType,
                                    'transaction_date'      => $this->toUserDateFormat($transactionDate),
                                    'invoice_or_bill_code'  => $invoiceNumber,
                                    'invoice_value'         => $data->transaction->grand_total,
                                    'tax_rate'              => $data->tax->rate,
                                    'taxable_value'         => $data->taxable_value,
                                    'cgst_value'            => $cs_gst_amt,
                                    'sgst_value'            => $cs_gst_amt,
                                    'igst_value'            => $i_gst_amt,
                                    'state_of_supply'       => $stateName,
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

    public function getGstr2Records(Request $request) : JsonResponse{
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



            $preparedData = ItemTransaction::with(['transaction'])
                                                ->whereHasMorph('transaction', [Purchase::class, PurchaseReturn::class], function ($query) use ($fromDate, $toDate) {
                                                    $query->whereBetween('transaction_date', [$fromDate, $toDate]);
                                                })
                                                ->selectRaw('transaction_id, transaction_type, tax_id, sum(tax_amount) as tax_amount, sum(unit_price) - sum(discount_amount) as taxable_value')
                                                ->groupBy('tax_id', 'transaction_id', 'transaction_type')
                                                ->get();

            if($preparedData->count() == 0){
                throw new \Exception('No Records Found!!');
            }

            $recordsArray = [];

            foreach ($preparedData as $data) {
                $transactionDate = $data->transaction instanceof Purchase ? $data->transaction->purchase_date : $data->transaction->return_date;
                $transactionType = $data->transaction instanceof Purchase ? 'Purchase' : 'Purchase Return';
                $invoiceNumber = $data->transaction instanceof Purchase ? $data->transaction->purchase_code : $data->transaction->return_code;
                $stateName = $data->transaction->state? $data->transaction->state->name : '';

                $companyStateID = app('company')['state_id'];
                $invoiceStateID = $data->transaction->party->state_id;

                //GST Calculation
                $taxAmount = $data->tax_amount;
                $cs_gst_amt = $i_gst_amt = 0;
                if(empty($invoiceStateID) || $companyStateID == $invoiceStateID){
                    $cs_gst_amt = $this->formatWithPrecision($taxAmount/2, comma:false);
                }else{
                    $i_gst_amt = $this->formatWithPrecision($taxAmount, comma:false);
                }
                //end

                $recordsArray[] = [
                                    'tax_number'            => $data->transaction->party->tax_number??'',
                                    'party_name'            => $data->transaction->party->getFullName(),
                                    'transaction_type'      => $transactionType,
                                    'transaction_date'      => $this->toUserDateFormat($transactionDate),
                                    'invoice_or_bill_code'  => $invoiceNumber,
                                    'invoice_value'         => $data->transaction->grand_total,
                                    'tax_rate'              => $data->tax->rate,
                                    'taxable_value'         => $data->taxable_value,
                                    'cgst_value'            => $cs_gst_amt,
                                    'sgst_value'            => $cs_gst_amt,
                                    'igst_value'            => $i_gst_amt,
                                    'state_of_supply'       => $stateName,
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

}
