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
                    <form class="row g-3 needs-validation" id="jobsForm" action="{{ route('assigned_jobs.update') }}" enctype="multipart/form-data">
                        {{-- CSRF Protection --}}
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="row_count" value="0">
                        <input type="hidden" name="total_amount" value="0">
                        <input type="hidden" name="ordered_product_id" value="{{ $orderedProduct->id }}">
                        <input type="hidden" id="base_url" value="{{ url('/') }}">
                        <div class="col-12 col-lg-12">
                            <div class="card">
                                <div class="card-header px-4 py-3">
                                    <h5 class="mb-0">{{ __('order.details') }}</h5>
                                </div>
                                <div class="card-body p-4 row g-3">
                                        <div class="col-md-6">
                                            <x-label for="job_code" name="{{ __('schedule.job_code') }}" />
                                            <!--  -->
                                            <div class="input-group mb-3">
                                                <x-input type="text" name="Job_code" :required="true" placeholder="Job Code" value="{{ $orderedProduct->job_code }}" disabled="true"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="customer_id" name="{{ __('customer.customer') }}" />
                                            <x-dropdown-customer selected="{{ $order->customer_id }}" disabled="true" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="mobile" name="{{ __('app.mobile') }}" />
                                            <!--  -->
                                            <div class="input-group mb-3">
                                                <x-input type="text" name="mobile" :required="true" placeholder="Mobile" value="{{ $order->party->mobile }}" disabled="true"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="address" name="{{ __('app.address') }}" />
                                            <x-textarea name="" disabled="true" value="{{ $order->party->shipping_address }}"/>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="start_date" name="{!! __('order.start_date_and_time') !!}" />
                                            <div class="input-group mb-3">
                                                <x-input type="text" value="{{ $orderedProduct->start_date .' '. $orderedProduct->start_time }}" disabled="true"/>
                                                <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="start_date" name="{!! __('order.end_date_and_time') !!}" />
                                            <div class="input-group mb-3">
                                                <x-input type="text" value="{{ $orderedProduct->end_date .' '. $orderedProduct->end_time }}" disabled="true"/>
                                                <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="note" name="{{ __('schedule.scheduler_note') }}" />
                                            <x-textarea name="" disabled="true" value="{{ $orderedProduct->assigned_user_note }}"/>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="note" name="{{ __('app.your_note') }}" />
                                            <x-textarea name="staff_status_note" value="{{ $orderedProduct->staff_status_note }}"/>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <x-label for="staff_status" name="{{ __('app.status') }}" />
                                            <x-dropdown-general optionNaming="StaffJobStatus" selected="{{ $orderedProduct->staff_status }}" dropdownName='staff_status'/>
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
    <script src="{{ versionedAsset('custom/js/assigned-jobs/assigned-jobs-edit.js') }}"></script>
    @endsection