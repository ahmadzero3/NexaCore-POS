@extends('layouts.app')
@section('title', __('app.print'))

@section('content')
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <x-breadcrumb :langArray="[
											'expense.expense',
                                            'expense.list',
                                            'app.print',
										]" />
        <div class="row">
            <div class="col-12 col-lg-12">

                @include('layouts.session')

                <div class="card">
                    <div class="card-body">
                        <div class="toolbar hidden-print">
                            <div class="text-end">
                                @can(['expense.edit'])
                                <a href="{{ route('expense.edit', ['id' => $expense->id]) }}" class="btn btn-primary"><i class="bx bx-edit"></i>{{ __('app.edit') }}</a>
                                @endcan
                                <button type="button" id="printButton" class="btn btn-secondary"><i class="bx bx-printer mr-1"></i>{{ __('app.print') }}</button>
                                <button type="button" id="generate_pdf" class="btn btn-danger"><i class="bx bx-file mr-1"></i>{{ __('app.export_to_pdf') }}</button>
                            </div>
                            <hr />
                        </div>
                        <div id="printForm">
                            <div class="invoice overflow-auto">
                                <div class="min-width-600">
                                    <header>
                                        <div class="row">
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <img src={{ "/company/getimage/" . app('company')['colored_logo'] }} width="80" alt="" />
                                                </a>
                                            </div>
                                            <div class="col company-details">
                                                <h2 class="name">
                                                    <a href="javascript:;">
                                                        {{ app('company')['name'] }}
                                                    </a>
                                                </h2>
                                                <div>{{ app('company')['address'] }}</div>
                                            </div>
                                        </div>
                                    </header>
                                    <main>
                                        <div class="row contacts">
                                            <div class="col invoice-to">
                                                <div class="text-gray-light text-uppercase">{{ __('expense.expense_category') }}:</div>
                                                <h2 class="to">{{ $expense->category->name }}</h2>
                                                @if($expense->subcategory)
                                                <div class="text-gray-light text-uppercase">{{ __('expense.subcategory.subcategory') }}:</div>

                                                <h2 class="to">{{ $expense->subcategory->name }}</h2>
                                                @endif
                                            </div>
                                            <div class="col invoice-details">
                                                <h1 class="invoice-id">{{ __('expense.number') }} #{{ $expense->expense_code }}</h1>
                                                <div class="date">{{ __('app.date') }}: {{ $expense->formatted_expense_date  }}</div>
                                            </div>
                                        </div>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th class="text-left text-uppercase">{{ __('item.item') }}</th>
                                                    <th class="text-left text-uppercase">{{ __('app.qty') }}</th>
                                                    <th class="text-end text-uppercase">{{ __('app.price_per_unit') }}</th>
                                                    <th class="text-end text-uppercase">{{ __('app.total') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                $i=1;
                                                @endphp

                                                @foreach($expenseItems as $items)
                                                <tr>
                                                    <td class="no">{{ $i++ }}</td>
                                                    <td class="text-left">
                                                        <h3>
                                                            <!-- Service Name -->
                                                            {{ $items->itemDetails->name }}
                                                        </h3>
                                                        <!-- Description -->
                                                        {{ $items->note }}
                                                    </td>
                                                    <td class="">
                                                        {{ $formatNumber->formatQuantity($items->quantity) }}
                                                    </td>
                                                    <td class="unit">
                                                        {{ $formatNumber->formatWithPrecision($items->unit_price) }}
                                                    </td>
                                                    <td class="unit">
                                                        {{ $formatNumber->formatWithPrecision($items->total) }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="2"></td>
                                                    <td colspan="2">{{ __('app.round_off') }}</td>
                                                    <td>{{ $formatNumber->formatWithPrecision($expense->round_off) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2"></td>
                                                    <td colspan="2">{{ __('app.total_dollar') }}</td>
                                                    <td>{{ $formatNumber->formatWithPrecision($expense->grand_total) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2"></td>
                                                    <td colspan="2">{{ __('payment.paid_amount') }}</td>
                                                    <td>{{$formatNumber->formatWithPrecision($expense->paid_amount)}}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <div class="thanks">{{ __('app.thank_you') }}!</div>

                                    </main>
                                    <footer>{{ __('app.computer_generated_receipt') }}</footer>
                                </div>
                                <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
                                <div></div>
                            </div>
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
@include("plugin.export")
<script src="{{ versionedAsset('custom/js/print/print-and-pdf.js') }}"></script>
@endsection