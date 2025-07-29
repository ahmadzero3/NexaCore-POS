<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SmtpSettingsRequest extends FormRequest
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
            'host'          =>  ['required', 'string', 'max:100'],
            'port'          =>  ['required', 'string', 'max:100'],
            'username'      =>  ['required', 'string', 'max:100'],
            'password'      =>  ['required', 'string', 'max:100'],
            'encryption'    =>  ['required', 'string', 'max:100'],
            'smtp_status'   =>  ['required', 'max:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'host.required' => 'A Application name should not be empty',
            'host.string' => 'A Application name should be string type',
            'host.max' => 'A Application name should not cross 100 characters',

            'port.required' => 'A Footer text should not be empty',
            'port.string' => 'A Footer text should be string type',
            'port.max' => 'A Footer text should not cross 100 characters',

            'username.required' => 'A Footer text should not be empty',
            'username.string' => 'A Footer text should be string type',
            'username.max' => 'A Footer text should not cross 100 characters',

            'password.required' => 'A Footer text should not be empty',
            'password.max' => 'A Footer text should not cross 100 characters',

            'smtp_status.required' => 'A Footer text should not be empty',
            'smtp_status.max' => 'Status value should be 1 or 0',
        ];
    }
}
