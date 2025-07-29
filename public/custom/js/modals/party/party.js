$(function() {
	"use strict";

    let originalButtonText;

    let openModal = $('#partyModal');

    let partyForm = $("#partyForm");

    let partySelectionBox = $('select[name="party_id"]')

    var partyType;

    partyForm.on("submit", function(e) {
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
        const _method = formObject.find('input[name="_method"]').val();
        /* Only if Save Operation called*/
        if(_method.toUpperCase() == 'POST' ){
            var formId = formObject.attr("id");
            $("#"+formId)[0].reset();

            partyForm.find(".datepicker")[0]._flatpickr.setDate(new Date());

        }
    }
    function closeModalAndAddOption(response){
        /*Close the Model*/
        openModal.modal('hide');

        /*Add created item in selection box*/
        var newOption = $('<option>', {
          value: response.data.id,
          text: response.data.first_name +' '+ response.data.last_name,
          currency_id: response.data.currency_id,
        });
        newOption.prop('selected', true);
        partySelectionBox.append(newOption);
        partySelectionBox.trigger('change');
        //setExchangeRateOnInputBox(false);

    }

    $(document).on('click', '.open-party-model', function() {
        partyType = $(this).data('party-type');

        partyForm.find('input[name="party_type"]').val(partyType);

        if(partyType == 'customer'){
            $('.customer-type-div').removeClass('d-none');
        }else{
            $('.customer-type-div').addClass('d-none');
        }
        openModal.modal('show');
    });

    function handleCreditLimit() {
        const $creditLimitInput = $("input[name='credit_limit']");
        const $creditLimitSelect = $("select[name='is_set_credit_limit']");

        // Set initial state on page load
        if($creditLimitSelect.val() == 0) {
            $creditLimitInput
                .val(0)
                .addClass('cursor-not-allowed')
                .attr('readonly', true);
        } else {
            $creditLimitInput
                .removeClass('cursor-not-allowed')
                .attr('readonly', false)
                .focus();
        }
    }

    $("select[name='is_set_credit_limit']").on('change', function() {
        handleCreditLimit();
    });

    $(document).ready(function () {
        handleCreditLimit();
    });

});//main function
