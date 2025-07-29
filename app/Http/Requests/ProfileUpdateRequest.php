<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {

        $rulesArray = [
            'first_name'          => ['required', 'string', 'max:100'],
            'last_name'           => ['required', 'string', 'max:100'],
            'username'            => ['required', 'string', 'max:100', Rule::unique('users')->ignore(auth()->user()->id)],
            'mylogo'              => ['nullable','image','mimes:jpeg,png,jpg,gif','max:1024'],
            'email'               => ['email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'mobile'               => ['nullable', 'max:20', Rule::unique(User::class)->ignore($this->user()->id)],
        ];

        return $rulesArray;

    }
    public function messages(): array
    {
        $responseMessages = [
            'id.required'           => 'ID Not found to update record',
            'id.exists'             => 'The selected id is invalid',
            'first_name.required'   => 'A First name should not be empty',
            'last_name.required'    => 'A Last name should not be empty',
        ];

        return $responseMessages;
    }
}
