<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
            'name'                            => ['required', 'string', 'max:100'],
            'description'                     => ['nullable','string', 'max:250'],
            'unit_price'                      => ['numeric', ],
            'tax_id'                          => ['numeric',],
            'tax_type'                        => ['string', 'max:100'],
            'status'                          => ['required'],
        ];

        if ($this->isMethod('PUT')) {
            $customerId                 = $this->input('id');
            $rulesArray['id']           = ['required'];
        }
        
        return $rulesArray;

    }
    public function messages(): array
    {
        $responseMessages = [];

        if ($this->isMethod('PUT')) {
            $responseMessages['id.required']    = 'ID Not found to update record';
        }

        return $responseMessages;
    }
}
