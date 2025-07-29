@extends('layouts.app')
@section('title', __('party.payment'))

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <x-breadcrumb :langArray="[
                                            'party.contacts',
                                            $partyData['party_type'],
                                            'party.payment',
                                        ]"/>
                <div class="row">
                    <form class="row g-3 needs-validation" id="paymentForm" action="{{ route('store.party.payment') }}" enctype="multipart/form-data">
                        {{-- CSRF Protection --}}
                        @csrf
                        @method('POST')

                        <input type="hidden" name="row_count" value="0">
                        <input type="hidden" name="total_amount" value="0">
                        <input type="hidden" id="base_url" value="{{ url('/') }}">
                        <div class="col-12 col-lg-12">
                            <div class="card">
                                <div class="card-header px-4 py-3 d-flex justify-content-between">
                                    <!-- Other content on the left side -->
                                    <div>
                                        <h5 class="mb-0">{{ __('party.payment') }}</h5>
                                    </div>
                                    <!-- Button pushed to the right side -->
                                    <x-anchor-tag href="{{ route('party.transaction.list', ['id' => $party->id, 'partyType' => $party->party_type]) }}" text="{{ __('party.transaction') }}" class="btn btn-outline-primary px-5" />
                                    
                                </div>
                                <div class="card-body p-4 row g-3">
                                        <div class="col-md-6 ">
                                            <x-label for="transaction_date" name="{{ __('app.date') }}" />
                                            <div class="input-group">
                                                <x-input type="text" additionalClasses="datepicker" name="transaction_date" :required="true" value=""/>
                                                <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="receipt_no" name="{{ __('payment.receipt_no') }}" />
                                            <x-input type="text" name="receipt_no" value=""/>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="party_id" name="{{ $partyData['party'] }}" />
                                            <select class="form-select" data-party-type='{{$partyData['party_type']}}' data-placeholder="" id="party_id" name="party_id">
                                                <option value="{{ $party->id }}">{{ $party->getFullName() }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="balance" name="{{ __('app.balance') }} <label class='text-danger'> ({{ $partyData['balance_message'] }}) </label>" />
                                            <x-input type="text" additionalClasses="" :readonly='true' name="balance" :required="false" value="{{ ($partyData['balance']) }}"/>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <x-label for="payment_type_id" name="{{ __('payment.payment_type') }}" />
                                            <select class="form-select select2 payment-type-ajax" name="payment_type_id" data-placeholder="Choose one thing"></select>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="payment" name="{{ __('payment.payment') }}" />
                                            <div class="input-group">
                                                <select class="form-select cu-flex-30" name="payment_direction">
                                                    <option value="pay" {{ $partyData['auto_payment_direction'] == 'pay' ? 'selected' : '' }}>You Pay</option>
                                                    <option value="receive" {{ $partyData['auto_payment_direction'] == 'collect' ? 'selected' : '' }}>You Collect</option>
                                                </select>

                                                <x-input type="text" additionalClasses="cu_numeric" name="payment" value=""/>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <x-label for="payment_note" name="{{ __('payment.note') }}" />
                                            <x-textarea name="payment_note" value=""/>
                                        </div>

                                        <div class="col-md-6">
                                            <x-label for="status" name="{{ __('payment.adjust') }}" />
                                            <div class="input-group">
                                                <select class="form-select" id="adjustment" name="">
                                                        <option value="none">None</option>
                                                        <option value="adjust">{{ $partyData['adjust_message'] }}</option>
                                                </select>
                                                <span class="btn btn-secondary load-records cursor-not-allowed">Load</span>
                                            </div>
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
                                            <h5 class="mb-0">{{ __('payment.due_payments') }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-4 row g-3">
                                        <div class="col-md-12 table-responsive">
                                            <table class="table table-bordered" id="duePaymentsRecordsTable">
                                                <thead>
                                                    <tr class="text-uppercase">
                                                        <th>#</th>
                                                        <th>{{ __('app.date') }}</th>
                                                        <th>{{ __('app.invoice_or_reference_no') }}</th>
                                                        <th>{{ __('app.grand_total') }}</th>
                                                        <th>{{ __('app.paid_amount') }}</th>
                                                        <th>{{ __('app.balance') }}</th>
                                                        <th>{{ __('payment.adjust_amount') }}</th>
                                                    </tr>
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
    <script src="{{ versionedAsset('custom/js/common/common.js') }}"></script>
    <script src="{{ versionedAsset('custom/js/party/payment/payment.js') }}"></script>
    
@endsection
