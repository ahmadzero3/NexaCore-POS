<select class="form-select " id="{{ $dropdownName }}" name="{{ $dropdownName }}" data-placeholder="Choose one thing">
    @foreach ($units as $data)
        <option value="{{ $data['id'] }}" {{ $selected == $data['id'] ? 'selected' : '' }}>{{ $data['name'] }}({{ $data['short_code'] }})</option>
    @endforeach
</select>
