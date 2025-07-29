@extends('layouts.app')
@section('title', __('app.roles_list'))

@section('css')
<link href="{{ versionedAsset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
@endsection
		@section('content')
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">

                    <x-breadcrumb :langArray="[
											'user.users',
											'app.permissions',
											'app.roles_list',
										]"/>

                    <div class="card">

					<div class="card-header px-4 py-3 d-flex justify-content-between">
					    <!-- Other content on the left side -->
					    <div>
					    	<h5 class="mb-0 text-uppercase">{{ __('app.roles_list') }}</h5>
					    </div>

					    @can('role.create')
					    <!-- Button pushed to the right side -->
					    <x-anchor-tag href="{{ route('role.create') }}" text="{{ __('app.create_role') }}" class="btn btn-primary px-5" />
					    @endcan
					</div>

					<div class="card-body">
                        <form class="row g-3 needs-validation" id="datatableForm" action="{{ route('role.delete') }}" enctype="multipart/form-data">
                            {{-- CSRF Protection --}}
                            @csrf
                            @method('POST')
							<div class="table-responsive">
								<table class="table table-striped table-bordered border w-100" id="datatable">
									<thead>
										<tr>
											<th class="d-none"><!-- Which Stores ID & it is used for sorting --></th>
	                                        <th><input class="form-check-input row-select" type="checkbox"></th>
											<th>{{ __('app.role_name') }}</th>
											<th>{{ __('app.status') }}</th>
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
		@endsection
@section('js')
<script src="{{ versionedAsset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ versionedAsset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ versionedAsset('custom/js/common/common.js') }}"></script>
<script src="{{ versionedAsset('custom/js/user/permission/role-list.js') }}"></script>
@endsection
