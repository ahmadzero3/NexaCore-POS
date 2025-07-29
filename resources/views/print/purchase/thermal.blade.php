<!DOCTYPE html>
<html lang="en" dir="{{ $appDirection }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoiceData['name'] }}</title>
    <link href="{{ versionedAsset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ versionedAsset('custom/css/print/pos.css') }}"/>
</head>
<body onload="window.print();">
    <div class="invoice-wrapper">
        <div class="container mt-3">
            <div class="invoice-header">
                <div class="invoice-title">{{ app('company')['name'] }}</div>
                <div>
                    {{ app('company')['address'] }}
                        <p>
                            @if(app('company')['mobile'] || app('company')['email'])
                                {{ app('company')['mobile'] ? 'M: '. app('company')['mobile'] : ''}}{{ app('company')['email'] ? ', Mail: '.app('company')['email'] : '' }}
                            @endif
                        </p>
                </div>
            </div>

            <div class="text-center">--------{{ $invoiceData['name'] }}--------</div>

            <div class="row">
                <div class="col-6">
                    <div>{{ __('app.name') }}: {{ $purchase->party->first_name.' '. $purchase->party->last_name }}</div>
                    <div>{{ __('app.mobile') }}: {{ $purchase->party->mobile }}</div>
                    <div>{{ __('purchase.seller') }}: {{ $purchase->user->first_name.' '. $purchase->user->last_name }}</div>
                </div>
                <div class="col-6 text-end">
                    <div>{{ __('purchase.invoice') }}: #SL0040</div>
                    <div>{{ __('app.date') }}: {{ $purchase->formatted_purchase_date  }}</div>
                    <div>{{ __('app.time') }}: {{ $purchase->format_created_time  }}</div>
                </div>
            </div>

            <table class="table table-sm mt-2">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('app.description') }}</th>
                        <th class="text-end">{{ __('app.price_per_unit') }}</th>
                        <th class="text-end">{{ __('app.qty') }}</th>
                        <th class="text-end">{{ __('app.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i=1;
                    @endphp

                    @foreach($purchase->itemTransaction as $transaction)
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>
                            {{ $transaction->item->name }}
                            <small>{{ $transaction->description }}</small>
                            <small>
                                @if ($transaction->itemSerialTransaction->count() > 0)
                                    <br>{{ $transaction->itemSerialTransaction->pluck('itemSerialMaster.serial_code')->implode(',') }}<br>
                                @endif
                            </small>
                        </td>
                        <td class="text-end">{{ $formatNumber->formatWithPrecision($transaction->total/$transaction->quantity) }}</td>
                        <td class="text-end">{{ $formatNumber->formatQuantity($transaction->quantity) }}</td>


                        {{--
                            Note:
                                Calculate Total = (Unit Price - Discount) + Tax
                                Here we are showing only Total, in below destriburted the discount and Tax
                        --}}
                        <td class="text-end">{{ $formatNumber->formatWithPrecision($transaction->total) }}</td>



                    </tr>
                    @endforeach

                    @php
                       $totalQty = $purchase->itemTransaction->sum(function ($transaction) {
                            return $transaction->quantity;
                        });
                    @endphp
                    <tr class="text-end fw-bold">
                        <td colspan="3">{{ __('app.total') }}</td>
                        <td>{{ $formatNumber->formatQuantity($totalQty) }}</td>
                        <td>{{ $formatNumber->formatWithPrecision($purchase->grand_total) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="row text-end">
                @php
                    $subtotal = $purchase->itemTransaction->sum(function ($transaction) {
                                /*if($transaction->tax_type == 'inclusive'){
                                    $unitPrice = calculatePrice($transaction->unit_price, $transaction->tax->rate, needInclusive: true);
                                }else{
                                    $unitPrice = calculatePrice($transaction->unit_price, $transaction->tax->rate, needInclusive: false);
                                }*/
                                $unitPrice = $transaction->unit_price;
                                return $unitPrice * $transaction->quantity;
                            });
                    $discount = $purchase->itemTransaction->sum(function ($transaction) {
                                return $transaction->discount_amount;
                            });

                    $taxAmount = $purchase->itemTransaction->sum(function ($transaction) {
                                return $transaction->tax_amount;
                            });

                @endphp
                <div class="col-8 text-end"><strong>{{ __('app.subtotal') }}</strong></div>
                <div class="col-4">{{ $formatNumber->formatWithPrecision($subtotal) }}</div>

                @if(app('company')['show_discount'])
                <div class="col-8 text-end"><strong>{{ __('app.discount') }}</strong></div>
                <div class="col-4">{{ $formatNumber->formatWithPrecision(-$discount) }}</div>
                @endif

                @if(app('company')['tax_type'] != 'no-tax')
                    <div class="col-8 text-end"><strong>{{ __('tax.tax') }}</strong></div>
                    <div class="col-4">{{ $formatNumber->formatWithPrecision($taxAmount) }}</div>
                @endif

                <div class="col-8 text-end"><strong>{{ __('app.round_off') }}</strong></div>
                <div class="col-4">{{ $formatNumber->formatWithPrecision($purchase->round_off) }}</div>

                <div class="col-8 text-end"><strong>{{ __('app.grand_total') }}</strong></div>
                <div class="col-4">{{ $formatNumber->formatWithPrecision($purchase->grand_total) }}</div>

                <div class="col-8 text-end"><strong>{{ __('payment.paid_amount') }}</strong></div>
                <div class="col-4">{{ $formatNumber->formatWithPrecision($purchase->paid_amount) }}</div>

                <div class="col-8 text-end"><strong>{{ __('payment.balance') }}</strong></div>
                <div class="col-4">{{ $formatNumber->formatWithPrecision($purchase->grand_total - $purchase->paid_amount) }}</div>


                @if(app('company')['show_mrp'])
                    @php
                        $savedAmount = $purchase->itemTransaction->sum(function ($transaction) {
                                    if($transaction->mrp > 0){
                                        return ($transaction->mrp * $transaction->quantity) - $transaction->total;
                                    }else{
                                        return 0;
                                    }
                            });

                    @endphp
                    <div class="col-8 text-end"><strong>{{ __('app.you_saved') }}</strong></div>
                    <div class="col-4">{{ $formatNumber->formatWithPrecision($savedAmount) }}</div>
                @endif

                @if(app('company')['show_party_due_payment'])
                    @php
                        $partyTotalDue = $purchase->party->getPartyTotalDueBalance();
                        $partyTotalDueBalance = $partyTotalDue['balance'];
                    @endphp
                <tr>
                    <div class="col-8 text-end"><strong>{{ __('app.previous_due') }}</strong></div>
                    <div class="col-4">{{ $formatNumber->formatWithPrecision($partyTotalDueBalance - ($purchase->grand_total - $purchase->paid_amount) ) }}</div>
                </tr>
                <tr>
                    <div class="col-8 text-end"><strong>{{ __('app.total_due_balance') . ($partyTotalDue['status'] == 'you_pay' ? '(You Pay)' : '(Receive)') }}</strong></div>
                    <div class="col-4">{{ $formatNumber->formatWithPrecision($partyTotalDueBalance) }}</div>
                </tr>
                @endif


            </div>

            @if(app('company')['show_tax_summary'] && app('company')['tax_type'] != 'no-tax')
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
                        <th colspan="2" class="text-center">{{ __('tax.gst') }}</th>
                        <th rowspan="2">{{ __('tax.tax_amount') }}</th>
                    </tr>
                    <tr>
                        <th>{{ __('tax.rate') }}%</th>
                        <th>{{ __('app.amount') }}</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @php

                if(app('company')['tax_type'] == 'tax'){
                    $taxSummary = $purchase->itemTransaction
                        ->groupBy('tax_id')
                        ->map(function ($group) {
                            $firstItem = $group->first();
                            $totalTaxableAmount = $group->sum(function ($item) use ($firstItem) {
                                $totalOfEachItem = ($item->unit_price-$item->discount_amount) * $item->quantity;
                                return $totalOfEachItem;
                                /*
                                if ($item->tax_type == 'inclusive') {
                                    return calculatePrice($totalOfEachItem, $firstItem->tax->rate, needInclusive: true);
                                } else {
                                    return calculatePrice($totalOfEachItem, $firstItem->tax->rate, needInclusive: false);
                                }*/
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
                    $taxSummary = $purchase->itemTransaction
                    ->groupBy('item.hsn') // First group by HSN
                    ->map(function ($hsnGroup) {
                        return $hsnGroup->groupBy('tax_id') // Then group by tax_id within each HSN group
                            ->map(function ($group) {
                                $firstItem = $group->first();
                                $totalTaxableAmount = $group->sum(function ($item) {
                                    $totalOfEachItem = ($item->unit_price - $item->discount_amount) * $item->quantity;
                                    return $totalOfEachItem;
                                    /*
                                    if ($item->tax_type == 'inclusive') {
                                        return calculatePrice($totalOfEachItem, $item->tax->rate, needInclusive: true);
                                    } else {
                                        return calculatePrice($totalOfEachItem, $item->tax->rate, needInclusive: false);
                                    }*/
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
                        @php
                            $isCSGST = (empty($purchase->state_id) || app('company')['state_id'] == $purchase->state_id) ? true:false;
                        @endphp
                        <td>{{ $summary['hsn'] }}</td>
                        <td class="text-end">{{ $formatNumber->formatWithPrecision($summary['total_taxable_amount']) }}</td>

                        @php
                            $cs_gst = $i_gst = '';
                            $cs_gst_amt = $i_gst_amt = '';
                            if($isCSGST){
                                $cs_gst = ($summary['tax_rate']/2).'%';
                                $cs_gst_amt = $formatNumber->formatWithPrecision($summary['total_tax']/2);
                            }else{
                                $i_gst = ($summary['tax_rate']).'%';
                                $i_gst_amt = $formatNumber->formatWithPrecision($summary['total_tax']);
                            }
                        @endphp
                        @if($isCSGST)
                            <!-- CGST & SGT -->
                            <td class="text-center">{{ $cs_gst }}</td>
                            <td class="text-end">{{ $cs_gst_amt }}</td>
                        @else
                            <!-- IGST -->
                            <td class="text-center">{{ $i_gst }}</td>
                            <td class="text-end">{{ $i_gst_amt }}</td>
                        @endif
                        <td class="text-end">{{ $formatNumber->formatWithPrecision($summary['total_tax']) }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        @endif


        </div>
    </div>
    <div class="container mt-3 mb-3">
        <button class="btn btn-success print-btn" onclick="window.print()">Print</button>
    </div>

    <!-- Bootstrap JS -->
    <script src="{{ versionedAsset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ versionedAsset('assets/js/jquery.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            window.print();
        });
    </script>
</body>
</html>
