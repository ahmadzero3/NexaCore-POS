@extends('layouts.app')
@section('title', __('order.report'))

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <x-breadcrumb :langArray="[
                                            'app.reports',
                                            'order.report',
                                        ]"/>
                <div class="row">
                    <form class="row g-3 needs-validation" id="reportForm" action="{{ route('report.order.ajax') }}" enctype="multipart/form-data">
                        {{-- CSRF Protection --}}
                        @csrf
                        @method('POST')

                        <input type="hidden" name="row_count" value="0">
                        <input type="hidden" name="total_amount" value="0">
                        <input type="hidden" id="base_url" value="{{ url('/') }}">
                        <div class="col-12 col-lg-12">
                            <div class="card">
                                <div class="card-header px-4 py-3">
                                    <h5 class="mb-0">{{ __('order.details') }}</h5>
                                </div>
                                <div class="card-body p-4 row g-3">
                                    
                                        <div class="col-md-6">
                                            <x-label for="from_date" name="{{ __('app.from_date') }}" />
                                            <div class="input-group mb-3">
                                                <x-input type="text" additionalClasses="datepicker" name="from_date" :required="true" value=""/>
                                                <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="to_date" name="{{ __('app.to_date') }}" />
                                            <div class="input-group mb-3">
                                                <x-input type="text" additionalClasses="datepicker" name="to_date" :required="true" value=""/>
                                                <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="customer_id" name="{{ __('customer.customer') }}" />
                                            <x-dropdown-customer selected="" />
                                        </div>
                                        
                                        
                                </div>

                                <div class="card-body p-4 row g-3">
                                        <div class="col-md-12">
                                            <div class="d-md-flex d-grid align-items-center gap-3">
                                                <x-button type="submit" class="primary px-4" text="{{ __('app.submit') }}" />
                                                <x-anchor-tag href="{{ route('dashboard') }}" text="{{ __('app.close') }}" class="btn btn-light px-4" />
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-12">
                            <div class="card">
                                <div class="card-header px-4 py-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <h5 class="mb-0">{{ __('app.records') }}</h5>
                                        </div>
                                        <div class="col-6 text-end">
                                            <div class="btn-group">
                                            <button type="button" class="btn btn-outline-success">{{ __('app.export') }}</button>
                                            <button type="button" class="btn btn-outline-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false"> <span class="visually-hidden">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><button type='button' class="dropdown-item" id="generate_excel"><i class="bx bx-spreadsheet mr-1"></i>{{ __('app.excel') }}</button></li>
                                                <li><button type='button' class="dropdown-item" id="generate_pdf"><i class="bx bx-file mr-1"></i>{{ __('app.pdf') }}</button></li>
                                            </ul>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-4 row g-3">
                                        <div class="col-md-12 table-responsive">
                                            <table class="table table-bordered" id="orderReport">
                                                <thead>
                                                    <th>#</th>
                                                    <th>{{ __('order.date') }}</th>
                                                    <th>{{ __('customer.name') }}</th>
                                                    <th>{{ __('order.status') }}</th>
                                                    <th>{{ __('app.total') }}</th>
                                                    <th>{{ __('payment.paid') }}</th>
                                                    <th>{{ __('payment.balance') }}</th>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!--end row-->
            </div>
        </div>
        <!-- Import Modals -->

        @endsection

@section('js')
    @include("plugin.export-table")
    <script src="{{ versionedAsset('custom/js/order/order-report.js') }}"></script>
    
@endsection
