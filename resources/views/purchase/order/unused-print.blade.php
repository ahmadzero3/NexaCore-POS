<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <!-- Bootstrap 5 CSS -->
    <link href="http://localhost:8000/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ versionedAsset('custom/css/print/print.css') }}">
</head>
<body>
    <!-- To switch to RTL, add dir="rtl" to the div below -->
    <h2 class="page-title">Purchase Order</h2>
    <div class="invoice" dir="ltr">
        <div class="company-header company-header border-bottom border-default">
            <img src={{ "/company/getimage/" . app('company')['colored_logo'] }} alt="Company Logo" class="company-logo">
            <div class="company-name">
                <h2>{{ app('company')['name'] }}</h2>
                <p class="company-contact">{{ app('company')['address'] }}</p>
                @if(app('company')['mobile'] || app('company')['email'])
                <p class="company-contact">
                {{ app('company')['mobile'] ? 'M: '. app('company')['mobile'] : ''}}{{ app('company')['email'] ? ', Mail: '.app('company')['email'] : '' }}</p>
                @endif
            </div>
            <div class="bill-info">
                <p class="bill-number">Bill #: {{ $order->order_code }}</p>
                <p>{{ __('order.date') }}: {{ $order->formatted_order_date  }}</p>
                <p>{{ __('app.due_date') }}: {{ $formatDate->toUserDateFormat($order->due_date)  }}</p>
            </div>
        </div>
        <div class="clearfix address-container">
            <div class="address address-box">
                <strong>{{ __('app.order_to') }}</strong><br>
                {{ $order->party->first_name.' '. $order->party->last_name }}<br>
                {{ $order->party->billing_address }}
            </div>
            <div class="address address-box">
                <strong>{{ __('app.ship_from') }}</strong><br>
                {{ $order->party->shipping_address }}
            </div>
        </div>

        @php
            $isHasBatchItem = ($order->itemTransaction->where('tracking_type', 'batch')->count() > 0) ? true : false;

            //Return from Controller
            $totalBatchTrackingRowCount = ($isHasBatchItem) ? $batchTrackingRowCount : 0;
        @endphp
        <table class="table table-bordered custom-table table-compact" id="item-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('item.item') }}</th>
                    <th>{{ __('item.hsn') }}</th>
                    @if($isHasBatchItem)
                        <th class="{{ !app('company')['enable_batch_tracking'] ? 'd-none':'' }}">{{ __('item.batch_no') }}</th>
                        <th class="{{ !app('company')['enable_mfg_date'] ? 'd-none':'' }}">{{ __('item.mfg_date') }}</th>
                        <th class="{{ !app('company')['enable_exp_date'] ? 'd-none':'' }}">{{ __('item.exp_date') }}</th>
                        <th class="{{ !app('company')['enable_model'] ? 'd-none':'' }}">{{ __('item.model_no') }}</th>
                        <th class="{{ !app('company')['show_mrp'] ? 'd-none':'' }}">{{ __('item.mrp') }}</th>
                        <th class="{{ !app('company')['enable_color'] ? 'd-none':'' }}">{{ __('item.color') }}</th>
                        <th class="{{ !app('company')['enable_size'] ? 'd-none':'' }}">{{ __('item.size') }}</th>
                    @endif
                    <th>{{ __('app.qty') }}</th>
                    <th>{{ __('app.price_per_unit') }}</th>
                    <th>{{ __('app.discount') }}</th>
                    <th>{{ __('tax.tax') }}</th>
                    <th>{{ __('app.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i=1;
                @endphp

                @foreach($order->itemTransaction as $transaction)
                <tr>
                    <td class="no">{{ $i++ }}</td>
                    <td class="text-left">
                        <!-- Service Name -->
                        <b>{{ $transaction->item->name }}</b>
                        <!-- Description -->
                        <small>{{ $transaction->description }}</small>
                   </td>
                   <td>
                       {{ $transaction->item->hsn }}
                   </td>
                   @if($isHasBatchItem)
                   <td class="{{ !app('company')['enable_batch_tracking'] ? 'd-none':'' }}">
                       {{ $transaction->batch ? $transaction->batch->batch_no : '' }}
                   </td>
                   <td class="{{ !app('company')['enable_mfg_date'] ? 'd-none':'' }}">
                       {{ $transaction->batch ? $formatDate->toUserDateFormat($transaction->batch->mfg_date) : '' }}
                   </td>
                   <td class="{{ !app('company')['enable_exp_date'] ? 'd-none':'' }}">
                       {{ $transaction->batch ? $formatDate->toUserDateFormat($transaction->batch->exp_date) : '' }}
                   </td>
                   <td class="{{ !app('company')['enable_model'] ? 'd-none':'' }}">
                       {{ $transaction->batch ? $transaction->batch->model_no : ''}}
                   </td>
                   <td class="{{ !app('company')['show_mrp'] ? 'd-none':'' }} text-end">
                       {{ $transaction->batch ? $formatNumber->formatWithPrecision($transaction->batch->mrp) : '0.00' }}
                   </td>
                   <td class="{{ !app('company')['enable_color'] ? 'd-none':'' }}">
                       {{ $transaction->batch ? $transaction->batch->color :'' }}
                   </td>
                   <td class="{{ !app('company')['enable_size'] ? 'd-none':'' }}">
                       {{ $transaction->batch ? $transaction->batch->size : '' }}
                   </td>
                   @endif
                   <td class="text-end">
                        {{ $formatNumber->formatQuantity($transaction->quantity) }}
                    </td>
                    <td class=" text-end">
                        {{ $formatNumber->formatWithPrecision(calculatePrice($transaction->unit_price, $transaction->tax->rate,  needInclusive: ($transaction->tax_type == 'inclusive' ? true : false) )) }}
                    </td>
                    <td class=" text-end">
                        {{ $formatNumber->formatWithPrecision($transaction->discount_amount) }}<br>
                        <small>
                            ({{ $formatNumber->formatWithPrecision($transaction->discount_amount) }}
                                {{ ($transaction->discount_type == 'fixed') ? '$' : '%' }})
                        </small>
                    </td>
                    <td class="text-end">
                        {{ $formatNumber->formatWithPrecision($transaction->tax_amount) }}<br>
                        <small>({{ $transaction->tax->rate }}%)</small>
                    </td>
                    <td class="text-end">
                        {{ $formatNumber->formatWithPrecision($transaction->total) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                @php
                $subtotal = $order->itemTransaction->sum(function ($transaction) {
                            if($transaction->tax_type == 'inclusive'){
                                $unitPrice = calculatePrice($transaction->unit_price, $transaction->tax->rate, needInclusive: true);
                            }else{
                                $unitPrice = calculatePrice($transaction->unit_price, $transaction->tax->rate, needInclusive: false);
                            }
                                return $unitPrice * $transaction->quantity;
                        });
                $discount = $order->itemTransaction->sum(function ($transaction) {
                            return $transaction->discount_amount;
                        });

                $taxAmount = $order->itemTransaction->sum(function ($transaction) {
                            return $transaction->tax_amount;
                        });

                @endphp
                <tr>
                    <td colspan="{{ 3+ $totalBatchTrackingRowCount }}" rowspan="4" class="tfoot-first-td">
                        <span class="invoice-note">Note:<br></span>{{ $order->note }}
                    </td>
                    <td colspan="2" class="text-end fw-bold">{{ __('app.subtotal') }}</td>
                    <td colspan="1" class="text-end">{{ $formatNumber->formatWithPrecision($subtotal) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-end fw-bold">{{ __('app.discount') }}</td>
                    <td colspan="1" class="text-end">{{ $formatNumber->formatWithPrecision($discount) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-end fw-bold">{{ __('tax.tax') }}</td>
                    <td colspan="1" class="text-end">{{ $formatNumber->formatWithPrecision($taxAmount) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-end fw-bold">{{ __('app.round_off') }}</td>
                    <td colspan="1" class="text-end">{{ $formatNumber->formatWithPrecision($order->round_off) }}</td>
                </tr>
                <tr>
                    <td colspan="{{ 3+ $totalBatchTrackingRowCount }}" rowspan="3" class="tfoot-first-td">
                        <b>{{ __('app.amount_in_words') }}:<br></b>
                    </td>
                    <td colspan="2" class="text-end fw-bold">{{ __('app.grand_total') }}</td>
                    <td colspan="1" class="text-end">{{ $formatNumber->formatWithPrecision($order->grand_total) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-end fw-bold">{{ __('payment.paid_amount') }}</td>
                    <td colspan="1" class="text-end">{{$formatNumber->formatWithPrecision($order->paid_amount)}}</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-end fw-bold">{{ __('payment.balance') }}</td>
                    <td colspan="1" class="text-end">{{$formatNumber->formatWithPrecision($order->grand_total - $order->paid_amount)}}</td>
                </tr>

            </tfoot>
        </table>
        @if(app('company')['show_tax_summary'])
        <table class="table table-bordered custom-table tax-breakdown table-compact">
            <thead>
                @if(app('company')['tax_type'] == 'tax')
                    <tr>
                        <th>{{ __('tax.tax') }}</th>
                        <th>{{ __('tax.taxable_amount') }}</th>
                        <th>{{ __('tax.rate') }}</th>
                        <th>{{ __('tax.tax_amount') }}</th>
                    </tr>
                 @else
                    {{-- GST --}}
                     <tr>
                        <th rowspan="2">{{ __('item.hsn') }}</th>
                        <th rowspan="2">{{ __('tax.taxable_amount') }}</th>
                        <th colspan="2" class="text-center">{{ __('tax.cgst') }}</th>
                        <th colspan="2" class="text-center">{{ __('tax.sgst') }}</th>
                        <th colspan="2" class="text-center">{{ __('tax.igst') }}</th>
                        <th rowspan="2">{{ __('tax.tax_amount') }}</th>
                    </tr>
                    <tr>
                        <th>{{ __('tax.rate') }}%</th>
                        <th>{{ __('app.amount') }}</th>
                        <th>{{ __('tax.rate') }}%</th>
                        <th>{{ __('app.amount') }}</th>
                        <th>{{ __('tax.rate') }}%</th>
                        <th>{{ __('app.amount') }}</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @php

                if(app('company')['tax_type'] == 'tax'){
                    $taxSummary = $order->itemTransaction
                        ->groupBy('tax_id')
                        ->map(function ($group) {
                            $firstItem = $group->first();
                            $totalTaxableAmount = $group->sum(function ($item) use ($firstItem) {
                                $totalOfEachItem = ($item->unit_price-$item->discount_amount) * $item->quantity;
                                if ($item->tax_type == 'inclusive') {
                                    return calculatePrice($totalOfEachItem, $firstItem->tax->rate, needInclusive: true);
                                } else {
                                    return calculatePrice($totalOfEachItem, $firstItem->tax->rate, needInclusive: false);
                                }
                            });
                            return [
                                'tax_id' => $firstItem->tax_id,
                                'tax_name' => $firstItem->tax->name,
                                'tax_rate' => $firstItem->tax->rate,
                                'total_taxable_amount' => $totalTaxableAmount,
                                'total_tax' => $group->sum('tax_amount')
                            ];
                        })
                        ->values();
                }
                else{
                    //GST
                    $taxSummary = $order->itemTransaction
                    ->groupBy('item.hsn') // First group by HSN
                    ->map(function ($hsnGroup) {
                        return $hsnGroup->groupBy('tax_id') // Then group by tax_id within each HSN group
                            ->map(function ($group) {
                                $firstItem = $group->first();
                                $totalTaxableAmount = $group->sum(function ($item) {
                                    $totalOfEachItem = ($item->unit_price - $item->discount_amount) * $item->quantity;
                                    if ($item->tax_type == 'inclusive') {
                                        return calculatePrice($totalOfEachItem, $item->tax->rate, needInclusive: true);
                                    } else {
                                        return calculatePrice($totalOfEachItem, $item->tax->rate, needInclusive: false);
                                    }
                                });
                                return [
                                    'hsn' => $firstItem->item->hsn,
                                    'tax_id' => $firstItem->tax_id,
                                    'tax_name' => $firstItem->tax->name,
                                    'tax_rate' => $firstItem->tax->rate,
                                    'total_taxable_amount' => $totalTaxableAmount,
                                    'total_tax' => $group->sum('tax_amount')
                                ];
                            });
                    })
                    ->flatMap(function ($hsnGroup) {
                        return $hsnGroup;
                    })
                    ->values();
                }

                @endphp
                @foreach($taxSummary as $summary)
                    @if(app('company')['tax_type'] == 'tax')
                    <tr>
                        <td>{{ $summary['tax_name'] }}</td>
                        <td class="text-end">{{ $formatNumber->formatWithPrecision($summary['total_taxable_amount']) }}</td>
                        <td class="text-center">{{ $summary['tax_rate'] }}%</td>
                        <td class="text-end">{{ $formatNumber->formatWithPrecision($summary['total_tax']) }}</td>
                    </tr>
                    @else
                    <tr>
                        <td>{{ $summary['hsn'] }}</td>
                        <td class="text-end">{{ $formatNumber->formatWithPrecision($summary['total_taxable_amount']) }}</td>

                        @php
                            $cs_gst = $i_gst = '';
                            $cs_gst_amt = $i_gst_amt = '';
                            if(app('company')['state_id'] == $order->state_id){
                                $cs_gst = ($summary['tax_rate']/2).'%';
                                $cs_gst_amt = $formatNumber->formatWithPrecision($summary['total_tax']/2);
                            }else{
                                $i_gst = ($summary['tax_rate']).'%';
                                $i_gst_amt = $formatNumber->formatWithPrecision($summary['total_tax']);
                            }
                        @endphp
                        <!-- CGST & SGT -->
                        <td class="text-center">{{ $cs_gst }}</td>
                        <td class="text-end">{{ $cs_gst_amt }}</td>
                        <td class="text-center">{{ $cs_gst }}</td>
                        <td class="text-end">{{ $cs_gst_amt }}</td>
                        <!-- IGST -->
                        <td class="text-center">{{ $i_gst }}</td>
                        <td class="text-end">{{ $i_gst_amt }}</td>
                        <td class="text-end">{{ $formatNumber->formatWithPrecision($summary['total_tax']) }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        @endif
        @if(app('company')['show_terms_and_conditions_on_invoice'])
        <div class="terms-and-conditions">
            <strong>{{ __('app.terms_and_conditions') }}</strong><br>
            <span class="cu-fs-1 ">{!! nl2br(app('company')['terms_and_conditions']) !!}</span>
        </div>
        @endif

        <div class="bottom-section clearfix">
            <div class="bank-details">
                <strong>{{ __('app.bank_details') }}</strong><br>
                <p style="">{!! nl2br(app('company')['bank_details']) !!}</p>
            </div>
            <div class="signature-box">
                <div class="signature-content">
                    @if(app('company')['show_signature_on_invoice'])
                    <img src="{{ "/company/signature/getimage/".app('company')['signature'] }}" alt="Signature" class="signature-image">
                    @endif
                    <p>{{ app('company')['name'] }}</p>
                    <p>{{ __('app.authorized_signatory') }}</p>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ versionedAsset('assets/js/jquery.min.js') }}"></script>
</body>
</html>
