<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PasswordRequest extends FormRequest
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
    public function rules() : array
    {
        return [
            'old_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::user()->password)) {
                        $fail('The old password is incorrect.');
                    }
                },
            ],
            'password' => 'required|min:8|confirmed',
        ];
    }

public function messages(): array
{
    $responseMessages = [
        'old_password.required' => 'The old password is required.',
        'old_password.*' => 'The old password is incorrect.',
        'password.required' => 'The new password is required.',
        'password.confirmed' => 'The new password and confirmation password do not match.',
    ];

    return $responseMessages;
}

}
