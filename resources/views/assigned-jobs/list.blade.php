@extends('layouts.app')
@section('title', __('schedule.jobs'))

@section('css')
<link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
@endsection
        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                    <x-breadcrumb :langArray="[
                                            'schedule.scheduling',
                                            'schedule.jobs',
                                        ]"/>

                    <div class="card">

                    <div class="card-header px-4 py-3 d-flex justify-content-between">
                        <!-- Other content on the left side -->
                        <div>
                            <h5 class="mb-0 text-uppercase">{{ __('schedule.assigned_jobs') }}</h5>
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
                                        <th>{{ __('schedule.job_code') }}</th>
                                        <th>{{ __('order.start_at') }}</th>
                                        <th>{{ __('order.end_at') }}</th>
                                        <th>{{ __('customer.customer') }}</th>
                                        <th>{{ __('app.mobile') }}</th>
                                        <th>{{ __('app.status') }}</th>
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
<script src="{{ versionedAsset('custom/js/assigned-jobs/assigned-jobs-list.js') }}"></script>
@endsection
