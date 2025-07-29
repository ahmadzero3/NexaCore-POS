@extends('layouts.app')
@section('title', __('account.profit_and_loss'))

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <x-breadcrumb :langArray="[
                                            'app.reports',
                                            'account.profit_and_loss',
                                        ]"/>
                <div class="row">
                    <form class="row g-3 needs-validation" id="reportForm" action="{{ route('report.profit_and_loss.ajax') }}" enctype="multipart/form-data">
                        {{-- CSRF Protection --}}
                        @csrf
                        @method('POST')

                        <input type="hidden" name="row_count" value="0">
                        <input type="hidden" name="total_amount" value="0">
                        <input type="hidden" id="base_url" value="{{ url('/') }}">
                        <div class="col-12 col-lg-12">
                            <div class="card">
                                <div class="card-header px-4 py-3">
                                    <h5 class="mb-0">{{ __('app.filter') }}</h5>
                                </div>
                                <div class="card-body p-4 row g-3">

                                        <div class="col-md-6">
                                            <x-label for="from_date" name="{{ __('app.from_date') }}" />
                                            <div class="input-group mb-3">
                                                <x-input type="text" additionalClasses="datepicker-month-first-date" name="from_date" :required="true" value=""/>
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
                                        <div class="col-md-6 mb-3">
                                            <x-label for="warehouse_id" name="{{ __('warehouse.warehouse') }}" />
                                            <select class="warehouse-ajax form-select" data-placeholder="Select Warehouse" id="warehouse_id" name="warehouse_id"></select>
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
                                            <h5 class="mb-0">{{ __('account.balance_sheet_report') }}</h5>
                                        </div>

                                    </div>
                                </div>
                                <div class="card-body p-4 row g-3">
                                        <div class="col-md-6 table-responsive">
                                            <table id="reportTable" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('item.transaction_type') }}</th>
                                                        <th>{{ __('app.total_amount') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ __('sale.sale_without_tax') }} (+)</td>
                                                        <td id="sale_without_tax" class='text-end' data-tableexport-celltype="number">0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('sale.sale_return_without_tax') }} (-)</td>
                                                        <td id="sale_return_without_tax" class='text-end' data-tableexport-celltype="number">0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('purchase.purchase_without_tax') }} (-)</td>
                                                        <td id="purchase_without_tax" class='text-end' data-tableexport-celltype="number">0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('purchase.purchase_return_without_tax') }} (+)</td>
                                                        <td id="purchase_return_without_tax" class='text-end' data-tableexport-celltype="number">0.00</td>
                                                    </tr>
                                                    {{--<tr class="fw-bold">
                                                        <td>{{ __('account.gross_profit') }}</td>
                                                        <td id="gross_profit" class='text-end' data-tableexport-celltype="number">0.00</td>
                                                    </tr>--}}
                                                    <tr>
                                                        <td>{{ __('account.expense_without_tax') }} (-)</td>
                                                        <td id="indirect_expense_without_tax" class='text-end' data-tableexport-celltype="number">0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('carrier.shipping_charge') }} (-)</td>
                                                        <td id="shipping_charge" class='text-end' data-tableexport-celltype="number">0.00</td>
                                                    </tr>
                                                    <tr class="fw-bold">
                                                        <td>{{ __('account.net_summary') }}</td>
                                                        <td id="net_profit" class='text-end' data-tableexport-celltype="number">0.00</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6 table-responsive">
                                            <table id="reportTable" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('item.transaction_type') }}</th>
                                                        <th>{{ __('app.total_amount') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{ __('account.gross_profit') }}<br>
                                                            <small>
                                                            (<i>{{ __('item.sale_price') }} - {{ __('item.avg_purchase_price') }}</i>)
                                                            <br>
                                                            <i>{{ __('item.sale_price') }} = {{ __('sale.total').' - '. __('app.discount_amount') .' - '. __('tax.tax_amount') }}</i>
                                                            </small>
                                                        </td>
                                                        <td id="sale_profit" class='text-end' data-tableexport-celltype="number">0.00</td>
                                                    </tr>
                                                </tbody>
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
    <script src="{{ versionedAsset('custom/js/common/common.js') }}"></script>
    <script src="{{ versionedAsset('custom/js/reports/profit-and-loss/profit.js') }}"></script>
@endsection
