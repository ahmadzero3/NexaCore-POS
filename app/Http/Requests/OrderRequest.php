<?php

namespace App\Http\Requests;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\FormatsDateInputs;
use Carbon\Carbon;

class OrderRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {

        $rulesArray = [
            'party_id'                           => ['required', 'integer', Rule::exists('parties', 'id')],
            'order_date'                            => ['required', 'date'],
            'order_status'                          => ['required', 'string','max:50'],
            'total_amount'                          => ['required', 'string','max:50'],
            'note'                                  => ['nullable', 'string','max:250'],
        ];
        return $rulesArray;

    }
    public function messages(): array
    {
        $responseMessages = [
            'party_id.required'     => 'Please Select Customer',
            'order_date.required'     => 'Please Select Date',
        ];
        return $responseMessages;
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
        $this->merge([
            'order_date' => $this->toSystemDateFormat($this->input('order_date')),
        ]);
    }
}
