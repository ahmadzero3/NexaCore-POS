$(function() {
    "use strict";

    let originalButtonText;

    const tableId = $('#gstr2Report');

    /**
     * Language
     * */
    const _lang = {
                total : "Total",
                noRecordsFound : "No Records Found!!",
            };

    $("#reportForm").on("submit", function(e) {
        e.preventDefault();
        const form = $(this);
        const formArray = {
            formId: form.attr("id"),
            csrf: form.find('input[name="_token"]').val(),
            url: form.closest('form').attr('action'),
            formObject : form,
        };
        ajaxRequest(formArray);
    });

    function disableSubmitButton(form) {
        originalButtonText = form.find('button[type="submit"]').text();
        form.find('button[type="submit"]')
            .prop('disabled', true)
            .html('  <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...');
    }

    function enableSubmitButton(form) {
        form.find('button[type="submit"]')
            .prop('disabled', false)
            .html(originalButtonText);
    }

    function beforeCallAjaxRequest(formObject){
        disableSubmitButton(formObject);
        showSpinner();
    }
    function afterCallAjaxResponse(formObject){
        enableSubmitButton(formObject);
        hideSpinner();
    }
    function afterSeccessOfAjaxRequest(formObject, response){
        formAdjustIfSaveOperation(response);
    }
    function afterFailOfAjaxRequest(formObject){
        showNoRecordsMessageOnTableBody();
    }

    function ajaxRequest(formArray){
        var formData = new FormData(document.getElementById(formArray.formId));
        var jqxhr = $.ajax({
            type: 'POST',
            url: formArray.url,
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': formArray.csrf
            },
            beforeSend: function() {
                // Actions to be performed before sending the AJAX request
                if (typeof beforeCallAjaxRequest === 'function') {
                    beforeCallAjaxRequest(formArray.formObject);
                }
            },
        });
        jqxhr.done(function(response) {
            // Actions to be performed after response from the AJAX request
            if (typeof afterSeccessOfAjaxRequest === 'function') {
                afterSeccessOfAjaxRequest(formArray.formObject, response);
            }
        });
        jqxhr.fail(function(response) {
            var message = response.responseJSON.message;
            iziToast.error({title: 'Error', layout: 2, message: message});
            if (typeof afterFailOfAjaxRequest === 'function') {
                afterFailOfAjaxRequest(formArray.formObject);
            }
        });
        jqxhr.always(function() {
            // Actions to be performed after the AJAX request is completed, regardless of success or failure
            if (typeof afterCallAjaxResponse === 'function') {
                afterCallAjaxResponse(formArray.formObject);
            }
        });
    }

    function formAdjustIfSaveOperation(response){
        var tableBody = tableId.find('tbody');

        var id = 1;
        var tr = "";
        
        var totalInvoiceValue = parseFloat(0);
        var totalTaxableValue = parseFloat(0);
        var totalCgstValue = parseFloat(0);
        var totalSgstValue = parseFloat(0);
        var totalIgstValue = parseFloat(0);
        
        $.each(response.data, function(index, item) {
            totalInvoiceValue += parseFloat(item.invoice_value);
            totalTaxableValue += parseFloat(item.taxable_value);
            totalCgstValue += parseFloat(item.cgst_value);
            totalSgstValue += parseFloat(item.sgst_value);
            totalIgstValue += parseFloat(item.igst_value);

            tr  +=`
                <tr>
                    <td>${id++}</td>
                    <td>${item.tax_number}</td>
                    <td>${item.party_name}</td>
                    <td>${item.transaction_type}</td>
                    <td>${item.invoice_or_bill_code}</td>
                    <td>${item.transaction_date}</td>
                    <td class='text-end' data-tableexport-celltype="number" >${_formatNumber(item.invoice_value)}</td>
                    <td class='text-end' data-tableexport-celltype="number" >${_formatNumber(item.tax_rate)}</td>
                    <td class='text-end' data-tableexport-celltype="number" >${_formatNumber(item.taxable_value)}</td>
                    <td class='text-end' data-tableexport-celltype="number" >${_formatNumber(item.cgst_value)}</td>
                    <td class='text-end' data-tableexport-celltype="number" >${_formatNumber(item.sgst_value)}</td>
                    <td class='text-end' data-tableexport-celltype="number" >${_formatNumber(item.igst_value)}</td>
                    <td>${item.state_of_supply}</td>
                </tr>
            `;
        });

        tr  +=`
            <tr class='fw-bold'>
                <td colspan='0' class='text-end tfoot-first-td'>${_lang.total}</td>
                <td class='text-end' data-tableexport-celltype="number">${_formatNumber(totalInvoiceValue)}</td>
                <td></td>
                <td class='text-end' data-tableexport-celltype="number">${_formatNumber(totalTaxableValue)}</td>
                <td class='text-end' data-tableexport-celltype="number">${_formatNumber(totalCgstValue)}</td>
                <td class='text-end' data-tableexport-celltype="number">${_formatNumber(totalSgstValue)}</td>
                <td class='text-end' data-tableexport-celltype="number">${_formatNumber(totalIgstValue)}</td>
                <td></td>
            </tr>
        `;

        // Clear existing rows:
        tableBody.empty();
        tableBody.append(tr);

        /**
         * Set colspan of the table bottom
         * */
        $('.tfoot-first-td').attr('colspan', columnCountWithoutDNoneClass(7));
    }

    function showNoRecordsMessageOnTableBody() {
        var tableBody = tableId.find('tbody');

        var tr = "<tr class='fw-bold'>";
        tr += `<td colspan='0' class='text-end tfoot-first-td text-center'>${_lang.noRecordsFound}</td>"`;
        tr += "</tr>";

        tableBody.empty();
        tableBody.append(tr);

        /**
         * Set colspan of the table bottom
         * */
        $('.tfoot-first-td').attr('colspan', columnCountWithoutDNoneClass(0));
    }
    function columnCountWithoutDNoneClass(minusCount) {
        return tableId.find('thead > tr:first > th').not('.d-none').length - minusCount;
    }

    /** 
     * 
     * Table Exporter
     * PDF, SpreadSheet
     * */
    $(document).on("click", '#generate_pdf', function() {
        tableId.tableExport({type:'pdf',escape:'false'});
    });

    $(document).on("click", '#generate_excel', function() {
        tableId.tableExport({
            formats: ["xlsx"],
            xlsx: {
                onCellFormat: function (cell, e) {
                    if (typeof e.value === 'string') {
                        // Remove commas and convert to number
                        var numValue = parseFloat(e.value.replace(/,/g, ''));
                        if (!isNaN(numValue)) {
                            return numValue;
                        }
                    }
                    return e.value;
                }
            }
        });
    });
    
});//main function
