"use strict";

    const tableId = $('#orderTable');

    let originalButtonText;

    let addRowButtonText;

    const rowCountStartFrom = 0;

    

    /**
     * Language
     * */
    const _lang = {
                pleaseSelectOption : "Please Select Option",
                clickTochange : "Click to Change",
                enterValidNumber : "Please enter a valid number",
                wantToDelete : "Do you want to delete?",
            };

    $("#scheduleForm").on("submit", function(e) {
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
        pageRedirect(formObject);
    }

    function pageRedirect(formObject){
        var redirectTo = '';
        if(formObject.response.id !== 'undefined'){
            redirectTo = '/order/receipt/'+formObject.response.id;
        }else{
            redirectTo = '/order/list';
        }
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
            formArray.formObject.response = data;
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
     * Add Service or Products on front view
     * */
    $("#add_row").on('click', function() {
        var buttonId = $("#add_row");

        let service = $('#service_id');
        var serviceId = service.val();
        if(serviceId == ''){
            iziToast.error({title: 'Warning', layout: 2, message: _lang.pleaseSelectOption});
            service.focus();
            return;
        }

        //Disable the Button
        disableAddRowButton(buttonId);

        /*If Service ID exist, get the service details*/
        $.getJSON(baseURL+'/service/get_service_records', {service_id:  serviceId}, function(jsonResponse, textStatus) {
            if(jsonResponse.status){
                /*Add Row to the Order Making Table*/
                addRowToServiceTable(jsonResponse.data);
            }
            else{
                iziToast.error({title: 'Error', layout: 2, message: jsonResponse.message});
            }

            /*Enable the Disabled Button*/
            enableAddRowButton(buttonId);
        });
    });
    /**
     * Make Loading of Add Button
     * */
    function disableAddRowButton(buttonId) {
        addRowButtonText = buttonId.text();
        // Set button text to "Loading..."
        buttonId.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...');
        // Disable the button
        buttonId.prop('disabled', true);
    }

    function enableAddRowButton(buttonId) {
        //Restore the actual button name
        buttonId.html(addRowButtonText);
        // Enable the button
        buttonId.prop('disabled', false);
    }

    /**
     * return Table row count
     * */
    function getRowCount(){
        var rowCount = returnDecimalValueByName('row_count');
        return rowCount;
    }

    /**
     * set table row count
     * */
    function setRowCount(){
        var increamentRowCount = getRowCount();
            increamentRowCount++;
        $('input[name="row_count"]').val(increamentRowCount);
    }

    /**
     * Create Service Table Row
     * Params: record Object
     * */
    function addRowToServiceTable(recordObject){
        //Find service table row count
        var currentRowId = getRowCount();

        var tableBody = tableId.find('tbody');

        var hiddenOrderedProductId  = '<input type="hidden" name="ordered_product_id['+ currentRowId +']" class="form-control" value="' + recordObject.ordered_product_id + '">';
        var hiddenServiceId  = '<input type="hidden" name="service_id['+ currentRowId +']" class="form-control" value="' + recordObject.id + '">';
        var inputItemName  = '<input type="text" name="name['+ currentRowId +']" class="form-control form-control-plaintext" readonly  value="' + recordObject.name + '" disabled>';
        var inputDescription  = '<textarea rows="1" type="text" name="description['+ currentRowId +']" class="form-control" placeholder="Description" disabled="true">' + recordObject.description + '</textarea>';
        var inputQuantity   = '<input type="number" name="quantity['+ currentRowId +']" class="form-control" value="' + recordObject.quantity + '" disabled>';
        var inputUnitPrice  = '<input type="number" name="unit_price['+ currentRowId +']" class="form-control" value="' + recordObject.unit_price + '">';
        var hiddenTotalUnitPrice  = '<input type="hidden" name="total_unit_price['+ currentRowId +']" class="form-control" value="' + recordObject.total_price + '">';

        var inputDiscount = '<div class="input-group">';
            inputDiscount +='<input type="text" name="discount['+ currentRowId +']" class="form-control" value="' + (recordObject.discount ?? '') + '">';
            inputDiscount +='<button class="btn btn-outline-secondary btn_discount_type" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'+_lang.clickTochange+'">%</button></div>';

        var hiddenDiscountType  = '<input type="hidden" name="discount_type['+ currentRowId +']" class="form-control" value="' + recordObject.discount_type + '">';
        var hiddenTotalPriceAfterDiscount  = '<input type="hidden" name="total_price_after_discount['+ currentRowId +']" class="form-control" value="' + recordObject.total_price_after_discount + '">';
        var inputDiscountAmount  = '<input type="text" name="discount_amount['+ currentRowId +']" class="form-control form-control-plaintext text-end" readonly value="' + (recordObject.discount_amount ?? '') + '">';

        var inputStartDate  = '<input type="text" name="start_date['+ currentRowId +']" class="form-control datepicker" value="' + (recordObject.start_date ?? '') + '" placeholder="Pick Date" disabled>';
        var inputStartTime  = '<div class="input-group mb-3">';
            inputStartTime  += '<input type="text" name="start_time['+ currentRowId +']" class="form-control time-picker" value="' + (recordObject.start_time ?? '') + '" placeholder="Pick Time" disabled>';
            inputStartTime  += '<span class="input-group-text cc-group-btn-size d-none" role="button" id="clear-input-without-focus"><i class="bx bx-x"></i></span></div>';

        var inputEndDate  = '<input type="text" name="end_date['+ currentRowId +']" class="form-control datepicker" value="' + (recordObject.end_date ?? '') + '" placeholder="Pick Date" disabled>';
        var inputEndTime  = '<div class="input-group mb-3">';
            inputEndTime  += '<input type="text" name="end_time['+ currentRowId +']" class="form-control time-picker" value="' + (recordObject.end_time ?? '') + '" placeholder="Pick Time" disabled>';
            inputEndTime  += '<span class="input-group-text cc-group-btn-size d-none" role="button" id="clear-input-without-focus"><i class="bx bx-x"></i></span></div>';

        var inputTaxSelectionBox  = generateTaxSelectionBox(recordObject.taxList, currentRowId, recordObject.tax_id);
        var hiddenTaxType  = '<input type="hidden" name="tax_type['+ currentRowId +']" class="form-control" value="' + recordObject.tax_type + '">';
        var inputTaxAmount  = '<input type="text" name="tax_amount['+ currentRowId +']" class="form-control text-end form-control-plaintext" readonly  value="' + (recordObject.tax_amount ?? '') + '">';

        var inputTotal  = '<input type="text" name="total['+ currentRowId +']" class="form-control form-control-plaintext text-end" readonly  value="0" disabled>';

        var inputDeleteButton = '<button type="button" class="btn btn-outline-danger remove"><i class="bx bx-trash me-0"></i></button>';

        var inputUserSelectionBox  = generateUserSelectionBox(recordObject.userList, currentRowId, recordObject.assigned_user_id);
        var inputAssignedUserNote  = '<textarea rows="1" type="text" name="assigned_user_note['+ currentRowId +']" class="form-control" placeholder="Note for Staff">' + recordObject.assigned_user_note + '</textarea>';

        var inputStaffStatusSelectionBox  = generateStaffStatusSelectionBox(recordObject.staff_status_list, currentRowId, recordObject.staff_status);
        var inputStaffStatusNote  = '<textarea rows="1" type="text" name="staff_status_note['+ currentRowId +']" class="form-control" placeholder="Staff Note">' + recordObject.staff_status_note + '</textarea>';

        var newRow = $('<tr id="'+ currentRowId +'">');
            newRow.append('<td>' + hiddenOrderedProductId + hiddenServiceId + inputItemName + inputDescription + '</td>');
            newRow.append('<td>' + inputQuantity + '</td>');
            newRow.append('<td class="d-none">' + inputUnitPrice + hiddenTotalUnitPrice + '</td>');
            newRow.append('<td class="d-none">' + inputDiscount + hiddenDiscountType + hiddenTotalPriceAfterDiscount + inputDiscountAmount + '</td>');
            newRow.append('<td>' + inputTotal + '</td>');
            //newRow.append('<td>' + (recordObject.discount ?? '') + '</td>');
            newRow.append('<td>' + inputStartDate + inputStartTime + '</td>');
            newRow.append('<td>' + inputEndDate + inputEndTime + '</td>');
            newRow.append('<td class="d-none">' + inputTaxSelectionBox + hiddenTaxType + inputTaxAmount + '</td>');
            newRow.append('<td>' + inputUserSelectionBox + inputAssignedUserNote + '</td>');
            newRow.append('<td>' + inputStaffStatusSelectionBox + inputStaffStatusNote + '</td>');
            newRow.append('<td class="d-none">' + inputDeleteButton + '</td>');
    
            // Add action buttons
            var actionButtonCell = $('<td>');

            // Append new row to the table body
            tableBody.append(newRow);

            afterAddRowFunctionality(currentRowId);
            
    }

    /**
     * HTML : After Add Row Functionality
     * */
    function afterAddRowFunctionality(currentRowId){
        //Set Row Count
        setRowCount();

        //Custom Function Reset Date & Time Picker
        resetFlatpickr();

        //Reinitiate Tooltip
        setTooltip();

        //Calculate Row Records
        rowCalculator(currentRowId);
    }
    
    /**
     * Generate Tax Type Selection Box
     * */
    function generateTaxSelectionBox(taxList, currentRowId, selectId = null) {
          var selectOption = '<select class="form-select" name="tax_id['+ currentRowId +']">';

          taxList.forEach(function(tax) {
            var selected = (selectId === tax.id) ? 'selected' : '';
            selectOption += '<option value='+ tax.id +' '+ selected +' data-tax-rate='+ tax.rate +' >'+ tax.name +'</option>';
          });

          selectOption += '</select>';

          return selectOption;
    }

    /**
     * Staff Status
     * @return option selection box
     * */
    function generateStaffStatusSelectionBox(staffStatusList, currentRowId, selectId = null){
        var selectOption = '<select class="form-select" name="staff_status['+ currentRowId +']">';

          selectOption += '<option value="">-Select-</option>'; 
          staffStatusList.forEach(function(status) {
            var selected = (selectId === status.id) ? 'selected' : '';
            selectOption += '<option value='+ status.id +' '+ selected +'>'+ status.name +'</option>';
          });

          selectOption += '</select>';

          return selectOption;
    }

    /**
     * Generate User Type Selection Box
     * */
    function generateUserSelectionBox(userList, currentRowId, selectId = null) {
          var selectOption = '<select class="form-select" name="user_id['+ currentRowId +']">';

          selectOption += '<option value="">-Select-</option>'; 
          userList.forEach(function(user) {
            var selected = (selectId === user.id) ? 'selected' : '';
            selectOption += '<option value='+ user.id +' '+ selected +'>'+ user.first_name +' '+ user.last_name +'</option>';
          });

          selectOption += '</select>';

          return selectOption;
    }

    /**
     * Delete Row
     * */
    $(document).on('click', '.remove', function() {
      $(this).closest('tr').remove();
    });
    /**
     * Reset Date & Time Pickers
     * */
    function resetFlatpickr(){
            $(".datepicker").flatpickr({
                dateFormat: dateFormatOfApp, // Set the date format
              });

            flatpickr(".time-picker",{
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",//"K" for AM & PM
            });
    }
  
    /**
     * Discount Type change on click
     * 
     * */
    $(document).on('click', '.btn_discount_type', function() {
      var _this = $(this);
      const currentRowId = _this.closest('tr').attr('id');
      
      var btnName = _this.text();

      // Select discount_type using this index
      var discountType = $('input[name="discount_type['+currentRowId+']"]');
      var discountTypeValue = discountType.val();

      //Change Button Name or Symbol
      if(discountTypeValue == 'percentage'){
        _this.text("$");
        discountType.val('fixed');
      }else{
        _this.text("%");
        discountType.val('percentage');
      }
      $('input[name="discount_amount['+currentRowId+']"]').focus().trigger('change');
    });
    /**
     * Row: Change events
     * */
    $(document).on('change', 'tr input, tr select', function() {
      const rowId = $(this).closest('tr').attr('id');
      rowCalculator(rowId);
    });

    /**
     * Calculate Sum Of Total Column(s)
     * */
    function returnSumOfTotalColumn() {
      var rowCount = getRowCount();
      let sumOfTotalColumn = 0;
        for (var i = rowCountStartFrom; i < rowCount; i++) {
            if($("#total[" + i + "]")){
                sumOfTotalColumn += returnDecimalValueByName('total['+i+']');
            }
        }
      return sumOfTotalColumn;
    }

    /**
     * Calculate Sum Of Tax Column(s)
     * */
    function returnSumOfTaxColumn() {
      var rowCount = getRowCount();
      let totalTaxAmount = 0;
        for (var i = rowCountStartFrom; i < rowCount; i++) {
            if($("#total[" + i + "]")){
                totalTaxAmount += returnDecimalValueByName('tax_amount['+i+']');
            }
        }
      return totalTaxAmount;
    }

    function setSumOfTablefooter(){
        var sumOfTotalColumn = returnSumOfTotalColumn();
        var totalTaxAmount = returnSumOfTaxColumn();
        //Set Sum Of Total
        $('.sum_of_total').text(_parseFix(sumOfTotalColumn));
        //Set sum of Tax
        $('.sum_of_tax').text(_parseFix(totalTaxAmount));
    }

    /**
     * Set bottom records of invoice
     * @set Sutotal
     * @set Tax Total
     * @set Grand Total
     * */
    function setBottomInvoiceTotal(){
        var sumOfTotalColumn = returnSumOfTotalColumn();
        var totalTaxAmount = returnSumOfTaxColumn();
        var subtotal = sumOfTotalColumn - totalTaxAmount;
        $(".subtotal").text(_parseFix(subtotal));
        $(".total_tax").text(_parseFix(totalTaxAmount));
        $(".grand_total").text(_parseFix(sumOfTotalColumn));
        //Set Grand Total
        setGrandTotalHidden(sumOfTotalColumn);
    }
    /**
     * Set Grand Total
     * Hidden Box
     * */
    function setGrandTotalHidden(grandTotal){
        $("input[name='total_amount'").val(grandTotal);
    }
    /**
     * Main:
     * Calculate specific row
     * */
    function rowCalculator(rowId){
        rowCalculateTotalUnitPrice(rowId);  // Serial#1
        rowCalculateDiscountAmount(rowId);  // Serial#2
        rowCalculateTaxAmount(rowId);       // Serial#3
        rowCalculateTotal(rowId);           // Serial#4
        //setSumOfTablefooter();              // Serial#5
        setBottomInvoiceTotal();              // Serial#6
    }
    /**
     * Row: Calculate Quantity x Price
     * */
    function rowCalculateTotalUnitPrice(rowId){
        var quantity = returnDecimalValueByName('quantity['+rowId+']');
        var unitPrice = returnDecimalValueByName('unit_price['+rowId+']');
        //Set to Hidden Box
        $("input[name='total_unit_price["+rowId+"]'").val(quantity * unitPrice);
    }

    /**
     * Row: Settle row Total
     * */
    function rowCalculateTotal(rowId){
        var rowFinalTotal = returnDecimalValueByName('total_price_after_discount['+rowId+']');
        $("input[name='total["+rowId+"]'").val(_parseFix(rowFinalTotal));
    }

    /**
     * Row: Calculate Discount Amount
     * Based on percentage & fixed
     * */
    function rowCalculateDiscountAmount(rowId){
        var discountAmount = 0;
        var totalPriceAfterDiscount = 0;
        var totalUnitPrice = returnDecimalValueByName('total_unit_price['+rowId+']');
        var discountType = returnRowDiscountType(rowId);
        var discountInput = returnDecimalValueByName('discount['+rowId+']');

        discountAmount = (discountType == 'percentage') ? (totalUnitPrice * discountInput)/100 : discountInput;
        totalPriceAfterDiscount = totalUnitPrice - discountAmount;
        //Set Discount Amount
        $("input[name='discount_amount["+rowId+"]'").val(discountAmount);
        $("input[name='total_price_after_discount["+rowId+"]'").val(totalPriceAfterDiscount);
    }

    /**
     * Row: Calculate tax
     * Based on Inclusive and exclusive
     * */
    function rowCalculateTaxAmount(rowId){
        var taxAmount = 0;
        var taxType = returnRowTaxType(rowId);
        var taxRate = $('select[name="tax_id[' + rowId + ']"] option:selected').data('tax-rate');
            taxRate = parseFloat(taxRate);
        var totalPriceAfterDiscount = returnDecimalValueByName('total_price_after_discount['+rowId+']');
        taxAmount = returnTaxValue(taxType, taxRate, totalPriceAfterDiscount);
        //Set Tax Amount
        $("input[name='tax_amount["+rowId+"]'").val(_parseFix(taxAmount));
    }

    /**
     * Row: find tax type
     * @return inclusive or exclusive
     * */
    function returnRowTaxType(rowId){
        return $("input[name='tax_type["+rowId+"]'").val();
    }

    /**
     * Row: Find disocunt type
     * @return percentage or fixed
     * */
    function returnRowDiscountType(rowId){
        return $("input[name='discount_type["+rowId+"]'").val();
    }

    /**
     * return Decimal input value
     * */
    function returnDecimalValueByName(inputBoxName){
        var _inpuBoxId = $("input[name ='"+inputBoxName+"']");
        var inputBoxValue = _inpuBoxId.val();

        if(inputBoxValue == '' || isNaN(inputBoxValue)){
            return parseFloat(0);
        }
        return parseFloat(inputBoxValue);
    } 
