<!-- Tax Modal: start -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >{{ __('message.email') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class=" needs-validation" id="emailForm" action="{{ route('email.send') }}" enctype="multipart/form-data">
                {{-- CSRF Protection --}}
                @csrf
                @method('POST')
               
                <div class="modal-body row g-3">
                        <div class="col-md-12">
                            <x-label for="email" name="{{ __('app.email') }}" />
                            <x-input type="email" name="email" :required='true' value=""/>
                        </div>
                        <div class="col-md-12">
                            <x-label for="subject" name="{{ __('message.subject') }}" />
                            <x-input type="subject" name="subject" :required='true' value=""/>
                        </div>
                        <div class="col-md-12">
                            <x-label for="content" name="{{ __('message.content') }}" />
                            <x-textarea name="content" value="" :required='true'/>
                        </div>
                        {{-- 
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="auto_attach_invoice" name="auto_attach_invoice">
                                <label class="form-check-label" for="auto_attach_invoice">
                                  {{ __('message.auto_attach_invoice') }}
                                </label>
                              </div>
                        </div>
                        --}}
                        
                        <div class="col-md-12">
                            <x-label for="message" name="{{ __('message.attachment') }}" />
                            <div class="input-group mb-3">
                                <input type="file" id="attachment" name="attachment" class="form-control">
                                <button type="button" id="removeBtn" class="btn btn-outline-secondary btn-remove" disabled>Remove</button>
                            </div>
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