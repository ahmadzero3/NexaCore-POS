@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
{{ app('company')['name'] }}
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
