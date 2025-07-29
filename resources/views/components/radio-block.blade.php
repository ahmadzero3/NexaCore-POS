<div class="form-check {{ $parentDivClass }}">
    <input class="form-check-input" type="{{ $boxType }}" id="{{ $id }}" name="{{ $boxName }}" value="{{ $value ?? '' }}" {{ $checked ? 'checked' : '' }}>
    <label class="form-check-label" for="{{ $id }}">{{ $text }}</label>
</div>