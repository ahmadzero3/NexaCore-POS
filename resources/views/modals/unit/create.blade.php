<!-- Tax Modal: start -->
<div class="modal fade" id="unitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >{{ __('unit.select_unit') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <div class="modal-body row g-3">
                        <div class="col-md-12">
                            <x-label for="primary" name="{{ __('unit.base') }}" />
                            <x-dropdown-units selected="" dropdownName='base_unit_id'/>
                        </div>
                        <div class="col-md-12">
                            <x-label for="primary" name="{{ __('unit.secondary') }}" />
                            <x-dropdown-units selected="" dropdownName='secondary_unit_id'/>
                        </div>
                        <div class="col-md-12">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="base-text"></span>
                                <input type="text" class="form-control bg-light text-primary cu_numeric text-center fw-bold" name='conversion_rate' placeholder="Greater then 0" >
                                <span class="input-group-text text-success" id="secondary-text"></span>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3" role="alert">
                            <h6 class="fw-bold text-primary">
                                {{ __('app.example') }}:
                            </h6>
                            <ul class="mb-0">
                                <li>
                                    <span class="text-dark">1 Kilogram</span> = <b>1000</b><span class="text-success"> Gram</span>
                                </li>
                                <li>
                                    <span class="text-dark">1 Box</span> = <b>12</b><span class="text-success"> Bottle</span>
                                </li>
                            </ul>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.close') }}</button>
                    <x-button type="button" class="btn btn-primary setUnits" text="{{ __('app.save') }}" />
                </div>

        </div>
    </div>
</div>
<!-- Tax Modal: end -->
