@extends('layouts.app')
@section('title', __('payment.edit_close_cash'))

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <x-breadcrumb :langArray="['payment.cash_and_bank', 'payment.close_cash_list', 'payment.edit_close_cash']" />

        <div class="card">
            <div class="card-header px-4 py-3">
                <h5 class="mb-0 text-uppercase">
                    {{ __('payment.edit_close_cash') }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('close-cash.update', $closeCash->id) }}" id="editCloseCashForm">
                    <input type="hidden" id="base_url" value="{{ url('/') }}">

                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('app.opening_balance') }}</label>
                            <x-input type="text"
                                name="opening_balance"
                                :value="$closeCash->opening_balance"
                                :required="true" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('app.today_income') }}</label>
                            <x-input type="text"
                                name="today_income"
                                :value="$closeCash->today_income"
                                :required="true" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('app.total_income') }}</label>
                            <x-input type="text"
                                name="total_income"
                                :value="$closeCash->total_income"
                                :required="true" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('app.today_expense') }}</label>
                            <x-input type="text"
                                name="today_expenses"
                                :value="$closeCash->today_expenses"
                                :required="true" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('app.balance') }}</label>
                            <x-input type="text"
                                name="balance"
                                :value="$closeCash->balance"
                                :required="true" />
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('app.update') }}
                        </button>
                        <a href="{{ route('close.cash.list') }}" class="btn btn-secondary">
                            {{ __('app.close') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ versionedAsset('custom/js/transaction/edit-list-close-cash.js') }}"></script>
@endsection