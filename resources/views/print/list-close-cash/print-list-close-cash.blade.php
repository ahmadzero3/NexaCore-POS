<!DOCTYPE html>
<html lang="ar" dir="{{ $appDirection }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('payment.close_cash_list') }}</title>
    @include('print.common.css')
</head>

<body>
    <div class="invoice-container">
        <span class="invoice-name">{{ __('payment.close_cash_list') }}</span>
        <div class="invoice">
            <table class="header">
                <tr>
                    @include('print.common.header')

                    <td class="bill-info">
                        <span class="bill-number">{{ __('app.reports') }}: {{ $closeCash->id }}</span><br>
                        <span class="cu-fs-16">{{ __('app.date') }}: {{ \Carbon\Carbon::parse($closeCash->created_at)->format('d/m/Y') }}</span><br>
                        <span class="cu-fs-16">{{ __('app.time') }}: {{ \Carbon\Carbon::parse($closeCash->created_at)->format('H:i') }}</span>
                    </td>
                </tr>
            </table>

            <table class="table-bordered custom-table table-compact">
                <thead>
                    <tr>
                        <th>{{ __('app.opening_balance') }}</th>
                        <th>{{ __('app.today_income') }}</th>
                        <th>{{ __('app.total_income') }}</th>
                        <th>{{ __('app.today_expense') }}</th>
                        <th>{{ __('app.closing_balance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-end">{{ $formatNumber->formatWithPrecision($closeCash->opening_balance) }}</td>
                        <td class="text-end">{{ $formatNumber->formatWithPrecision($closeCash->today_income) }}</td>
                        <td class="text-end">{{ $formatNumber->formatWithPrecision($closeCash->total_income) }}</td>
                        <td class="text-end">{{ $formatNumber->formatWithPrecision($closeCash->today_expenses) }}</td>
                        <td class="text-end">{{ $formatNumber->formatWithPrecision($closeCash->balance) }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="table-bordered custom-table table-compact mt-20">
                <thead>
                    <tr>
                        <th>{{ __('app.created_by') }}</th>
                        <th>{{ __('app.created_at') }}</th>
                        <th>{{ __('app.update_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $closeCash->created_by_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($closeCash->created_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($closeCash->updated_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="summary-section">
                <table class="table-bordered custom-table table-compact">
                    <tr>
                        <td class="text-end fw-bold" width="70%">{{ __('app.amount_in_words') }}:</td>
                        <td class="text-end">{{ $formatNumber->spell($closeCash->balance) }}</td>
                    </tr>
                </table>
            </div>

            @include('print.common.terms-conditions')
            @include('print.common.bank-signature')

        </div>
    </div>
    <script>
        // Auto-print functionality
        window.addEventListener('DOMContentLoaded', (event) => {
            try {
                window.print();
            } catch(e) {
                console.error('Print failed:', e);
            }
        });

        // Close window after print
        window.onafterprint = function(event) {
            setTimeout(() => {
                window.close();
            }, 500);
        };
    </script>
</body>

</html>