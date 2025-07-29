<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\Warehouse;
use Illuminate\Foundation\Http\FormRequest;

class WarehouseRequest extends FormRequest
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
            'description'          => ['nullable','string', 'max:250'],
        ];

        if ($this->isMethod('PUT')) {
            $paymentId             = $this->input('id');
            $rulesArray['id']      = ['required'];
            $rulesArray['name']    = ['required', 'string', 'max:20', Rule::unique('warehouses')->ignore($paymentId)];
        }else{
             $rulesArray['name']   = ['string', 'max:20', 'unique:'.Warehouse::class];
        }
        
        return $rulesArray;

    }
    public function messages(): array
    {
        $responseMessages = [
            'name.required'     => 'A Warehouse Name should not be empty',
            'status.required'   => 'Please Select Status',
        ];

        if ($this->isMethod('PUT')) {
            $responseMessages['id.required']    = 'ID Not found to update record';
        }

        return $responseMessages;
    }
}
