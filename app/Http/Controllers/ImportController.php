<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use App\Enums\Item as ItemEnums;

use App\Services\ItemTransactionService;
use App\Services\ItemService;

use App\Traits\FormatNumber;
use App\Traits\FormatsDateInputs;
use App\Models\Items\Item;
use App\Models\Items\ItemCategory;
use App\Models\Items\ItemTransaction;
use App\Models\Unit;
use App\Models\Tax;
use App\Models\State;
use App\Models\Items\ItemBatchTransaction;
use App\Models\Party\Party;
use App\Services\AccountTransactionService;

use App\Services\PartyTransactionService;
use App\Enums\ItemTransactionUniqueCode;
use App\Models\Currency;
use App\Models\Items\Brand;

class ImportController extends Controller
{
    use FormatsDateInputs;

    use FormatNumber;

    public $reader ;

    public $itemModel;

    public $defaultItemCategory;

    public $dateFormat;

    public $itemService;

    public $accountTransactionService;

    public $itemTransactionService;

    function __construct(Xlsx $reader, Item $itemModel, ItemService $itemService, AccountTransactionService $accountTransactionService, ItemTransactionService $itemTransactionService)
    {
        $this->itemModel  = $itemModel ;
        $this->reader  = $reader ;
        $this->defaultItemCategory  = ItemEnums::DEFAULT_ITEM_CATEGORY->value ;
        $this->dateFormat  = app('company')['date_format'];
        $this->itemService  = $itemService ;
        $this->accountTransactionService  = $accountTransactionService ;
        $this->itemTransactionService  = $itemTransactionService ;
    }

    public function items() : View {
        return view('import.item');
    }

    public function parties() : View {
        return view('import.party');
    }

    /**
     * Import the Excel Sheet Records
     * @return JsonResponse
     * */
    public function importItems(Request $request)
    {
        $file = $request->file('excel_file');

        $spreadsheet = $this->reader->load($file->getPathname());

        // Select the second sheet
        $sheetNumberOne = 0;
        $sheetOne = $spreadsheet->getSheet($sheetNumberOne); // Sheet indices start at 0, so 1 is the second sheet

        $sheetNumberTwo = 1;
        $sheetTwo = $spreadsheet->getSheet($sheetNumberTwo); // Sheet indices start at 0, so 1 is the second sheet

        // Get the data from the second sheet
        $data = $sheetOne->toArray();
        $dataTwo = $sheetTwo->toArray();

        $itemIds = [];

        try{
            DB::beginTransaction();

            // Do something with the data
            $i = 0;
            if(count($data) <= 1){
                throw new \Exception(__('app.records_not_found'));
            }
            foreach ($data as $row) {
                $i++;
                if($i === 1){
                    continue;
                }

                $itemName       = trim($row[0]);//required
                $description    = trim($row[1]);
                $itemType       = trim($row[2]);//required, "Product" or "Service"
                $hsn            = trim($row[3]);
                $sku            = trim($row[4]);
                $itemCode       = trim($row[5]);//required
                $category       = trim($row[6]);
                $brand          = trim($row[7]);

                $mrp            = trim($row[8]);
                $msp            = trim($row[9]);

                $purchasePrice  = trim($row[10]);
                $taxRate        = trim($row[11]);//required
                $taxName        = trim($row[12]);//required
                $taxType        = trim($row[13]);//required


                $profitMargin   = trim($row[14]);
                $salePrice      = trim($row[15]);
                $discountOnSale = trim($row[16]);
                $discountType   = trim($row[17]);
                $wholesalePrice = trim($row[18]);


                $opeingStockQty = trim($row[19]);
                $minimumStockQty= trim($row[20]);
                $itemLocation   = trim($row[21]);
                $baseUnit       = trim($row[22]);//required
                $secondaryUnit  = trim($row[23]);
                $conversionRate = trim($row[24]);

                $recordDetails = "Sheet:".$sheetNumberOne.", Row:".($i);

                $validator = Validator::make([
                        'itemName'  => $itemName,
                        'itemType'  => $itemType,
                        'itemCode'  => $itemCode,
                        'taxRate'   => $taxRate,
                        'taxName'   => $taxName,
                        'taxType'   => $taxType,
                        'baseUnit'  => $baseUnit,
                        'conversionRate'    => $conversionRate,
                        'mrp'               => $mrp,
                        'msp'               => $msp,
                        'profitMargin'      => $profitMargin,
                        'salePrice'         => $salePrice,
                        'discountOnSale'    => $discountOnSale,
                        'discountType'      => $discountType,
                        'wholesalePrice'    => $wholesalePrice,
                        'purchasePrice'     => $purchasePrice,
                        'opeingStockQty'    => $opeingStockQty,
                        'minimumStockQty'   => $minimumStockQty,
                    ],[
                        'itemName' => ['required', 'string', 'max:100', app('company')['is_item_name_unique'] ? Rule::unique('items', 'name') : null],
                        'itemType' => ['required',
                                        function ($attribute, $value, $fail) {
                                            if (!in_array(strtoupper($value), ['PRODUCT', 'SERVICE'])) {
                                                $fail(__('item.item_type_should_not_empty'));
                                            }
                                            return true;
                                        },
                                    ],
                        'itemCode' => 'required|string|max:255|unique:items,item_code',
                        'taxRate' => ['required','numeric','max:100'],
                        'taxName' => ['required','string','max:255'],
                        'taxType' => ['required',
                                        function ($attribute, $value, $fail) {
                                            if (!in_array(strtoupper($value), ['INCLUSIVE', 'EXCLUSIVE'])) {
                                                $fail('Tax type must be either Inclusive or Exclusive.');
                                            }
                                            return true;
                                        },
                                    ],
                        'baseUnit' => ['required','string','max:255'],
                        'conversionRate' => ['nullable','numeric',
                                        function ($attribute, $value, $fail) {
                                            if(!empty($value)){
                                                if(!is_numeric($value) && $value >0 ){
                                                    $fail(__('item.invalid_conversion_rate'));
                                                }
                                            }
                                            return true;
                                        },
                                    ],
                        'mrp'   => ['nullable','numeric',
                                        function ($attribute, $value, $fail) {
                                            if(!empty($value)){
                                                if(!is_numeric($value) && $value < 0 ){
                                                    $fail(__('item.invalid_mrp'));
                                                }
                                            }
                                            return true;
                                        },
                                    ],
                        'msp'   => ['nullable','numeric',
                                        function ($attribute, $value, $fail) {
                                            if(!empty($value)){
                                                if(!is_numeric($value) && $value < 0 ){
                                                    $fail(__('item.invalid_msp'));
                                                }
                                            }
                                            return true;
                                        },
                                    ],
                        'profitMargin'   => ['nullable','numeric',
                                        function ($attribute, $value, $fail) {
                                            if(!empty($value)){
                                                if(!is_numeric($value) && $value < 0 ){
                                                    $fail(__('item.invalid_profit_margin'));
                                                }
                                            }
                                            return true;
                                        },
                                    ],

                        'salePrice'   => ['nullable','numeric',
                                        function ($attribute, $value, $fail) {
                                            if(!empty($value)){
                                                if(!is_numeric($value) && $value < 0 ){
                                                    $fail(__('item.invalid_sale_price'));
                                                }
                                            }
                                            return true;
                                        },
                                    ],
                        'discountOnSale'   => ['nullable','numeric',
                                        function ($attribute, $value, $fail) {
                                            if(!empty($value)){
                                                if(!is_numeric($value) && $value < 0 ){
                                                    $fail(__('item.invalid_discount_on_sale'));
                                                }
                                            }
                                            return true;
                                        },
                                    ],
                        'discountType' => ['nullable',
                                        function ($attribute, $value, $fail) {

                                            if(!empty($value)){
                                                if (!in_array(strtoupper($value), ['PERCENTAGE', 'FIXED'])) {
                                                    $fail('Discount type must be either Percentage or Fixed.');
                                                }
                                            }
                                            return true;
                                        },
                                    ],
                        'wholesalePrice'   => ['nullable','numeric',
                                        function ($attribute, $value, $fail) {
                                            if(!empty($value)){
                                                if(!is_numeric($value) && $value < 0 ){
                                                    $fail(__('Invalid Wholesale Price!'));
                                                }
                                            }
                                            return true;
                                        },
                                    ],
                        'purchasePrice'   => ['nullable','numeric',
                                        function ($attribute, $value, $fail) {
                                            if(!empty($value)){
                                                if(!is_numeric($value) && $value < 0 ){
                                                    $fail(__('item.invalid_purchase_price'));
                                                }
                                            }
                                            return true;
                                        },
                                    ],
                        'opeingStockQty'   => ['nullable','numeric',
                                        function ($attribute, $value, $fail) {
                                            if(!empty($value)){
                                                if(!is_numeric($value) && $value < 0 ){
                                                    $fail(__('item.invalid_opening_quantity'));
                                                }
                                            }
                                            return true;
                                        },
                                    ],
                        'minimumStockQty'   => ['nullable','numeric',
                                        function ($attribute, $value, $fail) {
                                            if(!empty($value)){
                                                if(!is_numeric($value) && $value < 0 ){
                                                    $fail(__('item.invalid_minimum_quantity'));
                                                }
                                            }
                                            return true;
                                        },
                                    ],
                    ],[
                        'itemName.required' => __('item.item_name_should_not_empty'),
                        'itemName.string' => 'Item Name should be a string',
                        'itemName.max' => 'Item Name max 255 letters.',
                        'itemName.unique' => __('item.item_name_already_exist'),

                        'itemType.required' => __('item.item_type_should_not_empty'),
                        'itemCode.required' => __('item.item_code_should_not_empty'),
                        'itemCode.unique' => __('item.item_code_already_exist'),

                        'taxRate.required' => __('item.tax_rate_should_not_empty'),
                        'taxRate.numeric' => 'Tax Rate should be a numeric!',
                        'taxName.required' => __('item.tax_name_should_not_empty'),
                        'taxType.required' => __('item.tax_type_should_not_empty'),
                        'baseUnit.required' => __('item.base_unit_should_not_empty'),
                        'conversionRate.numeric' => __('item.invalid_conversion_rate'),
                        'mrp.numeric' => __('item.invalid_mrp'),
                        'msp.numeric' => __('item.invalid_msp'),
                        'profitMargin.numeric' => __('item.invalid_profit_margin'),
                        'salePrice.numeric' => __('item.invalid_sale_price'),
                        'discountOnSale.numeric' => __('item.invalid_discount_on_sale'),
                        'wholesalePrice.numeric' => __('item.invalid_wholesale_price'),
                        'purchasePrice.numeric' => __('item.invalid_purchase_price'),
                        'opeingStockQty.numeric' => __('item.invalid_opening_quantity'),
                        'minimumStockQty.numeric' => __('item.invalid_minimum_quantity'),
                    ]
                );

                if ($validator->fails()) {
                    throw new \Exception($validator->errors()->first(). " " . $recordDetails);
                }


                $itemCategoryId = (function() use ($recordDetails, $category) {
                    $response = $this->saveCategory($category);
                    if (!$response['status']) {
                        throw new \Exception($response['message'] . " " . $recordDetails);
                    }
                    return $response['id'];
                })();

                $brandId = (function() use ($recordDetails, $brand) {
                    $response = $this->saveBrand($brand);
                    if (!$response['status']) {
                        throw new \Exception($response['message'] . " " . $recordDetails);
                    }
                    return $response['id'];
                })();

                //Serial 1
                $baseUnitId = (function() use ($recordDetails, $baseUnit) {
                    $response = $this->savebaseUnit($baseUnit);
                    if (!$response['status']) {
                        throw new \Exception($response['message'] . " " . $recordDetails);
                    }
                    return $response['id'];
                })();
                //Serial 2
                $secondaryUnitId = (function() use ($recordDetails, $secondaryUnit, $baseUnitId) {
                    if(empty($secondaryUnit)){
                        return $baseUnitId;
                    }
                    $response = $this->saveSecondaryUnit($secondaryUnit);
                    if (!$response['status']) {
                        throw new \Exception($response['message'] . " " . $recordDetails);
                    }
                    return $response['id'];
                })();

                $taxId = (function() use ($recordDetails, $taxName, $taxRate) {
                    $response = $this->savetax($taxName, $taxRate);
                    if (!$response['status']) {
                        throw new \Exception($response['message'] . " " . $recordDetails);
                    }
                    return $response['id'];
                })();

                /**
                 * If Sale price is not 0 then it calculates the Profit Margin
                 */
                $purchasePrice = (!empty($purchasePrice)) ? $purchasePrice : 0;
                $salePrice = (!empty($salePrice)) ? $salePrice : 0;
                $profitMargin = (!empty($profitMargin)) ? $profitMargin : 0;

                if($salePrice>0){
                    $profitMargin = calculateProfitMargin($purchasePrice, $salePrice, $taxRate, $taxType);
                }
                else if($profitMargin > 0){
                    $salePrice = calculateSalePrice($purchasePrice, $profitMargin, $taxRate, $taxType);
                }
                else{
                    //Sale Price <=0 || profit margin == 0
                    $salePrice = $purchasePrice;
                }

                $itemModel = Item::create([
                    'count_id'          =>  (Item::select('count_id')->orderBy('id', 'desc')->first()?->count_id ?? 0)+1,
                    'is_service'        =>  (strtoupper($itemType) == 'PRODUCT') ? 0 : 1,
                    'item_code'         =>  $itemCode,
                    'name'              =>  $itemName,
                    'description'       =>  $description,
                    'hsn'               =>  $hsn,
                    'sku'               =>  $sku,

                    'item_category_id'  =>  $itemCategoryId,
                    'brand_id'          =>  $brandId,
                    'base_unit_id'      =>  $baseUnitId,
                    'secondary_unit_id' =>  $secondaryUnitId,

                    'conversion_rate'   =>  (!empty($conversionRate) && ($baseUnitId!=$secondaryUnitId)) ? $conversionRate : 1,

                    'profit_margin'     =>  $profitMargin,

                    'sale_price'                =>  $salePrice,
                    'is_sale_price_with_tax'    =>  (strtoupper($taxType) == 'INCLUSIVE') ? 1 : 0,
                    'sale_price_discount'       =>  (!empty($discountOnSale))? $discountOnSale : 0,
                    'sale_price_discount_type'  =>  (empty($discountType) || strtoupper($discountType) == 'PERCENTAGE') ? 'percentage' : 'fixed',

                    'wholesale_price'            =>  (!empty($wholesalePrice)) ? $wholesalePrice : 0,
                    'is_wholesale_price_with_tax'=>  (strtoupper($taxType) == 'INCLUSIVE') ? 1 : 0,

                    'purchase_price'            =>  $purchasePrice,
                    'is_purchase_price_with_tax'=>  (strtoupper($taxType) == 'INCLUSIVE') ? 1 : 0,

                    'tax_id'                    =>  $taxId,

                    'mrp'                       =>  (!empty($mrp)) ? $mrp : 0,
                    'msp'                       =>  (!empty($msp)) ? $msp : 0,

                    'tracking_type'             =>  'regular',
                    'min_stock'                 =>  (!empty($minimumStockQty)) ? $minimumStockQty : 0,
                    'item_location'             =>  $itemLocation,

                    'status'                    =>  1,
                ]);
                /**
                 * Record Item Transaction
                 * Import ItemTransactionService
                 * @return Model
                 * */
                $transactionResponse = $this->itemTransactionService->recordItemTransactionEntry($itemModel, [
                    'item_id'                   => $itemModel->id,
                    'transaction_date'          => Carbon::now()->format('Y-m-d'),
                    'warehouse_id'              => $request->warehouse_id,
                    'tracking_type'             => 'regular',
                    //'item_location'             => $itemLocation,
                    'mrp'                       => 0,
                    'quantity'                  => (!empty($opeingStockQty))? $opeingStockQty : 0,
                    'unit_id'                   => $baseUnitId,
                    'unit_price'                => (!empty($purchasePrice))? $purchasePrice : 0,
                    'tax_type'                  => (strtoupper($taxType) == 'INCLUSIVE') ? 'inclusive' : 'exclusive',
                    'total'                     => ((!empty($opeingStockQty))? $opeingStockQty : 0) * ((!empty($purchasePrice)) ? $purchasePrice : 0),
                ]);

                if(!$transactionResponse){
                    throw new \Exception(__('item.failed_to_record_item_transactions'). " " . $recordDetails);
                }

                //Update Account
                $this->accountTransactionService->itemOpeningStockTransaction($itemModel);


                //collect item id in array
                $itemIds[] = $itemModel->id;

            }//foreach



            $transactionCollection = collect();
            $j = 0;
            if(count($dataTwo) > 1){
                foreach ($dataTwo as $row) {
                    $j++;
                    if($j === 1){
                        continue;
                    }
                    if(empty(trim($row[0]))){
                        continue;
                    }
                    $itemName       = trim($row[0]);//required
                    $batchNo        = trim($row[1]);
                    $mfgDate        = trim($row[2]);
                    $expDate        = trim($row[3]);
                    $modelNo        = trim($row[4]);
                    $mrp            = trim($row[5]);
                    $color          = trim($row[6]);
                    $size           = trim($row[7]);
                    $openingQuantity = trim($row[8]);

                    $recordDetails = "Sheet:".$sheetNumberTwo.", Row:".($j);

                    $validator = Validator::make([
                            'itemName'  => $itemName,
                            'mrp'       => $mrp,
                            'mfgDate'   => $mfgDate,
                            'expDate'   => $expDate,
                            'openingQuantity'   => $openingQuantity,
                        ],[
                            'itemName'  => ['required','string','max:255', Rule::exists('items', 'name')],
                            'mrp'       => ['nullable','numeric',
                                            function ($attribute, $value, $fail) {
                                                if(!empty($value)){
                                                    if(!is_numeric($value) && $value < 0 ){
                                                        $fail(__('item.invalid_mrp'));
                                                    }
                                                }
                                                return true;
                                            },
                                        ],
                            'mfgDate' => ['nullable', 'date_format:'.implode(',', $this->getDateFormats())],
                            'expDate' => ['nullable', 'date_format:'.implode(',', $this->getDateFormats())],
                            'openingQuantity' => ['nullable','numeric',
                                            function ($attribute, $value, $fail) {
                                                if(!empty($value)){
                                                    if(!is_numeric($value) && $value >0 ){
                                                        $fail('Invalid opening stock quantity.');
                                                    }
                                                }
                                                return true;
                                            },
                                        ],

                        ],[
                            'itemName.required' => 'Batch Entry: Item Name required!',
                            'itemName.string' => 'Batch Entry: Item Name should be a string',
                            'itemName.max' => 'Batch Entry:Item Name max 255 letters.',
                            'itemName.exists' => 'Batch Entry: Item Name Not exist in the record!',
                            'mrp.numeric' => "Batch Entry: MRP should be a numeric number.",
                            'mfgDate.date_format' => "Batch Entry: Manufacture Date format should be like this ".implode(',', $this->getDateFormats()),
                            'expDate.date_format' => "Batch Entry: Expiry Date format should be like this ".implode(',', $this->getDateFormats()),
                            'openingQuantity.numeric' => __('item.invalid_mrp'),

                        ]
                    );

                    if ($validator->fails()) {
                        throw new \Exception($validator->errors()->first(). " [" . $recordDetails."]");
                    }

                    //Find the transaction id of item name
                    $recordExist = $transactionCollection->firstWhere('itemName', $itemName);

                    if(!$recordExist){

                        $itemModel = Item::where('name', $itemName)->get()->first();

                        /**
                         * Delete already added transaction as opening
                         *
                         * */
                        $itemModel->itemTransaction()->delete();

                        /**
                         * Record Item Transaction
                         * Import ItemTransactionService
                         * @return Model
                         * */
                        $transactionResponse = $this->itemTransactionService->recordItemTransactionEntry($itemModel, [
                            'item_id'                   => $itemModel->id,
                            'transaction_date'          => Carbon::now()->format('Y-m-d'),
                            'warehouse_id'              => $request->warehouse_id,
                            'tracking_type'             => 'batch',
                            //'item_location'             => $itemModel->item_location,
                            'mrp'                       => 0,
                            'quantity'                  => (!empty($openingQuantity))? $openingQuantity : 0,
                            'unit_price'                => $itemModel->sale_price,
                            'unit_id'                   => $itemModel->base_unit_id,
                            'tax_type'                  => ($itemModel->is_sale_price_with_tax) ? 'inclusive' : 'exclusive',
                        ]);

                        if(!$transactionResponse){
                            throw new \Exception(__('item.failed_to_record_item_transactions'). " " . $recordDetails);
                        }

                        $transactionCollection->push(['itemModel' => $itemModel,'itemName' => $itemName, 'transactionId' => $transactionResponse->id]);

                    }

                    /**
                     * Record Batch Entry for each batch
                     * */
                    $batchArray = [
                            'batch_no'              =>  (!empty($batchNo)) ? $batchNo : null,
                            'mfg_date'              =>  $mfgDate? $this->toSystemDateFormat($mfgDate):null,
                            'exp_date'              =>  $expDate? $this->toSystemDateFormat($expDate):null,
                            'model_no'              =>  $modelNo,
                            'mrp'                   =>  (!empty($mrp))? $mrp : 0,
                            'color'                 =>  $color,
                            'size'                  =>  $size,
                            'quantity'              =>  $openingQuantity,
                        ];

                    $batchTransaction = $this->itemTransactionService->recordItemBatches($transactionCollection->firstWhere('itemName', $itemName)['transactionId'], $batchArray, $transactionCollection->firstWhere('itemName', $itemName)['itemModel']->id, $request->warehouse_id, ItemTransactionUniqueCode::ITEM_OPENING->value);

                    if(!$batchTransaction){
                        throw new \Exception(__('item.failed_to_save_batch_records'));
                    }



                }//foreach $dataTwo
            }//if count($date)

            /**
             * Update Transactions and Item Stock
             * */
            if($transactionCollection->isNotEmpty()){
                foreach ($transactionCollection as $transactionData) {
                    /**
                     * Update stock_in pof Item Transactions
                     * Because One Item Transaction has multiple batch opening quantity
                     * */
                    $totalStock = ItemBatchTransaction::where('item_transaction_id', $transactionData['transactionId'])->sum('quantity');

                    ItemTransaction::where('id', $transactionData['transactionId'])->update(['quantity' => $totalStock]);

                    //Update tracking type of Item Model
                    $transactionData['itemModel']->tracking_type = 'batch';
                    $transactionData['itemModel']->save();

                    //update Item Stock
                    $this->itemService->updateItemStock($transactionData['itemModel']->id);

                    //Update Account
                    $this->accountTransactionService->itemOpeningStockTransaction($transactionData['itemModel']);
                }
            }

            //Update Item Master Average Purchase Price
            $this->itemTransactionService->updateItemMasterAveragePurchasePrice($itemIds);

            DB::commit();

            session(['record' => [
                                    'type' => 'success',
                                    'status' => "Success",
                                    'message' => "Data imported successfully!!",
                                ]]);

            return response()->json([
                'status'    => true,
                'message' => __('app.record_saved_successfully'),
            ]);

        } catch (\Exception $e) {
                DB::rollback();

                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 409);

        }

    }

    public function saveCategory($categoryName) : array
    {
        if(empty($categoryName)){
            $categoryName = $this->defaultItemCategory;
        }
        // Validate category name using Laravel validation rules
        $validator = Validator::make(
            [
            'name' => $categoryName,
            ],
            [
            'name' => 'required|string|max:255', // Adjust table and column names as needed
            ]
        );

        if ($validator->fails()) {
            return [
                'status'    => false,
                'message'   => $validator->errors()->first(),
            ];
        }

        $category = ItemCategory::firstOrCreate(['name' => $categoryName]);

        return [
            'status' => true,
            'message' => 'Category created successfully.',
            'id' => $category->id,
        ];
    }

    public function saveBrand($brandName) : array
    {
        if(empty($brandName)){
            return [
                'status' => true,
                'message' => 'Brand Name is Empty.',
                'id' => null,
            ];
        }
        // Validate category name using Laravel validation rules
        $validator = Validator::make(
            [
            'name' => $brandName,
            ],
            [
            'name' => 'nullable|string|max:255', // Adjust table and column names as needed
            ]
        );

        if ($validator->fails()) {
            return [
                'status'    => false,
                'message'   => $validator->errors()->first(),
            ];
        }

        $brand = Brand::firstOrCreate(['name' => $brandName]);

        return [
            'status' => true,
            'message' => 'Brand created successfully.',
            'id' => $brand->id,
        ];
    }
    public function saveBaseUnit($baseUnitName) : array
    {
        // Validate category name using Laravel validation rules
        $validator = Validator::make(
            [
            'name' => $baseUnitName,
            ],
            [
            'name' => 'required|string|max:255', // Adjust table and column names as needed
            ],
        );

        if ($validator->fails()) {
            return [
                'status'    => false,
                'message'   => $validator->errors()->first(),
            ];
        }

        // Create the category on successful validation
        $baseUnit = Unit::firstOrCreate(['name' => $baseUnitName, 'short_code' => $baseUnitName]);

        return [
                'status'    => true,
                'message'   => '',
                'id'        => $baseUnit->id,
            ];
    }
    public function saveSecondaryUnit($secondaryUnitName) : array
    {
        // Validate category name using Laravel validation rules
        $validator = Validator::make(
            [
            'name' => $secondaryUnitName,
            ],
            [
            'name' => 'nullable|string|max:255', // Adjust table and column names as needed
            ],
        );

        if ($validator->fails()) {
            return [
                'status'    => false,
                'message'   => $validator->errors()->first(),
            ];
        }

        // Create the category on successful validation
        $secondaryUnit = Unit::firstOrCreate(['name' => $secondaryUnitName, 'short_code' => $secondaryUnitName]);

        return [
                'status'    => true,
                'message'   => '',
                'id'        => $secondaryUnit->id,
            ];
    }
    public function savetax(string $taxName, $taxRate) : array
    {
        // Validate category name using Laravel validation rules
        $validator = Validator::make(
            [
            'name' => $taxName,
            ],
            [
            'name' => 'required|string|max:255', // Adjust table and column names as needed
            ]
        );

        if ($validator->fails()) {
            return [
                'status'    => false,
                'message'   => $validator->errors()->first(),
            ];
        }

        // Create the category on successful validation
        $tax = Tax::firstOrCreate(['name' => $taxName, 'rate' => $taxRate]);

        return [
                'status'    => true,
                'message'   => '',
                'id'        => $tax->id,
            ];
    }

    /**
     * Import the Excel Sheet Records
     * @return JsonResponse
     * */
    public function importParties(Request $request)
    {
        $file = $request->file('excel_file');

        $spreadsheet = $this->reader->load($file->getPathname());

        // Select the second sheet
        $sheetNumberOne = 0;
        $sheetOne = $spreadsheet->getSheet($sheetNumberOne); // Sheet indices start at 0, so 1 is the second sheet

        // Get the data from the second sheet
        $data = $sheetOne->toArray();

        $partyTransactionService = new PartyTransactionService();

        try{
            DB::beginTransaction();

            // Do something with the data
            $i = 0;
            if(count($data) <= 1){
                throw new \Exception(__('app.records_not_found'));
            }

            $currencyId = Currency::where('is_company_currency', 1)->first()->id;

            foreach ($data as $row) {
                $i++;
                if($i === 1){
                    continue;
                }

                $partyType              = strtolower(trim($row[0]));//required
                $firstName              = trim($row[1]);//required
                $lastName               = trim($row[2]);
                $email                  = trim($row[3]);
                $phone                  = trim($row[4]);
                $mobile                 = trim($row[5]);
                $whatsApp               = trim($row[6]);
                $taxNumber              = trim($row[7]);
                $stateName              = trim($row[8]); //GST Enabled users
                $billingAddress         = trim($row[9]);
                $shippingAddress        = trim($row[10]);
                $openingBalance         = trim($row[11]);
                $transactionDate        = trim($row[12]);//opeingBalanceDate
                $openingBalanceType     = trim($row[13]);
                $creditLimit            = trim($row[14]);
                $isWholesaleCustomer    = trim($row[15]);//Only for Customer (Yes/No)

                $recordDetails = "Sheet:".$sheetNumberOne.", Row:".($i);

                $validator = Validator::make([
                        'partyType'             => $partyType,
                        'firstName'             => $firstName,
                        'lastName'              => $lastName,
                        'email'                 => $email,
                        'phone'                 => $phone,
                        'mobile'                => $mobile,
                        'whatsapp'              => $whatsApp,
                        'taxNumber'             => $taxNumber,
                        'stateName'             => $stateName,
                        'billingAddress'        => $billingAddress,
                        'shippingAddress'       => $shippingAddress,
                        'openingBalance'        => $openingBalance,
                        'transactionDate'        => $transactionDate,
                        'openingBalanceType'        => $openingBalanceType,
                    ],[
                        'partyType'     => ['required',
                                            function ($attribute, $value, $fail) {
                                                if (!in_array(strtoupper($value), ['CUSTOMER', 'SUPPLIER'])) {
                                                    $fail('Party type must be either Customer or Supplier.');
                                                }
                                                return true;
                                            },
                                        ],
                        'firstName'     => 'required|string|max:255',
                        'lastName'      => 'nullable|string|max:255',
                        'email'         => ['nullable', 'email', 'max:100', Rule::unique('parties')->where('party_type', $partyType)],
                        'phone'         => ['nullable', 'string', 'max:20', Rule::unique('parties')->where('party_type', $partyType)],
                        'mobile'        => ['nullable', 'string', 'max:20', Rule::unique('parties')->where('party_type', $partyType)],
                        'whatsapp'      => ['nullable', 'string', 'max:20', Rule::unique('parties')->where('party_type', $partyType)],
                        'taxNumber'     => ['nullable', 'string', 'max:100'],
                        'stateName'     => ['nullable', 'string', 'max:100'],
                        'billingAddress'    => ['nullable', 'string', 'max:500'],
                        'shippingAddress'   => ['nullable', 'string', 'max:500'],
                        'openingBalance'    => ['nullable','numeric',
                                                function ($attribute, $value, $fail) {
                                                    if(!empty($value)){
                                                        if(!is_numeric($value) && $value >0 ){
                                                            $fail('Invalid Opening Balance!');
                                                        }
                                                    }
                                                    return true;
                                                },
                                            ],
                        'transactionDate' => ['nullable', 'date_format:'.implode(',', ['d-m-Y', 'd/m/Y', 'Y-m-d', 'Y/m/d'])],

                        'openingBalanceType'     => ['nullable',
                                            function ($attribute, $value, $fail) {
                                                if (!in_array(strtoupper($value), ['TO PAY', 'TO RECEIVE'])) {
                                                    $fail('Opening Balance type must be either "To Pay" or "To Receive".');
                                                }
                                                return true;
                                            },
                                        ],
                    ],[
                        'firstName.required' => 'First Name should not be a empty',
                        'firstName.string' => 'First Name should be a string',
                        'firstName.max' => 'First Name max 255 letters.',
                        'partyType.required' => "Party type must be either Customer or Supplier",
                    ]
                );

                if ($validator->fails()) {
                    throw new \Exception($validator->errors()->first(). " " . $recordDetails);
                }

                $stateId = (function() use ($recordDetails, $stateName) {
                    if(empty($stateName)){
                        return null;
                    }
                    $response = $this->saveState($stateName);
                    if (!$response['status']) {
                        throw new \Exception($response['message'] . " " . $recordDetails);
                    }
                    return $response['id'];
                })();

                /**
                 * If opening balance is not empty then need to select opeing balance type
                 * */
                if(!empty($openingBalance) && $openingBalance>0){
                    if(empty($openingBalanceType)){
                        throw new \Exception('Opening Balance type must be either "To Pay" or "To Receive'. " " . $recordDetails);
                    }
                }

                $partyModel = Party::create([
                    'party_type'            =>  $partyType,
                    'first_name'            =>  $firstName,
                    'last_name'             =>  !empty($lastName)? $lastName: null,
                    'email'                 =>  !empty($email)? $email: null,
                    'phone'                 =>  !empty($phone)? $phone: null,
                    'mobile'                =>  !empty($mobile)? $mobile: null,
                    'whatsapp'              =>  !empty($whatsApp)? $whatsApp: null,
                    'tax_number'            =>  !empty($taxNumber)? $taxNumber: null,
                    'state_id'              =>  $stateId,
                    'billing_address'       =>  !empty($billingAddress)? $billingAddress: null,
                    'shipping_address'      =>  !empty($shippingAddress)? $shippingAddress: null,
                    'is_set_credit_limit'   =>  !empty($creditLimit)? ($creditLimit > 0 ? 1 : 0): 0,
                    'credit_limit'          =>  !empty($creditLimit)? $creditLimit: 0,
                    'status'                =>  1,
                    'currency_id'           =>  $currencyId,
                    'is_wholesale_customer' =>  ($partyType == 'customer' && !empty($isWholesaleCustomer) && strtoupper($isWholesaleCustomer) == strtoupper('Yes') )? 1 : 0,
                ]);

                /**
                 * Record Opening Balance Transaction
                 * Import PartyTransactionService
                 * @return Model
                 * */
                $transactionResponse = $partyTransactionService->recordPartyTransactionEntry($partyModel, [
                    'transaction_date'      =>  (!empty($transactionDate))? $this->toSystemDateFormat($transactionDate) : Carbon::now()->format('Y-m-d'),
                    'to_pay'                =>  (strtoupper($openingBalanceType) == 'TO PAY')? ($openingBalance??0) : 0,
                    'to_receive'                =>  (strtoupper($openingBalanceType) == 'TO RECEIVE')? ($openingBalance??0) : 0,
                ]);

                if(!$transactionResponse){
                    throw new \Exception(__('party.failed_to_record_party_transactions'). " " . $recordDetails);
                }

                //Update Account
                $this->accountTransactionService->partyOpeningBalanceTransaction($partyModel);

                //Account Create or Update
                $acccountCreateOrUpdate = $this->accountTransactionService->createOrUpdateAccountOfParty(partyId: $partyModel->id, partyName: $partyModel->first_name." ".$partyModel->last_name, partyType: $partyModel->party_type );
                if(!$acccountCreateOrUpdate){
                    throw new \Exception(__('account.failed_to_create_or_update_account'));
                }

            }//foreach

            DB::commit();

            session(['record' => [
                                    'type' => 'success',
                                    'status' => "Success",
                                    'message' => "Data imported successfully!!",
                                ]]);

            return response()->json([
                'status'    => true,
                'message' => __('app.record_saved_successfully'),
            ]);

        } catch (\Exception $e) {
                DB::rollback();

                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 409);

        }

    }

    public function saveState($stateName) : array
    {
        // Validate state name using Laravel validation rules
        $validator = Validator::make(
            [
            'name' => $stateName,
            ],
            [
            'name' => 'required|string|max:255', // Adjust table and column names as needed
            ]
        );

        if ($validator->fails()) {
            return [
                'status'    => false,
                'message'   => $validator->errors()->first(),
            ];
        }

        $state = State::firstOrCreate(['name' => $stateName]);

        return [
            'status' => true,
            'message' => 'Category created successfully.',
            'id' => $state->id,
        ];
    }
}
