<!DOCTYPE html>
<html lang="ar" dir="{{ $appDirection }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('payment.receipt') }}</title>
    @include('print.common.css')
</head>
<body onload="window.print();">
    <div class="invoice-container">
        <span class="invoice-name">{{ __('payment.payment_out') }}</span>
        <div class="invoice">
            <table class="header">
                <tr>
                    @include('print.common.header')

                    <td class="bill-info">
                        <span class="bill-number">{{ __('payment.receipt_no') }} #: {{ $payment->reference_no }}</span><br>
                        <span class="cu-fs-16">{{ __('payment.date') }}: {{ $payment->formatted_transaction_date  }}</span><br>
                        <span class="cu-fs-16">{{ __('app.time') }}: {{ $payment->format_created_time  }}</span><br>
                    </td>
                </tr>
            </table>
            <table class="addresses">
                <tr>
                    <td class="address">
                        <span class="fw-bold cu-fs-18">{{ __('payment.paid_to') }}</span><br>
                        <span>{{ $purchase->party->first_name.' '. $purchase->party->last_name }}<br>
                        {{ $purchase->party->billing_address }}</span>
                    </td>
                </tr>
            </table>

        <table class="table-bordered custom-table table-compact" id="item-table">
            <thead>
                <tr>
                    <th>{{ __('payment.payment_type') }}</th>
                    <th>{{ __('payment.amount') }}</th>
                </tr>
            </thead>
            <tbody>

                <tr>

                    <td class="text-left">
                        <b>{{ $payment->paymentType->name }}</b>
                   </td>
                   <td class="text-end">
                       {{ $formatNumber->formatWithPrecision($payment->amount) }}
                   </td>
                </tr>
                <tr>
                    <td>
                        {{ __('party.due_balance') }} {{ $balanceData['status']=='you_pay' ? '(Pay)' : ($balanceData['status']=='you_collect' ? '(Collect)' : '') }}
                    </td>
                    <td class="text-end">
                       {{ $formatNumber->formatWithPrecision($balanceData['balance']) }}
                   </td>
                </tr>

            </tbody>
        </table>


        <table class="">
            <tr>
                <td class="signature">
                     @if(app('company')['show_signature_on_invoice'])
                         @php
                            if($isPdf){
                                 //No image Path
                                $defaultSignature = 'app/public/images/noimages/no-image-found.jpg';

                                //Company logo path
                                $signaturePath = 'app/public/images/signature/';

                                $signature = storage_path(
                                    !empty(app('company')['signature']) &&
                                    file_exists(storage_path($signaturePath . app('company')['signature']))
                                        ? $signaturePath . app('company')['signature']
                                        : $defaultSignature
                                );


                            }else{
                                //Routing or direct view
                                $signature = url('/company/signature/getimage/'.app('company')['signature']);
                            }
                        @endphp

                        <img src="{{ $signature }}" alt="Logo" class="company-logo">
                        @endif
                        <p>{{ app('company')['name'] }}</p>
                        <p>{{ __('app.authorized_signatory') }}</p>
                </td>
            </tr>
        </table>


    </div>
    </div>
</body>
</html>
