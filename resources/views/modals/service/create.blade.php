<!-- Tax Modal: start -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >{{ __('service.create') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class=" needs-validation" id="serviceForm" action="{{ route('service.store') }}" enctype="multipart/form-data">
                {{-- CSRF Protection --}}
                @csrf
                @method('POST')
                <div class="modal-body row g-3">
                        <div class="col-md-6">
                            <x-label for="name" name="{{ __('app.name') }}" />
                            <x-input type="text" name="name" :required="true" value=""/>
                        </div>
                        <div class="col-md-6">
                            <x-label for="unit_price" name="{{ __('app.unit_price') }}" />
                            <x-input type="text" name="unit_price" :required="true" value=""/>
                        </div>
                        <div class="col-md-6">
                            <x-label for="tax_id" name="{{ __('tax.tax') }}" />
                            <x-drop-down-taxes selected="" />
                        </div>
                        <div class="col-md-6">
                            <x-label for="tax_type" name="{{ __('tax.tax_type') }}" />
                            <x-dropdown-status selected="" dropdownName='tax_type' optionNaming='InclusiveExclusive'/>
                        </div>
                        <div class="col-md-6">
                            <x-label for="description" name="{{ __('app.description') }}" />
                            <x-textarea name="description" value=""/>
                        </div>
                        <!-- Hidden Fields -->
                        <x-input type="hidden" name="status" value="1"/>
                    
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