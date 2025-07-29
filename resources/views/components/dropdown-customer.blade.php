<select class="form-select single-select-clear-field" {{ $disabled ? 'disabled' : '' }} id="party_id" name="party_id" data-placeholder="Choose one thing">
    <option></option>
    @foreach ($customers as $customer)
        <option value="{{ $customer->id }}" {{ $selected == $customer->id ? 'selected' : '' }}>{{ $customer->first_name .' '.$customer->last_name }}</option>
    @endforeach
</select>
