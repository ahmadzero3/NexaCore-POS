<select class="form-select {{ ($showSelectOptionAll)? 'single-select-clear-field' : '' }}" id="item_category_id" name="item_category_id" data-placeholder="Choose one thing" {{ ($isMultiple) ? 'multiple' : '' }}>
    @if($showSelectOptionAll)
    <option></option>
    @endif
    @foreach ($categories as $category)
        <option value="{{ $category->id }}" {{ $selected == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
    @endforeach
</select>
