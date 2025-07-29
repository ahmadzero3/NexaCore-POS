<!-- Tax Modal: start -->
<div class="modal fade" id="smsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >{{ __('message.create_sms') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class=" needs-validation" id="smsForm" action="{{ route('sms.send') }}" enctype="multipart/form-data">
                {{-- CSRF Protection --}}
                @csrf
                @method('POST')
                
                <input type="hidden" id="fromSMSModel" value="smsModal">
                <div class="modal-body row g-3">
                        <div class="col-md-12">
                            <x-label for="mobile_numbers" name="{{ __('message.mobile_number') }}" />
                            <x-input type="text" name="mobile_numbers" :required="true" value="" placeholder="{{ __('message.comma_separator_for_multiple_numbers') }}" :autofocus='true'/>
                        </div>
                        <div class="col-md-12">
                            <x-label for="message" name="{{ __('message.message') }}" />
                            <x-textarea name="message" value=""/>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <x-button type="submit" class="btn btn-primary" text="{{ __('app.submit') }}" />
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Tax Modal: end -->