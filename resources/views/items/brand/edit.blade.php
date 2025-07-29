@extends('layouts.app')
@section('title', __('item.brand.update'))

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <x-breadcrumb :langArray="[
                                            'item.item',
                                            'item.brand.list',
                                            'item.brand.update',
                                        ]"/>
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header px-4 py-3">
                                <h5 class="mb-0">{{ __('item.brand.details') }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <form class="row g-3 needs-validation" id="itemCategoryForm" action="{{ route('item.brand.update') }}" enctype="multipart/form-data">
                                    {{-- CSRF Protection --}}
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name='id' value="{{ $brand->id }}" />
                                    <input type="hidden" id="base_url" value="{{ url('/') }}">

                                    <div class="col-md-12">
                                        <x-label for="name" name="{{ __('app.name') }}" />
                                        <x-input type="text" name="name" :required="true" value="{{ $brand->name }}"/>
                                    </div>
                                    <div class="col-md-12">
                                        <x-label for="description" name="{{ __('app.description') }}" />
                                        <x-textarea name="description" value="{{ $brand->description }}"/>
                                    </div>
                                    <div class="col-md-12 d-none">
                                        <x-label for="status" name="{{ __('app.status') }}" />
                                        <x-dropdown-status selected="{{ $brand->status }}" dropdownName='status'/>
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
<script src="{{ versionedAsset('custom/js/items/brand/item-brand.js') }}"></script>
@endsection
