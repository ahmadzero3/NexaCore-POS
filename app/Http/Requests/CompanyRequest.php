<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Company;

class CompanyRequest extends FormRequest
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
            'name'              =>  ['required', 'string', 'max:100'],
            'mobile'            =>  ['required', 'string', 'max:20'],
            'email'            =>  ['required', 'string', 'email', 'max:50'],
            'tax_number'       => ['nullable', 'string', 'max:100'],
            'address'           =>  ['required', 'string', 'max:300'],
            //'bank_details'           =>  ['required', 'string'],
            'state_id'          => ['nullable', 'integer', Rule::exists('states', 'id')],
            'colored_logo'      =>  ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }

   /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'state_id' => (!empty($this->input('state_id'))) ? $this->input('state_id') : null,
        ]);
    }
}
