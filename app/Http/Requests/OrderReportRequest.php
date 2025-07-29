<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\FormatsDateInputs;

class OrderReportRequest extends FormRequest
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
        return [
            'customer_id'                           => ['nullable', 'integer', Rule::exists('customers', 'id')],
            'from_date'                             => ['required', 'date'],
            'to_date'                               => ['required', 'date'],
        ];
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
            'from_date' => $this->toSystemDateFormat($this->input('from_date')),
            'to_date' => $this->toSystemDateFormat($this->input('to_date')),
        ]);
    }
}
