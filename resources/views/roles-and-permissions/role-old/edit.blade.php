@extends('layouts.app')

		@section('content')
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<x-breadcrumb :langArray="[
											'user.users',
											'app.permissions',
											'app.edit_role',
										]"/>
				<div class="row">
					<div class="col-12 col-lg-12">

						{{-- Form: Start --}}
						<form class=" needs-validation" id="roleForm" action="{{ route('role.update') }}" enctype="multipart/form-data">
                        {{-- CSRF Protection --}}
                        @csrf
                        @method('PUT')

                        <input type="hidden" name='id' value="{{ $role->id }}" />

                        <div class="card">

                            <div class="card-header px-4 py-3">
                                <h5 class="mb-0">{{ __('app.general') }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <x-label for="name" name="{{ __('app.role_name') }}" />
                                        <x-input type="text" name="name" :required="true" value="{{ $role->name }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="status" name="{{ __('app.status') }}" />
                                        <x-dropdown-status selected="{{ $role->status }}" dropdownName='status'/>
                                    </div>
                                </div>

                            </div>

                            <div class="card-header px-4 py-3">
                                <h5 class="mb-0">{{ __('app.permissions') }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <table class="table table-bordered mb-0">
											<thead>
												<tr>
													<th scope="col">{{ __('app.group') }}</th>
													<th scope="col">
														<input class="form-check-input" type="checkbox" id="select_all">
														<label for="select_all">{{ __('app.select_all') }}</label>
													</th>
													<th scope="col">{{ __('app.permissions') }}</th>
												</tr>
											</thead>
											<tbody>
												<tr>

														@foreach ($roleAndPermission->unique('group_name') as $uniqueGroupName)

																@php
																	$groupId = $uniqueGroupName->permission_group_id;

																	$groupSelect = "group_".$groupId;

																	$permissionSelect = $groupSelect."_p";

																@endphp

														  		<tr>
														  			<td>{{ $uniqueGroupName->group_name }}</td>

														  			<td>
																		<input class="form-check-input row-select" type="checkbox" id="{{ $groupSelect }}">
																		<label for="{{ $groupSelect }}">{{ __('app.select_all') }}</label>
																	</td>

																	<td>

																		@php
																			$filteredData = $roleAndPermission->sortBy('group_name')->where('group_name', $uniqueGroupName->group_name);
																		@endphp

										            					@foreach($filteredData as $parmissionData)
										            							@php
										            								$permissionRecordId = $parmissionData->id;
										            								$permissionName = $parmissionData->name;
										            								$permissionId = "permission_".$permissionRecordId;

										            								$checked = ($allocatedPermissions->contains($permissionRecordId)) ? 'checked' : '';
										            							@endphp
										            						    <input class="form-check-input {{ $permissionSelect }}" {{ $checked }} type="checkbox" name="permission[{{ $permissionName }}]" id="{{ $permissionId }}">
																				<label for="{{ $permissionId }}">{{ $parmissionData->display_name }}</label>
																				<br>
										            					@endforeach

														  			</td>
														  		</tr>

														@endforeach



												</tr>
											</tbody>
										</table>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">

                                    <div class="col-md-12">
                                        <div class="d-md-flex d-grid align-items-center gap-3">
                                            <x-button type="submit" class="primary px-4" text="{{ __('app.submit') }}" />
                                            <x-anchor-tag href="{{ route('dashboard') }}" text="{{ __('app.close') }}" class="btn btn-light px-4" />
                                        </div>
                                    </div>


                            </div>


                        </div>{{-- card --}}

                        </form>
                        {{-- Form: End --}}


					</div>
				</div>
				<!--end row-->
			</div>
		</div>
		@endsection

        @section('js')
        <script src="{{ versionedAsset('custom/js/user/permission/role.js') }}"></script>
        @endsection
