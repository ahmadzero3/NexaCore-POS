<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Language;

class LanguageRequest extends FormRequest
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
            'status'                => ['required'],
            'direction'             => ['required', 'string', 'max:20'],
            'code'                  => ['required', 'string', 'max:20'],
            'emoji'                  => ['required', 'string', 'max:20'],
        ];

        if ($this->isMethod('PUT')) {
            $languageId             = $this->input('id');
            $rulesArray['id']      = ['required'];
            $rulesArray['name']    = ['required', 'string', 'max:20', Rule::unique('languages')->ignore($languageId)];
        }else{
             $rulesArray['name']   = ['string', 'max:20', 'unique:'.Language::class];
        }
        
        return $rulesArray;

    }
    public function messages(): array
    {
        $responseMessages = [
            'name.required'     => 'A Language Name should not be empty',
            'status.required'   => 'Please Select Status',
            'emoji.required'   => 'Please Select Country Flag',
        ];

        if ($this->isMethod('PUT')) {
            $responseMessages['id.required']    = 'ID Not found to update record';
        }

        return $responseMessages;
    }
}
