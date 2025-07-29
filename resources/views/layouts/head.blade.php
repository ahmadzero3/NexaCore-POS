<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--favicon-->
	<link rel="icon" href='{{ url("/fevicon/" . $fevicon) }}' type="image/png" />
	<!--plugins-->
	<link href="{{ versionedAsset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet" />
	<link href="{{ versionedAsset('assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
	<link href="{{ versionedAsset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
	<link href="{{ versionedAsset('assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
	<!-- Select2 -->
	<link rel="stylesheet" href="{{ versionedAsset('custom/libraries/select2-theme/select2-4.1.0-rc.0/dist/css/select2.min.css') }}">
	<link rel="stylesheet" href="{{ versionedAsset('custom/libraries/select2-theme/select2-bootstrap-5-theme-1.3.0/dist/select2-bootstrap-5-theme.min.css') }}">
	<!-- loader-->
	<script src="{{ versionedAsset('assets/js/pace.min.js') }}"></script>
	<link href="{{ versionedAsset('assets/css/pace.min.css') }}" rel="stylesheet" />
	<!-- Bootstrap CSS -->
	@if($appDirection=='ltr')
	<link href="{{ versionedAsset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ versionedAsset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
	<link href="{{ versionedAsset('assets/css/app.css') }}" rel="stylesheet">
	@else
	<link href="{{ versionedAsset('assets/rtl/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ versionedAsset('assets/rtl/css/bootstrap-extended.css') }}" rel="stylesheet">
	<link href="{{ versionedAsset('assets/rtl/css/app.css') }}" rel="stylesheet">
	@endif
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
	<link href="{{ versionedAsset('assets/css/icons.css') }}" rel="stylesheet">
	<!-- Notification Toast -->
	<link rel="stylesheet" href="{{ versionedAsset('custom/libraries/iziToast/dist/css/iziToast.min.css') }}">
	<!-- Date & Time Picker -->
	<link rel="stylesheet" href="{{ versionedAsset('custom/libraries/flatpickr/flatpickr.min.css') }}">
	<!-- Autocomplete -->
	<link href="{{ versionedAsset('assets/plugins/jquery-ui/jquery-ui.css') }}" rel="stylesheet" />
	<!-- Theme Style CSS -->
	<link rel="stylesheet" href="{{ versionedAsset('assets/css/dark-theme.css') }}" />
	<link rel="stylesheet" href="{{ versionedAsset('assets/css/semi-dark.css') }}" />
	<link rel="stylesheet" href="{{ versionedAsset('assets/css/header-colors.css') }}" />
	<!-- BoxIcon CSS -->
	<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
	<!-- Flags CSS -->
	<link rel="stylesheet" href="{{ versionedAsset('custom/libraries/flag-icons-main/css/flag-icons.min.css') }}">
	<!-- Custom CSS -->
	<link rel="stylesheet" href="{{ versionedAsset('custom/css/custom.css') }}">
	@yield('css')
	<title>@yield('title', app('company')['name'])</title>

	<style>
  .card-header {
    background-color: {{ $custom['card_header_color'] ?? '#f5f5f5' }};
  }
  .card {
    border: 1px solid {{ $custom['card_border_color'] ?? '#dddddd' }};
  }
  h1, h2, h3, h4, h5, h6 .heading {
  color: {{ $custom['heading_color'] ?? '#333333' }} !important;
  }
</style>
</head>