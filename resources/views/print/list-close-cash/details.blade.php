@extends('layouts.app')
@section('title', __('app.close_cash_list_details'))

@section('css')
<link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
<style>
    .detail-card {
        border-left: 4px solid #0d6efd;
        background: #f8f9fa;
    }

    .financial-summary {
        font-size: 1.1rem;
    }

    .value-display {
        font-weight: 600;
        color: #2a3042;
    }

    header {
        padding: 10px 0;
        margin-bottom: 20px;
        border-bottom: 1px solid #008cff;
    }
</style>
@endsection

@section('content')

<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <x-breadcrumb :langArray="[
            'payment.cash_and_bank',
            'payment.close_cash_list',
            'app.close_cash_list_details',
        ]" />

        <div class="card">
            <div class="card-header px-4 py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 text-uppercase">
                    {{ __('app.close_cash_list_details') }}
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('close.cash.list') }}" class="btn btn-outline-primary">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('app.back') }}
                    </a>
                    <a href="{{ route('close-cash.edit', $closeCash->id) }}" class="btn btn-outline-dark">
                        <i class="bx bx-edit me-1"></i> {{ __('app.edit') }}
                    </a>

                    <a href="{{ route('close-cash.print', $closeCash->id) }}" target="_blank" class="btn btn-outline-secondary">
                        <i class="bx bx-printer me-1"></i> {{ __('app.print') }}
                    </a>

                    <a href="{{ route('close-cash.print', ['id' => $closeCash->id, 'type' => 'pdf']) }}"
                        class="btn btn-outline-danger px-4">
                        <i class="bx bxs-file-pdf mr-1"></i>{{ __('app.pdf') }}
                    </a>
                </div>
            </div>

            <div class="px-4 py-3 gap-2 align-items-center justify-content-between">
                <header>
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <a href="javascript:;">
                                <img src="{{ '/company/getimage/' . app('company')['colored_logo'] }}" width="80" alt="Company Logo">
                            </a>
                        </div>
                        <div class="col text-end">
                            <h2 class="name mb-1">
                                {{ app('company')['name'] }}
                            </h2>
                            <div class="text-muted">{{ app('company')['address'] }}</div>
                        </div>
                    </div>
                </header>
            </div>



            <div class="card-body">
                <div class="row g-4">
                    <!-- Financial Summary -->
                    <div class="col-12">
                        <div class="p-4 detail-card rounded-3">
                            <h6 class="mb-4 text-uppercase border-bottom pb-2">
                                <i class="bx bx-line-chart me-2"></i>
                                {{ __('app.financial_summary') }}
                            </h6>

                            <div class="financial-summary">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        {{ __('app.opening_balance') }}:
                                    </div>
                                    <div class="col-md-6 value-display">
                                        {{ number_format($closeCash->opening_balance, 2) }}
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        {{ __('app.today_income') }}:
                                    </div>
                                    <div class="col-md-6 value-display text-success">
                                        + {{ number_format($closeCash->today_income, 2) }}
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        {{ __('app.total_income') }}:
                                    </div>
                                    <div class="col-md-6 value-display">
                                        {{ number_format($closeCash->total_income, 2) }}
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        {{ __('app.today_expense') }}:
                                    </div>
                                    <div class="col-md-6 value-display text-danger">
                                        - {{ number_format($closeCash->today_expenses, 2) }}
                                    </div>
                                </div>

                                <div class="row mb-3 pt-2 border-top">
                                    <div class="col-md-6 fw-bold">
                                        {{ __('app.closing_balance') }}:
                                    </div>
                                    <div class="col-md-6 value-display text-primary fw-bold">
                                        {{ number_format($closeCash->balance, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div class="col-12">
                        <div class="p-4 detail-card rounded-3">
                            <h6 class="mb-4 text-uppercase border-bottom pb-2">
                                <i class="bx bx-info-circle me-2"></i>
                                {{ __('app.transaction_details') }}
                            </h6>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="fw-500">{{ __('app.created_by') }}:</div>
                                    <div class="text-muted">
                                        <i class="bx bx-user me-1"></i>
                                        {{ $closeCash->created_by_name }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="fw-500">{{ __('app.created_at') }}:</div>
                                    <div class="text-muted">
                                        <i class="bx bx-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($closeCash->created_at)->format('d/m/Y H:i') }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="fw-500">{{ __('app.update_at') }}:</div>
                                    <div class="text-muted">
                                        <i class="bx bx-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($closeCash->updated_at)->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection