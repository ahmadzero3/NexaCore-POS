<!-- Tax Modal: start -->
<div class="modal fade" id="chequeTransferModal" >
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title form-heading" >{{ __('payment.transfer_cheque') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class=" needs-validation" id="chequeTransferForm" action="{{ route('cheque.deposit.store') }}" enctype="multipart/form-data">
                {{-- CSRF Protection --}}
                @csrf
                @method('POST')
                <div class="modal-body row g-3">
                        <div class="col-md-12">
                            <x-label for="received_from" id="label_deposit_or_transfer" name="{{ __('payment.received_from') }}" />
                            <input type="text" class="form-control cursor-not-allowed" readonly id="received_from" value="">
                        </div>
                        <div class="col-md-12">
                            <x-label for="transfer_to_payment_type_id" id="label_transfer_from_or_to" name="{{ __('payment.deposit_to') }}" />
                            <x-dropdown-payment-type :dontShowCheque="true" paymentTypeName="transfer_to_payment_type_id" selected="" />
                        </div>
                        
                        <div class="col-md-12">
                            <x-label for="amount" name="{{ __('payment.amount') }}" />
                            <x-input type="text" additionalClasses="cu_numeric text-end cursor-not-allowed" name="amount" value="" :readonly='true'/>
                        </div>

                        <div class="col-md-12">
                            <x-label for="transfer_date" name="{{ __('payment.transfer_date') }}" />
                            <div class="input-group">
                                <x-input type="text" additionalClasses="datepicker" name="transfer_date" :required="true" value=""/>
                                <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                            </div>
                        </div>

                        
                        <div class="col-md-12">
                            <x-label for="note" name="{{ __('app.note') }}" />
                            <x-textarea name="note" value=""/>
                        </div>
                        <!-- Hidden Fields -->
                        <x-input type="hidden" name="cheque_transaction_id" value=""/>
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