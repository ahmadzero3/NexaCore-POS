@extends('layouts.app-pos')

@section('title', 'Customer Display')

@section('css')
    <link rel="stylesheet" href="{{ asset('custom/css/customer-display.css') }}">
@endsection

@section('content')
    <div class="customer-display-container">
        <div class="header">
            <h1>{{ app('site')['name'] }}</h1>
            <h4>
                Your Order is From (Branch: <span id="warehouse-name" class="warehouse-name">N/A</span>)
                of {{ app('company')['name'] ?? '' }}
            </h4>
        </div>

        <div class="news-ticker">
            <div class="ticker-content">
                <span>
                    {{ app('company')['name'] ?? '' }} |
                    {{ app('company')['email'] ?? '' }} |
                    {{ app('company')['mobile'] ?? '' }} |
                    {{ app('company')['address'] ?? '' }}
                </span>
                <span>
                    {{ app('company')['name'] ?? '' }} |
                    {{ app('company')['email'] ?? '' }} |
                    {{ app('company')['mobile'] ?? '' }} |
                    {{ app('company')['address'] ?? '' }}
                </span>
                <span>
                    {{ app('company')['name'] ?? '' }} |
                    {{ app('company')['email'] ?? '' }} |
                    {{ app('company')['mobile'] ?? '' }} |
                    {{ app('company')['address'] ?? '' }}
                </span>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="items-tbody">
                    <tr class="empty-row">
                        <td colspan="4" class="no-items">No items added yet</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="totals">
            <div class="total-row final-total">
                <span>Total:</span>
                <span id="display-total">0.00</span>
            </div>
        </div>
    </div>

    @include('layouts.footer')
@endsection

@section('js')
    <script src="{{ asset('custom/js/sale/customer-display.js') }}"></script>
@endsection
