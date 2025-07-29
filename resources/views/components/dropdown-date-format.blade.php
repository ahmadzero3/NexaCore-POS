<select class="form-select single-select-clear-field" name="date_format" data-placeholder="Choose one thing">
    @foreach ($dropdownData as $option)
        <option value="{{ $option }}" {{ $selected == $option ? 'selected' : '' }}>{{ $option }}</option>
    @endforeach
</select>