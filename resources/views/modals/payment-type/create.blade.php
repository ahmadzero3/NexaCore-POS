<!-- Tax Modal: start -->
<div class="modal fade" id="paymentTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >{{ __('payment.create_payment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class=" needs-validation" id="taxForm" action="{{ route('payment.type.store') }}" enctype="multipart/form-data">
                {{-- CSRF Protection --}}
                @csrf
                @method('POST')
                <div class="modal-body row g-3">
                        <div class="col-md-12">
                            <x-label for="name" name="{{ __('app.name') }}" />
                            <x-input type="text" name="name" :required="true" value="" :autofocus='true'/>
                        </div>
                        <div class="col-md-12">
                            <x-label for="description" name="{{ __('app.description') }}" />
                            <x-textarea name="description" value=""/>
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