<!-- Tax Modal: start -->
<div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >{{ __('item.create') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class=" needs-validation" id="itemForm" action="{{ route('item.store') }}" enctype="multipart/form-data">
                {{-- CSRF Protection --}}
                @csrf
                @method('POST')
                <input type="hidden" id="operation" name="operation" value="save">
                <input type="hidden" name="serial_number_json" value=''>
                <input type="hidden" name="batch_details_json" value=''>
                <x-input type="hidden" name="status" value="1"/>

                <div class="modal-body row g-3">



                        <div class="col-md-6">
                            <x-label for="name" name="{{ __('app.name') }}" />
                            <x-input type="text" name="name" :required="true" value=""/>
                        </div>
                        <div class="col-md-6">
                            <x-label for="name" name="{{ __('item.item_type') }}" />
                            <select class="form-select " id="is_service" name="is_service" >
                                    <option value="0">Product</option>
                                    <option value="1">Service</option>
                            </select>
                        </div>
                        @if(app('company')['show_hsn'])
                        <div class="col-md-6">
                            <x-label for="hsn" name="{{ __('item.hsn') }}" />
                            <x-input type="text" name="hsn" :required="false" value=""/>
                        </div>
                        @endif
                        @if(app('company')['show_sku'])
                        <div class="col-md-6">
                            <x-label for="sku" name="{{ __('item.sku') }}" />
                            <x-input type="text" name="sku" :required="false" value=""/>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <x-label for="hsn" name="{{ __('item.code') }}" />
                            <div class="input-group mb-3">
                                <x-input type="text" name="item_code" :required="true" value=""/>
                                <button class="btn btn-outline-secondary auto-generate-code" type="button">{{ __('app.auto') }}</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <x-label for="brand_id" name="{{ __('item.brand.brand') }}" />
                            <x-dropdown-brand selected="" :showSelectOptionAll=true />
                        </div>
                        <div class="col-md-6">
                            <x-label for="item_category_id" name="{{ __('item.category.category') }}" />
                            <x-dropdown-item-category selected="" :isMultiple=false />
                        </div>
                        <div class="col-md-6">
                            <x-label for="description" name="{{ __('app.description') }}" />
                            <x-textarea name="description" value=""/>
                        </div>

                        <hr>
                        <div class="col-md-4">
                            <x-label for="primary" name="{{ __('unit.base') }}" />
                            <x-dropdown-units selected="" dropdownName='base_unit_id'/>
                        </div>
                        <div class="col-md-4">
                            <x-label for="primary" name="{{ __('unit.secondary') }}" />
                            <x-dropdown-units selected="" dropdownName='secondary_unit_id'/>
                        </div>
                        <div class="col-md-4">
                            <x-label for="conversion_rate" name="{{ __('item.conversion_rate') }}" />
                            <x-input type="text" additionalClasses="cu_numeric" name="conversion_rate" :required="false" value="1"/>
                        </div>


                        <hr>


                        <div class="col-md-12 mb-3 item-type-product">
                                <div class="d-flex align-items-center gap-3">

                                    <x-radio-block id="regular_tracking" boxName="tracking_type" text="{{ __('item.regular') }}" value="regular" boxType="radio" parentDivClass="fw-bold" :checked=true />

                                    @if(app('company')['enable_batch_tracking'])
                                    <x-radio-block id="batch_tracking" boxName="tracking_type" text="{{ __('item.batch_tracking') }}" value="batch" boxType="radio" parentDivClass="fw-bold"/>
                                    @endif

                                    @if(app('company')['enable_serial_tracking'])
                                    <x-radio-block id="serial_tracking" boxName="tracking_type" text="{{ __('item.serial_no_tracking') }}" value="serial" boxType="radio" parentDivClass="fw-bold"/>
                                    @endif

                                </div>
                        </div>
                        <ul class="nav nav-tabs nav-success" role="tablist">
                                        <li class="nav-item d-none" role="presentation">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#successhome" role="tab" aria-selected="true">
                                                <div class="d-flex align-items-center">
                                                    <div class="tab-icon"><i class='bx bx-dollar font-18 me-1'></i>
                                                    </div>
                                                    <div class="tab-title">{{ __('item.pricing') }}</div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item item-type-product d-none" role="presentation">
                                            <a class="nav-link" data-bs-toggle="tab" href="#successprofile" role="tab" aria-selected="false">
                                                <div class="d-flex align-items-center">
                                                    <div class="tab-icon"><i class='bx bx-box font-18 me-1'></i>
                                                    </div>
                                                    <div class="tab-title">{{ __('item.stock') }}</div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item d-none" role="presentation">
                                            <a class="nav-link" data-bs-toggle="tab" href="#successcontact" role="tab" aria-selected="false">
                                                <div class="d-flex align-items-center">
                                                    <div class="tab-icon"><i class='bx bx-image-add font-18 me-1'></i>
                                                    </div>
                                                    <div class="tab-title">{{ __('app.image') }}</div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content py-3">
                                        <div class="tab-pane fade show active" id="successhome" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <x-label for="sale_price" name="{{ __('item.sale_price') }}" />
                                                    <div class="input-group mb-3">
                                                        <x-input type="text" name="sale_price" :required="true" additionalClasses='cu_numeric' value="0"/>
                                                        <x-dropdown-general optionNaming="withOrWithoutTax" selected="" dropdownName='is_sale_price_with_tax'/>
                                                    </div>
                                                </div>
                                                {{-- Id company is enabled with discount then only show this else hide it --}}
                                                <div class="col-md-4 {{ app('company')['show_discount'] ? '' : 'd-none' }}">
                                                    <x-label for="discount_on_sale" name="{{ __('item.discount_on_sale') }}" />
                                                    <div class="input-group mb-3">
                                                        <x-input type="text" name="sale_price_discount" :required="false" additionalClasses='cu_numeric' value=""/>
                                                        <x-dropdown-general optionNaming="amountOrPercentage" selected="" dropdownName='sale_price_discount_type'/>
                                                    </div>
                                                </div>
                                                @if(app('company')['show_mrp'])
                                                <div class="col-md-4">
                                                    <x-label for="mrp" name="{{ __('item.mrp') }}" />
                                                    <x-input type="text" name="mrp" :required="false" additionalClasses='cu_numeric' value=""/>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <x-label for="purchase_price" name="{{ __('item.purchase_price') }}" />
                                                    <div class="input-group mb-3">
                                                        <x-input type="text" name="purchase_price" :required="false" additionalClasses='cu_numeric' value=""/>
                                                        <x-dropdown-general optionNaming="withOrWithoutTax" selected="" dropdownName='is_purchase_price_with_tax'/>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <x-label for="tax_id" name="{{ __('tax.tax') }}" />
                                                    <x-drop-down-taxes selected="" />
                                                </div>
                                                <div class="col-md-4">
                                                    <x-label for="wholesale_price" name="{{ __('item.wholesale_price') }}" />
                                                    <div class="input-group mb-3">
                                                        <x-input type="text" name="wholesale_price" :required="true" additionalClasses='cu_numeric' value="0"/>
                                                        <x-dropdown-general optionNaming="withOrWithoutTax" selected="" dropdownName='is_wholesale_price_with_tax'/>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="tab-pane fade d-none" id="successprofile" role="tabpanel">

                                           <div class="row">
                                                <div class="col-md-4">
                                                    <x-label for="warehouse_id" name="{{ __('warehouse.warehouse') }}" />
                                                    <x-dropdown-warehouse selected="" dropdownName='warehouse_id'/>
                                                </div>
                                                <div class="col-md-4">
                                                    <x-label for="transaction_date" name="{{ __('app.as_of_date') }}" />
                                                    <div class="input-group mb-3">
                                                        <x-input type="text" additionalClasses="" name="transaction_date" :required="true" value="{{ $formatDate->toUserDateFormat(date('Y-m-d')) }}"/>
                                                        <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                                    </div>
                                                </div>

                                           </div>
                                           <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <x-label for="opening_quantity" name="{{ __('item.opening_quantity') }}" />
                                                    <div class="input-group mb-3">
                                                        <x-input type="text" additionalClasses="cu_numeric" name="opening_quantity" :required="false" value=""/>

                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <x-label for="at_price" name="{{ __('item.at_price') }}" />
                                                    <x-input type="text" additionalClasses="cu_numeric" name="at_price" :required="false" value=""/>
                                                </div>

                                           </div>


                                           <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <x-label for="min_stock" name="{{ __('item.min_stock') }}" />
                                                    <x-input type="text" additionalClasses="cu_numeric" name="min_stock" :required="false" value=""/>
                                                </div>
                                                <div class="col-md-4">
                                                    <x-label for="location" name="{{ __('item.item_location') }}" />
                                                    <x-input type="text" name="item_location" :required="false" value=""/>
                                                </div>
                                           </div>

                                        </div>
                                        <div class="tab-pane fade d-none" id="successcontact" role="tabpanel">
                                            <div class="col-md-12">
                                                <x-label for="picture" name="{{ __('app.image') }}" />
                                                <x-browse-image
                                                                src="{{ url('/noimage/') }}"
                                                                name='image'
                                                                imageid='uploaded-image-1'
                                                                inputBoxClass='input-box-class-1'
                                                                imageResetClass='image-reset-class-1'
                                                                />
                                            </div>
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
