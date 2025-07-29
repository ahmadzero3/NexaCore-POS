<!-- Tax Modal: start -->
<div class="modal fade" id="taxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >{{ __('tax.create_tax') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class=" needs-validation" id="taxForm" action="{{ route('tax.store') }}" enctype="multipart/form-data">
                {{-- CSRF Protection --}}
                @csrf
                @method('POST')
                <div class="modal-body row g-3">
                        <div class="col-md-12">
                            <x-label for="name" name="{{ __('app.name') }}" />
                            <x-input type="text" name="name" :required="true" value="" :autofocus='true'/>
                        </div>
                        <div class="col-md-12">
                            <x-label for="rate" name="{{ __('tax.rate') }}" />
                            <x-input type="text" name="rate" :required="true" additionalClasses='cu_numeric' value=""/>
                        </div>
                        <div class="col-md-12">
                            <x-label for="status" name="{{ __('app.status') }}" />
                            <x-dropdown-status selected="" dropdownName='status'/>
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