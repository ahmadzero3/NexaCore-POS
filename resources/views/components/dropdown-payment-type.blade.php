<select class="form-select single-select-clear-field" id="{{ $paymentTypeName }}" name="{{ $paymentTypeName }}" data-placeholder="Choose one thing">
    <option></option>
    @foreach ($paymentTypes as $paymentType)
        <option value="{{ $paymentType->id }}" {{ $selected == $paymentType->id ? 'selected' : '' }}>{{ $paymentType->name }}</option>
    @endforeach
</select>
