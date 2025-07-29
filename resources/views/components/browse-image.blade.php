<div class="d-flex align-items-start align-items-sm-center gap-4">
  <img src="{{ $src }}" alt="image" class="d-block rounded border" height="100" width="100" id="{{ $imageid }}">
  <div class="button-wrapper">
    <label for="{{ $name }}" class="btn btn-outline-primary px-3" tabindex="0">
      {{ __('app.browse') }}
      <input type="file" id="{{ $name }}" name="{{ $name }}" class="{{ $inputBoxClass }}" hidden="" accept="image/png, image/jpeg">
    </label>
    <button type="button" class="btn btn-outline-secondary px-3 {{ $imageResetClass }}">
      {{ __('app.reset') }}
    </button>
    <p class="text-muted mb-0">{{ __('app.allowsed_size_of_image') }}</p>
  </div>
</div>