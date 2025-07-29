<div id="sidebar-pos-overlay" class="sidebar-pos-overlay"></div>
<div id="sidebar-pos" class="sidebar-pos">
    <div class="sidebar-pos-header">
        <span id="sidebar-pos-close" class="sidebar-pos-close">&times;</span>
        <h4>Pending Invoices</h4>
    </div>
    <div class="sidebar-pos-content">
        @php
        $pendingSales = \App\Models\Sale\Sale::where('invoice_status', 'pending')->orderByDesc('created_at')->get();
        @endphp
        @if($pendingSales->count())
        @foreach($pendingSales as $sale)
        <div class="sidebar-pos-sale-item">
            <div><strong>Sale Code:</strong> {{ $sale->sale_code }}</div>
            <div><strong>Sale Date:</strong> {{ $sale->sale_date }}</div>
            <div><strong>Created At:</strong> {{ $sale->created_at }}</div>
            <div class="sidebar-pos-invoice-actions">
                <strong>Invoice Status:</strong>
                <span class="sidebar-pos-invoice-status">
                    {{ ucfirst($sale->invoice_status) }}
                </span>
                <div class="sidebar-pos-buttons">
                    <button class="sidebar-pos-action-btn finish-invoice-btn" title="Finish" data-sale-id="{{ $sale->id }}">
                        <i class='bx bx-check'></i>
                    </button>
                    <button class="sidebar-pos-action-btn" title="Delete" data-sale-id="{{ $sale->id }}">
                        <i class='bx bxs-trash-alt'></i>
                    </button>
                    <button class="sidebar-pos-action-btn" title="Return">
                        <i class='bx bx-redo bx-tada bx-flip-vertical'></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
        @else
        <p>No pending invoices found.</p>
        @endif
    </div>
</div>