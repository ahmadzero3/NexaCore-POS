function updateCustomerDisplay(items, totals) {
    const itemsList = document.getElementById('items-list');
    itemsList.innerHTML = '';

    if (items.length === 0) {
        itemsList.innerHTML = '<tr><td colspan="4" class="no-items">No items added yet</td></tr>';
        return;
    }

    items.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.name}</td>
            <td>${item.quantity}</td>
            <td>${item.unitPrice}</td>
            <td>${item.total}</td>
        `;
        itemsList.appendChild(row);
    });

    document.getElementById('subtotal').textContent = totals.subtotal || '0.00';
    document.getElementById('grand-total').textContent = totals.grandTotal || '0.00';
}

window.addEventListener('message', function(event) {
    if (event.data.type === 'UPDATE_CUSTOMER_DISPLAY') {
        updateCustomerDisplay(event.data.items, event.data.totals);
    }
});

if (window.opener) {
    window.opener.postMessage({
        type: 'CUSTOMER_DISPLAY_READY'
    }, '*');
}
