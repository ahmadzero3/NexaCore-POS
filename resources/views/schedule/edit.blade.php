@extends('layouts.app')
@section('title', __('schedule.update_schedule'))

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <x-breadcrumb :langArray="[
                                            'schedule.scheduling',
                                            'order.list',
                                            'schedule.update_schedule',
                                        ]"/>
                <div class="row">
                    <form class="row g-3 needs-validation" id="scheduleForm" action="{{ route('schedule.update') }}" enctype="multipart/form-data">
                        {{-- CSRF Protection --}}
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="row_count" value="0">
                        <input type="hidden" name="total_amount" value="0">
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <input type="hidden" id="base_url" value="{{ url('/') }}">
                        <div class="col-12 col-lg-12">
                            <div class="card">
                                <div class="card-header px-4 py-3">
                                    <h5 class="mb-0">{{ __('order.details') }}</h5>
                                </div>
                                <div class="card-body p-4 row g-3">
                                    
                                        <div class="col-md-6">
                                            <x-label for="customer_id" name="{{ __('customer.customer') }}" />
                                            <x-dropdown-customer selected="{{ $order->party_id }}" disabled="true" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="order_date" name="{{ __('order.date') }}" />
                                            <div class="input-group mb-3">
                                                <x-input type="text" additionalClasses="datepicker" name="order_date" :required="true" value="{{ $order->formatted_order_date }}" disabled="true"/>
                                                <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="order_code" name="{{ __('order.code') }}" />
                                            <!--  -->
                                            <div class="input-group mb-3">
                                                <x-input type="text" name="prefix_code" :required="true" placeholder="Prefix Code" value="{{ $order->order_code }}" disabled="true"/>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <x-label for="order_status" name="{{ __('app.status') }}" />
                                            <x-dropdown-order-status selected="" dropdownName='order_status'/>
                                        </div>
                                </div>
                                <div class="card-header px-4 py-3">
                                    <h5 class="mb-0">{{ __('service.services') }}</h5>
                                </div>
                                <div class="card-body p-4 row g-3">
                                        <div class="col-md-12 table-responsive">
                                            <table class="table mb-0 table-striped table-bordered" id="orderTable">
                                                <thead>
                                                    <tr>
                                                        <th class="col w-10">{{ __('service.name') }}</th>
                                                        <th class="col w-5">{{ __('app.qty') }}</th>
                                                        <th class="col w-5 d-none">{{ __('app.price') }}</th>
                                                        <th class="col w-5 d-none">{{ __('app.discount') }}</th>
                                                        <th class="col w-5">{{ __('app.total') }}</th>
                                                        <th class="col w-10">{{ __('order.start_at') }}</th>
                                                        <th class="col w-10">{{ __('order.end_at') }}</th>
                                                        <th class="col w-5 d-none">{{ __('tax.tax') }}</th>
                                                        <th class="col w-5">{{ __('app.staff') }}</th>
                                                        <th class="col w-5">{{ __('app.staff_status') }}</th>
                                                        <th class="col w-5 d-none">{{ __('app.action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                
                                            </table>
                                        </div>
                                        <div class="col-md-8">
                                            <x-label for="note" extraClass="fw-bold" name="{{ __('order.note') }}" /> :
                                            <p>{{ $order->note }}
                                            </p>
                                        </div>
                                        <div class="col-md-4 mt-4">
                                            <table class="table mb-0 table-striped table-sm">
                                               <tbody>
                                                  <tr>
                                                     <td><span class="fw-bold">{{ __('app.subtotal') }}</span></td>
                                                     <td><span class="fw-bold subtotal">0.00</span></td>
                                                  </tr>
                                                  <tr>
                                                     <td><span class="fw-bold">{{ __('tax.total') }}</span></td>
                                                     <td><span class="fw-bold total_tax">0.00</span></td>
                                                  </tr>
                                                  <tr>
                                                     <td><span class="fw-bold">{{ __('app.grand_total') }}</span></td>
                                                     <td><span class="fw-bold grand_total">0.00</span></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-8">
                                            <x-label for="schedule_note" name="{{ __('app.note') }}" />
                                            <x-textarea name='schedule_note' value='{{ $order->schedule_note }}' />
                                        </div>
                                </div>
                                <div class="card-body p-4 row g-3">
                                        <div class="col-md-12">
                                            <div class="d-md-flex d-grid align-items-center gap-3">
                                                <x-button type="submit" class="primary px-4" text="{{ __('app.submit') }}" />
                                                <x-anchor-tag href="{{ route('dashboard') }}" text="{{ __('app.close') }}" class="btn btn-light px-4" />
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!--end row-->
            </div>
        </div>

        @endsection

@section('js')
<script src="{{ versionedAsset('custom/js/schedule/schedule.js') }}"></script>
<script src="{{ versionedAsset('custom/js/schedule/schedule-edit.js') }}"></script>
@endsection
