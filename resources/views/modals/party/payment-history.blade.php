<!-- Tax Modal: start -->
<div class="modal fade" id="partyPaymentHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >{{ __('payment.history') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <div class="modal-body row g-3">
                    <div class="mb-0">
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-4">
                                <p class="mb-1"><small class="text-muted">{{ __('supplier.name') }}:</small><span id="party-name" class=""></span></p>
                                <p class="mb-1"><small class="text-muted">{{ __('payment.balance') }}:</small><span id="balance-amount" class=""></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-bordered" id="payment-history-table">
                            <thead>
                                <tr>
                                    <th class="text-center">{{ __('payment.transaction_date') }}</th>
                                    <th class="text-center">{{ __('payment.payment_direction') }}</th>
                                    <th class="text-center">{{ __('payment.receipt_no') }}</th>
                                    <th class="text-center">{{ __('payment.payment_type') }}</th>
                                    <th class="text-center">{{ __('payment.amount') }}</th>
                                    <th class="text-center">{{ __('app.action') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.close') }}</button>
                </div>
            
        </div>
    </div>
</div>
<!-- Tax Modal: end -->