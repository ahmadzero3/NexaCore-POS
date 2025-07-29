$(function() {
	"use strict";

    let originalButtonText;

    let openModal = $('#loadSoldItemsModal');

    const makePaymentForm = $("#paymentForm");

    let party = $('#party_id');
    let modalItemId = $('#modal_item_id');

    const paymentFor = $("#payment_for").val();//purchase, purchase_return, sale, sale return

    makePaymentForm.on("submit", function(e) {
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
    }
    function afterCallAjaxResponse(formObject){
        enableSubmitButton(formObject);
    }
    function afterSeccessOfAjaxRequest(formObject, response){
        formAdjustIfSaveOperation(formObject);
        closeModalAndAddOption(response);
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
            iziToast.success({title: 'Success', layout: 2, message: response.message});
            // Actions to be performed after response from the AJAX request
            if (typeof afterSeccessOfAjaxRequest === 'function') {
                afterSeccessOfAjaxRequest(formArray.formObject, response);
            }
        });
        jqxhr.fail(function(response) {
                var message = response.responseJSON.message;
                iziToast.error({title: 'Error', layout: 2, message: message});
        });
        jqxhr.always(function() {
            // Actions to be performed after the AJAX request is completed, regardless of success or failure
            if (typeof afterCallAjaxResponse === 'function') {
                afterCallAjaxResponse(formArray.formObject);
            }
        });
    }

    function formAdjustIfSaveOperation(formObject){
        //const _method = formObject.find('input[name="_method"]').val();
        loadDatatables();
    }
    function closeModalAndAddOption(response){
        /*Close the Model*/
        openModal.modal('hide');
    }

    $(document).on('click', '#show_load_items_modal', function() {
        var partyId = party.val();
        if(!partyId){
            iziToast.error({title: 'Error', layout: 2, message:"Please Select Party"});
            party.select2('open');
            return;
        }

        openModal.modal('show');
    });

    $(document).on('click', '.load-sold-items', function() {
        var partyId = party.val();
        var itemId = modalItemId.val() || '';
        var url = baseURL + `/sale/invoice/sold-items/`+partyId+`/`;
        ajaxGetRequest(url ,itemId, 'sold-items');
    });

    // $(document).on('click', '.delete-payment', function() {
    //     var paymentId = $(this).closest('tr').attr('id');
    //     deletePaymentRequest(paymentId);
    // });
    /**
     * Caller:
     * Function to single delete request
     * Call Delete Request
    */
    // async function deletePaymentRequest(paymentId) {
    //     const confirmed = await confirmAction();//Defined in ./common/common.js
    //     if (confirmed) {
    //         var url = baseURL + `/payment/${paymentFor}/delete/`;
    //         ajaxGetRequest(url ,paymentId, 'delete-payment');
    //     }
    // }


    function ajaxGetRequest(url, id, _from) {
          $.ajax({
            url: url + id,
            type: 'GET',
            headers: {
              'X-CSRF-TOKEN': makePaymentForm.find('input[name="_token"]').val(),
            },
            beforeSend: function() {
              showSpinner();
            },
            success: function(response) {
              if (_from == 'sold-items') {
                handleHistoryResponse(response);
              } else {
               //
              }
            },
            error: function(response) {
               var message = response.responseJSON.message;
               iziToast.error({title: 'Error', layout: 2, message: message});
            },
            complete: function() {
              hideSpinner();
            },
          });
    }

    function handlePaymentResponse(response) {
        //
    }

    function handleHistoryResponse(response, showModel = true) {

        $("#party-name").text(response.party_name);

        let totalQuantity = 0;

        var table = $('#payment-history-table tbody');

        table.empty(); // Clear existing rows

        var newRow = '';
        $.each(response.sold_items, function(index, item) {
            totalQuantity += parseFloat(item.quantity);
            newRow = `
                <tr id="${item.id}">
                    <td>${item.sale_code}</td>
                    <td>${item.sale_date}</td>
                    <td class="fw-bold">${item.warehouse_name}</td>
                    <td>${item.item_name}</td>
                    <td>${item.brand_name}</td>
                    <td class="text-end">${item.unit_price}</td>
                    <td class="text-end">${item.quantity}</td>
                    <td class="text-end ${itemSettings.show_discount === 0 ? 'd-none' : ''}">${item.discount_amount}</td>
                    <td class="text-end ${appTaxType === 'no-tax' ? 'd-none' : ''}">${item.tax_amount}</td>
                    <td class="text-end">${item.total}</td>
                    <td class="d-none">
                        <div class="d-flex order-actions justify-content-center">
                            <a href="${baseURL}/payment/print/${item.id}" target="_blank" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Print"><i class="bx bxs-printer"></i></a>
                            <a href="${baseURL}/payment/pdf/${item.id}" target="_blank" class="ms-1 text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="PDF"><i class="bx bxs-file-pdf"></i></a>
                            <a href="javascript:;" role="button" class="ms-1 delete-payment text-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Delete"><i class="bx bxs-trash"></i></a>
                        </div>
                    </td>
                </tr>
            `;

            table.append(newRow);
        });

        if(newRow === ''){
            const emptyRow = `
                <tr>
                    <td colspan="11" class="text-center">No data available</td>
                </tr>
            `;
            table.append(emptyRow);
        }
        // Create a new row for total amount
        const totalRow = `
            <tr>
                <th colspan="6" class="text-end">Total:</th>
                <th class="text-end">${_parseFix(totalQuantity)}</th>
                <th colspan="3"></th>
            </tr>
        `;
        table.append(totalRow);

        //show only if not shown, in delete payment condition no need to show modal
        if(showModel){
            //openModal.modal('show');
        }

        setTooltip();
    }

    function handleDeleteResponse(response) {
        //
    }

    $(document).ready(function () {
        /**
         * POPUP modal has the item selection ajax
         */
        initSelect2ItemList($('#loadSoldItemsModal'));
    });

});//main function
