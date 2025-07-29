<!-- Alert : Start-->
@php
    $session = session('record');
@endphp
@if (isset($session['type']))
<div class="alert alert-{{ $session['type'] }} border-0 bg-{{ $session['type'] }} alert-dismissible fade show py-2">
    <div class="d-flex align-items-center">
        <div class="font-35 text-white">
            @if($session['type']=='success')
            <i class='bx bxs-check-circle'></i>
            @endif
            @if($session['type']=='danger')
            <i class='bx bxs-message-square-x'></i>
            @endif
            @if($session['type']=='info')
            <i class='bx bxs-info-square'></i>
            @endif
        </div>
        <div class="ms-3">
            <h6 class="mb-0 text-white">{{ $session['status'] }}</h6>
            <div class="text-white">
                @if(isset($session['sms']) && $session['sms']!=null)
                    {{ __('message.sms_status') }} : {{ $session['sms'] }}
                @endif
                @if(isset($session['email']) && $session['email']!=null)
                    {{ __('message.email_status') }} : {{ $session['email'] }}
                @endif

                @if(isset($session['message']) && $session['message']!=null)
                    <div class="text-dark">
                    {!! $session['message'] !!}
                    </div>
                @endif

                {{ session()->forget('record') }}

            </div>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<!-- Alert : End -->
