<select class="form-select single-select-clear-field" id="role_id" name="role_id" data-placeholder="Choose one thing">
    <option></option>
    @foreach ($roles as $name => $id)
        <option value="{{ $id }}" {{ $selected == $id ? 'selected' : '' }}>{{ $name }}</option>
    @endforeach
</select>
