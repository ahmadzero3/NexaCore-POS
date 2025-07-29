<?php

namespace App\Http\Requests;
use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {

        $rulesArray = [
            'first_name'                            => ['required', 'string', 'max:100'],
            'last_name'                             => ['nullable', 'string', 'max:100'],
            'whatsapp'                              => ['nullable', 'string', 'max:100'],
            'address'                               => ['nullable', 'string', 'max:100'],
            'status'                                => ['required'],
        ];

        if ($this->isMethod('PUT')) {
            $customerId                 = $this->input('id');
            $rulesArray['id']           = ['required'];
            $rulesArray['mobile']       = ['nullable', 'string', 'max:20', Rule::unique('customers')->ignore($customerId)];
            $rulesArray['email']        = ['nullable', 'email', 'max:100', Rule::unique('customers')->ignore($customerId)];
        }else{
             $rulesArray['mobile']      = ['nullable', 'string', 'max:20', 'unique:'.Customer::class];
             $rulesArray['email']       = ['nullable', 'email', 'email', 'max:255', 'unique:'.Customer::class];
        }
        
        return $rulesArray;

    }
    public function messages(): array
    {
        $responseMessages = [
            'first_name.required'     => 'A First name should not be empty',
            'last_name.required'     => 'A Last name should not be empty',
            'status.required'   => 'Please Select Status',
            'mobile.max'        => 'Mobile number should be greater than 20 digits.',
        ];

        if ($this->isMethod('PUT')) {
            $responseMessages['id.required']    = 'ID Not found to update record';
        }

        return $responseMessages;
    }
}
