function updateCustomerDisplay(items, totals, ticker = null) {
    const tbody = document.getElementById('items-tbody');
    if (!tbody) return; // ✅ avoid null errors

    tbody.innerHTML = '';

    if (!items || items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="no-items">No items added yet</td></tr>';
    } else {
        items.forEach(item => {
            const row = tbody.insertRow();
            row.insertCell(0).textContent = item.name;
            row.insertCell(1).textContent = item.quantity;
            row.insertCell(2).textContent = item.unitPrice;
            row.insertCell(3).textContent = item.total;
        });
    }

    document.getElementById('display-total').textContent = (totals?.grandTotal) || '0.00';

    // ✅ update ticker live if sent
    if (ticker && document.querySelector('.ticker-content')) {
        document.querySelector('.ticker-content').textContent = ticker;
    }
}

window.addEventListener('message', function (event) {
    if (event.data.type === 'UPDATE_CUSTOMER_DISPLAY') {
        updateCustomerDisplay(event.data.items, event.data.totals, event.data.ticker);
    } else if (
        event.data.type === 'WAREHOUSE_CHANGED' ||
        event.data.type === 'INITIAL_WAREHOUSE_INFO'
    ) {
        document.getElementById('warehouse-name').textContent = event.data.warehouseName || 'N/A';
    }
});

if (window.opener) {
    window.opener.postMessage({ type: 'CUSTOMER_DISPLAY_READY' }, '*');
    window.opener.postMessage({ type: 'REQUEST_INITIAL_WAREHOUSE' }, '*');
}
