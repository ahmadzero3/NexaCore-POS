<!doctype html>
<html class="{{ $themeMode }}" lang="en" dir="{{ $appDirection }}">

<meta name="csrf-token" content="{{ csrf_token() }}">

@include('layouts.head')

<body>
	<!-- Page Loader -->
	@include('layouts.page-loader')

	<!--wrapper-->
	<div class="wrapper">
		@yield('content')
	</div>
	<!--end wrapper-->


	{{-- @include('layouts.search') --}}

	{{-- @include('layouts.switcher') --}}

	@include('layouts.script')

</body>

</html>