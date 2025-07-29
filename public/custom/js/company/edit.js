$(function() {
    "use strict";

    let originalButtonText;

    $("#companyForm, #prefixForm, #generalForm, #itemForm, #printForm, #moduleForm").on("submit", function(e) {
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
    function afterSeccessOfAjaxRequest(formObject){
        formAdjustIfSaveOperation(formObject);
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
        jqxhr.done(function(data) {
            iziToast.success({title: 'Success', layout: 2, message: data.message});
            // Actions to be performed after response from the AJAX request
            if (typeof afterSeccessOfAjaxRequest === 'function') {
                afterSeccessOfAjaxRequest(formArray.formObject);
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

    /**
     * Image Browse & Reset
     * */
    function loadImageBrowser(uploadedImage, accountFileInput, accountImageReset) {
        if (uploadedImage.length) {
            const avatarSrc = uploadedImage.attr("src");

            accountFileInput.on("change", function() {
              if (accountFileInput[0].files[0]) {

                uploadedImage.attr("src", window.URL.createObjectURL(accountFileInput[0].files[0]));
              }
            });

            accountImageReset.on("click", function() {
              accountFileInput[0].value = "";
              uploadedImage.attr("src", avatarSrc);
            });
        }

    }

    $(document).ready(function() {
        loadImageBrowser($("#uploaded-image-1"), $(".input-box-class-1"), $(".image-reset-class-1"));
        loadImageBrowser($("#uploaded-image-2"), $(".input-box-class-2"), $(".image-reset-class-2"));


        // Trigger the change event on page load to set the initial state
        updateAvgUpdatePurchasePriceRadioButtons();

        // Trigger the change event on page load to set the initial state
        isBatchNoisCompulsaryRadioButtons();
    });

    // Listen for changes on the checkbox
    $('#auto_update_purchase_price').change(updateAvgUpdatePurchasePriceRadioButtons);
    function updateAvgUpdatePurchasePriceRadioButtons() {
        var isChecked = $('#auto_update_purchase_price').is(':checked');
        $('input[name="auto_update_average_purchase_price"]').prop('disabled', !isChecked);
    }

    // Listen for changes on the checkbox
    $('#enable_batch_tracking').change(isBatchNoisCompulsaryRadioButtons);
    function isBatchNoisCompulsaryRadioButtons() {
        var isChecked = $('#enable_batch_tracking').is(':checked');
        $('input[name="is_batch_compulsory"]').prop('disabled', !isChecked);
    }

    //First Load:
    function first_load(){
        $(".company_tab").show();
        $(".prefix_tab").hide();
        $(".general_tab").hide();
        $(".item_tab").hide();
        $(".print_tab").hide();
        $(".module_tab").hide();
    }
    function show_prefix() {
        $(".company_tab").hide();
        $(".prefix_tab").show();
        $(".general_tab").hide();
        $(".item_tab").hide();
        $(".print_tab").hide();
        $(".module_tab").hide();
    }
    function show_general() {
        $(".prefix_tab").hide();
        $(".company_tab").hide();
        $(".general_tab").show();
        $(".item_tab").hide();
        $(".print_tab").hide();
        $(".module_tab").hide();
    }
    function show_item() {
        $(".prefix_tab").hide();
        $(".company_tab").hide();
        $(".general_tab").hide();
        $(".item_tab").show();
        $(".print_tab").hide();
        $(".module_tab").hide();
    }
    function show_print() {
        $(".prefix_tab").hide();
        $(".company_tab").hide();
        $(".general_tab").hide();
        $(".item_tab").hide();
        $(".print_tab").show();
        $(".module_tab").hide();
    }

    function show_module() {
        $(".prefix_tab").hide();
        $(".company_tab").hide();
        $(".general_tab").hide();
        $(".item_tab").hide();
        $(".print_tab").hide();
        $(".module_tab").show();
    }

    first_load();

    $(".show_company").on("click", function(){
        first_load();
    });
    $(".show_prefix").on("click", function(){
        show_prefix();
    });
    $(".show_general").on("click", function(){
        show_general();
    });
    $(".show_item").on("click", function(){
        show_item();
    });
    $(".show_print").on("click", function(){
        show_print();
    });
    $(".show_module").on("click", function(){
        show_module();
    });

});//main function
