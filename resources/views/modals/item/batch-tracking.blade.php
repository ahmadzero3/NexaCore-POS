<!-- Tax Modal: start -->
<div class="modal fade" id="batchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >{{ __('item.batch_number') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <div class="modal-body row g-3">
                    
                    <div class="col-md-12">
                        <table class="table table-bordered" id="batch_number_table">
                            <thead>
                                <tr>
                                    <th class="text-center">{{ __('item.batch') }}</th>
                                    <th class="text-center {{ !app('company')['enable_mfg_date'] ? 'd-none':'' }}">{{ __('item.mfg_date') }}</th>
                                    <th class="text-center {{ !app('company')['enable_exp_date'] ? 'd-none':'' }}">{{ __('item.exp_date') }}</th>
                                    <th class="text-center {{ !app('company')['enable_model'] ? 'd-none':'' }}">{{ __('item.model_no') }}</th>
                                    <th class="text-center {{ !app('company')['show_mrp'] ? 'd-none':'' }}">{{ __('item.mrp') }}</th>
                                    <th class="text-center {{ !app('company')['enable_color'] ? 'd-none':'' }}">{{ __('item.color') }}</th>
                                    <th class="text-center {{ !app('company')['enable_size'] ? 'd-none':'' }}">{{ __('item.size') }}</th>
                                    <th class="text-center">{{ __('item.opening_quantity') }}</th>
                                    <th class="text-center">{{ __('app.action') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <button class="btn btn-outline-secondary add_row" type="button">{{ __('app.add_row') }}</button>
                    </div>
                    <label class="fst-italic" id="" data-name="">
                    <span class="text-danger">{{ __('app.note') }}</span>: {{ __('app.save_data_before_close_window') }}
                    </label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.close') }}</button>
                    <x-button type="button" class="btn btn-primary saveBatch" text="{{ __('app.save') }}" />
                </div>
            
        </div>
    </div>
</div>
<!-- Tax Modal: end -->