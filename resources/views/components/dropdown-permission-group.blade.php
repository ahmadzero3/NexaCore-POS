<select class="form-select single-select-clear-field" id="permission_group_id" name="permission_group_id" data-placeholder="Choose one thing">
    <option></option>
    @foreach ($groups as $name => $id)
        <option value="{{ $id }}" {{ $selected == $id ? 'selected' : '' }}>{{ $name }}</option>
    @endforeach
</select>
