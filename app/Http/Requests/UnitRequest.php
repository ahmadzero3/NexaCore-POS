<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\Unit;
use Illuminate\Foundation\Http\FormRequest;

class UnitRequest extends FormRequest
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
            'status'               => ['required'],
            'description'          => ['nullable','string', 'max:250'],
        ];

        if ($this->isMethod('PUT')) {
            $unitId             = $this->input('id');
            $rulesArray['id']      = ['required'];
            $rulesArray['name']    = ['required', 'string', 'max:50', Rule::unique('units')->ignore($unitId)];
            $rulesArray['short_code']    = ['required', 'string', 'max:50', Rule::unique('units')->ignore($unitId)];
        }else{
             $rulesArray['name']   = ['string', 'max:50', 'unique:'.Unit::class];
             $rulesArray['short_code']   = ['string', 'max:20', 'unique:'.Unit::class];
        }
        
        return $rulesArray;

    }
    public function messages(): array
    {
        $responseMessages = [
            'name.required'     => 'A Unit Name should not be empty',
            'status.required'   => 'Please Select Status',
        ];

        if ($this->isMethod('PUT')) {
            $responseMessages['id.required']    = 'ID Not found to update record';
        }

        return $responseMessages;
    }
}
