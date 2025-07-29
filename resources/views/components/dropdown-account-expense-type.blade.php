<select class="form-select single-select-field" id="account_group_id" name="account_group_id" data-placeholder="Choose one thing">
    @foreach ($accountGroups as $group)
        <option value="{{ $group->id }}" {{ $selected == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
    @endforeach
</select>
