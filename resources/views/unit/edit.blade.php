@extends('layouts.app')
@section('title', __('unit.update'))

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <x-breadcrumb :langArray="[
                                            'app.settings',
                                            'unit.list',
                                            'unit.update',
                                        ]"/>
                <div class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card">
                            <div class="card-header px-4 py-3">
                                <h5 class="mb-0">{{ __('unit.details') }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <form class="row g-3 needs-validation" id="unitForm" action="{{ route('unit.update') }}" enctype="multipart/form-data">
                                    {{-- CSRF Protection --}}
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name='id' value="{{ $unit->id }}" />
                                    <input type="hidden" id="base_url" value="{{ url('/') }}">
                                    <div class="col-md-6">
                                        <x-label for="name" name="{{ __('app.name') }}" />
                                        <x-input type="text" name="name" :required="true" value="{{ $unit->name }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="short_code" name="{{ __('app.short_code') }}" />
                                        <x-input type="text" name="short_code" :required="true" value="{{ $unit->short_code }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="description" name="{{ __('app.description') }}" />
                                        <x-textarea name="description" value="{{ $unit->description }}"/>
                                    </div>
                                    <div class="col-md-6 d-none">
                                        <x-label for="status" name="{{ __('app.status') }}" />
                                        <x-dropdown-status selected="{{ $unit->status }}" dropdownName='status'/>
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
<script src="{{ versionedAsset('custom/js/unit/unit.js') }}"></script>
@endsection
