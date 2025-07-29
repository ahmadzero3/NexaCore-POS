<select class="form-select single-select-clear-field" id="expense_subcategory_id" name="expense_subcategory_id" data-placeholder="Choose one thing">
    <option></option>
    @foreach ($subcategories as $category)
        <option value="{{ $category->id }}" {{ $selected == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
    @endforeach
</select>
