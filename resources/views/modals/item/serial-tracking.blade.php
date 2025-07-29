<!-- Tax Modal: start -->
<div class="modal fade" id="serialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >{{ __('item.serial_or_imei_number') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <div class="modal-body row g-3">
                    
                    <div class="col-md-12">
                        <x-label for="serial_number" name="{{ __('item.serial_number') }}" />
                        <div class="input-group mb-3">
                            <input type="text" id="serial_number" class="form-control" />
                            <button class="btn btn-outline-secondary serial_number_add_btn" type="button">{{ __('app.add') }}</button>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-bordered" id="serial_number_table">
                            <thead>
                                <tr>
                                    <th class="text-center">{{ __('item.serial_or_imei_number') }}</th>
                                    <th class="text-center">{{ __('app.action') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <label class="fst-italic" id="" data-name="">
                    <span class="text-danger">{{ __('app.note') }}</span>: {{ __('app.save_data_before_close_window') }}
                    </label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.close') }}</button>
                    <x-button type="button" class="btn btn-primary setSerial" text="{{ __('app.save') }}" />
                </div>
            
        </div>
    </div>
</div>
<!-- Tax Modal: end -->