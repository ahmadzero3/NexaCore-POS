<select class="form-select single-select-clear-field" id="language_id" name="language_id" data-placeholder="Choose one thing">
    <option></option>
    @foreach ($languages as $language)
        <option value="{{ $language->id }}" {{ $selected == $language->id ? 'selected' : '' }}>{{ $language->name }}</option>
    @endforeach
</select>
