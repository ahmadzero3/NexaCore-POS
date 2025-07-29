@extends('layouts.app')
@section('title', __('order.update_order'))

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <x-breadcrumb :langArray="[
                                            'order.orders',
                                            'order.list',
                                            'order.update_order',
                                        ]"/>
                <div class="row">
                    <form class="row g-3 needs-validation" id="orderForm" action="{{ route('order.update') }}" enctype="multipart/form-data">
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
                                            <x-label for="party_id" name="{{ __('customer.customer') }}" />
                                            <div class="input-group">
                                                <x-dropdown-customer selected="{{ $order->party_id }}" />
                                                <button type="button" class="input-group-text open-party-model" data-party-type='customer'>
                                                        <i class='text-primary bx bx-plus-circle'></i>
                                                    </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="order_date" name="{{ __('order.date') }}" />
                                            <div class="input-group mb-3">
                                                <x-input type="text" additionalClasses="datepicker" name="order_date" :required="true" value="{{ $order->order_date }}"/>
                                                <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="order_code" name="{{ __('order.code') }}" />
                                            <!--  -->
                                            <div class="input-group mb-3">
                                                <x-input type="text" name="prefix_code" :required="true" placeholder="Prefix Code" value="{{ $order->prefix_code }}"/>
                                                <span class="input-group-text">#</span>
                                                <x-input type="text" name="count_id" :required="true" placeholder="Serial Number" value="{{ $order->count_id }}"/>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <x-label for="order_status" name="{{ __('app.status') }}" />
                                            <x-dropdown-order-status selected="" dropdownName='order_status'/>
                                        </div>
                                </div>
                                <div class="card-header px-4 py-3">
                                    <h5 class="mb-0">{{ __('service.select') }}</h5>
                                </div>
                                <div class="card-body p-4 row g-3">
                                        <div class="col-md-10">
                                            <x-label for="customer_id" name="{{ __('service.select_name') }}" />
                                            <div class="input-group">
                                                <x-dropdown-services selected="" />
                                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#serviceModal"><i class="bx bx-plus-circle me-0"></i>
                                        </button>
                                                &nbsp;
                                                <x-button type="button" buttonId="add_row" class="btn btn-outline-primary px-5 rounded-1" text="{{ __('app.add_row') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-12 table-responsive">
                                            <table class="table mb-0 table-striped table-bordered" id="orderTable">
                                                <thead>
                                                    <tr>
                                                        <th scope="col w-10">{{ __('service.name') }}</th>
                                                        <th scope="col w-5">{{ __('app.qty') }}</th>
                                                        <th scope="col w-5">{{ __('app.price') }}</th>
                                                        <th scope="col w-5">{{ __('app.discount') }}</th>
                                                        <th scope="col w-10">{{ __('order.start_at') }}</th>
                                                        <th scope="col w-10">{{ __('order.end_at') }}</th>
                                                        <th scope="col w-5">{{ __('tax.tax') }}</th>
                                                        <th scope="col w-5">{{ __('app.total') }}</th>
                                                        <th scope="col w-5">{{ __('app.action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="6" class="fw-bold text-end">
                                                            {{ __('app.total') }}
                                                        </td>
                                                        <td class="fw-bold text-end sum_of_tax">
                                                            0.00
                                                        </td>
                                                        <td class="fw-bold text-end sum_of_total">
                                                            0.00
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div class="col-md-8">
                                            <x-label for="note" name="{{ __('order.note') }}" />
                                            <x-textarea name='note' value='{{ $order->note }}'/>
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
                                </div>
                                <div class="card-header px-4 py-3">
                                    <h5 class="mb-0">{{ __('payment.history') }}</h5>
                                </div>
                                <div class="card-body p-4 row g-3">
                                    <div class="col-md-12 table-responsive">
                                        <table class="table mb-0 table-striped table-bordered" id="orderTable">
                                            <thead>
                                                <tr>
                                                    <th scope="col w-10">#</th>
                                                    <th scope="col w-5">{{ __('app.date') }}</th>
                                                    <th scope="col w-5">{{ __('payment.type') }}</th>
                                                    <th scope="col w-5">{{ __('payment.amount') }}</th>
                                                    <th scope="col w-5">{{ __('payment.note') }}</th>
                                                    <th scope="col w-5">{{ __('app.action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $i=1;
                                                @endphp
                                                @if(!$payments->isEmpty())
                                                    @foreach ($payments as $payment)
                                                    <tr>
                                                        <td>{{ $i++ }}</td>
                                                        <td>{{ $payment->payment_date }}</td>
                                                        <td>{{ $payment->paymentType->name }}</td>
                                                        <td>{{ $payment->amount }}</td>
                                                        <td>{{ $payment->note }}</td>
                                                        <td><button type="button" class="btn btn-outline-danger"><i class="bx bx-trash me-0 payment-delete" data-record-id="{{ $payment->id }}"></i></button></td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="6" class="text-center">
                                                            {{ __('app.no_records_found') }}
                                                        </td>
                                                    </tr>
                                                @endif


                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-header px-4 py-3">
                                    <h5 class="mb-0">{{ __('payment.payment') }}</h5>
                                </div>
                                <div class="card-body p-4 row g-3">
                                        <div class="col-md-6">
                                            <x-label for="amount" name="{{ __('payment.amount') }}" />
                                            <div class="input-group mb-3">
                                                <x-input type="number" name="amount" value=""/>
                                                <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-dollar"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="customer_id" name="{{ __('payment.type') }}" />
                                            <div class="input-group">
                                                <x-dropdown-payment-type selected="" />
                                                <button type="button" class="input-group-text" data-bs-toggle="modal" data-bs-target="#paymentTypeModal">
                                                    <i class='text-primary bx bx-plus-circle'></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <x-label for="payment_note" name="{{ __('payment.note') }}" />
                                            <x-textarea name="payment_note" value=""/>
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
        <!-- Import Modals -->
        @include("modals.service.create")
        @include("modals.party.create")
        @include("modals.payment-type.create")

        @endsection

@section('js')
<script src="{{ versionedAsset('custom/js/order/order.js') }}"></script>
<script src="{{ versionedAsset('custom/js/order/order-edit.js') }}"></script>
<script src="{{ versionedAsset('custom/js/modals/service/service.js') }}"></script>
<script src="{{ versionedAsset('custom/js/modals/party/party.js') }}"></script>
<script src="{{ versionedAsset('custom/js/modals/payment-type/payment-type.js') }}"></script>
@endsection
