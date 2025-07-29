<?php

namespace App\Http\Controllers\Reports;

use App\Traits\FormatNumber;
use App\Traits\FormatsDateInputs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

use App\Models\Items\Item;
use App\Models\Items\ItemTransaction;

use App\Models\Sale\SaleReturn;
use App\Models\Purchase\Purchase;
use App\Models\Purchase\PurchaseReturn;
use App\Models\Expenses\Expense;
use App\Enums\ItemTransactionUniqueCode;
use App\Models\User;
use App\Services\Reports\ProfitAndLoss\SaleProfitService;
use App\Services\Reports\ProfitAndLoss\SaleReturnProfitService;


class ProfitReportController extends Controller
{
    use FormatsDateInputs;

    use FormatNumber;

    private $saleProfitService;

    private $saleReturnProfitService;

    public function __construct(SaleProfitService $saleProfitService, SaleReturnProfitService $saleReturnProfitService)
    {
        $this->saleProfitService = $saleProfitService;
        $this->saleReturnProfitService = $saleReturnProfitService;
    }


    public function getProfitRecords(Request $request) : JsonResponse{
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
            $warehouseId        = $request->input('warehouse_id');

            /**
             * Get sale Total without tax
             * */
            $saleRecords = $this->saleProfitService->saleTotalAmount($fromDate, $toDate, $warehouseId);
            $saleTotalWithoutTaxAmount = $saleRecords['totalNetPrice'] - $saleRecords['totalTax'];

            /**
             * Get sale Return Total without tax
             * */
            $saleReturnRecords = $this->saleReturnProfitService->saleReturnTotalAmount($fromDate, $toDate, $warehouseId);
            $saleReturnTotalWithoutTaxAmount = $saleReturnRecords['totalNetPrice'] - $saleReturnRecords['totalTax'];

            /**
             * Get Purchase Total without Tax
             * */
            $purchaseRecords = $this->purchaseTotalAmount($fromDate, $toDate, $warehouseId);
            $purchaseTotalWithoutTaxAmount = $purchaseRecords['totalNetPrice'] - $purchaseRecords['totalTax'];

            /**
             * Get shipping charge from the purchase
             */
            $shippingChargeAmount = $purchaseRecords['totalShippingCharge'];
            
            /**
             * Get Purchase Return Total without Tax
             * */
            $purchaseReturnRecords = $this->purchaseReturnTotalAmount($fromDate, $toDate, $warehouseId);
            $purchaseReturnTotalWithoutTaxAmount = $purchaseReturnRecords['totalNetPrice'] - $purchaseReturnRecords['totalTax'];

            /**
             * Calculate Gross Profit
             * */
            $grossProfit = $saleTotalWithoutTaxAmount - $saleReturnTotalWithoutTaxAmount;

            $grossProfit = $grossProfit - ($purchaseTotalWithoutTaxAmount - $purchaseReturnTotalWithoutTaxAmount);


            /**
             * Get Expense Total
             * */
            $expenseTotalWithoutTaxAmount = $this->expenseTotalAmount($fromDate, $toDate);

            /**
             * Calculate Net profit
             * */
            $netProfit = $grossProfit - $expenseTotalWithoutTaxAmount - $shippingChargeAmount;

            /**
             * Get sale Total without tax
             * */
            $saleProfitTotalAmount = $this->saleProfitService->saleProfitTotalAmount($fromDate, $toDate, $warehouseId);

            


            $recordsArray = [
                                    'sale_without_tax'              => $this->formatWithPrecision($saleTotalWithoutTaxAmount),
                                    'sale_return_without_tax'       => $this->formatWithPrecision($saleReturnTotalWithoutTaxAmount),
                                    'purchase_without_tax'          => $this->formatWithPrecision($purchaseTotalWithoutTaxAmount),
                                    'purchase_return_without_tax'   => $this->formatWithPrecision($purchaseReturnTotalWithoutTaxAmount),
                                    'gross_profit'                  => $this->formatWithPrecision($grossProfit),
                                    'indirect_expense_without_tax'  => $this->formatWithPrecision($expenseTotalWithoutTaxAmount),
                                    'shipping_charge'               => $this->formatWithPrecision($shippingChargeAmount),
                                    'net_profit'                    => $this->formatWithPrecision($netProfit),
                                    'sale_profit'                   => $this->formatWithPrecision($saleProfitTotalAmount['theoreticalProfit']),
                                ];

            return response()->json([
                        'status'    => true,
                        'message'   => "Records are retrieved!!",
                        'data'      => $recordsArray,
                    ]);
        } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 409);

        }
    }

    public function purchaseTotalAmount($fromDate, $toDate, $warehouseId){
        //If warehouseId is not provided, fetch warehouses accessible to the user
        $warehouseIds = $warehouseId ? [$warehouseId] : User::find(auth()->id())->getAccessibleWarehouses()->pluck('id');

        $purchase = Purchase::with(['itemTransaction' => fn($q) => $q->whereIn('warehouse_id', $warehouseIds)])
                    ->select('id', 'purchase_date', 'shipping_charge', 'is_shipping_charge_distributed', 'round_off')
                    ->whereBetween('purchase_date', [$fromDate, $toDate])
                    ->get();

        if ($purchase->isNotEmpty()) {
            $totalDiscount = $purchase->flatMap->itemTransaction->sum('discount_amount') + $purchase->sum('round_off');
            $totalNetPrice = $purchase->flatMap->itemTransaction->sum('total');

            // Calculate total shipping charge only for records where is_shipping_charge_distributed = 0
            $totalShippingCharge = $purchase->where('is_shipping_charge_distributed', 0)->sum('shipping_charge');

            $totalTax = $purchase->flatMap->itemTransaction->sum('tax_amount');
        }


        return [
                'totalDiscount' => $totalDiscount ?? 0,
                'totalNetPrice' => $totalNetPrice ?? 0,
                'totalShippingCharge' => $totalShippingCharge ?? 0,
                'totalTax' => $totalTax ?? 0,
        ];
    }

    public function purchaseReturnTotalAmount($fromDate, $toDate, $warehouseId){
        //If warehouseId is not provided, fetch warehouses accessible to the user
        $warehouseIds = $warehouseId ? [$warehouseId] : User::find(auth()->id())->getAccessibleWarehouses()->pluck('id');

        $purchase = PurchaseReturn::with(['itemTransaction' => fn($q) => $q->whereIn('warehouse_id', $warehouseIds)])
            ->select('id', 'return_date')
            ->whereBetween('return_date', [$fromDate, $toDate])
            ->get();

        if($purchase->isNotEmpty()){
            $totalDiscount = $purchase->flatMap->itemTransaction->sum('discount_amount') + $purchase->sum('round_off');
            $totalNetPrice = $purchase->flatMap->itemTransaction->sum('total');
            $totalTax = $purchase->flatMap->itemTransaction->sum('tax_amount');
        }

        return [
                'totalDiscount' => $totalDiscount ?? 0,
                'totalNetPrice' => $totalNetPrice ?? 0,
                'totalTax' => $totalTax ?? 0,
        ];
    }



    public function expenseTotalAmount($fromDate, $toDate){
        return Expense::select('id', 'expense_date')
                        ->whereBetween('expense_date', [$fromDate, $toDate])
                        ->sum('grand_total');
    }

}
