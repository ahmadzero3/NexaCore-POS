	<!-- Bootstrap JS -->
	<script src="{{ versionedAsset('assets/js/bootstrap.bundle.min.js') }}"></script>
	<!--plugins-->
	<script src="{{ versionedAsset('assets/js/jquery.min.js') }}"></script>
	<script src="{{ versionedAsset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
	<script src="{{ versionedAsset('assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
	<script src="{{ versionedAsset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
	<script src="{{ versionedAsset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ versionedAsset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
	<script src="{{ versionedAsset('assets/plugins/chartjs/js/chart.js') }}"></script>
    <!-- select2 -->
    <script src="{{ versionedAsset('custom/libraries/select2-theme/select2-4.1.0-rc.0/dist/js/select2.min.js') }}"></script>
    <!-- Sweetalert -->
    <script src="{{ versionedAsset('custom/libraries/sweetalert/sweetalert.min.js') }}"></script>
	<!-- Notification Toast -->
    <script src="{{ versionedAsset('custom/libraries/iziToast/dist/js/iziToast.min.js') }}"></script>
    <!-- Date & Time Picker -->
    <script src="{{ versionedAsset('custom/libraries/flatpickr/flatpickr.min.js') }}"></script>
    <!-- Autocomplete -->
	<script src="{{ versionedAsset('assets/plugins/jquery-ui/jquery-ui.js') }}"></script>
    <!-- Number Library -->
    <script src="{{ versionedAsset('custom/libraries/numbro/numbro.min.js') }}"></script>
    <!-- All libraries Settings -->
    <script src="{{ versionedAsset('custom/js/plugin-settings.js') }}"></script>

    <script type="text/javascript">
		/*Configure the Application Date Format*/
		var appCompanyName = "{{ app('company')['name'] }}";
		var appTaxType = "{{ app('company')['tax_type'] }}";
		var dateFormatOfApp = "{{ app('company')['date_format'] }}";
		var numberPrecision = {{ app('company')['number_precision'] }};
		var quantityPrecision = {{ app('company')['quantity_precision'] }};
		var itemSettings = {
			show_sku : {{ app('company')['show_sku'] }},
			show_mrp : {{ app('company')['show_mrp'] }},
			show_discount : {{ app('company')['show_discount'] }},
			enable_serial_tracking : {{ app('company')['enable_serial_tracking'] }},
			enable_batch_tracking : {{ app('company')['enable_batch_tracking'] }},
			enable_mfg_date : {{ app('company')['enable_mfg_date'] }},
			enable_exp_date : {{ app('company')['enable_exp_date'] }},
			enable_color : {{ app('company')['enable_color'] }},
			enable_size : {{ app('company')['enable_size'] }},
			enable_model : {{ app('company')['enable_model'] }},
		};
		var baseURL = '{{ url('') }}';
        var _csrf_token = '{{ csrf_token() }}';
        var allowUserToPurchaseDiscount = {{ auth()->check() && auth()->user()->can('general.permission.to.apply.discount.to.purchase') ? 1 : 0 }};
        var allowUserToSaleDiscount = {{ auth()->check() && auth()->user()->can('general.permission.to.apply.discount.to.sale') ? 1 : 0; }};
        var isEnableSecondaryCurrency = {{ auth()->check() && app('company')['is_enable_secondary_currency'] ? 1 : 0; }};
        var isEnableCarrierCharge = {{ auth()->check() && app('company')['is_enable_carrier_charge'] ? 1 : 0; }};
	</script>
    <!-- Clear Cache -->
    <script src="{{ versionedAsset('custom/js/cache.js') }}"></script>

	@yield('js')
	<!--app JS-->
	@if($appDirection=='ltr')
		<script src="{{ versionedAsset('assets/js/app.js') }}"></script>
	@else
		<script src="{{ versionedAsset('assets/rtl/js/app.js') }}"></script>
	@endif

	<!-- Custom Library -->
	<script src="{{ versionedAsset('custom/js/custom.js') }}"></script>
