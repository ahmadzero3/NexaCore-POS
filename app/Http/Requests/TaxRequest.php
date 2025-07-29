<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Tax;
use Illuminate\Validation\Rule;

class TaxRequest extends FormRequest
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
            'rate'                 => ['required', 'numeric', 'between:0,100'],
            'status'               => ['required'],
        ];

        if ($this->isMethod('PUT')) {
            $taxId                 = $this->input('id');
            $rulesArray['id']      = ['required'];
            $rulesArray['name']    = ['required', 'string', 'max:20', Rule::unique('taxes')->ignore($taxId)];
        }else{
             $rulesArray['name']   = ['string', 'max:20', 'unique:'.Tax::class];
        }
        
        return $rulesArray;

    }
    public function messages(): array
    {
        $responseMessages = [
            'name.required'     => 'A Tax Name should not be empty',
            'rate.required'     => 'A Tax Rate should not be empty',
            'rate.between'      => 'The tax rate must be between 0 to 100',
            'status.required'   => 'Please Select Status',
        ];

        if ($this->isMethod('PUT')) {
            $responseMessages['id.required']    = 'ID Not found to update record';
        }

        return $responseMessages;
    }
}
