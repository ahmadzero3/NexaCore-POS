<label class="form-label {{ $extraClass ?? '' }}" for="{{ $for }}" id="{{ $id }}" data-name="{{ $labelDataName }}">
{!! $name !!}
@if($optionalText)
	<small class="text-muted">({{ __("app.optional") }})</small>
@endif
</label>