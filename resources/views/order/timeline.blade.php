@extends('layouts.app')
@section('title', __('order.timeline'))

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                

                <x-breadcrumb :langArray="[
                                            'order.orders',
                                            'order.list',
                                            'order.timeline',
                                        ]"/>

                <div class="row">
                    <div class="col-12 col-lg-12">
                            <div class="container py-2">
                                <h2 class="font-weight-light text-center text-muted py-3">{{ __('order.order_timeline') }}</h2>
                                <!-- timeline item 1 -->
                                <div class="row">
                                    <!-- timeline item 1 left dot -->
                                    <div class="col-auto text-center flex-column d-none d-sm-flex">
                                        <div class="row h-50">
                                            <div class="col">&nbsp;</div>
                                            <div class="col">&nbsp;</div>
                                        </div>
                                        <h5 class="m-2">
                                        <span class="badge rounded-pill bg-light border">&nbsp;</span>
                                    </h5>
                                        <div class="row h-50">
                                            <div class="col border-end">&nbsp;</div>
                                            <div class="col">&nbsp;</div>
                                        </div>
                                    </div>
                                    <!-- timeline item 1 event content -->
                                    <div class="col py-2">
                                        <div class="card radius-15">
                                            <div class="card-body">
                                                <div class="float-end text-muted">{{ $order->created_at }}</div>
                                                <h4 class="card-title text-muted">{{ __('order.created') }}</h4>
                                                <p class="card-text">
                                                    {{ __('customer.name') }} : {{ $order->party->first_name }}<br>
                                                    {{ __('customer.mobile') }} : {{ $order->party->mobile }}<br>
                                                    {{ __('customer.address') }} : {{ $order->party->address }}<br>
                                                    {{ __('order.status') }} : {{ $order->order_status }}<br>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--/row-->
                                <!-- timeline item 4 -->
                                <div class="row">
                                    <div class="col-auto text-center flex-column d-none d-sm-flex">
                                        <div class="row h-50">
                                            <div class="col border-end">&nbsp;</div>
                                            <div class="col">&nbsp;</div>
                                        </div>
                                        <h5 class="m-2">
                                        <span class="badge rounded-pill bg-primary">&nbsp;</span>

                                    </h5>
                                        <div class="row h-50">
                                            <div class="col">&nbsp;</div>
                                            <div class="col">&nbsp;</div>
                                        </div>
                                    </div>
                                    <div class="col py-2">
                                        <div class="card radius-15">
                                            <div class="card-body">
                                                
                                                <h4 class="card-title text-primary">{{ __('schedule.schedule') }}</h4>
                                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-target="#t2_details" data-bs-toggle="collapse">Show Details ▼</button>
                                                <div class="collapse border" id="t2_details">
                                                    <div class="p-2 text-monospace">
                                                        <table class="table mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>{{ __('service.name')}}</th>
                                                                    <th>{{ __('order.start_date')}}</th>
                                                                    <th>{{ __('order.end_date')}}</th>
                                                                    <th>{{ __('schedule.assigned_jobs')}}</th>
                                                                    <th>{{ __('app.staff_status')}}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($order->orderedProducts as $products)
                                                                <tr>
                                                                    <td>{{ $products->service->name }}</td>
                                                                    <td>{{ $products->start_date .' '. $products->start_time  }}</td>
                                                                    <td>{{ $products->end_date .' '. $products->end_time  }}</td>
                                                                    <td>{{ $products->user->first_name??'' }}</td>
                                                                    <td>{{ $products->staff_status??'' }}</td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--/row-->
                              
                                
                                
                            </div>
                    </div>
                </div>
                <!--end row-->
            </div>
        </div>
        @endsection

       
