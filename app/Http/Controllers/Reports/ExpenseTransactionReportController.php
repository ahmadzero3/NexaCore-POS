<?php

namespace App\Http\Controllers\Reports;

use App\Traits\FormatNumber;
use App\Traits\FormatsDateInputs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

use App\Models\Items\ItemTransaction;
use App\Models\Expenses\Expense;
use App\Enums\ItemTransactionUniqueCode;

class ExpenseTransactionReportController extends Controller
{
    use FormatsDateInputs;

    use FormatNumber;

    public function getExpenseRecords(Request $request) : JsonResponse{
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
            $expenseCategoryId  = $request->input('expense_category_id');
            $expenseSubcategoryId  = $request->input('expense_subcategory_id');

            $preparedData = Expense::with('category','subcategory')
                                    ->when($expenseCategoryId, function ($query) use ($expenseCategoryId) {
                                        return $query->where('expense_category_id', $expenseCategoryId);
                                    })
                                    ->when($expenseSubcategoryId, function ($query) use ($expenseSubcategoryId) {
                                        return $query->where('expense_subcategory_id', $expenseSubcategoryId);
                                    })
                                    ->whereBetween('expense_date', [$fromDate, $toDate])
                                    ->get();


            if($preparedData->count() == 0){
                throw new \Exception('No Records Found!!');
            }

            $recordsArray = [];

            foreach ($preparedData as $data) {
                $recordsArray[] = [
                                    'expense_date'          => $this->toUserDateFormat($data->expense_date),
                                    'expense_code'          => $data->expense_code,
                                    'category_name'         => $data->category->name,
                                    'subcategory_name'      => $data->subcategory->name??'',
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
     * Item Expense Report
     * */
    function getExpenseItemRecords(Request $request): JsonResponse{
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
            $expenseCategoryId  = $request->input('expense_category_id');
            $expenseItemId      = $request->input('expense_item_id');
            $expenseSubcategoryId  = $request->input('expense_subcategory_id');


            $preparedData = Expense::with('category', 'subcategory','items.itemDetails')
                                                ->whereBetween('expense_date', [$fromDate, $toDate])
                                                ->when($expenseCategoryId, function ($query) use ($expenseCategoryId) {
                                                    return $query->where('expense_category_id', $expenseCategoryId);
                                                })
                                                ->when($expenseSubcategoryId, function ($query) use ($expenseSubcategoryId) {
                                                    return $query->where('expense_subcategory_id', $expenseSubcategoryId);
                                                })
                                                ->when($expenseItemId, function ($query) use ($expenseItemId) {
                                                    return $query->whereHas('items', function ($query) use ($expenseItemId) {
                                                        return $query->where('expense_item_master_id', $expenseItemId);
                                                    });
                                                })
                                                ->get();

            if($preparedData->count() == 0){
                throw new \Exception('No Records Found!!');
            }
            $recordsArray = [];
            foreach ($preparedData as $data) {

                foreach($data->items as $transaction){
                    $recordsArray[] = [
                                    'expense_date'          => $this->toUserDateFormat($data->expense_date),
                                    'expense_code'          => $data->expense_code,
                                    'category_name'         => $data->category->name,
                                    'subcategory_name'      => $data->subcategory->name,
                                    'item_name'             => $transaction->itemDetails->name,
                                    'unit_price'            => $this->formatWithPrecision($transaction->unit_price, comma:false),
                                    'quantity'              => $this->formatWithPrecision($transaction->quantity, comma:false),
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
     * Item Expense Report
     * */
    function getExpensePaymentRecords(Request $request): JsonResponse{
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
            $expenseCategoryId  = $request->input('expense_category_id');
            $paymentTypeId      = $request->input('payment_type_id');
            $expenseSubcategoryId  = $request->input('expense_subcategory_id');

            $preparedData = Expense::with('category', 'subcategory', 'paymentTransaction')
                                                ->when($fromDate, function ($query) use ($fromDate, $toDate) {
                                                    return $query->whereHas('paymentTransaction', function ($query) use ($fromDate, $toDate) {
                                                        $query->whereBetween('transaction_date', [$fromDate, $toDate]);
                                                    });
                                                })
                                                ->when($expenseCategoryId, function ($query) use ($expenseCategoryId) {
                                                    return $query->where('expense_category_id', $expenseCategoryId);
                                                })
                                                ->when($expenseSubcategoryId, function ($query) use ($expenseSubcategoryId) {
                                                    return $query->where('expense_subcategory_id', $expenseSubcategoryId);
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
                                    'expense_code'          => $data->expense_code,
                                    'category_name'         => $data->category->name,
                                    'subcategory_name'      => $data->subcategory->name,
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
