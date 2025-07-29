@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.final.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-flag-checkered fa-fw" aria-hidden="true"></i>
    {{ trans('installer_messages.final.title') }}
@endsection

@section('container')

	@if(session('message')['dbOutputLog'])
		<p><strong><small>{{ trans('installer_messages.final.migration') }}</small></strong></p>
		<pre><code>{{ session('message')['dbOutputLog'] }}</code></pre>
	@endif

	<p><strong><small>{{ trans('installer_messages.final.console') }}</small></strong></p>
	<pre><code>{{ $finalMessages }}</code></pre>

	<p><strong><small>{{ trans('installer_messages.final.log') }}</small></strong></p>
	<pre><code>{{ $finalStatusMessage }}</code></pre>

	<p><strong><small>{{ trans('installer_messages.final.env') }}</small></strong></p>
	<pre><code>{{ $finalEnvFile }}</code></pre>
	<p><strong><small>Default Credentials:</small></strong></p>
	<table class="cs-table">
			<thead>
				<tr>
					<th>
						{{ __('app.email') }}
					</th>
					<th>
						{{ __('app.password') }}
					</th>

				</tr>
			</thead>
			<tbody>
				<tr>
					<td>admin@example.com</td>
					<td>12345678</td>
				</tr>
			</tbody>

		</table>
    <div class="buttons">
        <a href="{{ url('/') }}" class="button">{{ trans('installer_messages.final.exit') }}</a>
    </div>

@endsection
