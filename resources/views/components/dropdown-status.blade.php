<select class="form-select" name="{{ $dropdownName }}">
    @foreach ($dropdownData as $option)
        <option value="{{ $option['status'] }}" {{ $selected == $option['status'] ? 'selected' : '' }}>{{ $option['name'] }}</option>
    @endforeach
</select>