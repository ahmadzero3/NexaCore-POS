@extends('layouts.app')
@section('title', $lang['party_create'])

		@section('content')
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<x-breadcrumb :langArray="[
											'party.contacts',
											$lang['party_list'],
											$lang['party_create'],
										]"/>
				<div class="row">
					<div class="col-12 col-lg-12">
                        <div class="card">
                            <div class="card-header px-4 py-3">
                                <h5 class="mb-0">{{ $lang['party_details'] }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <form class="row g-3 needs-validation" id="partyForm" action="{{ route('party.store') }}" enctype="multipart/form-data">
                                    {{-- CSRF Protection --}}
                                    @csrf
                                    @method('POST')

                                    <input type="hidden" name="party_type" value="{{ $lang['party_type'] }}">
                                    <input type="hidden" id="operation" name="operation" value="save">
                                    <input type="hidden" id="base_url" value="{{ url('/') }}">

                                    @if($lang['party_type'] == 'customer')
                                    <div class="col-md-12 mb-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <x-radio-block id="retailer" boxName="is_wholesale_customer" text="{{ __('party.retailer') }}" value="0" boxType="radio" parentDivClass="fw-bold" :checked=true />
                                                <x-radio-block id="wholesaler" boxName="is_wholesale_customer" text="{{ __('party.wholesaler') }}" value="1" boxType="radio" parentDivClass="fw-bold"/>
                                            </div>
                                    </div>
                                    @endif
                                    <div class="col-md-6">
                                        <x-label for="first_name" name="{{ __('app.first_name') }}" />
                                        <x-input type="text" name="first_name" :required="true" value=""/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="last_name" name="{{ __('app.last_name') }}" />
                                        <x-input type="text" name="last_name" :required="false" value=""/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="email" name="{{ __('app.email') }}" />
                                        <x-input type="email" name="email" :required="false" value=""/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="phone" name="{{ __('app.phone') }}" />
                                        <x-input type="number" name="phone" :required="false" value=""/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="mobile" name="{{ __('app.mobile') }}" />
                                        <x-input type="number" name="mobile" :required="false" value=""/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="whatsapp" name="{{ __('app.whatsapp_number') }}" />
                                        <x-input type="number" name="whatsapp" :required="false" value=""/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="tax_number" name="{{ __('tax.tax_number') }}" />
                                        <x-input type="text" name="tax_number" :required="false" value=""/>
                                    </div>
                                    @if(app('company')['tax_type'] == 'gst')
                                    <div class="col-md-6">
                                        <x-label for="state_id" name="{{ __('app.state_name') }}" />
                                        <x-dropdown-states selected="" dropdownName='state_id'/>
                                    </div>
                                    @endif

                                    <div class="col-md-6 {{ !app('company')['is_enable_secondary_currency'] ? 'd-none' : '' }}">
                                        <x-label for="currency_id" name="{{ __('currency.currency') }}" />
                                        <x-dropdown-currency selected="" dropdownName='currency_id'/>
                                    </div>

                                    <div class="col-md-6">
                                        <x-label for="status" name="{{ __('app.status') }}" />
                                        <x-dropdown-status selected="" dropdownName='status'/>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="default_party" name="default_party">
                                            <label class="form-check-label" for="default_party">
                                              {{ $lang['party_type'] == 'customer' ? __('customer.default_customer') : __('supplier.default_supplier') }}
                                            </label>
                                          </div>
                                    </div>

                                    <ul class="nav nav-tabs nav-success" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#successhome" role="tab" aria-selected="true">
                                                <div class="d-flex align-items-center">
                                                    <div class="tab-icon"><i class='bx bx-map font-18 me-1'></i>
                                                    </div>
                                                    <div class="tab-title">{{ __('app.address') }}</div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item item-type-product" role="presentation">
                                            <a class="nav-link" data-bs-toggle="tab" href="#successprofile" role="tab" aria-selected="false">
                                                <div class="d-flex align-items-center">
                                                    <div class="tab-icon"><i class='bx bx-dollar font-18 me-1'></i>
                                                    </div>
                                                    <div class="tab-title">{{ __('party.credit_and_balance') }}</div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content py-3">
                                        <div class="tab-pane fade show active" id="successhome" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <x-label for="billing_address" name="{{ __('party.billing_address') }}" />
                                                    <x-textarea name="billing_address" value=""/>
                                                </div>
                                                <div class="col-md-6">
                                                    <x-label for="shipping_address" name="{{ __('party.shipping_address') }}" />
                                                    <x-textarea name="shipping_address" value=""/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="successprofile" role="tabpanel">

                                           <div class="row">
                                                <div class="col-md-4">
                                                    <x-label for="opening_balance" name="{{ __('app.opening_balance') }}" />
                                                    <div class="input-group mb-3">
                                                        <x-input type="text" additionalClasses="cu_numeric" name="opening_balance" :required="false" value=""/>

                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <x-label for="transaction_date" name="{{ __('app.as_of_date') }}" />
                                                    <div class="input-group mb-3">
                                                        <x-input type="text" additionalClasses="datepicker" name="transaction_date" :required="true" value=""/>
                                                        <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                                    </div>
                                                </div>

                                           </div>
                                           <div class="row mb-3">
                                                <div class="col-md-4 mb-3 item-type-product">
                                                    <x-label for="" name="{{ __('app.opening_balance_is') }}" />
                                                    <div class="d-flex align-items-center gap-3">

                                                        <x-radio-block id="to_pay" boxName="opening_balance_type" text="{{ __('party.to_pay') }}" value="to_pay" boxType="radio" parentDivClass="fw-bold" :checked=true />

                                                        <x-radio-block id="to_receive" boxName="opening_balance_type" text="{{ __('party.to_receive') }}" value="to_receive" boxType="radio" parentDivClass="fw-bold"/>
                                                    </div>
                                                </div>
                                           </div>

                                           <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <x-label for="credit_limit" name="{{ __('party.credit_limit') }}" />
                                                    <div class="input-group mb-3">
                                                        <x-dropdown-general optionNaming="creditLimit" selected="" dropdownName='is_set_credit_limit'/>
                                                        <x-input type="text" additionalClasses="cu_numeric" name="credit_limit" :required="false" value="0"/>
                                                    </div>
                                                </div>
                                           </div>
                                        </div>

                                    </div>
                                    <div class="col-md-12">
                                        <div class="d-md-flex d-grid align-items-center gap-3">
                                            <x-button type="submit" class="primary px-4" text="{{ __('app.submit') }}" />
                                            <x-anchor-tag href="{{ route('dashboard') }}" text="{{ __('app.close') }}" class="btn btn-light px-4" />
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
					</div>
				</div>
				<!--end row-->
			</div>
		</div>
		@endsection

@section('js')
<script src="{{ versionedAsset('custom/js/party/party.js') }}"></script>
@endsection
