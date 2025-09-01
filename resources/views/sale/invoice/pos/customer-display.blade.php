@extends('layouts.app-pos')

@section('title', 'Customer Display')


@section('css')
    <link rel="stylesheet" href="{{ asset('custom/css/customer-display.css') }}">
@endsection

@section('content')
    <div class="customer-display-container">
        <div class="header">
            <h1>{{ app('site')['name'] }}</h1>
            <h4>Your Order</h4>
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
                <tbody id="items-list">
                    <tr class="empty-row">
                        <td colspan="4" class="no-items">No items added yet</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="totals">
            <div>
                <span>Subtotal:</span>
                <span id="subtotal">0.00</span>
            </div>

            <div class="grand-total">
                <span>Grand Total:</span>
                <span id="grand-total">0.00</span>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('custom/js/sale/customer-display.js') }}"></script>
@endsection
