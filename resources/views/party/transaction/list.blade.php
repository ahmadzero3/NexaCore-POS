@extends('layouts.app')
@section('title', __('party.transaction'))

@section('css')
<link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
@endsection
        @section('content')

        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                    <x-breadcrumb :langArray="[
                                            'party.contacts',
                                            $partyData['party_type'],
                                            'party.transaction',
                                        ]"/>

                    <div class="card">

                    <div class="card-header px-4 py-3 d-flex justify-content-between">
                        <!-- Other content on the left side -->
                        <div>
                            <h5 class="mb-0">
                                <span class="text-uppercase">{{ __('app.balance') }}</span> :
                                <span class="text-{{ $partyData['balance']>=0 ? 'danger' : 'success'}} cash-in-hand">
                                    {{ abs($partyData['balance']) }}
                                </span>
                                <small class="text-transform-none">{{ $partyData['balance_message'] }}</small>
                            </h5>
                        </div>
                        <!-- Button pushed to the right side -->
                        <x-anchor-tag href="{{ route('party.payment.create', ['id' => $party->id, 'partyType' => $party->party_type]) }}" text="{{ __('payment.payment') }}" class="btn btn-primary px-5" />

                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <x-label for="from_date" name="{{ __('app.from_date') }}" />
                                <a tabindex="0" class="text-primary" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Filter by Sale Date"><i class="fadeIn animated bx bx-info-circle"></i></a>
                                <div class="input-group mb-3">
                                    <x-input type="text" additionalClasses="datepicker-month-first-date " name="from_date" :required="true" value=""/>
                                    <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <x-label for="to_date" name="{{ __('app.to_date') }}" />
                                <a tabindex="0" class="text-primary" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Filter by Sale Date"><i class="fadeIn animated bx bx-info-circle"></i></a>
                                <div class="input-group mb-3">
                                    <x-input type="text" additionalClasses="datepicker" name="to_date" :required="true" value=""/>
                                    <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <form class="row g-3 needs-validation" id="datatableForm" action="" enctype="multipart/form-data">
                            {{-- CSRF Protection --}}
                            @csrf
                            @method('POST')
                            <input type="hidden" id="base_url" value="{{ url('/') }}">
                            <input type="hidden" id="party_id" value="{{ $party->id }}">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered border w-100" id="datatable">
                                    <thead>
                                        <tr>
                                            <th>{{ __('app.type') }}</th>
                                            <th>{{ __('app.date') }}</th>
                                            <th>{{ __('app.status') }}</th>
                                            <th>{{ __('payment.type') }}</th>
                                            <th>{{ __('app.note') }}</th>
                                            <th>{{ __('payment.amount') }}</th>
                                            <th>{{ __('app.created_at') }}</th>
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

        @include("modals.transaction.cash-in-hand")

        @endsection
@section('js')
<script src="{{ versionedAsset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ versionedAsset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ versionedAsset('custom/js/common/common.js') }}"></script>
<script src="{{ versionedAsset('custom/js/party/transaction/list.js') }}"></script>

@endsection
