<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserRequest extends FormRequest
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
            'last_name'                             => ['required', 'string', 'max:100'],
            'role_id'                               => ['required'],
            'status'                                => ['required', 'max:1'],
            'mylogo'                                => ['nullable','image','mimes:jpeg,png,jpg,gif','max:1024'],
        ];

        if ($this->isMethod('PUT')) {
            $userId                = $this->input('id');
            $rulesArray['id']       = ['required'];
            $rulesArray['username'] = ['required', 'string', 'max:100', Rule::unique('users')->ignore($userId)];
            $rulesArray['email']    = ['required', 'string', 'max:100', Rule::unique('users')->ignore($userId)];
            $rulesArray['mobile']    = ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($userId)];
            if (!empty($this->input('password'))) {
                $rulesArray['password'] = ['required', 'confirmed', Rules\Password::defaults()];
            }
            
        }else{
             $rulesArray['username']    = ['required', 'string', 'max:100', 'unique:users'];
             $rulesArray['email']       = ['required', 'string', 'email', 'max:255', 'unique:'.User::class];
             $rulesArray['password']    = ['required', 'confirmed', Rules\Password::defaults()];
             $rulesArray['mobile']       = ['nullable', 'string', 'max:20', Rule::unique('users')];
        }
        
        return $rulesArray;

    }
    public function messages(): array
    {
        $responseMessages = [
            'first_name.required'     => 'A First name should not be empty',
            'last_name.required'     => 'A Last name should not be empty',
            'status.required'   => 'Please Select Status',
            'status.max'        => 'Status value should be 1 or 0',
        ];

        if ($this->isMethod('PUT')) {
            $responseMessages['id.required']    = 'ID Not found to update record';
            $responseMessages['id.exists']      = 'The selected id is invalid';
        }

        return $responseMessages;
    }
}
