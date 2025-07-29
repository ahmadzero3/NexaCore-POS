<link rel="stylesheet" href="{{ asset('custom/css/transaction/apply-close-cash-print.css') }}">


<div class="receipt">
    <div class="header">
        <div class="header-top">
            <span class="date">{{ \Carbon\Carbon::now()->format('d-m-Y') }}</span>
            <h2 class="logo-text">{{ app('site')['name'] }}</h2>
            <span class="time">{{ \Carbon\Carbon::now()->format('H:i:s') }}</span>
        </div>
        <div class="header-bottom">
            <span class="user">User: {{ $data['userName'] }}</span>
        </div>
    </div>
    <div class="content">
        <table class="table table-bordered table-striped no-stripe mb-0 table-three">
            <tbody>
                <tr class="table-3-tr-1">
                    <td class="w-50 bg-gray text-right tr-p">Opening Balance</td>
                    <td class="w-50 bg-gray text-right tr-p">{{ number_format($data['openingBalance'], 2) }}</td>
                </tr>
                <tr class="table-3-tr-2">
                    <td class="w-50 bg-gray text-right tr-p">Today Income</td>
                    <td class="w-50 bg-gray text-right tr-p">{{ number_format($data['todayIncome'], 2) }}</td>
                </tr>
                <tr class="bg-green">
                    <td class="w-50 text-right tr-p">Total Income</td>
                    <td class="w-50 text-right tr-p">{{ number_format($data['totalIncome'], 2) }}</td>
                </tr>
                <tr class="bg-red">
                    <td class="w-50 text-right tr-p">Today Expense (−)</td>
                    <td class="w-50 text-right tr-p">{{ number_format($data['todayExpenses'], 2) }}</td>
                </tr>
                <tr class="bg-blue">
                    <td class="w-50 text-right tr-p">Balance / Cash In Hand</td>
                    <td class="w-50 text-right tr-p">{{ number_format($data['closingBalance'], 2) }}</td>
                </tr>
                <tr class="bg-yellow">
                    <td class="w-50 text-right tr-p">
                        <h4><b>Today Closing Balance</b></h4>
                    </td>
                    <td class="w-50 text-right tr-p">
                        <h4><b>{{ number_format($data['closingBalance'], 2) }}</b></h4>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="footer">
        <div class="left-section">NexaCore POS</div>
        <div class="right-section">All rights reserved</div>
    </div>
</div>

<script src="{{ asset('custom/js/transaction/apply-close-cash-print.js') }}"></script>