@extends('layouts.app')
@section('title', $lang['party_list'])

@section('css')
<link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
@endsection
		@section('content')
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
					<x-breadcrumb :langArray="[
											'party.contacts',
											$lang['party_list'],
										]"/>

                    <div class="card">

					<div class="card-header px-4 py-3 d-flex justify-content-between align-items-center">
					    <!-- Other content on the left side -->
					    <div>
					    	<h5 class="mb-0 text-uppercase">{{ $lang['party_list'] }}</h5>
					    </div>
					    <div class="d-flex gap-2">
						    @can('import.party')
						    <!-- Button pushed to the right side -->
						    <x-anchor-tag href="{{ route('import.party') }}" text="{{ __('app.import') }}" class="btn btn-outline-primary px-5" />
						    @endcan

						    @can($lang['party_type'].'.create')
						    <!-- Button pushed to the right side -->
						    <x-anchor-tag href="{{ route('party.create', ['partyType' => $lang['party_type']]) }}" text="{{ $lang['party_create'] }}" class="btn btn-primary px-5" />
						    @endcan
						</div>
					</div>
					<div class="card-body">

						@if($lang['party_type'] == 'customer')
						<div class="row g-3">
							<div class="col-md-3">
                                <x-label for="customer_type" name="{{ __('customer.type') }}" />
                                <select class="form-select single-select-clear-field" id="customer_type" name="customer_type" data-placeholder="Choose one thing">
								    <option></option>
								    <option value='1'>{{ __('party.wholesalers') }}</option>
								    <option value='0'>{{ __('party.retailers') }}</option>
								</select>
                            </div>
                        </div>
                        @endif

                        <form class="row g-3 needs-validation" id="datatableForm" action="{{ route('party.delete') }}" enctype="multipart/form-data">
                            {{-- CSRF Protection --}}
                            @csrf
                            @method('POST')
                            <input type="hidden" id="base_url" value="{{ url('/') }}">
                            <input type="hidden" name="party_type" value="{{ $lang['party_type'] }}">
                            <div class="table-responsive">
								<table class="table table-striped table-bordered border w-100" id="datatable">
									<thead>
										<tr>
											<th class="d-none"><!-- Which Stores ID & it is used for sorting --></th>
	                                        <th><input class="form-check-input row-select" type="checkbox"></th>
											<th>{{ __('app.name') }}</th>
											<th>{{ __('app.mobile') }}</th>
											<th>{{ __('app.whatsapp') }}</th>
											<th>{{ __('app.email') }}</th>
											<th>{{ __('app.balance') }}</th>
											<th>{{ __('app.balance_type') }}</th>
											<th>{{ __('app.status') }}</th>
											<th>{{ __('app.created_by') }}</th>
											<th>{{ __('app.created_at') }}</th>
											<th>{{ __('app.action') }}</th>
										</tr>
									</thead>
								</table>
							</div>
                        </form>
						
					</div>
				</div>
					</div>
				</div>
				<!--end row-->
			</div>
		</div>

		@include("modals.party.payment-history")

		@endsection
@section('js')
<script src="{{ versionedAsset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ versionedAsset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ versionedAsset('custom/js/common/common.js') }}"></script>
<script src="{{ versionedAsset('custom/js/party/party-list.js') }}"></script>
@endsection
