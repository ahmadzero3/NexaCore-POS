<?php

namespace App\Http\Requests;

use App\Models\Party\Party;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\FormatsDateInputs;

class PurchaseReturnRequest extends FormRequest
{

    use FormatsDateInputs;

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
            'party_id'             => ['required', 'integer', Rule::exists('parties', 'id')->where('party_type','supplier')],
            'return_date'        => ['required', 'date_format:'.implode(',', $this->getDateFormats())],
            'prefix_code'          => ['nullable', 'string','max:250'],
            'return_code'           => ['required', 'string','max:50'],
            'count_id'             => ['required', 'numeric'],
            'reference_no'           => ['nullable', 'string','max:50'],
            'round_off'            => ['nullable',Rule::requiredIf( fn () => empty($this->input('round_off'))),'numeric',],
            'grand_total'          => ['required', 'numeric'],
            'note'                 => ['nullable', 'string','max:250'],
            'state_id'             => ['nullable', 'integer', Rule::exists('states', 'id')],
            'row_count'            => ['required', 'integer', 'min:1'],
            'currency_id'          => ['nullable', 'integer', 'min:1'],
            'exchange_rate'        => ['nullable', 'numeric', 'min:0'],
        ];

        //For Update Operation
        if ($this->isMethod('PUT')) {
            $rulesArray['return_id']          = ['required'];
        }

        //For Convert Opearation
        if ($this->input('operation') == 'convert') {
            //Which is Purchase Order Id
            //$rulesArray['purchase_id']          = ['required'];
        }
        return $rulesArray;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        /**
         * @method formatDateInput
         * Defined in Trait FormatsDateInputs
         * */
        $returnDate  = $this->input('return_date');
        $this->merge([
            'return_date' => $this->toSystemDateFormat($returnDate),
            'return_code' => $this->getPurchaseCode(),
            'state_id' => (!empty($this->input('state_id'))) ? $this->input('state_id') : null,
        ]);

        //check is invoice_currency_id is exist or not
        if($this->has('invoice_currency_id')){
            $this->merge([
                'currency_id' => $this->input('invoice_currency_id'),
                'exchange_rate' => $this->input('exchange_rate')==0 ? 1 : $this->input('exchange_rate'),
            ]);
        }else{
            //get from party currency id and currency rate
            $partyCurrency = Party::with('currency')->select('currency_id')
                                    ->find($this->input('party_id'));
            $this->merge([
                'currency_id' => $partyCurrency->currency_id,
                'exchange_rate' => $partyCurrency->currency->exchange_rate,
            ]);
        }
    }

    /**
     *
     * @return string
     */
    protected function getPurchaseCode()
    {
        $prefixCode = $this->input('prefix_code');
        $countId = $this->input('count_id');

        return $prefixCode . $countId;
    }

    public function messages(): array
    {
        $responseMessages = [
            'row_count.min'     => __('item.please_select_items'),
        ];

        if ($this->isMethod('PUT')) {
            $responseMessages['id.required']    = 'ID Not found to update record';
        }
        return $responseMessages;
    }

}
