<!doctype html>
<html class="{{ $themeMode }}" lang="en" dir="{{ $appDirection }}">

<meta name="csrf-token" content="{{ csrf_token() }}">

@include('layouts.head')

<body>
  <!-- Page Loader -->
  @include('layouts.page-loader')

  <!--wrapper-->
  <div class="wrapper">
    @include('layouts.navigation')
    @include('layouts.header')
    @yield('content')
    @include('layouts.footer')
  </div>
  <!--end wrapper-->

  @include('layouts.script')
  <script src="{{ versionedAsset('custom/js/header.js') }}"></script>
</body>

</html>