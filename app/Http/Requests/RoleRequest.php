<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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

        $rulesArray = [
            'status'                    => ['required', 'max:1'],
        ];

        if ($this->isMethod('PUT')) {
            $groupId            = $this->input('id');
            $rulesArray['id']   = ['required'];
            $rulesArray['name'] = ['required', 'string', 'max:100', Rule::unique('roles')->ignore($groupId)];
        }else{
            $rulesArray['name'] = ['required', 'string', 'max:100', 'unique:roles'];
        }
        
        return $rulesArray;

    }
    public function messages(): array
    {
        $responseMessages = [
            'name.required'     => 'A Group name should not be empty',
            'name.string'       => 'A Group name should be string type',
            'name.max'          => 'A Group name should not cross 100 characters',
            'name.unique'       => 'The Group Name is already taken',
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
