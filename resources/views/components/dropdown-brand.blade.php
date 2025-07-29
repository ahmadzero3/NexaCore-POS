<select class="form-select {{ ($showSelectOptionAll)? 'single-select-clear-field' : '' }}" id="{{ $name }}" name="{{ $name }}" data-placeholder="Choose one thing" >
    @if($showSelectOptionAll)
    <option></option>
    @endif
    @foreach ($categories as $category)
        <option value="{{ $category->id }}" {{ $selected == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
    @endforeach
</select>
