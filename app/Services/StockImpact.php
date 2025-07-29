<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;

use App\Enums\ItemTransactionUniqueCode;

class StockImpact
{
    public function returnStockImpact($uniqueCode, $quantity)
    {
        switch ($uniqueCode) {
            case ItemTransactionUniqueCode::ITEM_OPENING->value:
            case ItemTransactionUniqueCode::PURCHASE->value:
            case ItemTransactionUniqueCode::SALE_RETURN->value:
            case ItemTransactionUniqueCode::STOCK_RECEIVE->value:
                return [
                    'impact'    => 'positive',
                    'quantity'  => $quantity,
                    'color'     => 'primary',
                ];

            case ItemTransactionUniqueCode::PURCHASE_ORDER->value:
            case ItemTransactionUniqueCode::SALE_ORDER->value:
                return [
                    'impact'    => 'neutral',
                    'quantity'  => 0,
                    'color'     => 'secondary',
                ];

            case ItemTransactionUniqueCode::PURCHASE_RETURN->value:
            case ItemTransactionUniqueCode::SALE->value:
            case ItemTransactionUniqueCode::STOCK_TRANSFER->value:
                return [
                    'impact'    => 'negative',
                    'quantity'  => -$quantity,
                    'color'     => 'danger',
                ];

            default:
                return [
                    'impact'    => 'unknown',
                    'quantity'  => $quantity,
                    'color'     => 'warning',
                ];
        }
    }
}