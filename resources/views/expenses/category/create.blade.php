@extends('layouts.app')
@section('title', __('expense.category.create'))

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <x-breadcrumb :langArray="[
                                            'expense.expense',
                                            'expense.category.list',
                                            'expense.category.create',
                                        ]"/>
                <div class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card">
                            <div class="card-header px-4 py-3">
                                <h5 class="mb-0">{{ __('expense.category.details') }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <form class="row g-3 needs-validation" id="expenseCategoryForm" action="{{ route('expense.category.store') }}" enctype="multipart/form-data">
                                    {{-- CSRF Protection --}}
                                    @csrf
                                    @method('POST')
                                    
                                    <input type="hidden" id="base_url" value="{{ url('/') }}">
                                
                                    <div class="col-md-6">
                                        <x-label for="name" name="{{ __('app.name') }}" />
                                        <x-input type="text" name="name" :required="true" value=""/>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <x-label for="parent_id" name="{{ __('expense.category.type') }}" />
                                        <x-dropdown-account-expense-type selected="" />
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <x-label for="description" name="{{ __('app.description') }}" />
                                        <x-textarea name="description" value=""/>
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
        <!-- Import Modals -->
        @include("modals.tax.create")

        @endsection

@section('js')
<script src="{{ versionedAsset('custom/js/expenses/expense-category.js') }}"></script>
@endsection
