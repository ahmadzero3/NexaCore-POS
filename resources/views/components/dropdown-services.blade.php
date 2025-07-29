<select class="form-select single-select-clear-field" name="service_id" id="service_id" data-placeholder="Choose one thing">
    <option></option>
    @foreach ($services as $name => $id)
        <option value="{{ $id }}" {{ $selected == $id ? 'selected' : '' }}>{{ $name }}</option>
    @endforeach
</select>
