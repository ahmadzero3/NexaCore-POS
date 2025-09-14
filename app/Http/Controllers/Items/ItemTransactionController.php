<?php

namespace App\Http\Controllers\Items;

use App\Enums\ItemTransactionUniqueCode;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

use App\Traits\FormatNumber;
use App\Traits\FormatsDateInputs;
use App\Models\Items\Item;
use App\Models\Items\ItemTransaction;
use App\Models\StockAdjustment;
use App\Models\StockTransfer;
use App\Services\StockImpact;
use App\Services\ItemTransactionService;

class ItemTransactionController extends Controller
{
    use FormatsDateInputs;

    use FormatNumber;

    private $stockImpact;

    public $itemTransactionService;

    function __construct(StockImpact $stockImpact, ItemTransactionService $itemTransactionService)
    {
        $this->stockImpact = $stockImpact;
        $this->itemTransactionService = $itemTransactionService;
    }

    public function list($id) : View {
        $item = Item::with('baseUnit', 'category')->find($id);

        $avgPrices = $this->itemTransactionService->calculateEachItemSaleAndPurchasePrice([$id], warehouseId:null, useGlobalPurchasePrice: true);
        $item->avg_purchase_price = $avgPrices[$id]['purchase']['average_purchase_price'] ?? 0;
        $item->avg_sale_price = $avgPrices[$id]['sale']['average_sale_price'] ?? 0;

        return view('items.transaction.list', compact('item'));
    }

    public function datatableList(Request $request){
        $data = ItemTransaction::with('unit')->where('item_id', $request->item_id);

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('transaction_date', function ($row) {
                        return $row->formatted_transaction_date;
                    })
                    ->addColumn('reference_no', function ($row) {
                        return $row->transaction->getTableCode()??'';
                    })
                    ->addColumn('price', function ($row) {
                        return $this->formatWithPrecision($row->unit_price);
                    })
                    ->addColumn('transaction_type', function ($row) {

                        if($row->transaction_type == getMorphedModelName(StockAdjustment::class)){
                            return $row->transaction_type . (($row->unique_code == ItemTransactionUniqueCode::STOCK_ADJUSTMENT_INCREASE->value) ? " (". __('app.increase').")" : " (".__('app.decrease').")");
                        }
                        else if($row->transaction_type == getMorphedModelName(StockTransfer::class)){
                            return $row->transaction_type . (($row->unique_code == ItemTransactionUniqueCode::STOCK_RECEIVE->value) ? " (". __('app.receive').")" : " (".__('app.send').")")    ;
                        }

                        return $row->transaction_type;

                    })
                    ->addColumn('quantity', function ($row) {
                        return $this->formatQuantity($row->quantity);
                    })
                    ->addColumn('stock_impact', function ($row) {
                        return $this->stockImpact->returnStockImpact($row->unique_code, $row->quantity);
                    })
                    ->addColumn('unit_name', function ($row) {
                        return $row->unit->name;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
    }

}
