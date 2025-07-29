$(function() {
	"use strict";

    let originalButtonText;

    

    let emailModal = $('#emailModal');

    let smsModal = $('#smsModal');

    const makeEmailForm = $("#emailForm");

    const makeSMSForm = $("#smsForm");

    const $attachment = $('#attachment');

    const $removeBtn = $('#removeBtn');

    makeEmailForm.on("submit", function(e) {
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
        //
    }
    function closeModalAndAddOption(response){
        /*Close the Model*/
        emailModal.modal('hide');
    }

    $(document).on('click', '.notify-through-email', function() {
        var invoiceId = $(this).attr('data-id');
        var model = $(this).attr('data-model');//Sale, Purchase, Expense
        var url = baseURL + `/${model}/email/get-content/`;
        ajaxGetRequest(url, invoiceId, 'notify-through-email');
    });
    $(document).on('click', '.notify-through-sms', function() {
        var invoiceId = $(this).attr('data-id');
        var model = $(this).attr('data-model');//Sale, Purchase, Expense
        var url = baseURL + `/${model}/sms/get-content/`;
        ajaxGetRequest(url, invoiceId, 'notify-through-sms');
    });

    function ajaxGetRequest(url, id, _from) {
          $.ajax({
            url: url + id,
            type: 'GET',
            headers: {
              'X-CSRF-TOKEN': makeEmailForm.find('input[name="_token"]').val(),
            },
            beforeSend: function() {
              showSpinner();
            },
            success: function(response) {
              handlePaymentResponse(response, _from);
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

    function handlePaymentResponse(response, _from) {
        if(_from === 'notify-through-email'){
            //email id
            makeEmailForm.find('input[name="email"]').val(response.email);

            //subject
            makeEmailForm.find('input[name="subject"]').val(response.subject);

            //Content
            makeEmailForm.find('textarea[name="content"]').val(response.content);

            //Clear attachements
            resetAttachment();

            emailModal.modal('show');    
        }else{
            //SMS Model
            makeSMSForm.find('input[name="mobile_numbers"]').val(response.mobile);

            //SMS Content
            makeSMSForm.find('textarea[name="message"]').val(response.content);

            smsModal.modal('show');    
        }
        
    }

    /**
     * Email File Attachment code
     * */
    $attachment.on('change', function() {
        if (this.files.length > 0) {
            $removeBtn.prop('disabled', false)
                      .removeClass('btn-outline-secondary')
                      .addClass('btn-outline-danger');
        } else {
            resetAttachment();
        }
    });

    $removeBtn.on('click', function(){
        resetAttachment();
    });

    function resetAttachment() {
        $attachment.val('');
        $removeBtn.prop('disabled', true)
                  .removeClass('btn-outline-danger')
                  .addClass('btn-outline-secondary');
    }
  

});//main function
