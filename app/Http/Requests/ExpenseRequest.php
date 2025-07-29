<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\FormatsDateInputs;

class ExpenseRequest extends FormRequest
{

    use FormatsDateInputs;

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
            'expense_category_id'                   => ['required', 'integer', Rule::exists('expense_categories', 'id')],
            'expense_subcategory_id'                => ['nullable', 'integer', Rule::exists('expense_subcategories', 'id')],
            'expense_date'                          => ['required', 'date'],
            'prefix_code'                           => ['nullable', 'string','max:250'],
            'count_id'                              => ['required', 'numeric'],
            'expense_code'                          => ['required', 'string','max:50'],
            'round_off'                             => ['nullable',Rule::requiredIf( fn () => empty($this->input('round_off'))),'numeric',],
            'grand_total'                           => ['required', 'numeric'],
            'note'                                  => ['nullable', 'string','max:250'],
            'row_count'                             => ['required', 'integer', 'min:1'],
        ];

        if ($this->isMethod('PUT')) {
            $rulesArray['expense_id']          = ['required'];
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
        /**
         * @method formatDateInput
         * Defined in Trait FormatsDateInputs
         * */
        $this->merge([
            'expense_date' => $this->toSystemDateFormat($this->input('expense_date')),
            'expense_code' => $this->getExpenseCode(),
        ]);
    }

    /**
     * Get the expense code by concatenating prefix_code and count_id.
     *
     * @return string
     */
    protected function getExpenseCode()
    {
        $prefixCode = $this->input('prefix_code');
        $countId = $this->input('count_id');

        return $prefixCode . $countId;
    }

    public function messages(): array
    {
        $responseMessages = [
            'row_count.min'     => __('item.please_select_items'),
        ];

        if ($this->isMethod('PUT')) {
            $responseMessages['id.required']    = 'ID Not found to update record';
        }
        return $responseMessages;
    }

}
