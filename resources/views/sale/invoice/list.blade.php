@extends('layouts.app')
@section('title', __('sale.invoices'))

@section('css')
<link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
@endsection
        @section('content')

        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                    <x-breadcrumb :langArray="[
                                            'sale.invoices',
                                            'sale.list',
                                        ]"/>

                    <div class="card">

                    <div class="card-header px-4 py-3 d-flex justify-content-between">
                        <!-- Other content on the left side -->
                        <div>
                            <h5 class="mb-0 text-uppercase">{{ __('sale.list') }}</h5>
                        </div>

                        @can('sale.invoice.create')
                        <!-- Button pushed to the right side -->
                        <x-anchor-tag href="{{ route('sale.invoice.create') }}" text="{{ __('sale.create') }}" class="btn btn-primary px-5" />
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <x-label for="party_id" name="{{ __('customer.customer') }}" />

                                <a tabindex="0" class="text-primary" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Search by name, mobile, phone, whatsApp, email"><i class="fadeIn animated bx bx-info-circle"></i></a>

                                <select class="party-ajax form-select" data-party-type='customer' data-placeholder="Select Customer" id="party_id" name="party_id"></select>
                            </div>
                            <div class="col-md-3">
                                <x-label for="user_id" name="{{ __('user.user') }}" />
                                <x-dropdown-user selected="" :showOnlyUsername='true' :canViewAllUsers="auth()->user()->can('sale.invoice.can.view.other.users.sale.invoices')" />
                            </div>
                            <div class="col-md-3">
                                <x-label for="from_date" name="{{ __('app.from_date') }}" />
                                <a tabindex="0" class="text-primary" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Filter by Sale Date"><i class="fadeIn animated bx bx-info-circle"></i></a>
                                <div class="input-group mb-3">
                                    <x-input type="text" additionalClasses="datepicker-edit" name="from_date" :required="true" value=""/>
                                    <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <x-label for="to_date" name="{{ __('app.to_date') }}" />
                                <a tabindex="0" class="text-primary" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Filter by Sale Date"><i class="fadeIn animated bx bx-info-circle"></i></a>
                                <div class="input-group mb-3">
                                    <x-input type="text" additionalClasses="datepicker-edit" name="to_date" :required="true" value=""/>
                                    <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                </div>
                            </div>
                        </div>

                        <form class="row g-3 needs-validation" id="datatableForm" action="{{ route('sale.invoice.delete') }}" enctype="multipart/form-data">
                            {{-- CSRF Protection --}}
                            @csrf
                            @method('POST')
                            <input type="hidden" id="base_url" value="{{ url('/') }}">
                            <input type="hidden" id="payment_for" value="sale-invoice">
                            <div class="table-responsive">
                            <table class="table table-striped table-bordered border w-100" id="datatable">
                                <thead>
                                    <tr>
                                        <th class="d-none"><!-- Which Stores ID & it is used for sorting --></th>
                                        <th><input class="form-check-input row-select" type="checkbox"></th>
                                        <th>{{ __('sale.code') }}</th>
                                        <th>{{ __('app.date') }}</th>
                                        <th>{{ __('supplier.supplier') }}</th>
                                        <th>{{ __('app.total') }}</th>
                                        <th>{{ __('payment.balance') }}</th>
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

        @include("modals.payment.invoice-payment", ['payment_for' => 'sale-invoice'])
        @include("modals.payment.invoice-payment-history")
        @include("modals.email.send")
        @include("modals.sms.send")

        @endsection
@section('js')
<script src="{{ versionedAsset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ versionedAsset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ versionedAsset('custom/js/common/common.js') }}"></script>
<script src="{{ versionedAsset('custom/js/sale/sale-list.js') }}"></script>
<script src="{{ versionedAsset('custom/js/modals/payment/invoice-payment.js') }}"></script>
<script src="{{ versionedAsset('custom/js/modals/email/send.js') }}"></script>
<script src="{{ versionedAsset('custom/js/sms/sms.js') }}"></script>
@endsection
