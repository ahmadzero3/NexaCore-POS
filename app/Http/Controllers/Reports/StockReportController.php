<?php

namespace App\Http\Controllers\Reports;

use App\Traits\FormatNumber;
use App\Traits\FormatsDateInputs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Items\Item;
use App\Models\Items\ItemTransaction;
use App\Models\Items\ItemBatchTransaction;
use App\Models\Items\ItemSerialTransaction;
use App\Models\Items\ItemBatchQuantity;
use App\Models\Items\ItemSerialQuantity;
use App\Models\Items\ItemGeneralQuantity;
use App\Enums\ItemTransactionUniqueCode;
use App\Models\User;
use App\Services\StockImpact;
use App\Services\ItemTransactionService;

class StockReportController extends Controller
{
    use FormatsDateInputs;

    use FormatNumber;

    private $stockImpact;

    private $itemTransactionService;

    function __construct(StockImpact $stockImpact, ItemTransactionService $itemTransactionService)
    {
        $this->stockImpact = $stockImpact;
        $this->itemTransactionService = $itemTransactionService;
    }

    /**
    * Report -> Stock Report -> Serial
    * @return JsonResponse
    * */
    function getBatchWiseStockRecords(Request $request): JsonResponse{
        try{
            $itemId             = $request->input('item_id');
            $brandId             = $request->input('brand_id');
            $batchMasterId      = $request->input('batch_id');
            $warehouseId        = $request->input('warehouse_id');

            //If warehouseId is not provided, fetch warehouses accessible to the user
            $warehouseIds = $warehouseId ? [$warehouseId] : User::find(auth()->id())->getAccessibleWarehouses()->pluck('id');

            $preparedData = ItemBatchQuantity::with('itemBatchMaster.item')
                                                ->when($batchMasterId, function ($query) use ($batchMasterId) {
                                                    $query->where('item_batch_master_id', $batchMasterId);
                                                })
                                                ->whereIn('warehouse_id', $warehouseIds)
                                                ->when($itemId, function ($query) use ($itemId) {
                                                    $query->where('item_id', $itemId);
                                                })
                                                ->when($brandId, function ($query) use ($brandId) {
                                                    return $query->whereHas('itemBatchMaster.item', function ($query) use ($brandId) {
                                                        $query->where('brand_id', $brandId); // Corrected to `brand_id`
                                                    });
                                                })
                                                ->get();


            if($preparedData->count() == 0){
                throw new \Exception('No Records Found!!');
            }

            $recordsArray = [];

            foreach ($preparedData as $data) {
                $remainingDays = $this->itemTransactionService->daysDifferenceByDate($data->itemBatchMaster->exp_date);
                $recordsArray[] = [
                                    'warehouse'             => $data->warehouse->name,
                                    'item_name'             => $data->itemBatchMaster->item->name,
                                    'brand_name'             => $data->itemBatchMaster->item->brand->name??'',
                                    'batch_no'              => $data->itemBatchMaster->batch_no??'',
                                    'mfg_date'              => $data->itemBatchMaster->formatted_mfg_date??'',
                                    'exp_date'              => $data->itemBatchMaster->formatted_exp_date??'',
                                    'days_until_expiry'     => $remainingDays,
                                    'model_no'              => $data->itemBatchMaster->model_no??'',
                                    'color'                 => $data->itemBatchMaster->color??'',
                                    'size'                  => $data->itemBatchMaster->size??'',
                                    'quantity'              => $data->quantity,
                                    'stock_impact_color'    => ($remainingDays <= 0) ? 'danger' : '',
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
     * Report -> Stock Report -> Serial
     * @return JsonResponse
     * */
    function getSerialWiseStockRecords(Request $request): JsonResponse{
        try{
            $itemId             = $request->input('item_id');
            $brandId             = $request->input('brand_id');
            $serialMasterId      = $request->input('serial_id');
            $warehouseId        = $request->input('warehouse_id');

            //If warehouseId is not provided, fetch warehouses accessible to the user
            $warehouseIds = $warehouseId ? [$warehouseId] : User::find(auth()->id())->getAccessibleWarehouses()->pluck('id');

            $preparedData = ItemSerialQuantity::with('itemSerialMaster')
                                                ->when($serialMasterId, function ($query) use ($serialMasterId) {
                                                    $query->where('item_serial_master_id', $serialMasterId); // Corrected to 'id'
                                                })
                                                ->whereIn('warehouse_id', $warehouseIds)
                                                ->when($itemId, function ($query) use ($itemId) {
                                                    $query->where('item_id', $itemId);
                                                })
                                                ->when($brandId, function ($query) use ($brandId) {
                                                    return $query->whereHas('itemSerialMaster.item', function ($query) use ($brandId) {
                                                        $query->where('brand_id', $brandId); // Corrected to `brand_id`
                                                    });
                                                })
                                                ->get();

            if($preparedData->count() == 0){
                throw new \Exception('No Records Found!!');
            }

            $recordsArray = [];

            foreach ($preparedData as $data) {
                $recordsArray[] = [
                                    'warehouse'             => $data->warehouse->name,
                                    'item_name'             => $data->itemSerialMaster->item->name,
                                    'brand_name'             => $data->itemSerialMaster->item->brand->name??'',
                                    'serial_code'           => $data->itemSerialMaster->serial_code??'',
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


    function getGeneralStockRecords(Request $request): JsonResponse{
        try{
            $itemId             = $request->input('item_id');
            $brandId             = $request->input('brand_id');
            $categoryId         = $request->input('item_category_id');
            $warehouseId         = $request->input('warehouse_id');

            //If warehouseId is not provided, fetch warehouses accessible to the user
            $warehouseIds = $warehouseId ? [$warehouseId] : User::find(auth()->id())->getAccessibleWarehouses()->pluck('id');

            $preparedData = ItemGeneralQuantity::with('item', 'warehouse')
                                                ->when($itemId, function ($query) use ($itemId) {
                                                    return $query->where('item_id', $itemId);
                                                })
                                                ->whereIn('warehouse_id', $warehouseIds)
                                                ->when($categoryId, function ($query) use ($categoryId) {
                                                    return $query->whereHas('item', function ($query) use ($categoryId) {
                                                        return $query->where('item_category_id', $categoryId);
                                                    });
                                                })
                                                ->when($brandId, function ($query) use ($brandId) {
                                                    return $query->whereHas('item', function ($query) use ($brandId) {
                                                        $query->where('brand_id', $brandId); // Corrected to `brand_id`
                                                    });
                                                })
                                                ->get();


            if($preparedData->count() == 0){
                throw new \Exception('No Records Found!!');
            }

            $recordsArray = [];

            foreach ($preparedData as $data) {
                $recordsArray[] = [
                                    'warehouse'             => $data->warehouse->name,
                                    'item_name'             => $data->item->name,
                                    'item_code'             => $data->item->item_code,
                                    'brand_name'            => $data->item->brand->name??'',
                                    'category_name'         => $data->item->category->name,
                                    'purchase_price'        => $this->formatWithPrecision($data->item->purchase_price, comma:false),
                                    'sale_price'            => $this->formatWithPrecision($data->item->sale_price, comma:false),
                                    'quantity'              => $this->formatWithPrecision($data->quantity, comma:false),
                                    'unit_name'             => $data->item->baseUnit->name,
                                    'stock_impact_color'    => ($data->quantity <= 0) ? 'danger' : '',
                                    'stock_value_cost'      => $this->formatWithPrecision($data->item->purchase_price * $data->quantity, comma:false),
                                    'stock_value_sale'      => $this->formatWithPrecision($data->item->sale_price * $data->quantity, comma:false),
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
