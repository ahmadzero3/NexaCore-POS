<select class="form-select single-select-clear-field" {{ $disabled ? 'disabled' : '' }} id="user_id" name="user_id" data-placeholder="Choose one thing">
    <option></option>
    @foreach ($users as $user)
        <option value="{{ $user->id }}" {{ $selected == $user->id ? 'selected' : '' }}>
            @if($showOnlyUsername)
                {{ $user->username }}
                @else
                {{ $user->first_name .' '.$user->last_name }}
            @endif
        </option>
    @endforeach
</select>
