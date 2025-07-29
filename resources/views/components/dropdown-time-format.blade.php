<select class="form-select single-select-clear-field" name="time_format" data-placeholder="Choose one thing">
    @foreach ($dropdownData as $key => $value)
        <option value="{{ $key }}" {{ $selected == $key ? 'selected' : '' }}>{{ $value }}</option>
    @endforeach
</select>