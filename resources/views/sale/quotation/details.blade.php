@extends('layouts.app')
@section('title', __('sale.quotation.print'))

		@section('content')
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<x-breadcrumb :langArray="[
											'sale.sale',
                                            'sale.quotation.list',
                                            'app.print',
										]"/>
				<div class="row">
					<div class="col-12 col-lg-12">

                        @include('layouts.session')

                        <input type="hidden" id="base_url" value="{{ url('/') }}">

                        <div class="card">
                    <div class="card-body">
                        <div class="toolbar hidden-print">
                                <div class="text-end">

                                    @if($quotation->sale)
                                        <a href="{{ route('sale.invoice.details', ['id' => $quotation->sale->id]) }}" class="btn btn-outline-success"><i class="bx bx-check-double"></i>{{ __('app.view_invoice') }}</a>
                                    @else
                                        <a href="{{ route('convert.quotation.to.sale.invoice', ['id' => $quotation->id]) }}" class="btn btn-outline-success"><i class="bx bx-transfer-alt"></i>{{ __('sale.convert_to_sale') }}</a>
                                    @endif

                                    @can(['sale.quotation.edit'])
                                    <a href="{{ route('sale.quotation.edit', ['id' => $quotation->id]) }}" class="btn btn-outline-primary"><i class="bx bx-edit"></i>{{ __('app.edit') }}</a>
                                    @endcan

                                     <a class="btn btn-outline-dark px-4 notify-through-email" data-model="quotation" data-id="{{$quotation->id}}" role="button">
                                    </i><i class="bx bx-envelope"></i>{{ __('app.email') }}</a>

                                    <a class="btn btn-outline-info px-4 notify-through-sms" data-model="quotation" data-id="{{$quotation->id}}" role="button">
                                    </i><i class="bx bx-envelope"></i>{{ __('message.sms') }}</a>

                                    <a href="{{ route('sale.quotation.print', ['id' => $quotation->id]) }}" target="_blank" class="btn btn-outline-secondary px-4"><i class="bx bx-printer mr-1"></i>{{ __("app.print") }}</a>

                                    <a href="{{ route('sale.quotation.pdf', ['id' => $quotation->id]) }}" target="_blank" class="btn btn-outline-danger px-4"><i class="bx bxs-file-pdf mr-1"></i>{{ __("app.pdf") }}</a>

                                </div>
                                <hr/>
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
                                                <div class="text-gray-light fw-bold text-uppercase">{{ __('sale.quotation.for') }}:</div>
                                                <h2 class="to">{{ $quotation->party->first_name.' '. $quotation->party->last_name }}</h2>
                                                <div class="address">{{ $quotation->party->invoiceing_address }}</div>
                                            </div>

                                            <div class="col invoice-to">
                                                <div class="text-gray-light fw-bold text-uppercase">{{ __('app.ship_to') }}:</div>
                                                <div class="address">{{ $quotation->party->shipping_address }}</div>
                                            </div>

                                            <div class="col invoice-details">
                                                <h1 class="invoice-id">{{ __('sale.quotation.quotation') }} #{{ $quotation->quotation_code }}</h1>
                                                <div class="date">{{ __('app.date') }}: {{ $quotation->formatted_quotation_date  }}</div>
                                                @if($quotation->due_date)
                                                <div class="date">{{ __('app.due_date') }}: {{ $formatDate->toUserDateFormat($quotation->due_date)  }}</div>
                                                @endif
                                                <div class="text-gray-light fw-bold">{{ __('app.status') }}: {{ $quotation->quotation_status  }}</div>
                                            </div>
                                        </div>
                                        @php
                                            $isHasBatchItem = ($quotation->itemTransaction->where('tracking_type', 'batch')->count() > 0) ? true : false;

                                            //Return from Controller
                                            $totalBatchTrackingRowCount = ($isHasBatchItem) ? $batchTrackingRowCount : 0;

                                        @endphp
                                        <table id="printInvoice">
                                            <thead>
                                                <tr class="text-uppercase">
                                                    <th>#</th>
                                                    <th class="text-left">{{ __('item.item') }}</th>
                                                    <th class="text-left {{ !app('company')['show_hsn'] ? 'd-none':'' }}">{{ __('item.hsn') }}</th>
                                                    <th class="{{ !app('company')['show_mrp'] ? 'd-none':'' }}">{{ __('item.mrp') }}</th>
                                                    @if($isHasBatchItem)

                                                        <th class="{{ !app('company')['enable_batch_tracking'] ? 'd-none':'' }}">{{ __('item.batch_no') }}</th>
                                                        <th class="{{ !app('company')['enable_mfg_date'] ? 'd-none':'' }}">{{ __('item.mfg_date') }}</th>
                                                        <th class="{{ !app('company')['enable_exp_date'] ? 'd-none':'' }}">{{ __('item.exp_date') }}</th>
                                                        <th class="{{ !app('company')['enable_model'] ? 'd-none':'' }}">{{ __('item.model_no') }}</th>
                                                        <th class="{{ !app('company')['enable_color'] ? 'd-none':'' }}">{{ __('item.color') }}</th>
                                                        <th class="{{ !app('company')['enable_size'] ? 'd-none':'' }}">{{ __('item.size') }}</th>
                                                    @endif
                                                    <th class="text-left">{{ __('app.qty') }}</th>
                                                    <th class="text-end">{{ __('app.price_per_unit') }}</th>
                                                    <th scope="col" class="text-end {{ !app('company')['show_discount'] ? 'd-none':'' }}">{{ __('app.discount') }}</th>
                                                    <th class="text-end {{ (app('company')['tax_type'] == 'no-tax') ? 'd-none':'' }}">{{ __('tax.tax') }}</th>
                                                    <th class="text-end">{{ __('app.total') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $i=1;
                                                @endphp

                                                @foreach($quotation->itemTransaction as $transaction)
                                                <tr>
                                                    <td class="no">{{ $i++ }}</td>
                                                    <td class="text-left">
                                                        <h3>
                                                            <!-- Service Name -->
                                                            {{ $transaction->item->name }}
                                                        </h3>
                                                        <!-- Description -->
                                                        <small>{{ $transaction->description }}</small>
                                                        <small>
                                                            @if ($transaction->itemSerialTransaction->count() > 0)
                                                                <br>{{ $transaction->itemSerialTransaction->pluck('itemSerialMaster.serial_code')->implode(',') }}<br>
                                                            @endif
                                                        </small>
                                                   </td>
                                                   <td class="{{ !app('company')['show_hsn'] ? 'd-none':'' }}">
                                                       {{ $transaction->item->hsn }}
                                                   </td>
                                                   <td class="{{ !app('company')['show_mrp'] ? 'd-none':'' }}">
                                                        {{ $formatNumber->formatWithPrecision($transaction->batch ? $transaction->batch->itemBatchMaster->mrp : $transaction->mrp) }}
                                                    </td>
                                                   @if($isHasBatchItem)
                                                        <td class="{{ !app('company')['enable_batch_tracking'] ? 'd-none':'' }}">
                                                            {{ $transaction->batch ? $transaction->batch->itemBatchMaster->batch_no : '' }}
                                                        </td>
                                                        <td class="{{ !app('company')['enable_mfg_date'] ? 'd-none':'' }}">
                                                            {{ $transaction->batch ? $formatDate->toUserDateFormat($transaction->batch->itemBatchMaster->mfg_date) : '' }}
                                                        </td>
                                                        <td class="{{ !app('company')['enable_exp_date'] ? 'd-none':'' }}">
                                                            {{ $transaction->batch ? $formatDate->toUserDateFormat($transaction->batch->itemBatchMaster->exp_date) : '' }}
                                                        </td>
                                                        <td class="{{ !app('company')['enable_model'] ? 'd-none':'' }}">
                                                            {{ $transaction->batch ? $transaction->batch->itemBatchMaster->model_no : ''}}
                                                        </td>
                                                        <td class="{{ !app('company')['enable_color'] ? 'd-none':'' }}">
                                                            {{ $transaction->batch ? $transaction->batch->itemBatchMaster->color :'' }}
                                                        </td>
                                                        <td class="{{ !app('company')['enable_size'] ? 'd-none':'' }}">
                                                            {{ $transaction->batch ? $transaction->batch->itemBatchMaster->size : '' }}
                                                        </td>
                                                    @endif
                                                   <td class="">
                                                        {{ $formatNumber->formatQuantity($transaction->quantity) }}<br>
                                                        <small>{{ $transaction->unit->name }}</small>
                                                    </td>
                                                    <td class="unit">
                                                        {{ $formatNumber->formatWithPrecision($transaction->unit_price) }}
                                                    </td>
                                                    <td class="unit {{ !app('company')['show_discount'] ? 'd-none':'' }}">
                                                        {{ $formatNumber->formatWithPrecision($transaction->discount_amount) }}<br>
                                                        <small>
                                                            ({{ $formatNumber->formatWithPrecision($transaction->discount) }}
                                                                {{ ($transaction->discount_type == 'fixed') ? '$' : '%' }})
                                                        </small>
                                                    </td>
                                                    <th scope="col" class="unit {{ (app('company')['tax_type'] == 'no-tax') ? 'd-none':'' }}">
                                                        {{ $formatNumber->formatWithPrecision($transaction->tax_amount) }}<br>
                                                        <small>({{ $transaction->tax->rate }}%)</small>
                                                    </td>
                                                    <td class="unit">
                                                        {{ $formatNumber->formatWithPrecision($transaction->total) }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                @php
                                                $subtotal = $quotation->itemTransaction->sum(function ($transaction) {
                                                            /*if($transaction->tax_type == 'inclusive'){
                                                                $unitPrice = calculatePrice($transaction->unit_price, $transaction->tax->rate, needInclusive: true);
                                                            }else{
                                                                $unitPrice = calculatePrice($transaction->unit_price, $transaction->tax->rate, needInclusive: false);
                                                            }*/
                                                            $unitPrice = $transaction->unit_price;
                                                            return $unitPrice * $transaction->quantity;
                                                        });
                                                $discount = $quotation->itemTransaction->sum(function ($transaction) {
                                                            return $transaction->discount_amount;
                                                        });

                                                $taxAmount = $quotation->itemTransaction->sum(function ($transaction) {
                                                            return $transaction->tax_amount;
                                                        });


                                                $columnCount = 5 + $totalBatchTrackingRowCount + app('company')['show_hsn'] + app('company')['show_discount'] + app('company')['show_mrp'] - (app('company')['tax_type'] == 'no-tax' ? 1 : 0);

                                                @endphp
                                                <tr>
                                                    <td colspan="{{ $columnCount }}" class="tfoot-first-td">{{ __('app.subtotal') }}</td>
                                                    <td>{{ $formatNumber->formatWithPrecision($subtotal) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="{{ $columnCount }}" class="tfoot-first-td">{{ __('app.discount') }}(-)</td>
                                                    <td>{{ $formatNumber->formatWithPrecision($discount) }}</td>
                                                </tr>
                                                <tr class="{{ (app('company')['tax_type'] == 'no-tax') ? 'd-none':'' }}">
                                                    <td colspan="{{ $columnCount }}" class="tfoot-first-td">{{ __('tax.tax') }}</td>
                                                    <td>{{ $formatNumber->formatWithPrecision($taxAmount) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="{{ $columnCount }}" class="tfoot-first-td">{{ __('app.round_off') }}</td>
                                                    <td>{{ $formatNumber->formatWithPrecision($quotation->round_off) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="{{ $columnCount }}" class="tfoot-first-td">{{ __('app.grand_total') }}</td>
                                                    <td>{{ $formatNumber->formatWithPrecision($quotation->grand_total) }}</td>
                                                </tr>
                                                @if(app('company')['is_enable_secondary_currency'])
                                                    <tr>
                                                        <td colspan="{{ $columnCount }}" class="tfoot-first-td">{{ __('currency.converted_to').'-'.$quotation->currency->code }}</td>
                                                        <td>{{ $formatNumber->formatWithPrecision($quotation->grand_total * $quotation->exchange_rate) }}</td>
                                                    </tr>
                                                @endif
                                                <tr class="d-none">
                                                    <td colspan="{{ $columnCount }}" class="tfoot-first-td">{{ __('payment.paid_amount') }}</td>
                                                    <td>{{$formatNumber->formatWithPrecision($quotation->paid_amount)}}</td>
                                                </tr>

                                            </tfoot>
                                        </table>
                                    </main>

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

        @include("modals.email.send")
        @include("modals.sms.send")

		@endsection

@section('js')
    <script src="{{ versionedAsset('custom/js/modals/email/send.js') }}"></script>
    <script src="{{ versionedAsset('custom/js/sms/sms.js') }}"></script>
@endsection
