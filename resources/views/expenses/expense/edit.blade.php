@extends('layouts.app')
@section('title', __('expense.update'))

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <x-breadcrumb :langArray="[
                                            'expense.expense',
                                            'expense.list',
                                            'expense.update',
                                        ]"/>
                <div class="row">
                    <form class="row g-3 needs-validation" id="expenseForm" action="{{ route('expense.update') }}" enctype="multipart/form-data">
                        {{-- CSRF Protection --}}
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="expense_id" value="{{ $expense->id }}">
                        <input type="hidden" name="row_count" value="0">
                        <input type="hidden" name="row_count_payments" value="0">
                        <input type="hidden" id="base_url" value="{{ url('/') }}">
                        <input type="hidden" id="operation" name="operation" value="update-expense">
                        <input type="hidden" id="selectedPaymentTypesArray" value="{{ $selectedPaymentTypesArray }}">
                        <div class="col-12 col-lg-12">
                            <div class="card">
                                <div class="card-header px-4 py-3">
                                    <h5 class="mb-0">{{ __('expense.details') }}</h5>
                                </div>
                                <div class="card-body p-4 row g-3">

                                        <div class="col-md-6">
                                            <x-label for="category_id" name="{{ __('expense.category.category') }}" />
                                            <div class="input-group">
                                                <x-dropdown-expense-category selected="{{ $expense->expense_category_id }}" />
                                                <button type="button" class="input-group-text" data-bs-toggle="modal" data-bs-target="#expenseCategoryModal">
                                                    <i class='text-primary bx bx-plus-circle'></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <x-label for="subcategory_id" name="{{ __('expense.subcategory.subcategory') }}" />
                                            <div class="input-group">
                                            <x-dropdown-expense-subcategory selected="{{ $expense->expense_subcategory_id }}" />
                                                <button type="button" class="input-group-text" data-bs-toggle="modal" data-bs-target="#expenseSubcategoryModal">
                                                    <i class='text-primary bx bx-plus-circle'></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="expense_date" name="{{ __('app.date') }}" />
                                            <div class="input-group mb-3">
                                                <x-input type="text" additionalClasses="datepicker-edit" name="expense_date" :required="true" value="{{ $expense->formatted_expense_date }}"/>
                                                <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="order_code" name="{{ __('expense.number') }}" />
                                            <!--  -->
                                            <div class="input-group mb-3">
                                                <x-input type="text" name="prefix_code" :required="true" placeholder="Prefix Code" value="{{ $expense->prefix_code }}"/>
                                                <span class="input-group-text">#</span>
                                                <x-input type="text" name="count_id" :required="true" placeholder="Serial Number" value="{{ $expense->count_id }}"/>
                                            </div>
                                        </div>

                                </div>
                                <div class="card-header px-4 py-3">
                                    <h5 class="mb-0">{{ __('expense.items') }}</h5>
                                </div>
                                <div class="card-body p-4 row g-3">
                                        <div class="col-md-10">
                                            <x-label for="customer_id" name="{{ __('item.enter_item_name') }}" />
                                            <div class="input-group">
                                                <input type="text" id="search_item" value="" class="form-control" required placeholder="Search/Add Items">

                                                &nbsp;
                                                <x-button type="button" buttonId="add_row" class="btn btn-outline-primary px-5 rounded-1" text="{{ __('app.add_row') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-12 table-responsive">
                                            <table class="table mb-0 table-striped table-bordered" id="orderTable">
                                                <thead>
                                                    <tr>
                                                        <th scope="col w-5">{{ __('app.action') }}</th>
                                                        <th scope="col w-10">{{ __('item.item') }}</th>
                                                        <th scope="col w-5">{{ __('app.qty') }}</th>
                                                        <th scope="col w-5">{{ __('app.price_per_unit') }}</th>
                                                        <th scope="col w-5">{{ __('app.total') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="5" class="text-center fw-light fst-italic default-row">
                                                            No items are added yet!!
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="2" class="fw-bold text-end">
                                                            {{ __('app.total') }}
                                                        </td>
                                                        <td class="fw-bold sum_of_quantity">
                                                            0
                                                        </td>
                                                        <td class="fw-bold text-end">
                                                        </td>
                                                        <td class="fw-bold text-end sum_of_total">
                                                            0
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div class="col-md-8">
                                            <x-label for="note" name="{{ __('app.note') }}" />
                                            <x-textarea name='note' value=''/>
                                        </div>
                                        <div class="col-md-4 mt-4">
                                            <table class="table mb-0 table-striped">
                                               <tbody>
                                                  <tr>
                                                     <td class="w-50">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" {{ ($expense->round_off!=0)? 'checked' : '' }} id="round_off_checkbox">
                                                            <label class="form-check-label fw-bold cursor-pointer" for="round_off_checkbox">{{ __('app.round_off') }}</label>
                                                        </div>
                                                    </td>
                                                     <td class="w-50">
                                                        <x-input type="text" additionalClasses="text-end cu_numeric round_off " name="round_off" :required="false" placeholder="Round-Off" value="0"/>
                                                    </td>
                                                  </tr>
                                                  <tr>
                                                     <td><span class="fw-bold">{{ __('app.grand_total') }}</span></td>
                                                     <td>
                                                        <x-input type="text" additionalClasses="text-end grand_total" readonly=true name="grand_total" :required="true" placeholder="Round-Off" value="0"/>
                                                    </td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                        </div>
                                </div>
                                <div class="card-header px-4 py-3">
                                    <h5 class="mb-0">{{ __('payment.payment') }}</h5>
                                </div>
                                <div class="card-body p-4 row g-3 ">
                                    <div class="payment-container">
                                        <div class="row payment-type-row-0 py-3 ">
                                            <div class="col-md-6">
                                                <x-label for="amount" id="amount_lang" labelDataName="{{ __('payment.amount') }}" name="<strong>#1</strong> {{ __('payment.amount') }}" />
                                                <div class="input-group mb-3">
                                                    <x-input type="text" additionalClasses="cu_numeric" name="payment_amount[0]" value=""/>
                                                    <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-dollar"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <x-label for="payment_type" id="payment_type_lang" name="{{ __('payment.type') }}" />
                                                <div class="input-group">
                                                    <select class="form-select select2 payment-type-ajax" name="payment_type_id[0]" data-placeholder="Choose one thing">
                                                    </select>

                                                    <button type="button" class="input-group-text" data-bs-toggle="modal" data-bs-target="#paymentTypeModal">
                                                        <i class='text-primary bx bx-plus-circle'></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <x-label for="payment_note" id="payment_note_lang" name="{{ __('payment.note') }}" />
                                                <x-textarea name="payment_note[0]" value=""/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <x-anchor-tag class="add_payment_type" href="javascript:;" text="<div class='d-flex align-items-center'><i class='fadeIn animated bx bx-plus font-30 text-primary'></i><div class=''>{{ __('payment.add_payment_type') }}</div></div>" />
                                    </div>
                                </div>

                                <div class="card-body p-4 row g-3">
                                        <div class="col-md-12">
                                            <div class="d-md-flex d-grid align-items-center gap-3">
                                                <x-button type="button" class="primary px-4" buttonId="submit_form" text="{{ __('app.submit') }}" />
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
        @include("modals.expense-category.create")
        @include("modals.expense-subcategory.create")
        @include("modals.payment-type.create")

        @endsection

@section('js')
    <script type="text/javascript">
        const itemsTableRecords = '{!! $expenseItemsJson !!}';
    </script>
    <script src="{{ versionedAsset('custom/js/expenses/expense.js') }}"></script>
    <script src="{{ versionedAsset('custom/js/modals/expense-category/expense-category.js') }}"></script>
    <script src="{{ versionedAsset('custom/js/modals/expense-subcategory/expense-subcategory.js') }}"></script>
    <script src="{{ versionedAsset('custom/js/modals/payment-type/payment-type.js') }}"></script>
    <script src="{{ versionedAsset('custom/js/payment-types/payment-type-select2-ajax.js') }}"></script>
    <script src="{{ versionedAsset('custom/js/common/common.js') }}"></script>
@endsection
