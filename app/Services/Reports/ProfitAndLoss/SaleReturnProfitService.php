<?php
namespace App\Services\Reports\ProfitAndLoss;

use App\Models\Sale\SaleReturn;
use App\Models\User;

class SaleReturnProfitService{

    public function saleReturnTotalAmount($fromDate, $toDate, $warehouseId){
        //If warehouseId is not provided, fetch warehouses accessible to the user
        $warehouseIds = $warehouseId ? [$warehouseId] : User::find(auth()->id())->getAccessibleWarehouses()->pluck('id');

        $sales = SaleReturn::with(['itemTransaction' => fn($q) => $q->whereIn('warehouse_id', $warehouseIds)])
            ->select('id', 'return_date')
            ->whereBetween('return_date', [$fromDate, $toDate])
            ->get();

        if($sales->isNotEmpty()){
            $totalDiscount = $sales->flatMap->itemTransaction->sum('discount_amount') + $sales->sum('round_off');
            $totalNetPrice = $sales->flatMap->itemTransaction->sum('total');
            $totalTax = $sales->flatMap->itemTransaction->sum('tax_amount');
        }

        return [
                'totalDiscount' => $totalDiscount ?? 0,
                'totalNetPrice' => $totalNetPrice ?? 0,
                'totalTax' => $totalTax ?? 0,
        ];
    }

}
