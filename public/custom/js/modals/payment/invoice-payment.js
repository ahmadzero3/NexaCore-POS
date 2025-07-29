$(function() {
	"use strict";

    let originalButtonText;

    

    let openModal = $('#invoicePaymentModal');

    const makePaymentForm = $("#paymentForm");

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

    $(document).on('click', '.make-payment', function() {
        var invoiceId = $(this).attr('data-invoice-id');
        var url = baseURL + `/payment/${paymentFor}/get/`;
        ajaxGetRequest(url, invoiceId, 'make-payment');
    });

    $(document).on('click', '.payment-history', function() {
        var invoiceId = $(this).attr('data-invoice-id');
        var url = baseURL + `/payment/${paymentFor}/history/`;
        ajaxGetRequest(url ,invoiceId, 'payment-history');
    });

    $(document).on('click', '.delete-payment', function() {
        var paymentId = $(this).closest('tr').attr('id');
        deletePaymentRequest(paymentId);
    });
    /**
     * Caller:
     * Function to single delete request
     * Call Delete Request
    */
    async function deletePaymentRequest(paymentId) {
        const confirmed = await confirmAction();//Defined in ./common/common.js
        if (confirmed) {
            var url = baseURL + `/payment/${paymentFor}/delete/`;
            ajaxGetRequest(url ,paymentId, 'delete-payment');
        }
    }


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
              if(_from == 'delete-payment'){
                handleDeleteResponse(response);
              }
              else if (_from == 'payment-history') {
                handleHistoryResponse(response);
              } else {
                //make-payment
                handlePaymentResponse(response);
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
        //Party Selection
        var partyIdSelect = makePaymentForm.find('select[name="party_id"]');
        var newOption = $(`<option value="${response.data.party_id}">${response.data.party_name}</option>`); // Create a new option element
        partyIdSelect.html('').append(newOption);

        //Transaction date
        makePaymentForm.find('input[name="receipt_no"]').val('');

        //balance
        makePaymentForm.find('input[name="balance"]').val(response.data.balance);

        //Payment
        makePaymentForm.find('input[name="payment"]').val(response.data.balance);

        //Invoice Id
        makePaymentForm.find('input[name="invoice_id"]').val(response.data.invoice_id);

        //Form Name
        $('.form-heading').html(response.data.form_heading);

        openModal.modal('show');
    }

    function handleHistoryResponse(response, showModel = true) {
        $("#supplier-name").text(response.data.party_name);
        $("#invoice-number").text(response.data.invoice_code);
        $("#invoice-date").text(response.data.invoice_date);
        $("#balance-amount").text(response.data.balance_amount);
        $("#paid-amount").text(response.data.paid_amount);

        let totalAmount = 0;
        
        var table = $('#payment-history-table tbody');

        table.empty(); // Clear existing rows

        $.each(response.data.paymentTransactions, function(index, payment) {
            totalAmount += parseFloat(payment.amount);
            var newRow = `
                <tr id="${payment.payment_id}">
                    <td>${payment.transaction_date}</td>
                    <td>${payment.reference_no}</td>
                    <td>${payment.payment_type}</td>
                    <td class="text-end">${payment.amount}</td>
                    <td>
                        <div class="d-flex order-actions justify-content-center">
                            <a href="${baseURL}/payment/${paymentFor}/print/${payment.payment_id}" target="_blank" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Print"><i class="bx bxs-printer"></i></a>
                            <a href="${baseURL}/payment/${paymentFor}/pdf/${payment.payment_id}" target="_blank" class="ms-1 text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="PDF"><i class="bx bxs-file-pdf"></i></a>
                            <a href="javascript:;" role="button" class="ms-1 delete-payment text-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Delete"><i class="bx bxs-trash"></i></a>
                        </div>
                    </td>
                </tr>
            `;

            table.append(newRow);
        });

        // Create a new row for total amount
        const totalRow = `
            <tr>
                <th colspan="3" class="text-end">Total Amount:</th>
                <th class="text-end">${_parseFix(totalAmount)}</th>  <th></th>
            </tr>
        `;
        table.append(totalRow);

        //show only if not shown, in delete payment condition no need to show modal
        if(showModel){
            $('#invoicePaymentHistoryModal').modal('show');
        }

        setTooltip();
    }

    function handleDeleteResponse(response) {
        iziToast.success({title: 'Success', layout: 2, message: response.message});
        handleHistoryResponse(response, false);
        loadDatatables();
    }

    

});//main function
