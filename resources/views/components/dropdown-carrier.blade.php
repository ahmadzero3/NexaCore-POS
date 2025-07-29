<select class="form-select {{ ($showSelectOptionAll)? 'single-select-clear-field' : '' }}" id="{{ $name }}" name="{{ $name }}" data-placeholder="Choose one thing" >
    @if($showSelectOptionAll)
    <option></option>
    @endif
    @foreach ($carriers as $carrier)
        <option value="{{ $carrier->id }}" {{ $selected == $carrier->id ? 'selected' : '' }}>{{ $carrier->name }}</option>
    @endforeach
</select>
