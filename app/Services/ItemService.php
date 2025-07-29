<?php
namespace App\Services;

use App\Models\Items\Item;
use App\Models\Items\ItemTransaction;
use App\Models\Items\ItemGeneralQuantity;
use App\Enums\ItemTransactionUniqueCode;

class ItemService{

	/**
	 * Update Item Stock in Item Model
	 *
	 * */
	public function updateItemStock($itemId) : bool{
		$itemModel = Item::find($itemId);
		//Get the Sum of Quantity
		$baseUnitSumQuantity = $this->getSumOfItemQuantity($itemId);
		$itemModel->current_stock = $baseUnitSumQuantity;
		$itemModel->save();
		return true;
	}

	public function getSumOfItemQuantity($itemId)
	{
		$itemTransactions = ItemGeneralQuantity::where('item_id', $itemId)->sum('quantity');
		return $itemTransactions;
	}

    /**
     * Format the quantity based on the item and its units.
     *
     * @param float $quantity
     * @param int $itemId
     * @return string
     */
    public function getQuantityInUnit(float $quantity, int $itemId): string
    {
        $item = Item::with(['baseUnit', 'secondaryUnit'])->findOrFail($itemId);
        $baseUnit = $item->baseUnit;
        $secondaryUnit = $item->secondaryUnit;
        $conversionFactor = $item->conversion_rate;  // Assuming 1 box = 10 bottles

        // Handle zero conversion factor or zero quantity
        if ($quantity == 0 || $conversionFactor === 0) {
            return '0 ' . $baseUnit->name;
        }

        // Handle negative quantity
        if ($quantity < 0) {
            $quantity = abs($quantity); // Convert to positive for calculation
            $negativeIndicator = '-';   // We'll add this to the result to indicate a negative quantity
        } else {
            $negativeIndicator = '';    // No negative sign for positive quantities
        }

        $baseUnitQuantity = floor($quantity);  // Number of full boxes
        $fractionalQuantity = $quantity - $baseUnitQuantity;  // Remaining fraction

        // Convert fractional part to bottles
        $secondaryUnitQuantity = round($fractionalQuantity * $conversionFactor);

        $formattedQuantity = '';

        // If there are full boxes
        if ($baseUnitQuantity > 0) {
            $formattedQuantity .= "{$baseUnitQuantity} {$baseUnit->name}";
        }

        // If there are also bottles
        if ($secondaryUnitQuantity > 0) {
            if (!empty($formattedQuantity)) {
                $formattedQuantity .= ' ';
            }
            $formattedQuantity .= "{$secondaryUnitQuantity} {$secondaryUnit->name}";
        }

        // If there are no boxes but there are bottles (like in 0.8 case), show only bottles
        if ($baseUnitQuantity == 0 && $secondaryUnitQuantity > 0) {
            $formattedQuantity = "{$secondaryUnitQuantity} {$secondaryUnit->name}";
        }

        // Prepend the negative indicator if necessary
        return $negativeIndicator . ($formattedQuantity ?: '0 ' . $baseUnit->name);
    }


}
