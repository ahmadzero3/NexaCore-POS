$(function() {
	"use strict";

    let originalButtonText;

    

    let openModal = $('#chequeTransferModal');

    const makePaymentForm = $("#chequeTransferForm");

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
        loadDatatables();
    }
    function closeModalAndAddOption(response){
        /*Close the Model*/
        openModal.modal('hide');
    }

    $(document).on('click', '.make-cheque-transfer', function() {
        var transactionId = $(this).attr('data-cheque-transaction-id');
        var url = baseURL + `/transaction/cheque/details/get/`;
        ajaxGetRequest(url, transactionId, 'make-cheque-transfer');
    });

    $(document).on('click', '.reopen-cheque-transfer', async function() {
        const confirmed = await confirmAction(); // Defined in ./common/common.js
        if (confirmed) {
            // Submit reopen request
            var transactionId = $(this).data('cheque-transaction-id');
            var url = baseURL + '/transaction/cheque/re-open/';
            await ajaxGetRequest(url, transactionId, 'reopen-cheque-transfer');
        }
    });

   
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
                if(_from == 'make-cheque-transfer'){
                    handlechequeTransfer(response.data);
                }
                else if( _from == 'reopen-cheque-transfer'){
                    iziToast.success({title: 'Success', layout: 2, message: response.message});
                    handleChequeReopen(response.data)
                }else{
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

    function handlechequeTransfer(data) {
        //Adjustment date
        makePaymentForm.find('input[name="transaction_date"]').val(data.transaction_date);

        //Set current date
        makePaymentForm.find('input[name="deposit_date"]').flatpickr({
            dateFormat: dateFormatOfApp,//Defined in script.js
            defaultDate: new Date(),
        });;
        //Received from (Party name)
        makePaymentForm.find('input[id="received_from"]').val(data.received_from);

        //amount
        makePaymentForm.find('input[name="amount"]').val(data.amount);

        //Note
        makePaymentForm.find('textarea[name="note"]').val(data.note);

        makePaymentForm.find('input[name="cheque_transaction_id"]').val(data.id);

        makePaymentForm.find('label[id="label_deposit_or_transfer"]').text(data.label_deposit_or_transfer);
        makePaymentForm.find('label[id="label_transfer_from_or_to"]').text(data.label_transfer_from_or_to);

        openModal.modal('show');
    }

    function handleChequeReopen(data) {
        //Reload datatable
        loadDatatables();
    }
    
});//main function
