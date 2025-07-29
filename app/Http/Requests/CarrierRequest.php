<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CarrierRequest extends FormRequest
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
            'whatsapp'                              => ['nullable', 'string', 'max:100'],
            'phone'                                 => ['nullable', 'string', 'max:100'],
            'address'                               => ['nullable', 'string', 'max:500'],
            'note'                                  => ['nullable', 'string', 'max:500'],
            'status'                                => ['required'],
        ];

        if ($this->isMethod('PUT')) {
            $carrierId                     = $this->input('id');
            $rulesArray['id']           = ['required'];
            $rulesArray['name']       = ['required', 'string', 'max:100', Rule::unique('carriers')->ignore($carrierId)];
            $rulesArray['mobile']       = ['nullable', 'string', 'max:20', Rule::unique('carriers')->ignore($carrierId)];
            $rulesArray['phone']       = ['nullable', 'string', 'max:20', Rule::unique('carriers')->ignore($carrierId)];
            $rulesArray['whatsapp']     = ['nullable', 'string', 'max:20', Rule::unique('carriers')->ignore($carrierId)];
            $rulesArray['email']        = ['nullable', 'email', 'max:100', Rule::unique('carriers')->ignore($carrierId)];
        }else{
            $rulesArray['name']       = ['required', 'string', 'max:100', Rule::unique('carriers')];
            $rulesArray['mobile']       = ['nullable', 'string', 'max:20', Rule::unique('carriers')];
            $rulesArray['phone']       = ['nullable', 'string', 'max:20', Rule::unique('carriers')];
            $rulesArray['whatsapp']     = ['nullable', 'string', 'max:20', Rule::unique('carriers')];
            $rulesArray['email']        = ['nullable', 'email', 'email', 'max:255', Rule::unique('carriers')];
        }

        return $rulesArray;

    }
    public function messages(): array
    {
        $responseMessages = [
            'name.required'     => 'A Carrier Name should not be empty',
            'status.required'   => 'Please Select Status',
        ];

        if ($this->isMethod('PUT')) {
            $responseMessages['id.required']    = 'ID Not found to update record';
        }

        return $responseMessages;
    }
}
