@extends('layouts.app')
@section('title', __('customer.update_customer'))

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <x-breadcrumb :langArray="[
                                            'customer.customers',
                                            'customer.list',
                                            'customer.update_customer',
                                        ]"/>
                <div class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card">
                            <div class="card-header px-4 py-3">
                                <h5 class="mb-0">{{ __('customer.details') }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <form class="row g-3 needs-validation" id="customerForm" action="{{ route('customer.update') }}" enctype="multipart/form-data">
                                    {{-- CSRF Protection --}}
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name='id' value="{{ $customer->id }}" />
                                    <input type="hidden" id="base_url" value="{{ url('/') }}">
                                    <div class="col-md-6">
                                        <x-label for="first_name" name="{{ __('app.first_name') }}" />
                                        <x-input type="text" name="first_name" :required="true" value="{{ $customer->first_name }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="last_name" name="{{ __('app.last_name') }}" />
                                        <x-input type="text" name="last_name" :required="false" value="{{ $customer->last_name }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="email" name="{{ __('app.email') }}" />
                                        <x-input type="email" name="email" :required="false" value="{{ $customer->email }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="mobile" name="{{ __('app.mobile') }}" />
                                        <x-input type="number" name="mobile" :required="false" value="{{ $customer->mobile }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="whatsapp" name="{{ __('app.whatsapp_number') }}" />
                                        <x-input type="number" name="whatsapp" :required="false" value="{{ $customer->whatsapp }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="address" name="{{ __('app.address') }}" />
                                        <x-textarea name="address" value="{{ $customer->address }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="status" name="{{ __('app.status') }}" />
                                        <x-dropdown-status selected="{{ $customer->status }}" dropdownName='status'/>
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
<script src="{{ versionedAsset('custom/js/customer/customer.js') }}"></script>
@endsection
