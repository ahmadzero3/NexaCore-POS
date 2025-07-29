<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
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
            'permission_group_id'       => ['required', 'integer'],
            'display_name'              => ['required', 'string', 'max:100'],
            'status'                    => ['required', 'max:1'],
        ];

        if ($this->isMethod('PUT')) {
            $groupId            = $this->input('id');
            $rulesArray['id']   = ['required'];
            $rulesArray['name'] = ['required', 'string', 'max:100', Rule::unique('permissions')->ignore($groupId)];

        }else{
            $rulesArray['name'] = ['required', 'string', 'max:100','unique:permissions'];
        }

        return $rulesArray;

    }

    public function messages(): array
    {
        $responseMessages = [
            'permission_group_id.required'     => 'Please Select Group Name',
            'permission_group_id.integer'     => 'Group Name must be a Integer Type',
            'name.required'     => 'A Group name should not be empty',
            'name.string'       => 'A Group name should be string type',
            'name.max'          => 'A Group name should not cross 100 characters',

            'display_name.required'     => 'A Display name should not be empty',
            'display_name.string'       => 'A Display name should be string type',
            'display_name.max'          => 'A Display name should not cross 100 characters',

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
