<div class="cu-fs-16-only">
    @if($bankDetails && $bankDetails->count() > 0)
        <i>{{__('payment.bank_name')}}:</i> {{ $bankDetails->name }}<br>
        <i>{{__('payment.account_number')}}:</i> {{ $bankDetails->account_number }}<br>
        <i>{{__('payment.bank_code')}}:</i> {{ $bankDetails->bank_code }}<br>
        <i>{{__('app.other_details')}}:</i> {!! nl2br($bankDetails->description) !!}<br>
    @endif
</div>
