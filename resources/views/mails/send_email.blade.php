@component('mail::message')
<p>
    {!! $content !!}
</p>
<br>

<br>
<br>
{{ __('app.thank_you') }}
<br>
{{ app('company')['name'] }}
@endcomponent
