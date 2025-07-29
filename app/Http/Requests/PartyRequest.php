<?php

namespace App\Http\Requests;
use App\Models\Party\Party;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class PartyRequest extends FormRequest
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
        $partyType = $_POST['party_type']; // Assuming party_type is submitted via POST

        $rulesArray = [
            'first_name'                            => ['required', 'string', 'max:100'],
            'last_name'                             => ['nullable', 'string', 'max:100'],
            'whatsapp'                              => ['nullable', 'string', 'max:100'],
            'phone'                                 => ['nullable', 'string', 'max:100'],
            'tax_number'                            => ['nullable', 'string', 'max:100'],
            'billing_address'                       => ['nullable', 'string', 'max:500'],
            'shipping_address'                      => ['nullable', 'string', 'max:500'],
            'opening_balance'                       => ['nullable', 'numeric'],
            'status'                                => ['required'],
            'transaction_date'                      => ['required'],
            'is_set_credit_limit'                   => ['required', 'numeric'],
            'currency_id'                           => ['required', 'numeric'],
            'credit_limit'                          => ['nullable', 'numeric'],
        ];



        if ($this->isMethod('PUT')) {
            $patyId                     = $this->input('party_id');
            $rulesArray['party_id']     = ['required'];
            $rulesArray['mobile']       = ['nullable', 'string', 'max:20', Rule::unique('parties')->where('party_type', $partyType)->ignore($patyId)];
            $rulesArray['phone']       = ['nullable', 'string', 'max:20', Rule::unique('parties')->where('party_type', $partyType)->ignore($patyId)];
            $rulesArray['whatsapp']     = ['nullable', 'string', 'max:20', Rule::unique('parties')->where('party_type', $partyType)->ignore($patyId)];
            $rulesArray['email']        = ['nullable', 'email', 'max:100', Rule::unique('parties')->where('party_type', $partyType)->ignore($patyId)];
        }else{
            $rulesArray['mobile']       = ['nullable', 'string', 'max:20', Rule::unique('parties')->where('party_type', $partyType)];
            $rulesArray['phone']       = ['nullable', 'string', 'max:20', Rule::unique('parties')->where('party_type', $partyType)];
            $rulesArray['whatsapp']     = ['nullable', 'string', 'max:20', Rule::unique('parties')->where('party_type', $partyType)];
            $rulesArray['email']        = ['nullable', 'email', 'email', 'max:255', Rule::unique('parties')->where('party_type', $partyType)];
        }

        return $rulesArray;

    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'default_party' => $this->has('default_party') ? 1 : 0,
        ]);
    }

    public function messages(): array
    {
        $responseMessages = [
            'first_name.required'     => 'A First name should not be empty',
            'last_name.required'     => 'A Last name should not be empty',
            'status.required'   => 'Please Select Status',
        ];

        if ($this->isMethod('PUT')) {
            $responseMessages['id.required']    = 'ID Not found to update record';
        }

        return $responseMessages;
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();
            $data['opening_balance']                = $data['opening_balance']??0;
            $data['credit_limit']                   = $data['credit_limit']??0;
            //$data['is_set_credit_limit']            = $data['credit_limit']==0 ? 0 : 1;
            $this->replace($data);
        });
    }

}
