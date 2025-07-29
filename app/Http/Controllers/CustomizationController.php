<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sale\Sale;
use App\Models\Items\ItemTransaction;

class CustomizationController extends Controller
{
    public function edit()
    {
        $color = DB::table('customizations')->where('key', 'card_header_color')->value('value');
        $borderColor = DB::table('customizations')->where('key', 'card_border_color')->value('value');
        $headingColor = DB::table('customizations')->where('key', 'heading_color')->value('value');
        $toggle_switch = DB::table('customizations')->where('key', 'toggle_switch')->value('value');
        return view('customization.edit', compact('color', 'borderColor', 'headingColor', 'toggle_switch'));
    }

    public function update(Request $request)
    {
        // Validate the color inputs
        $request->validate([
            'color' => 'required|string|max:7',
            'border_color' => 'required|string|max:7',
            'heading_color' => 'required|string|max:7',
        ]);

        // Determine the toggle switch state
        $toggleState = $request->has('toggle_switch') ? 'active' : 'not_active';

        // Store the new settings in the database
        DB::table('customizations')->updateOrInsert(
            ['key' => 'toggle_switch'],
            ['value' => $toggleState]
        );

        DB::table('customizations')->updateOrInsert(
            ['key' => 'card_header_color'],
            ['value' => $request->input('color')]
        );

        DB::table('customizations')->updateOrInsert(
            ['key' => 'card_border_color'],
            ['value' => $request->input('border_color')]
        );

        DB::table('customizations')->updateOrInsert(
            ['key' => 'heading_color'],
            ['value' => $request->input('heading_color')]
        );

        return redirect()->route('customize.edit')->with('success', 'Settings updated successfully!');
    }

    /**
     * Return the top 4 trending item IDs by total sold quantity.
     */
    public function trendingItems(): array
    {
        // Use the model's morph class to match the stored transaction_type
        $transactionType = (new Sale())->getMorphClass();

        return ItemTransaction::query()
            ->select([
                'item_transactions.item_id',
                DB::raw('SUM(item_transactions.quantity) as total_quantity')
            ])
            ->where('item_transactions.transaction_type', $transactionType)
            ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
                return $query->where('item_transactions.created_by', auth()->user()->id);
            })
            ->groupBy('item_transactions.item_id')
            ->orderByDesc('total_quantity')
            ->limit(4)
            ->pluck('item_id')
            ->toArray();
    }
}
