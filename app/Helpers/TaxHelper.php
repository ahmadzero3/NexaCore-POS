<?php

if (!function_exists('calculateTax')) {
    function calculateTax($price, $taxRate, $isInclusive = true)
    {
        $taxRate = $taxRate / 100; // Convert percentage to decimal

        if ($isInclusive) {
            // Calculate price exclusive of tax
            $exclusivePrice = $price / (1 + $taxRate);
            $taxAmount = $price - $exclusivePrice;
            $inclusivePrice = $price;
        } else {
            // Calculate price inclusive of tax
            $exclusivePrice = $price;
            $taxAmount = $price * $taxRate;
            $inclusivePrice = $price + $taxAmount;
        }

        return [
            'exclusive_price' => round($exclusivePrice, 2),
            'inclusive_price' => round($inclusivePrice, 2),
            'tax_amount' => round($taxAmount, 2)
        ];
    }
}

if (!function_exists('calculatePrice')) {
    function calculatePrice($price, $taxRate, $needInclusive = true)
    {
        $taxArray = calculateTax($price, $taxRate, $needInclusive);

        return $needInclusive ? $taxArray['exclusive_price'] : $taxArray['inclusive_price'];
    }
}

if (!function_exists('calculateProfitMargin')) {
    // Function to calculate profit margin given the sale price
    function calculateProfitMargin($purchasePrice, $salePrice, $taxRate, $taxType) {
        // Handle edge cases
        if ($purchasePrice == 0 || $salePrice == 0) {
            return 0; // Return 0% profit margin if purchase or sale price is 0
        }

        if ($taxType === 'Inclusive') {
            // Calculate base price (excluding tax) from inclusive purchase price
            $base_price = calculatePrice($purchasePrice, $taxRate, true);
            // Calculate sale price excluding tax from inclusive sale price
            $sale_excl = calculatePrice($salePrice, $taxRate, true);
            // Compute profit margin
            $profit_margin = ($sale_excl / $base_price) - 1;
        } else {

            // Calculate sale price excluding tax (assuming salePrice is inclusive)
            $sale_excl = calculatePrice($salePrice, $taxRate, true);
            // Compute profit margin based on purchase price (exclusive)
            $profit_margin = ($sale_excl / $purchasePrice) - 1;

        }

        // Convert profit margin to percentage and round
        $profit_margin_percent = round($profit_margin * 100, 2);
        return $profit_margin_percent;
    }
}

if (!function_exists('calculateSalePrice')) {
    // Function to calculate sale price given the profit margin
    function calculateSalePrice($purchasePrice, $profitMargin, $taxRate, $taxType) {
        // Handle edge cases
        if ($purchasePrice == 0) {
            return 0; // Return 0 if purchase price is 0
        }

        $profitMargin_decimal = $profitMargin / 100;

        if ($taxType === 'Inclusive') {
            // Convert inclusive purchase price to exclusive
            $base_price = calculatePrice($purchasePrice, $taxRate, true);
            // Apply profit margin to base price
            $price_after_profit = $base_price * (1 + $profitMargin_decimal);
            // Convert to inclusive sale price
            $sale_price = calculatePrice($price_after_profit, $taxRate, false);
        } else {
            // Apply profit margin to purchase price (exclusive)
            $price_after_profit = $purchasePrice * (1 + $profitMargin_decimal);
            // Convert to inclusive sale price
            $sale_price = calculatePrice($price_after_profit, $taxRate, false);
        }

        // Round the sale price to 2 decimal places
        return round($sale_price, 2);
    }
}