@extends('layouts.app')
@section('title', __('order.list'))

@section('css')
<link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
@endsection
        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                    <x-breadcrumb :langArray="[
                                            'schedule.schedule',
                                            'order.list',
                                        ]"/>

                    <div class="card">

                    <div class="card-header px-4 py-3 d-flex justify-content-between">
                        <!-- Other content on the left side -->
                        <div>
                            <h5 class="mb-0 text-uppercase">{{ __('order.list') }}</h5>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                        <form class="row g-3 needs-validation" id="datatableForm" enctype="multipart/form-data">
                            {{-- CSRF Protection --}}
                            @csrf
                            @method('POST')
                            <table class="table table-striped table-bordered border w-100" id="datatable">
                                <thead>
                                    <tr>
                                        <th class="d-none"><!-- Which Stores ID & it is used for sorting --></th>
                                        <th><input class="form-check-input row-select" type="checkbox"></th>
                                        <th>{{ __('order.code') }}</th>
                                        <th>{{ __('app.date') }}</th>
                                        <th>{{ __('customer.customer') }}</th>
                                        <th>{{ __('app.mobile') }}</th>
                                        <th>{{ __('payment.status') }}</th>
                                        <th>{{ __('order.status') }}</th>
                                        <th>{{ __('app.created_at') }}</th>
                                        <th>{{ __('app.action') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </form>
                        </div>
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
<script src="{{ versionedAsset('custom/js/schedule/schedule-list.js') }}"></script>
@endsection
