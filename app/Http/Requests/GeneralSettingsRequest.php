<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingsRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'application_name'  =>  ['required', 'string', 'max:100'],
            'footer_text'       =>  ['required', 'string', 'max:100'],
            'language_id'       =>  ['required', 'max:20'],
            'timezone'       =>  ['required', 'string', 'max:100'],
            'date_format'       =>  ['required', 'string', 'max:100'],
            'time_format'       =>  ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'application_name.required' => 'A Application name should not be empty',
            'application_name.string' => 'A Application name should be string type',
            'application_name.max' => 'A Application name should not cross 100 characters',

            'footer_text.required' => 'A Footer text should not be empty',
            'footer_text.string' => 'A Footer text should be string type',
            'footer_text.max' => 'A Footer text should not cross 100 characters',

            'language.required' => 'A Language should not be empty',
        ];
    }

}
