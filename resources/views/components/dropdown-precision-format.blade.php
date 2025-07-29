<select class="form-select" name="{{ $selectionBoxName }}" data-placeholder="Choose one thing">
    @foreach ($dropdownData as $value)
        <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $value }}</option>
    @endforeach
</select>