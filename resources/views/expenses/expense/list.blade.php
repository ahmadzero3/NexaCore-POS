@extends('layouts.app')
@section('title', __('expense.list'))

@section('css')
<link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
@endsection
        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                    <x-breadcrumb :langArray="[
                                            'expense.expense',
                                            'expense.list',
                                        ]"/>

                    <div class="card">

                    <div class="card-header px-4 py-3 d-flex justify-content-between">
                        <!-- Other content on the left side -->
                        <div>
                            <h5 class="mb-0 text-uppercase">{{ __('expense.list') }}</h5>
                        </div>

                        @can('expense.create')
                        <!-- Button pushed to the right side -->
                        <x-anchor-tag href="{{ route('expense.create') }}" text="{{ __('expense.create') }}" class="btn btn-primary px-5" />
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <x-label for="category_id" name="{{ __('expense.category.category') }}" />
                                <x-dropdown-expense-category selected="" />
                            </div>
                        </div>
                        <form class="row g-3 needs-validation" id="datatableForm" action="{{ route('expense.delete') }}" enctype="multipart/form-data">
                            {{-- CSRF Protection --}}
                            @csrf
                            @method('POST')
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered border w-100" id="datatable">
                                    <thead>
                                        <tr>
                                            <th class="d-none"><!-- Which Stores ID & it is used for sorting --></th>
                                            <th><input class="form-check-input row-select" type="checkbox"></th>
                                            <th>{{ __('app.date') }}</th>
                                            <th>{{ __('expense.number') }}</th>
                                            <th>{{ __('expense.category.category') }}</th>
                                            <th>{{ __('expense.subcategory.subcategory') }}</th>
                                            <th>{{ __('payment.amount') }}</th>
                                            <th>{{ __('payment.payment_type') }}</th>
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
        @endsection
@section('js')
<script src="{{ versionedAsset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ versionedAsset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ versionedAsset('custom/js/common/common.js') }}"></script>
<script src="{{ versionedAsset('custom/js/expenses/expense-list.js') }}"></script>
@endsection
