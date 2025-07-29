<select class="form-select single-select-clear-field" id="{{ $dropdownName }}" name="{{ $dropdownName }}" data-placeholder="Choose one thing">
    <option></option>
    @foreach ($states as $data)
        <option value="{{ $data['id'] }}" {{ $selected == $data['id'] ? 'selected' : '' }}>{{ $data['name'] }}</option>
    @endforeach
</select>
