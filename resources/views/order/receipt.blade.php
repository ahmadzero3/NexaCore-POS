@extends('layouts.app')
@section('title', __('order.receipt'))

		@section('content')
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<x-breadcrumb :langArray="[
											'order.orders',
                                            'order.list',
                                            'order.receipt',
										]"/>
				<div class="row">
					<div class="col-12 col-lg-12">

                        @include('layouts.session')

                        <div class="card">
                    <div class="card-body">
                        <div class="toolbar hidden-print">
                                <div class="text-end">
                                    @can(['order.edit'])
                                    <a href="{{ route('order.edit', ['id' => $order->id]) }}" class="btn btn-primary"><i class="bx bx-edit"></i>{{ __('app.edit') }}</a>
                                    @endcan
                                    @can(['schedule.create', 'schedule.edit'])
                                    <a href="{{ route('schedule.edit', ['id' => $order->id]) }}" class="btn btn-success"><i class="bx bx-alarm-add"></i>{{ __('schedule.schedule') }}</a>
                                    @endcan
                                    @can(['order.view'])
                                    <a href="{{ route('order.timeline', ['id' => $order->id]) }}" class="btn btn-primary"><i class="bx bx-time"></i>{{ __('order.timeline') }}</a>
                                    @endcan
                                    <button type="button" id="printButton" class="btn btn-dark"><i class="bx bx-printer mr-1"></i>{{ __('app.print') }}</button>
                                    <button type="button" id="generate_pdf" class="btn btn-danger"><i class="bx bx-file mr-1"></i>{{ __('app.export_to_pdf') }}</button>
                                </div>
                                <hr/>
                            </div>
                        <div id="printReceipt">
                            <div class="invoice overflow-auto">
                                <div class="min-width-600">
                                    <header>
                                        <div class="row">
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <img src="assets/images/logo-icon.png" width="80" alt="" />
                                                </a>
                                            </div>
                                            <div class="col company-details">
                                                <h2 class="name">
                                                    <a target="_blank" href="javascript:;">
                                                    {{ app('company')['name'] }}
                                                    </a>
                                                </h2>
                                                <div>{{ app('company')['address'] }}</div>
                                            </div>
                                        </div>
                                    </header>
                                    <main>
                                        <div class="row contacts">
                                            <div class="col invoice-to">
                                                <div class="text-gray-light text-uppercase">{{ __('order.receipt_to') }}:</div>
                                                <h2 class="to">{{ $order->party->first_name .' '. $order->party->last_name }}</h2>
                                                <div class="address">{{ $order->party->address }}</div>
                                                <div class="email"><a href="mailto:{{ $order->party->email }}">{{ $order->party->email }}</a>
                                                </div>
                                            </div>
                                            <div class="col invoice-details">
                                                <h1 class="invoice-id">{{ __('order.receipt') }} #{{ $order->order_code }}</h1>
                                                <div class="date">{{ __('order.date_of_receipt') }}: {{ $order->formatted_order_date  }}</div>
                                            </div>
                                        </div>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th class="text-left text-uppercase">{{ __('app.description') }}</th>
                                                    <th class="text-left text-uppercase">{{ __('order.start_date') }}</th>
                                                    <th class="text-left text-uppercase">{{ __('order.end_date') }}</th>
                                                    <th class="text-right text-uppercase">{{ __('app.price') }}</th>
                                                    <th class="text-right text-uppercase">{{ __('app.discount') }}</th>
                                                    <th class="text-right text-uppercase">{{ __('tax.tax') }}</th>
                                                    <th class="text-right text-uppercase">TOTAL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $i=1;
                                                @endphp

                                                @foreach($order->orderedProducts as $product)
                                                <tr>
                                                    <td class="no">{{ $i++ }}</td>
                                                    <td class="text-left">
                                                        <h3>
                                                            <!-- Service Name -->
                                                            {{ $product->service->name }}
                                                        </h3>
                                                        <!-- Description -->
                                                        {{ $product->service->description }}
                                                   </td>
                                                   <td class="">
                                                        <!-- start date & time -->
                                                        {{ $product->formatted_start_date }}
                                                        <br>
                                                        {{ $product->start_time }}
                                                    </td>
                                                    <td class="">
                                                        <!-- start date & time -->
                                                        {{ $product->formatted_end_date }}
                                                        <br>
                                                        {{ $product->end_time }}
                                                    </td>
                                                    <td class="">
                                                        <!-- Price -->
                                                        {{ $product->unit_price }}
                                                    </td>
                                                    <td class="unit">
                                                        <!-- Discount -->
                                                        {{ $product->discount }}<br>
                                                        ({{ $product->discount_type }})
                                                    </td>
                                                    <td class="qty">
                                                        <!-- Tax -->
                                                        {{ $product->tax->name }}
                                                    </td>
                                                    <td class="unit">{{ $product->total_price_with_tax }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5"></td>
                                                    <td colspan="2">{{ __('app.subtotal') }}</td>
                                                    <td>{{ ($order->total_amount) - ($order->orderedProducts->sum('tax_amount')) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5"></td>
                                                    <td colspan="2">{{ __('tax.total') }}</td>
                                                    <td>{{ $order->orderedProducts->sum('tax_amount') }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5"></td>
                                                    <td colspan="2">{{ __('app.grand_total') }}</td>
                                                    <td>{{$order->total_amount}}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <div class="thanks">{{ __('app.thank_you') }}!</div>

                                    </main>
                                    <footer>{{ __('app.computer_generated_receipt') }}</footer>
                                </div>
                                <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
                                <div></div>
                            </div>
                        </div>
                    </div>
                </div>
					</div>
				</div>
				<!--end row-->
			</div>
		</div>
		@endsection

@section('js')
    @include("plugin.export")
    <script src="{{ versionedAsset('custom/js/order/order-receipt.js') }}"></script>
@endsection
