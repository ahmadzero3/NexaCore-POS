@if($multiSelect)
    @php
        $selectedIdArray = explode(',', $selected);
    @endphp
    <select class="form-select multiple-select-clear-field" id="{{ $dropdownName }}" name="{{ $dropdownName }}" data-placeholder="Choose one thing" multiple>
        @foreach ($warehouses as $data)
            <option value="{{ $data['id'] }}" {{ in_array($data['id'], $selectedIdArray) ? 'selected' : '' }}>{{ $data['name'] }}</option>
        @endforeach
    </select>
@else
    <select class="form-select {{ $enableToSelect ? 'single-select-clear-field' : '' }}" id="{{ $dropdownName }}" name="{{ $dropdownName }}" data-placeholder="Choose one thing">
        @if($showSelectOptionAll)
            <option value="">All</option>
        @elseif($enableToSelect)
            <option value=""></option>
        @endif
        @foreach ($warehouses as $data)
            <option value="{{ $data['id'] }}" {{ $selected == $data['id'] ? 'selected' : '' }}>{{ $data['name'] }}</option>
        @endforeach
    </select>
@endif




