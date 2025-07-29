@extends('layouts.app')
@section('title', __('account.balance_sheet'))
    
    @section('css')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.11/themes/default/style.min.css" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-treegrid/0.2.0/css/jquery.treegrid.min.css">

<style>
    .tree {
        width: 100%;
        border-collapse: collapse;
    }
    .tree th, .tree td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    .tree th {
        background-color: #f2f2f2;
    }
    .treegrid-expander {
        width: 16px;
        height: 16px;
        display: inline-block;
        position: relative;
        cursor: pointer;
    }
</style>
    @endsection

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <x-breadcrumb :langArray="[
                                            'app.reports',
                                            'account.balance_sheet',
                                        ]"/>
                <div class="row">
                    <form class="row g-3 needs-validation" id="reportForm" action="{{ route('report.account.balance-sheet.ajax') }}" enctype="multipart/form-data">
                        {{-- CSRF Protection --}}
                        @csrf
                        @method('POST')

                        <input type="hidden" name="row_count" value="0">
                        <input type="hidden" name="total_amount" value="0">
                        <input type="hidden" id="base_url" value="{{ url('/') }}">
                        <div class="col-12 col-lg-12">
                            <div class="card">
                                <div class="card-header px-4 py-3">
                                    <h5 class="mb-0">{{ __('app.filter') }}</h5>
                                </div>
                                <div class="card-body p-4 row g-3">
                                    
                                        <div class="col-md-6">
                                            <x-label for="from_date" name="{{ __('app.from_date') }}" />
                                            <div class="input-group mb-3">
                                                <x-input type="text" additionalClasses="datepicker" name="from_date" :required="true" value=""/>
                                                <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <x-label for="to_date" name="{{ __('app.to_date') }}" />
                                            <div class="input-group mb-3">
                                                <x-input type="text" additionalClasses="datepicker" name="to_date" :required="true" value=""/>
                                                <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                            </div>
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
                        <div class="col-12 col-lg-12">
                            <div class="card">
                                <div class="card-header px-4 py-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <h5 class="mb-0">{{ __('account.balance_sheet_report') }}</h5>
                                        </div>
                                        <div class="col-6 text-end">
                                            <div class="btn-group">
                                            <button type="button" class="btn btn-outline-success">{{ __('app.export') }}</button>
                                            <button type="button" class="btn btn-outline-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false"> <span class="visually-hidden">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><button type='button' class="dropdown-item" id="btnExport"><i class="bx bx-spreadsheet mr-1"></i>{{ __('app.excel') }}</button></li>
                                            </ul>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-4 row g-3">
                                        <div class="col-md-12 table-responsive">
                                          <button type="button" id="expandAll">Expand All</button>
                                            <button type="button" id="collapseAll">Collapse All</button>
                                            <table id="accountTree" class="table tree">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('account.account') }}</th>
                                                        <th>{{ __('account.debit') }}</th>
                                                        <th>{{ __('account.credit') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>

                                            

                                            <!-- Hidden Table to export data -->
                                            <div id="combined-table" class="d-none">
                                            </div>
                                            <!-- End -->
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

        @endsection

@section('js')
    @include("plugin.export-table")
    <script src="{{ versionedAsset('custom/js/accounts/balance-sheet-report.js') }}"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-treegrid/0.2.0/js/jquery.treegrid.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.11/jstree.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>

    <script type="text/javascript">
            $(function() {
    function loadTreeGrid() {
        $.ajax({
            url: '/report/api/account-tree',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                renderTreeGrid(data);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });
    }

   function renderTreeGrid(data) {
    var tbody = $('#accountTree tbody');
    tbody.empty();

    let totalDebit = 0;
    let totalCredit = 0;

    function addNode(node, parentId) {
        var rowClass = 'treegrid-' + node.id;
        if (parentId) {
            rowClass += ' treegrid-parent-' + parentId;
        }

        var debitValue = node.type === 'group' ? formatCurrency(node.debit) : node.debit;
        var creditValue = node.type === 'group' ? formatCurrency(node.credit) : node.credit;

        if (node.type === 'account') {
            totalDebit += parseFloat(node.debit) || 0;
            totalCredit += parseFloat(node.credit) || 0;
        }

        var row = $('<tr>', { class: rowClass })
            .append($('<td>').text(node.text))
            .append($('<td>').text(_parseFix(debitValue)).addClass('text-end'))
            .append($('<td>').text(_parseFix(creditValue)).addClass('text-end'));

        tbody.append(row);

        if (node.children && node.children.length > 0) {
            node.children.forEach(function(child) {
                addNode(child, node.id);
            });
        }
    }

    data.forEach(function(node) {
        addNode(node);
    });

    // Add the total row
    var totalRow = $('<tr>', { class: 'table-active font-weight-bold' })
        .append($('<td>').text('Total'))
        .append($('<td>').text(formatCurrency(_parseFix(totalDebit))).addClass('text-end'))
        .append($('<td>').text(formatCurrency(_parseFix(totalCredit))).addClass('text-end'));

    tbody.append(totalRow);

    $('.tree').treegrid({
        expanderExpandedClass: 'treegrid-expander-expanded',
        expanderCollapsedClass: 'treegrid-expander-collapsed'
    });
}

function formatCurrency(amount) {
    return _parseFix(amount);
    /*return new Intl.NumberFormat('en-US', { 
        style: 'currency', 
        currency: 'USD',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2 
    }).format(amount);*/
}

    /*function renderTreeGrid(data) {
        var tbody = $('#accountTree tbody');
        tbody.empty();

        function addNode(node, parentId) {
            var rowClass = 'treegrid-' + node.id;
            if (parentId) {
                rowClass += ' treegrid-parent-' + parentId;
            }

            var row = $('<tr>', { class: rowClass })
                .append($('<td>').text(node.text))
                .append($('<td>').text(node.debit).addClass('text-end'))
                .append($('<td>').text(node.credit).addClass('text-end'));

            tbody.append(row);

            if (node.children) {
                node.children.forEach(function(child) {
                    addNode(child, node.id);
                });
            }
        }

        data.forEach(function(node) {
            addNode(node);
        });

        $('.tree').treegrid({
            expanderExpandedClass: 'treegrid-expander-expanded',
            expanderCollapsedClass: 'treegrid-expander-collapsed'
        });
    }*/

    loadTreeGrid();

    $('#expandAll').on('click', function() {
        $('.tree').treegrid('expandAll');
    });

    $('#collapseAll').on('click', function() {
        $('.tree').treegrid('collapseAll');
    });

    $(document).on('click', '.action-btn', function() {
        var nodeId = $(this).data('id');
        alert('Action for node: ' + nodeId);
    });
});
        </script>    
@endsection
