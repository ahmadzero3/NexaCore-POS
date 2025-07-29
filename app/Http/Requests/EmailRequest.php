<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailRequest extends FormRequest
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
        return [
            'email'                    =>  ['required', 'email', 'max:250'],
            'subject'                  =>  ['required', 'string', 'max:250'],
            'content'                  =>  ['required', 'string', 'max:2000'],
            'attachment'                => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,txt'],
        ];
    }
}
