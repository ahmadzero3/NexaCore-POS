<select class="form-select single-select-clear-field" name="{{ $dropdownName }}" data-placeholder="Choose one thing">
    @foreach ($dropdownData as $option)
        <option value="{{ $option['value'] }}" {{ $selected == $option['value'] ? 'selected' : '' }}>{{ $option['name'] }}</option>
    @endforeach
</select>