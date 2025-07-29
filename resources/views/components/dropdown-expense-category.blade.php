<select class="form-select single-select-clear-field" id="expense_category_id" name="expense_category_id" data-placeholder="Choose one thing">
    <option></option>
    @foreach ($categories as $category)
        <option value="{{ $category->id }}" {{ $selected == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
    @endforeach
</select>
