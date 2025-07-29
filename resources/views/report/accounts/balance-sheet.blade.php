@extends('layouts.app')
@section('title', __('account.balance_sheet'))

    @section('css')
    <link href="{{ versionedAsset('assets/plugins/jquery-treegrid/css/jquery.treegrid.css') }}" rel="stylesheet" />
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

                                            <table class="table table-bordered" id="orderReport">
                                                <thead class="table-light">
                                                    <tr>
                                                        @foreach($mains as $main)
                                                            <th class="w-50">
                                                                {{ $main->name }}
                                                            </th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>



                                                        @php

                                                            function renderOptions($group, $indent = 0, $allAccounts, $treeRowId, $childOf = null) {
                                                                //$showArrow = ($indent!=0) ? ' &nbsp;&nbsp; ' : '';

                                                                $childOfClas = $childOf ? 'treegrid-parent-'.$childOf : '';

                                                                echo "<tr class='treegrid-".$treeRowId." ".$childOfClas."'>";
                                                                    echo "<td>";
                                                                        //echo str_repeat('&nbsp;', $indent * 4);
                                                                        //echo $showArrow;
                                                                        echo $group->name;
                                                                    echo "</td>";
                                                                    echo "<td>";
                                                                        echo "0.0000";
                                                                    echo "</td>";
                                                                echo "</tr>";

                                                                $parentTreeID = $treeRowId;

                                                                $treeRowId++;

                                                                $accounts = $allAccounts->where('group_id', $group->id);

                                                                 // Access the name property if an account is found
                                                                if ($accounts) {

                                                                    foreach($accounts as $account){
                                                                        echo "<tr class='treegrid-".$treeRowId." treegrid-parent-".$parentTreeID." '>";
                                                                        echo "<td>";
                                                                            //echo str_repeat('&nbsp;', $indent * 5);
                                                                            //echo $showArrow;
                                                                            echo $account->name;
                                                                        echo "</td>";
                                                                        echo "<td>";
                                                                            echo "0.0000";
                                                                        echo "</td>";
                                                                        echo "</tr>";

                                                                        $treeRowId++;
                                                                    }

                                                                }


                                                                if ($group->children->isNotEmpty()) {
                                                                    foreach ($group->children as $child) {
                                                                        renderOptions($child, $indent + 1, $allAccounts, $treeRowId++, $childOf = $parentTreeID);
                                                                    }
                                                                }
                                                            }

                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <table class="table table-bordered table-hover assetTree">
                                                                       @php
                                                                                    foreach ($assetGroups as $group){
                                                                                        renderOptions($group, 0, $allAccounts, $treeRowId = 1);
                                                                                    }
                                                                        @endphp
                                                                </table>
                                                            </td>
                                                            <td>
                                                                <table class="table table-bordered table-hover equityLiabilityTree">

                                                                        @php
                                                                                    foreach ($equityLiabilityGroups as $group){
                                                                                        renderOptions($group, 0, $allAccounts, $treeRowId = 1);
                                                                                    }
                                                                        @endphp

                                                                </table>
                                                            </td>
                                                        </tr>


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
    <script src="{{ versionedAsset('assets/plugins/jquery-treegrid/js/jquery.treegrid.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>

    <script type="text/javascript">
            $(document).ready(function() {
                $('.assetTree, .equityLiabilityTree').treegrid();
            });
        </script>
@endsection
