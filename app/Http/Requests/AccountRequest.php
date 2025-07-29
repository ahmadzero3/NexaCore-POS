<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
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
            'group_id'                       => ['numeric'],
        ];

        if ($this->isMethod('PUT')) {
            $rulesArray['id']           = ['required'];
        }
        
        return $rulesArray;
    }
}
