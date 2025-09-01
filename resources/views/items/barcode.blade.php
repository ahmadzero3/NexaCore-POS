@extends('layouts.app')
@section('title', __('item.generate_barcode'))

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <x-breadcrumb :langArray="[
                                            'app.utilities',
                                            'item.generate_barcode',
                                        ]"/>
                <div class="row">
                    <form class="row g-3 needs-validation" id="barcodeForm" action="{{ route('report.item.transaction.batch.ajax') }}" enctype="multipart/form-data">
                        {{-- CSRF Protection --}}
                        @csrf
                        @method('POST')

                        <input type="hidden" name="row_count" value="0">
                        <input type="hidden" id="base_url" value="{{ url('/') }}">

                        <div class="col-12 col-lg-12">
                            <div class="card">
                                    <div class="card-header px-4 py-3">
                                        <h5 class="mb-0">{{ __('item.generate_barcode') }}</h5>
                                    </div>

                                    <div class="card-body p-4 row g-3">
                                            <div class="col-md-6 mb-3">
                                                <x-label for="barcode_type" name="{{ __('item.barcode_type') }}" />
                                                <select class="form-select" data-placeholder="Select Barcode Type" id="barcode_type" name="barcode_type">
                                                    <option value="code128">CODE128</option>
                                                    <option value="code39">CODE39</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <x-label for="size" name="{{ __('item.size') }}" />
                                                <select class="form-select" data-placeholder="Select Barcode Type" id="size" name="size">
                                                    <option value="1_100x50">1 Labels (100 x 50mm)</option>
                                                    <option value="1_50x25">1 Labels (50 x 25mm)</option>
                                                    <option value="2_50x25">2 Labels (50 x 25mm)</option>
                                                    <option value="2_38x25">2 Labels (38 x 25mm)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-9">
                                                <x-label for="search_item" name="{{ __('item.item_name') }}" />
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text" id="basic-addon1"><i class="fadeIn animated bx bx-barcode-reader text-primary"></i></span>
                                                    <input type="text" id="search_item" value="" class="form-control" required placeholder="Scan Barcode/Search Items">
                                                </div>
                                            </div>
                                            <div class="col-md-12 table-responsive">
                                                <table class="table mb-0 table-striped table-bordered" id="itemsTable">
                                                    <thead>
                                                        <tr class="text-uppercase">
                                                            <th scope="col">{{ __('item.item') }}</th>
                                                            <th scope="col">{{ __('item.barcode') }}</th>
                                                            <th scope="col">{{ __('item.sale_price') }}</th>
                                                            <th scope="col">{{ __('app.qty') }}</th>
                                                            <th scope="col">{{ __('app.action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="5" class="text-center fw-light fst-italic default-row">
                                                                No items are added yet!!
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="4" class="fw-bold text-end tfoot-first-td">
                                                                {{ __('app.total') }}
                                                            </td>
                                                            <td class="fw-bold sum_of_quantity">
                                                                0
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                    </div>
                                    <div class="card-body p-4 row g-3">
                                            <div class="col-md-12">
                                                <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">
                                                    <x-button type="button" class="primary px-4" buttonId="generate" text="{{ __('app.generate') }}" />
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
                                            <h5 class="mb-0">{{ __('item.barcodes') }}</h5>
                                        </div>
                                        <div class="col-6 text-end">
                                            <button type="button" class="btn btn-outline-success printIFrame">{{ __('app.print') }}</button>
                                        </div>
                                        </div>

                                </div>
                                <div class="card-body p-4 row g-3">
                                        <div class="col-md-12 table-responsive">

                                            <!-- <div id="labels-container" class="print-only"></div> -->
                                            <iframe id="barcodeIframe" src="{{ route('generate.labels') }}" width="100%" height="500" frameborder="0"></iframe>
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
    <script src="{{ versionedAsset('custom/js/items/generate-barcode.js') }}"></script>
    <script src="{{ versionedAsset('custom/js/common/common.js') }}"></script>
@endsection
