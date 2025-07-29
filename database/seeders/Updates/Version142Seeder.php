<?php

namespace Database\Seeders\Updates;

use App\Models\Items\ItemGeneralQuantity;
use App\Models\Items\ItemTransaction;
use App\Models\Party\Party;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PermissionGroup;
use App\Models\Purchase\Purchase;
use App\Models\StockTransfer;
use Spatie\Permission\Models\Permission;

class Version142Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        echo "\Version142Seeder Running...";

        $this->updateParties();

        echo "\Version142Seeder Completed!!\n";
    }

    public function updateParties()
    {

        $stockTransfers = StockTransfer::with('itemStockTransfer')->get();

        if ($stockTransfers->isNotEmpty()) {
            foreach ($stockTransfers as $stockTransfer) {
                // Ensure the 'itemStockTransfer' relationship exists and is iterable
                if ($stockTransfer->itemStockTransfer) {
                    foreach ($stockTransfer->itemStockTransfer as $transfer) {

                        // Get From Warehouse Item Average Price
                        $warehouseId = $transfer->from_warehouse_id;

                        $toWarehouseId = $transfer->to_warehouse_id;
                        $itemId = $transfer->item_id; // Use $transfer for 'item_id'

                        // Get Purchase Price of the Item
                        $fromItemTransactionId = $transfer->from_item_transaction_id;
                        $toItemTransactionId = $transfer->to_item_transaction_id;

                        // Get Purchase Details
                        $worthItemsDetails = $this->worthItemsDetails($warehouseId, $itemId); // Current value

                        // Calculate Average Item Purchase Price
                        $averageItemPurchasePrice = $worthItemsDetails['totalPurchaseCost'] > 0
                            ? $worthItemsDetails['totalPurchaseCost'] / $worthItemsDetails['totalAvailableQuantity']
                            : 0;

                        $quantity = ItemTransaction::find($fromItemTransactionId)->quantity;
                        $itemTransaction = ItemTransaction::find($fromItemTransactionId); // Use correct transaction ID
                        $itemTransaction->unit_price = $averageItemPurchasePrice;
                        $itemTransaction->total = $averageItemPurchasePrice * $quantity;
                        $itemTransaction->save();


                        $quantity = ItemTransaction::find($fromItemTransactionId)->quantity;
                        $itemTransaction = ItemTransaction::find($toItemTransactionId); // Use correct transaction ID
                        $itemTransaction->unit_price = $averageItemPurchasePrice;
                        $itemTransaction->total = $averageItemPurchasePrice * $quantity;
                        $itemTransaction->save();

                    }
                }
            }
        }



    }//updateParties


    /**
     * Average purchase price has been used to calculate worth
     */
    public function worthItemsDetails($warehouseId, $itemId = null)
    {
        $availableItems = ItemGeneralQuantity::where('warehouse_id', $warehouseId)
            ->groupBy('item_id')
            ->where('quantity', '>', 0)
            ->when($itemId, function($query) use ($itemId){
                $query->where('item_id', $itemId);
            })
            ->selectRaw('item_id, SUM(quantity) as total_quantity')
            ->pluck('total_quantity', 'item_id');

        //Get Item Average Price
        $purchases = ItemTransaction::with('item')
            ->where(function ($query) {
                $query->where('transaction_type', getMorphedModelName(Purchase::class))
                    ->orWhere('transaction_type', getMorphedModelName('Item Opening'))
                    ->orWhere(function ($subQuery) {
                            $subQuery->where('transaction_type', getMorphedModelName('Stock Transfer'))
                                    ->where('unique_code', 'STOCK_RECEIVE');
                        });
            })
            ->where('warehouse_id', $warehouseId)
            ->whereIn('item_id', $itemIds ?? $availableItems->keys())
            ->get();

        $totalPurchaseCost = 0;
        $totalSalePrice = 0;
        $totalAvailableQuantity = 0;
        foreach ($availableItems as $itemId => $availableQuantity) {
            $relevantTransactions = $purchases->where('item_id', $itemId);
            if ($relevantTransactions->isNotEmpty()) {
                // $averagePurchasePrice = $relevantTransactions->map(function ($transaction) {
                //     return ($transaction['total'] > 0) ? ($transaction['total'] / $transaction['quantity']) : 0;
                // })->avg();

                $totalSum = $relevantTransactions->sum('total');
                $totalSumQty = $relevantTransactions->sum('quantity');

                $averagePurchasePrice =$totalSum/$totalSumQty;


                $item = $relevantTransactions->first()->item;

                $itemPurchaseCost = $averagePurchasePrice > 0 ?  $averagePurchasePrice * $availableQuantity : $item->purchase_price * $availableQuantity;
                $totalPurchaseCost += $itemPurchaseCost;

                $itemSalePrice = $item->sale_price * $availableQuantity;
                $totalSalePrice += $itemSalePrice;
                $totalAvailableQuantity += $availableQuantity;

            }
        }

        return [
            'totalPurchaseCost' => $totalPurchaseCost,
            'totalSalePrice' => $totalSalePrice,
            'totalAvailableQuantity' => $totalAvailableQuantity,
        ];
    }
}
