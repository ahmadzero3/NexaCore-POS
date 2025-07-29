<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogoRequest extends FormRequest
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
            'colored_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'light_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
    public function messages()
    {
        return [
            'colored_logo.image' => 'The colored logo must be an image.',
            'colored_logo.mimes' => 'The colored logo must be a JPEG, PNG, JPG, GIF, or SVG file.',
            'colored_logo.max' => 'The colored logo should not exceed 2048 kilobytes.',
            'light_logo.required' => 'The Light logo is required.',
            'light_logo.image' => 'The Light logo must be an image.',
            'light_logo.mimes' => 'The Light logo must be a JPEG, PNG, JPG, GIF, or SVG file.',
            'light_logo.max' => 'The Light logo should not exceed 2048 kilobytes.',
        ];
    }
}   
