<!DOCTYPE html>
<html lang="ar" dir="{{ $appDirection }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $printData['name'] }}</title>
    @include('print.common.css')
</head>
<body onload="window.print();">
    <div class="invoice-container">
        <span class="invoice-name">{{ $printData['name'] }}</span>
        <div class="invoice">
            <table class="header">
                <tr>
                    @include('print.common.header')

                    <td class="bill-info">
                        <span class="bill-number">#: {{ $transfer->transfer_code }}</span><br>
                        <span class="cu-fs-16">{{ __('app.date') }}: {{ $transfer->formatted_transfer_date }}</span><br>
                        <span class="cu-fs-16">{{ __('app.time') }}: {{ $transfer->format_created_time }}</span><br>
                    </td>
                </tr>
            </table>

         @php
            $isHasBatchItem = ($transfer->itemTransaction->where('tracking_type', 'batch')->count() > 0) ? true : false;

            //Return from Controller
            $totalBatchTrackingRowCount = ($isHasBatchItem) ? $batchTrackingRowCount : 0;
        @endphp
        <table class="table-bordered custom-table table-compact" id="item-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('warehouse.from_warehouse') }}</th>
                    <th>{{ __('warehouse.to_warehouse') }}</th>
                    <th>{{ __('item.item') }}</th>
                    @if(app('company')['show_hsn'])
                    <th>{{ __('item.hsn') }}</th>
                    @endif
                    @if($isHasBatchItem)
                        @if(app('company')['enable_batch_tracking'])
                        <th>{{ __('item.batch_no') }}</th>
                        @endif
                        @if(app('company')['enable_mfg_date'])
                        <th>{{ __('item.mfg_date') }}</th>
                        @endif
                        @if(app('company')['enable_exp_date'])
                        <th>{{ __('item.exp_date') }}</th>
                        @endif
                        @if(app('company')['enable_model'])
                        <th>{{ __('item.model_no') }}</th>
                        @endif

                        @if(app('company')['enable_color'])
                        <th>{{ __('item.color') }}</th>
                        @endif
                        @if(app('company')['enable_size'])
                        <th>{{ __('item.size') }}</th>
                        @endif
                    @endif
                    @if(app('company')['show_mrp'])
                    <th>{{ __('item.mrp') }}</th>
                    @endif
                    <th>{{ __('app.qty') }}</th>


                </tr>
            </thead>
            <tbody>
                @php
                    $i=1;
                @endphp

                @foreach($transfer->itemTransaction as $transaction)
                <tr>
                   <td class="no">{{ $i++ }}</td>
                   <td>
                    {{ $transaction->itemStockTransfer->fromWarehouse->name }}
                   </td>
                   <td>
                    {{ $transaction->itemStockTransfer->toWarehouse->name }}
                   </td>
                    <td class="">
                        <!-- Service Name -->
                        <b>{{ $transaction->item->name }}</b>
                        <!-- Description -->
                        <small>{{ $transaction->description }}</small>
                        <small>
                            @if ($transaction->itemSerialTransaction->count() > 0)
                                <br>{{ $transaction->itemSerialTransaction->pluck('itemSerialMaster.serial_code')->implode(',') }}<br>
                            @endif
                        </small>
                   </td>
                   @if(app('company')['show_hsn'])
                   <td>
                       {{ $transaction->item->hsn }}
                   </td>
                   @endif
                   @if($isHasBatchItem)

                       @if(app('company')['enable_batch_tracking'])
                       <td>
                           {{ $transaction->batch ? $transaction->batch->itemBatchMaster->batch_no : '' }}
                       </td>
                       @endif
                       @if(app('company')['enable_mfg_date'])
                       <td>
                           {{ $transaction->batch ? $formatDate->toUserDateFormat($transaction->batch->itemBatchMaster->mfg_date) : '' }}
                       </td>
                       @endif
                       @if(app('company')['enable_exp_date'])
                       <td>
                           {{ $transaction->batch ? $formatDate->toUserDateFormat($transaction->batch->itemBatchMaster->exp_date) : '' }}
                       </td>
                       @endif
                       @if(app('company')['enable_model'])
                       <td>
                           {{ $transaction->batch ? $transaction->batch->itemBatchMaster->model_no : ''}}
                       </td>
                       @endif

                       @if(app('company')['enable_color'])
                       <td>
                           {{ $transaction->batch ? $transaction->batch->itemBatchMaster->color :'' }}
                       </td>
                       @endif
                       @if(app('company')['enable_size'])
                       <td>
                           {{ $transaction->batch ? $transaction->batch->itemBatchMaster->size : '' }}
                       </td>
                       @endif
                   @endif
                   @if(app('company')['show_mrp'])
                   <td>
                       {{ $formatNumber->formatWithPrecision($transaction->batch ? $transaction->batch->itemBatchMaster->mrp : $transaction->mrp)}}
                   </td>
                   @endif
                   <td class="text-end">
                        {{ $formatNumber->formatQuantity($transaction->quantity) }}
                    </td>

                </tr>
                @endforeach
                <tr class="fw-bold">
                    <td class="text-end" colspan="{{ 4 + $totalBatchTrackingRowCount + app('company')['show_mrp'] + app('company')['show_hsn'] }}">
                            {{ __('app.total') }}
                    </td>
                    <td class="text-end">
                            {{ $formatNumber->formatWithPrecision($transfer->itemTransaction->sum('quantity')) }}
                    </td>


                </tr>
            </tbody>

        </table>



        @include('print.common.bank-signature', ['hideBankDetails'=> true])


    </div>
    </div>
</body>
</html>
