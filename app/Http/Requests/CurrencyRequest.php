<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Language;

class CurrencyRequest extends FormRequest
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
            'symbol'            => ['required', 'string'],
            'exchange_rate'     => ['required', 'numeric', 'min:0'],
        ];

        if ($this->isMethod('PUT')) {
            $currencyId             = $this->input('id');
            $rulesArray['id']      = ['required'];
            $rulesArray['name']    = ['required', 'string', 'max:50', Rule::unique('currencies')->ignore($currencyId)];
            $rulesArray['code']    = ['required', 'string', 'max:3', Rule::unique('currencies')->ignore($currencyId)];
        }else{
             $rulesArray['name']   = ['required','string', 'max:50', 'unique:'.Language::class];
             $rulesArray['code']   = ['required','string', 'max:3', 'unique:'.Language::class];
        }

        return $rulesArray;

    }
    public function messages(): array
    {
        $responseMessages = [
            'name.required'     => 'A Language Name should not be empty',
        ];

        if ($this->isMethod('PUT')) {
            $responseMessages['id.required']    = 'ID Not found to update record';
        }

        return $responseMessages;
    }
}
