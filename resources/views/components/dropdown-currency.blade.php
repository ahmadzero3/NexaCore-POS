<select class="form-select" id="{{ $name }}" name="{{ $name }}" data-placeholder="Choose one thing" >
    @foreach ($currencies as $currency)
        <option value="{{ $currency->id }}" data-symbol="{{ $currency->symbol }}" data-code="{{ $currency->code }}" data-name="{{ $currency->name }}" data-exchange-rate="{{ $currency->exchange_rate }}" {{ $selected == $currency->id ? 'selected' : '' }}>{{ $currency->code.'-'.$currency->name.' ('.$currency->symbol.')' }}</option>
    @endforeach
</select>
