@extends('layouts.app')
@section('title', __('app.transactions'))

@section('css')
<link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
@endsection
		@section('content')
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
					<x-breadcrumb :langArray="[
											'item.items',
											'app.transactions',
										]"/>

                    <div class="card">
						<div class="row g-0">
						  <div class="col-md-4 border-end">
							<img src="{{ url('/item/getimage/' . $item->image_path) }}" class="img-fluid" alt="Item Image">
						  </div>
						  <div class="col-md-8">
							<div class="card-body">
							  <h4 class="card-title">{{ $item->name }}</h4>
							  <div class="d-flex gap-3 py-3">
								  <div>{{ __('item.stock_quantity') }}</div>
								  <div class="text-default"><i class='bx bxs-building align-middle'></i> {{ $formatNumber->formatQuantity($item->current_stock) }}</div>
							  </div>
							  <div class="mb-3"> 
							  	<span>{{ __('app.price') }} : </span>
								<span class="price h4">{{ $formatNumber->formatWithPrecision($item->sale_price, comma:true) }}</span> 
								<span class="text-muted">/{{ $item->baseUnit->name }}</span> 
							</div>
							  <dl class="row">
								<dt class="col-sm-3">{{ __('item.code') }}#</dt>
								<dd class="col-sm-9">{{ $item->item_code }}</dd>

								<dt class="col-sm-3">{{ __('item.category.category') }}</dt>
								<dd class="col-sm-9">{{ $item->category->name }}</dd>

								<dt class="col-sm-3">{{ __('item.item_type') }}</dt>
								<dd class="col-sm-9">{{ ucfirst($item->tracking_type) }}</dd>

								<dt class="col-sm-3">{{ __('app.description') }}</dt>
								<dd class="col-sm-9">{{ $item->description }}</dd>
							  
							  </dl>
							</div>
						  </div>
						</div>
				  </div>


                    <div class="card">

					<div class="card-header px-4 py-3 d-flex justify-content-between">
					    <!-- Other content on the left side -->
					    <div>
					    	<h5 class="mb-0 text-uppercase">{{ __('app.transactions') }}</h5>
					    </div>
					    
					   
					</div>
					<div class="card-body">
						
						<div class="table-responsive">
                        <form class="row g-3 needs-validation" id="datatableForm" action="{{ route('item.delete') }}" enctype="multipart/form-data">
                            {{-- CSRF Protection --}}
                            @csrf
                            @method('POST')
                            <input type="hidden" id="item_id" value="{{ $item->id }}">
							<table class="table table-striped table-bordered border w-100" id="datatable">
								<thead>
									<tr>
										<th class="d-none"><!-- Which Stores ID & it is used for sorting --></th>
										<th>{{ __('app.date') }}</th>
										<th>{{ __('app.reference_no') }}</th>
										<th>{{ __('app.transaction_type') }}</th>
										<th>{{ __('item.price_per_unit') }}</th>
										<th>{{ __('item.quantity') }}</th>
										<th>{{ __('item.stock_impact') }}</th>
										<th>{{ __('unit.unit') }}</th>
									</tr>
								</thead>
							</table>
                        </form>
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
<script src="{{ versionedAsset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ versionedAsset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ versionedAsset('custom/js/common/common.js') }}"></script>
<script src="{{ versionedAsset('custom/js/items/item-transaction-list.js') }}"></script>
@endsection
