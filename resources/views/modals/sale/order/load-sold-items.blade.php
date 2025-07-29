<!-- Load Sold Items Modal: start -->
<div class="modal fade" id="loadSoldItemsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >{{ __('sale.sold_items_history') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class=" needs-validation" id="soldItemsForm" action="{{ url('sale/invoice/sold-items/' ) }}" enctype="multipart/form-data">
                {{-- CSRF Protection --}}
                @csrf
                @method('POST')
                <div class="modal-body row g-3">
                    <div class="mb-0">
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-4">
                                <h6 class="fw-bold mb-2">{{ __('party.details') }}</h6>
                                <p class="mb-1 fw-bold"><small class="text-muted">{{ __('party.name') }}:</small><span id="party-name" class=""></span></p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <x-label for="party_id" name="{{ __('item.item_name') }}" />
                                <div class="input-group">
                                    <select class="form-select item-ajax" data-placeholder="Select Item/Keep Empty to load all" id="modal_item_id" name="modal_item_id">
                                    </select>
                                    <button type="button" class="input-group-text load-sold-items btn btn-outline-primary">
                                        <i class='bx bx-search'></i>{{ __('app.search') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-12 table-responsive">
                        <table class="table table-bordered" id="payment-history-table">
                            <thead>
                                <tr>
                                    <th class="text-center">{{ __('sale.code') }}</th>
                                    <th class="text-center">{{ __('sale.date') }}</th>
                                    <th class="text-center">{{ __('warehouse.warehouse') }}</th>
                                    <th class="text-center">{{ __('item.item_name') }}</th>
                                    <th class="text-center">{{ __('item.brand.brand') }}</th>
                                    <th class="text-center">{{ __('app.price') }}</th>
                                    <th class="text-center">{{ __('item.quantity') }}</th>
                                    @if(app('company')['show_discount'])
                                    <th class="text-center">{{ __('item.discount') }}</th>
                                    @endif
                                    @if(app('company')['tax_type'] != 'no-tax')
                                    <th class="text-center">{{ __('tax.tax') }}</th>
                                    @endif
                                    <th class="text-center">{{ __('app.total') }}</th>
                                    {{-- <th class="text-center">{{ __('app.action') }}</th> --}}
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.close') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Tax Modal: end -->
