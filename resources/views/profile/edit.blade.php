@extends('layouts.app')
@section('title', __('user.update_profile'))

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">

                <x-breadcrumb :langArray="[
                                            'user.users',
                                            'user.update_profile',
                                        ]"/>

                <div class="row">
                    <div class="col-12 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="my-3">{{ __('app.options') }}</h5>
                                <div class="fm-menu">
                                    <div class="list-group list-group-flush">
                                        <a href="javascript:;" class="list-group-item py-1 active text-white show_profile"><i class='bx bx-user me-2'></i><span>{{ __('user.profile') }}</span></a>
                                        <a href="javascript:;" class="list-group-item py-1 show_password"><i class='bx bx-lock-open-alt me-2'></i><span>{{ __('app.change_password') }}</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-9">
                        <!--Tab: Profile -->
                        <div class="card profile_tab">
                            <div class="card-header px-4 py-3">
                                <h5 class="mb-0">{{ __('user.profile') }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <form class="row g-3 needs-validation" id="profileForm" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
                                    {{-- CSRF Protection --}}
                                    @csrf
                                    @method('PUT')
                                    <div class="col-md-12">
                                        <x-label for="profile_picture" name="{{ __('user.profile_picture') }}" />
                                        <x-browse-image
                                                        src="{{ url('/users/getimage/' . $user->avatar)}}"
                                                        name='avatar'
                                                        imageid='uploaded-image-1'
                                                        inputBoxClass='input-box-class-1'
                                                        imageResetClass='image-reset-class-1'
                                                        />
                                    </div>
                                    <div class="col-md-12">
                                        <x-label for="first_name" name="{{ __('user.first_name') }}" />
                                        <x-input type="text" name="first_name" :required="true" value="{{ $user->first_name }}"/>
                                    </div>
                                    <div class="col-md-12">
                                        <x-label for="last_name" name="{{ __('user.last_name') }}" />
                                        <x-input type="text" name="last_name" :required="true" value="{{ $user->last_name }}"/>
                                    </div>
                                    <div class="col-md-12">
                                        <x-label for="username" name="{{ __('user.username') }}" />
                                        <x-input type="text" name="username" :required="true" value="{{ $user->username }}"/>
                                    </div>
                                    <div class="col-md-12">
                                        <x-label for="email" name="{{ __('user.email') }}" />
                                        <x-input type="email" name="email" :required="true" value="{{ $user->email }}"/>
                                    </div>
                                    <div class="col-md-12">
                                        <x-label for="mobile" name="{{ __('app.mobile') }}" />
                                        <x-input type="number" name="mobile" :required="false" value="{{ $user->mobile }}"/>
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
                        <!--Tab End: Profile -->
                        <!--Tab: Change Password-->
                        <div class="card password_tab">
                            <div class="card-header px-4 py-3">
                                <h5 class="mb-0">{{ __('app.change_password') }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <form class="row g-3 needs-validation" id="passwordForm" action="{{ route('user.profile.password') }}" enctype="multipart/form-data">
                                    {{-- CSRF Protection --}}
                                    @csrf
                                    @method('PUT')
                                    <div class="col-md-12">
                                            <x-label for="old_password" name="{{ __('user.old_password') }}" :optionalText="false" />
                                            <x-input type="password" name="old_password" :required="true" value=""/>
                                        </div>
                                        <div class="col-md-12">
                                            <x-label for="password" name="{{ __('user.password') }}" :optionalText="false" />
                                            <x-input type="password" name="password" :required="true" value=""/>
                                        </div>
                                        <div class="col-md-12">
                                            <x-label for="password_confirmation" name="{{ __('user.confirm_password') }}" :optionalText="false" />
                                            <x-input type="password" name="password_confirmation" :required="true" value=""/>
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
                        <!--Tab End: Change Password -->
                    </div>
                </div>
                <!--end row-->
            </div>
        </div>
        @endsection
@section('js')
<script src="{{ versionedAsset('custom/js/profile/profile.js') }}"></script>
@endsection
