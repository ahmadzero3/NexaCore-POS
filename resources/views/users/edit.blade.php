@extends('layouts.app')
@section('title', __('user.update_user'))

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <x-breadcrumb :langArray="[
                                            'user.users',
                                            'user.users_list',
                                            'user.update_user',
                                        ]"/>
                <div class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card">
                            <div class="card-header px-4 py-3">
                                <h5 class="mb-0">{{ __('user.user_details') }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <form class="row g-3 needs-validation" id="userForm" action="{{ route('user.update') }}" enctype="multipart/form-data">
                                    {{-- CSRF Protection --}}
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name='id' value="{{ $user->id }}" />
                                    <input type="hidden" id="base_url" value="{{ url('/') }}">
                                    <div class="col-md-6">
                                        <x-label for="user_picture" name="{{ __('user.user_picture') }}" />
                                        <x-browse-image 
                                                        src="{{ url('/users/getimage/' . $user->avatar) }}" 
                                                        name='avatar' 
                                                        imageid='uploaded-image-1' 
                                                        inputBoxClass='input-box-class-1' 
                                                        imageResetClass='image-reset-class-1' 
                                                        />
                                    </div>
                                    <br>
                                    <div class="col-md-6">
                                        <x-label for="first_name" name="{{ __('user.first_name') }}" />
                                        <x-input type="text" name="first_name" :required="true" value="{{ $user->first_name }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="last_name" name="{{ __('user.last_name') }}" />
                                        <x-input type="text" name="last_name" :required="true" value="{{ $user->last_name }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="username" name="{{ __('user.username') }}" />
                                        <x-input type="text" name="username" :required="true" value="{{ $user->username }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="email" name="{{ __('user.email') }}" />
                                        <x-input type="email" name="email" :required="true" value="{{ $user->email }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="mobile" name="{{ __('app.mobile') }}" />
                                        <x-input type="number" name="mobile" :required="false" value="{{ $user->mobile }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="textRows='10'/>" name="{{ __('app.role') }}" />
                                        <x-drop-down-roles selected="{{ $user->role_id }}" />
                                        <div class="valid-feedback"></div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <x-label for="password" name="{{ __('user.password') }}" :optionalText="true" />
                                        <x-input type="password" name="password" :required="false" value=""/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="password_confirmation" name="{{ __('user.confirm_password') }}" :optionalText="true" />
                                        <x-input type="password" name="password_confirmation" :required="false" value=""/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="status" name="{{ __('app.status') }}" />
                                        <x-dropdown-status selected="{{ $user->status }}" dropdownName='status'/>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_allowed_all_warehouses" name="is_allowed_all_warehouses" {{ ($user->is_allowed_all_warehouses) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_allowed_all_warehouses">
                                              {{ __('warehouse.allow_all_warehouses') }}
                                            </label>
                                          </div>
                                    </div>
                                    {{-- Initially Hidden --}}
                                    <div class="warehouse-div">
                                        <div class="col-md-6">
                                            <x-label for="warehouse_ids[]" name="{{ __('warehouse.warehouse') }}" />
                                            <x-dropdown-warehouse selected="{{ $user->userWarehouses->isNotEmpty() ? $user->userWarehouses->pluck('warehouse_id')->implode(',') : 'No warehouses assigned' }}" dropdownName='warehouse_ids[]' :multiSelect='true'/>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-12">
                                        <div class="d-md-flex d-grid align-items-center gap-3">
                                            <x-button type="submit" class="primary px-4" text="{{ __('app.submit') }}" />
                                            <x-anchor-tag href="{{ route('dashboard') }}" text="{{ __('app.close') }}" class="btn btn-light px-4" />
                                        </div>
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
<script src="{{ versionedAsset('custom/js/user/user.js') }}"></script>
@endsection
