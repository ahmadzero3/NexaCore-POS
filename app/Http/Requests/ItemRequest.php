<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class ItemRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $rulesArray = [
            'hsn'                             => ['nullable', 'string', 'max:100'],
            'item_category_id'                => ['required'],

            'brand_id'                        => ['nullable'],

            'base_unit_id'                    => ['required'],
            'secondary_unit_id'               => ['required'],
            'conversion_rate'                 => ['required'],
            'description'                     => ['nullable','string', 'max:250'],
            'status'                          => ['required'],
            //Pricing Tab
            'sale_price'                      => ['required', 'numeric'],
            'is_sale_price_with_tax'          => ['required', 'numeric'],

            'sale_price_discount'             => ['nullable', 'numeric'],
            'sale_price_discount_type'        => ['required', 'string', 'max:100'],

            'purchase_price'                  => ['nullable', 'numeric'],
            'is_purchase_price_with_tax'      => ['required', 'numeric'],

            'tax_id'                          => ['required', 'numeric'],

            'wholesale_price'                 => ['nullable', 'numeric'],
            'is_wholesale_price_with_tax'     => ['required', 'numeric'],

            'profit_margin'                   => ['nullable', 'numeric'],

            //Stock Tab
            'tracking_type'                   => ['required', 'string', 'max:100'],
            'warehouse_id'                    => ['required'],
            'transaction_date'                => ['required'],
            'opening_quantity'                => ['nullable', 'numeric'],
            'serial_number_json'              => ['nullable'],
            'batch_details_json'              => ['nullable'],
            'stock_entry_price'               => ['nullable', 'numeric'],
            'min_stock'                       => ['nullable', 'numeric'],
            'item_location'                   => ['nullable', ],
            'msp'                             => ['nullable', 'numeric'],


        ];

        if ($this->isMethod('PUT')) {
            $itemId                     = $this->input('item_id');
            //$rulesArray['mrp']           = ['required'];
            $rulesArray['name']          = ['required', 'string', 'max:100', (app('company')['is_item_name_unique']) ? Rule::unique('items')->where('name', $_POST['name'])->ignore($itemId) : null];
            $rulesArray['item_code']     = ['required', 'string', 'max:100', Rule::unique('items')->where('item_code', $_POST['item_code'])->ignore($itemId)];
        }else{
            $rulesArray['name']          = ['required', 'string', 'max:100', (app('company')['is_item_name_unique']) ? Rule::unique('items')->where('name', $_POST['name']) : null];
            $rulesArray['item_code']     = ['required', 'string', 'max:100', Rule::unique('items')->where('item_code', $_POST['item_code'])];
        }

        if ($this->has('sku')) {
            $rulesArray['sku'] = ['nullable', 'string', 'max:100'];
        }

        if ($this->has('mrp')) {
            $rulesArray['mrp'] = ['nullable', 'numeric'];
        }
        return $rulesArray;

    }
    public function messages(): array
    {
        $responseMessages = [];

        if ($this->isMethod('PUT')) {
            $responseMessages['item_id.required']    = 'ID Not found to update record';
        }

        return $responseMessages;
    }
    /**
     * Get the "after" validation callables for the request.
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();
            $data['sale_price']             = $data['sale_price']??0;
            $data['sale_price_discount']    = $data['sale_price_discount']??0;
            $data['purchase_price']         = $data['purchase_price']??0;
            $data['wholesale_price']        = $data['wholesale_price']??0;
            $data['min_stock']              = $data['min_stock']??0;
            $data['opening_quantity']       = $data['opening_quantity']??0;
            $data['at_price']               = $data['at_price']??0;
            $data['conversion_rate']        = ($data['is_service']) ? 1 : $data['conversion_rate'];
            $data['tracking_type']          = ($data['is_service']) ? 'regular' : $data['tracking_type'];
            $data['min_stock']              = ($data['is_service']) ? 0 : $data['min_stock'];
            $data['item_location']          = ($data['is_service']) ? null : $data['item_location'];
            $data['sku']                    = $data['sku'] ?? null;
            $data['mrp']                    = $data['mrp'] ?? 0;
            $data['msp']                    = $data['msp'] ?? 0;
            $data['profit_margin']          = $data['profit_margin'] ?? 0;

            $this->replace($data);
        });
    }
}
