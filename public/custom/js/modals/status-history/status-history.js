$(function() {
	"use strict";

    let originalButtonText;



    let openModal = $('#statusHistoryModal');

    const statusHistoryForm = $("#statusHistoryForm");

    const historyOf = $("#history_of").val();//sale-order

    statusHistoryForm.on("submit", function(e) {
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

    // $(document).on('click', '.make-payment', function() {
    //     var recordId = $(this).attr('data-id');
    //     var url = baseURL + `/status-history/${historyOf}/get/`;
    //     ajaxGetRequest(url, recordId, 'make-payment');
    // });

    $(document).on('click', '.status-history', function() {
        var recordId = $(this).attr('data-id');
        var url = baseURL + `/status-history/${historyOf}/`;
        ajaxGetRequest(url ,recordId, 'payment-history');
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
    //         var url = baseURL + `/payment/${historyOf}/delete/`;
    //         ajaxGetRequest(url ,paymentId, 'delete-payment');
    //     }
    // }


    function ajaxGetRequest(url, id, _from) {
          $.ajax({
            url: url + id,
            type: 'GET',
            headers: {
              'X-CSRF-TOKEN': statusHistoryForm.find('input[name="_token"]').val(),
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



    function handleHistoryResponse(response, showModel = true) {

        var table = $('#status-history-table tbody');

        table.empty(); // Clear existing rows

        var count = 1;

        console.table(response.data.statusHistory);

        $.each(response.data.statusHistory, function(index, history) {
            console.log(count);

            var newRow = `
                <tr id="${count++}">
                    <td>${count}</td>
                    <td>${history.status_date}</td>
                    <td>${history.status}</td>
                    <td>${history.created_by}</td>
                    <td>${history.updated_by}</td>
                </tr>
            `;

            table.append(newRow);
        });

        //show only if not shown, in delete payment condition no need to show modal
        if(showModel){
            openModal.modal('show');
        }

        setTooltip();
    }

    function handleDeleteResponse(response) {
        //
    }

    function handlePaymentResponse(response) {
        //
    }



});//main function
