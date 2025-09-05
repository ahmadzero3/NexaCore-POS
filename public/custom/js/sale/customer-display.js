function updateCustomerDisplay(items, totals) {
    const tbody = document.getElementById('items-tbody');
    tbody.innerHTML = '';

    if (items.length === 0) {
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

    document.getElementById('display-total').textContent = totals.grandTotal || '0.00';
}

window.addEventListener('message', function (event) {
    if (event.data.type === 'UPDATE_CUSTOMER_DISPLAY') {
        updateCustomerDisplay(event.data.items, event.data.totals);
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
