<select class="form-select" name="{{ $dropdownName }}">
    @if($showSelectOptionAll)
    <option value="">All</option>
    @endif
    @foreach ($dropdownData as $option)
        <option value="{{ $option['id'] }}" {{ strtoupper($selected) == strtoupper($option['id']) ? 'selected' : '' }}>{{ $option['name'] }}</option>
    @endforeach
</select>