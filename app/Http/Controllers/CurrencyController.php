<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Models\Currency;
use App\Http\Requests\CurrencyRequest;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use App\Enums\Currency as CurrencyEnum;
use Illuminate\Support\Facades\DB;

class CurrencyController extends Controller
{
    private $excludedIdsToDelete;

    public function __construct()
    {
        //
    }

    public function messageToUser(){
        session(['record' => [
            'type' => 'info',
            'status' => "Information",
            'message' => "While adding a new currency, please note that your store's main currency is [<i>Company Currency</i>]. Transactions in the new currency will be converted based on exchange rates. Ensure the exchange rate is accurate to avoid discrepancies in pricing and accounting. You can update the exchange rate at any time after creating the new currency.
            <br>
            * The <i>Company Currency</i> always has a value of 1.00
            <br>
            * You need to set an exchange rate against the <i>Company Currency</i> to add a new currency.
            ",
        ]]);
        return true;
    }
    /**
     * Create a new payment-types.
     *
     * @return \Illuminate\View\View
     */
    public function create() : View {
        $this->messageToUser();
        return view('currency.create');
    }

    /**
     * Edit a payment-types.
     *
     * @param int $id The ID of the payment-types to edit.
     * @return \Illuminate\View\View
     */
    public function edit($id) : View {

        $this->messageToUser();

        $currency = Currency::find($id);

        return view('currency.edit', compact('currency'));
    }
    /**
     * Return JsonResponse
     * */
    public function store(CurrencyRequest $request) : JsonResponse {

        DB::beginTransaction();

        $filename = null;

        // Get the validated data from the PaymentTypesRequest
        $validatedData = $request->validated();
        $validatedData['is_company_currency'] = $request->has('is_company_currency') ? 1 : 0;

        // Create a new tax record using Eloquent and save it
        $currency = Currency::create($validatedData);

        //Update Currency of column
        if($validatedData['is_company_currency'] == 1){
            Currency::where('id', '!=', $currency->id)->update(['is_company_currency' => 0]);
        }

        /*Company should have atleast one currency as main currency*/
        $this->validateCompanyCurrency();

        DB::commit();
        return response()->json([
            'status'  => true,
            'message' => __('app.record_saved_successfully'),
            'data'  => [
                'id' => $currency->id,
                'name' => $currency->name,
            ]
        ]);
    }

    /**
     * Return JsonResponse
     * */
    public function update(CurrencyRequest $request) : JsonResponse {
        DB::beginTransaction();

        $validatedData = $request->validated();
        $validatedData['is_company_currency'] = $request->has('is_company_currency') ? 1 : 0;
        // Save the tax details
        Currency::where('id', $validatedData['id'])->update($validatedData);

        //Update Currency of column
        if($validatedData['is_company_currency'] == 1){
            Currency::where('id', '!=', $validatedData['id'])->update(['is_company_currency' => 0]);
        }

        /*Company should have atleast one currency as main currency*/
        $this->validateCompanyCurrency();


        DB::commit();
        return response()->json([
            'message' => __('app.record_updated_successfully'),
        ]);
    }

    public function validateCompanyCurrency(){

        //validate is company currency in currency column is value 1 else show exception
        $companyCurrency = Currency::where('is_company_currency', 1)->count();

        if($companyCurrency == 0){
            throw new \Exception("Company should have atleast one currency as main currency");
        }
        return true;
    }

    public function list() : View {
        $this->messageToUser();

        return view('currency.list');
    }

    public function datatableList(Request $request){

        $data = Currency::query();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at->format(app('company')['date_format']);
                    })
                    ->addColumn('action', function($row){
                            $id = $row->id;

                            $editUrl = route('currency.edit', ['id' => $id]);
                            $deleteUrl = route('currency.delete', ['id' => $id]);


                            $actionBtn = '<div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded font-22 text-option"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="' . $editUrl . '"><i class="bi bi-trash"></i><i class="bx bx-edit"></i> '.__('app.edit').'</a>
                                </li>';

                            $actionBtn .=  '<li>
                                    <button type="button" class="dropdown-item text-danger deleteRequest" data-delete-id='.$id.'><i class="bx bx-trash"></i> '.__('app.delete').'</button>
                                </li>';

                            $actionBtn .='</ul>
                        </div>';
                            return $actionBtn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
    }

    public function delete(Request $request) : JsonResponse{

        $selectedRecordIds = $request->input('record_ids');

        /**
         * Filter Restricted Currency' Id's
         * */

        if(empty($selectedRecordIds)){
            return response()->json([
                    'status'    => false,
                    'message' => __('app.invalid_record'),
                ],409);
        }

        // Perform validation for each selected record ID
        foreach ($selectedRecordIds as $recordId) {

            $record = Currency::find($recordId)->where('is_company_currnecy', 0);
            if (!$record) {
                // Invalid record ID, handle the error (e.g., show a message, log, etc.)
                return response()->json([
                    'status'    => false,
                    'message' => __('app.invalid_record_id',['record_id' => $recordId]),
                ]);

            }
            // You can perform additional validation checks here if needed before deletion
        }

        /**
         * All selected record IDs are valid, proceed with the deletion
         * Delete all records with the selected IDs in one query
         * */
        try {
            $deletedRecords = Currency::whereIn('id', $selectedRecordIds)->where('is_company_currency', 0)->delete();

            if ($deletedRecords === 0) {
            return response()->json([
                'status'    => false,
                'message' => __('app.cannot_delete_records'),
            ], 422);
            }
        }catch (\Exception $e){
            return response()->json(['message' => __('app.cannot_delete_records')], 422);
        }


        return response()->json([
            'status'    => true,
            'message' => __('app.record_deleted_successfully'),
        ]);
    }

    public function switchTheme(string $themeMode = 'light-theme')
    {
        // Validate theme mode
        if (!in_array($themeMode, ['dark-theme', 'light-theme'])) {
            throw new \InvalidArgumentException("Invalid theme mode: $themeMode");
        }

        // Set the cookie with the theme mode
        Cookie::queue('theme_mode', $themeMode, 60 * 60 * 24 * 30); // Expires in 30 days

        return response()->json([
            'status'    => true,
            'message' => "Theme Set",
            'theme' => $themeMode,
        ]);
    }

    public function setCurrencyCookie(array $currencyData)
    {
        // Encode array to json
        $jsonEncoded = json_encode($currencyData);

        // Set cookie with json data
        Cookie::queue('currency_data', $jsonEncoded, 60 * 60 * 24 * 30);

        //Set Cookie
        App::setLocale($currencyData['currency_code']);
    }

    public function switchCurrency($id)
    {
        $currency = Currency::find($id);
        if($currency){

            $currencyData = [
                          'currency_code' => $currency->code,
                          'currency_flag' => $currency->emoji,
                          'direction' => $currency->direction,
                          'emoji' => $currency->emoji,
                        ];

            $this->setCurrencyCookie($currencyData);

            return redirect()->back();
        }
    }

    public function setDefaultCurrency(){
        $currencyData = [
                          'currency_code' => config('app.locales'),
                          'currency_flag' => 'flag-icon-us',//It's a Emoji Code, which shows Flag on browser
                          'direction' => 'ltr',
                          'emoji' => 'flag-icon-us',
                        ];
        $this->setCurrencyCookie($currencyData);
    }
}
