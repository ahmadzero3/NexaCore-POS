
"use strict";

    let originalButtonText;



    const isService = $("input[name='is_service']");

    /**
     * Language
     * */
    const _lang = {
                batchBtnName : "Batch",
                serialBtnName : "Serial",
                enterSerialNumber : "Please Enter Serial Number!",
                productSelected : "Product Selected!",
                serviceSelected : "Service Selected!",
            };

    $("#itemForm").on("submit", function(e) {
        e.preventDefault();
        const form = $(this);

        // Validate opening_quantity
        const openingQuantity = form.find('input[name="opening_quantity"]').val();
        if (!openingQuantity || parseFloat(openingQuantity) <= 0) {
            iziToast.error({title: 'Error', layout: 2, message: 'Stock quantity is required.'});
            return;
        }

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
        pageRedirect(formObject)
    }
    function pageRedirect(formObject){
        var redirectTo = '/item/list';
        setTimeout(function() {
           location.href = baseURL + redirectTo;
        }, 1000);
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
        const _method = formObject.find('input[name="_method"]').val();
        /* Only if Save Operation called*/
        if(_method.toUpperCase() == 'POST' ){
            var formId = formObject.attr("id");
            $("#"+formId)[0].reset();
        }
    }

    /**
     * Select All
     * */
    $("#select_all").on("click", function () {
        var checkBox = $(this).prop("checked");

        $(".row-select").prop("checked", checkBox);

        $(".row-select").each(function() {
            //Permission checkbox class name

            var permissionClass = $(this).attr("id");

            $("."+permissionClass+"_p").prop("checked", checkBox);

        });
    });

    /**
     * Group Checkbox operation
     * */
    $(".row-select").on("click", function () {
        var checkBox = $(this).prop("checked");

        var groupClassName = $(this).attr("id")

        //Multiple
        $("."+groupClassName+"_p").each(function() {

            $(this).prop("checked", checkBox);

        });
    });

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

        showButtonOfTracking();
    });

    $(document).on('click', '.auto-generate-code', function() {
        $("input[name='item_code']").val(getRandomInt(1000000000, 9999999999));
    });

    $(document).on('change', 'input[name="tracking_type"]', function() {
        showButtonOfTracking();
    });

    function showButtonOfTracking() {

        var currentValue = $('input[name="tracking_type"]:checked').val();
        var btn = '';
        if(currentValue == 'batch'){
            btn += `<button class="btn btn-outline-primary trackBtn batchBtn" type="button">${_lang.batchBtnName}</button>`;
        }else if(currentValue == 'serial'){
            btn += `<button class="btn btn-outline-info trackBtn serialBtn" type="button">${_lang.serialBtnName}</button>`;
        }else{
            //
        }
        $(".trackBtn").remove();
        $("input[name='opening_quantity']").after(btn);
    }

    /**
     * Service & Product Radio button
     * */
    $("input[name='item_type_radio']").on("change", function(){
      const checkedRadio = $(this);
      const checkedValue = checkedRadio.val();
      if(checkedValue == 'product'){
        iziToast.info({title: 'Info', layout: 2, message: _lang.productSelected});
        $('.item-type-product').show();
        isService.val(0);
      }else{//servic
        iziToast.info({title: 'Info', layout: 2, message: _lang.serviceSelected});
        $('.item-type-product').hide();
        isService.val(1);
      }
    });

    /**
     * Avoid form submit
     * */
    $('#itemForm input[name="conversion_rate"]').on('keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {  // 13 is the Enter key code
            e.preventDefault();
            return false;
        }
    });

    /**
    * Calculate Sale Price using Sale Profit Margin
    * When i enter value in the sale_profit_margin, sale profit auto calulate
    * and vise versa to profit margin
    */
    const saleProfitMarginInput = $('input[name="profit_margin"]');
    const salePriceInput = $('input[name="sale_price"]');
    const purchasePriceInput = $('input[name="purchase_price"]');
    const taxSelect = $('select[name="tax_id"]'); // Tax selection box

    // Function to calculate sale price based on profit margin and tax
    saleProfitMarginInput.on('input', function () {
        const profitMargin = parseFloat($(this).val()) || 0;
        const purchasePrice = parseFloat(purchasePriceInput.val()) || 0;
        const taxRate = parseFloat(taxSelect.find(':selected').data('tax-rate')) || 0; // Get selected tax rate

        // Validate inputs
        if (isNaN(profitMargin) || isNaN(purchasePrice) || purchasePrice <= 0) {
            salePriceInput.val('');
            return;
        }

        // Prevent division by zero
        if (profitMargin > 100) {
            alert('Profit margin must be less or equal to 100%');
            saleProfitMarginInput.val('');
            salePriceInput.val('');
            return;
        }

        // Calculate sale price before tax
        let salePriceBeforeTax = purchasePrice * (1 + profitMargin / 100);

        // Apply tax
        let salePrice = salePriceBeforeTax * (1 + taxRate / 100);

        salePriceInput.val(_parseFix(salePrice)); // Format to 2 decimal places
    });

    // Function to calculate profit margin based on sale price
    salePriceInput.on('input', function () {
        const salePrice = parseFloat($(this).val()) || 0;
        const purchasePrice = parseFloat(purchasePriceInput.val()) || 0;
        const taxRate = parseFloat(taxSelect.find(':selected').data('tax-rate')) || 0; // Get selected tax rate

        // Validate inputs
        if (isNaN(salePrice) || isNaN(purchasePrice) || purchasePrice <= 0) {
            saleProfitMarginInput.val('');
            return;
        }

        // Remove tax from sale price
        const salePriceBeforeTax = salePrice / (1 + taxRate / 100);

        // Calculate profit margin correctly
        const profitMargin = ((salePriceBeforeTax - purchasePrice) / purchasePrice) * 100;

        saleProfitMarginInput.val(_parseFix(profitMargin)); // Format to 2 decimal places
    });

    // Recalculate sale price when tax is changed
    taxSelect.on('change', function () {
        saleProfitMarginInput.trigger('input'); // Recalculate based on selected tax
    });

    purchasePriceInput.on('input', function () {
        saleProfitMarginInput.trigger('input'); // Recalculate based on selected tax
    });
