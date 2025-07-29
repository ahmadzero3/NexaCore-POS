<!-- Tax Modal: start -->
<div class="modal fade" id="invoicePaymentModal" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title form-heading" >{{ __('payment.make_payment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class=" needs-validation" id="paymentForm" action="{{ url('payment/'. $payment_for.'/store' ) }}" enctype="multipart/form-data">
                {{-- CSRF Protection --}}
                @csrf
                @method('POST')
                <div class="modal-body row g-3">
                        <div class="col-md-6">
                            <x-label for="party_id" name="{{ __('party.party') }}" />
                            <select class="form-select" data-party-type='supplier' data-placeholder="Select Supplier" id="party_id" name="party_id"></select>
                        </div>
                        
                        <div class="col-md-6">
                            <x-label for="transaction_date" name="{{ __('app.date') }}" />
                            <div class="input-group mb-3">
                                <x-input type="text" additionalClasses="datepicker" name="transaction_date" :required="true" value=""/>
                                <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <x-label for="receipt_no" name="{{ __('payment.receipt_no') }}" />
                            <x-input type="text" name="receipt_no" value=""/>
                        </div>
                        <div class="col-md-6">
                            <x-label for="balance" name="{{ __('payment.balance') }}" />
                            <x-input type="text" additionalClasses="cu_numeric text-end" name="balance" readonly/>
                        </div>
                        <div class="col-md-6">
                            <x-label for="payment_type_id" name="{{ __('payment.payment_type') }}" />
                            <select class="form-select select2 payment-type-ajax" name="payment_type_id" data-placeholder="Choose one thing"></select>
                        </div>
                        <div class="col-md-6">
                            <x-label for="payment" name="{{ __('payment.payment') }}" />
                            <x-input type="text" additionalClasses="cu_numeric text-end" name="payment" value=""/>
                        </div>
                        <div class="col-md-6">
                            <x-label for="payment_note" name="{{ __('payment.note') }}" />
                            <x-textarea name="payment_note" value=""/>
                        </div>
                        <!-- Hidden Fields -->
                        <x-input type="hidden" name="invoice_id" value=""/>
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