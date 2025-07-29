<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\PaymentTypes;
use Illuminate\Foundation\Http\FormRequest;

class PaymentTypesRequest extends FormRequest
{
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
            'status'               => ['required'],
            'description'          => ['nullable','string', 'max:300'],
            'account_number'        => ['nullable','string', 'max:50'],
            'bank_code'             => ['nullable','string', 'max:50'],
        ];

        if ($this->isMethod('PUT')) {
            $paymentId             = $this->input('id');
            $rulesArray['id']      = ['required'];
            $rulesArray['name']    = ['required', 'string', 'max:255', Rule::unique('payment_types')->ignore($paymentId)];
        }else{
             $rulesArray['name']   = ['string', 'max:255', 'unique:'.PaymentTypes::class];
        }
        
        return $rulesArray;

    }
    public function messages(): array
    {
        $responseMessages = [
            'name.required'     => 'A Payment Name should not be empty',
            'status.required'   => 'Please Select Status',
        ];

        if ($this->isMethod('PUT')) {
            $responseMessages['id.required']    = 'ID Not found to update record';
        }

        return $responseMessages;
    }
}
