"use strict";

    const tableId = $('#orderTable');

    let submitButton = 'button[id="submit_form"]';
    let originalButtonText;

    let addRowButtonText;

    const rowCountStartFrom = 0;



    const operation = $('#operation').val();

    const itemSearchInputBoxId = $("#search_item");

    var searchedItemPrice = 0;

    var buttonId = $("#add_row");

    /**
     * Language
     * */
    const _lang = {
                pleaseSelectItem : "Item Name Should not be empty",
                clickTochange : "Click to Change",
                enterValidNumber : "Please enter a valid number",
                wantToDelete : "Do you want to delete?",
                paymentAndGrandTotalMismatched : "Total Payment Amount Should be equal to Grand Total!",
                rowAddedSuccessdully : "Item Added!",
            };

    $("#submit_form").on('click', function(event) {
        event.preventDefault();

        /**
         * Payment Validation
         * */
        if(!validatePaymentAndInvoiceTotal()){
            return false;
        }

        $("#expenseForm").submit();

    });

    function validatePaymentAndInvoiceTotal(){
        if(_parseFix(calculateTotalPayment()) != getGrandTotal()){
            iziToast.error({title: 'Warning', layout: 2, message: _lang.paymentAndGrandTotalMismatched});
            return false;
        }
        return true;
    }

    $("#expenseForm").on("submit", function(e) {
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
        originalButtonText = form.find(submitButton).text();
        form.find(submitButton)
            .prop('disabled', true)
            .html('  <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...');
    }

    function enableSubmitButton(form) {
        form.find(submitButton)
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
            redirectTo = '/expense/print/'+formObject.response.id;
        }else{
            redirectTo = '/expense/list';
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
        const _method = formObject.find('input[name="_method"]').val();
        /* Only if Save Operation called*/
        if(_method.toUpperCase() == 'POST' ){
            var formId = formObject.attr("id");
            $("#"+formId)[0].reset();
        }
    }
    /**
     * When i click on Enter Button, call addRow()
     * */
    itemSearchInputBoxId.on("keydown", function(event) {
        if (event.key === "Enter") {
            addRow();
        }
    });
    /**
     * Add Service or Products on front view
     * call addRow()
     * */
    $("#add_row").on('click', function() {
        addRow();
    });

    /**
     * Add Row to front view
     * */
    function addRow(){
        var itemValue = itemSearchInputBoxId.val().trim();
        if(itemValue == ''){
            iziToast.error({title: 'Warning', layout: 2, message: _lang.pleaseSelectItem});
            itemSearchInputBoxId.focus();
            return;
        }

        //Disable the Button
        disableAddRowButton(buttonId);
        //JSON Data to add row
        addRowToExpenseItemsTable(defaultJsonData());
        /*Enable the Disabled Button*/
        enableAddRowButton(buttonId);
        //Make Input box empty and keep curson on it
        itemSearchInputBoxId.val('').focus();
        //Row Added Message
        rowAddedSuccessdully();
    }

    function rowAddedSuccessdully(){
        //iziToast.success({title: _lang.rowAddedSuccessdully, layout: 2, message: ''});
        itemSearchInputBoxId.autocomplete("close");

        //reset variable
        searchedItemPrice =0;

    }
    /**
     * Prepaired JSON data
     * */
    function defaultJsonData() {
        var dataObject = {
              name: $('#search_item').val().trim(),
              description: "",
              quantity: 1,
              unit_price: searchedItemPrice,
              total_price: 0
            };

        return dataObject;
    }

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
    function addRowToExpenseItemsTable(recordObject){

        //Find service table row count
        var currentRowId = getRowCount();

        var tableBody = tableId.find('tbody');

        var inputItemName  = '<input type="text" name="name['+ currentRowId +']" class="form-control" placeholder="Item Name" value="' + recordObject.name + '">';
        var inputDescription  = '<textarea rows="1" type="text" name="description['+ currentRowId +']" class="form-control" placeholder="Description">' + recordObject.description + '</textarea>';
        var inputQuantity   = '<input type="text" name="quantity['+ currentRowId +']" class="form-control cu_numeric" value="' + recordObject.quantity + '">';
        var inputUnitPrice  = '<input type="text" name="unit_price['+ currentRowId +']" class="form-control cu_numeric" value="' + recordObject.unit_price + '">';
        var hiddenTotalUnitPrice  = '<input type="hidden" name="total_unit_price['+ currentRowId +']" class="form-control" value="' + recordObject.total_price + '">';

        var inputTotal  = '<input type="text" name="total['+ currentRowId +']" class="form-control form-control-plaintext text-end" readonly  value="0">';

        var inputDeleteButton = '<button type="button" class="btn btn-outline-danger remove"><i class="bx bx-trash me-0"></i></button>';


        var newRow = $('<tr id="'+ currentRowId +'">');
            newRow.append('<td>' + inputItemName + inputDescription + '</td>');
            newRow.append('<td>' + inputQuantity + '</td>');
            newRow.append('<td>' + inputUnitPrice + hiddenTotalUnitPrice + '</td>');
            newRow.append('<td>' + inputTotal + '</td>');

            newRow.append('<td>' + inputDeleteButton + '</td>');

            // Add action buttons
            var actionButtonCell = $('<td>');

            // Append new row to the table body
            tableBody.prepend(newRow);

            afterAddRowFunctionality(currentRowId);

    }

    /**
     * HTML : After Add Row Functionality
     * */
    function afterAddRowFunctionality(currentRowId){
        //Remove Default existing row if exist
        removeDefaultRowFromTable();

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
     * Remove Default Row from table
     * */
    function removeDefaultRowFromTable() {
        if($('.default-row').length){
            $('.default-row').closest('tr').remove();
        }
    }

    function afterRemoveFunctions() {
        setBottomOfTableRecords();
    }
    /**
     * Delete Row
     * */
    $(document).on('click', '.remove', function() {
      $(this).closest('tr').remove();
      // Check if there are no more rows in tbody
      var tableBody = tableId.find('tbody');
      if (tableBody.children('tr').length === 0) {
        tableBody.append(
          '<tr>' +
            '<td colspan="5" class="text-center fw-light fst-italic default-row">' +
            'No items are added yet !!' +
            '</td>' +
          '</tr>'
        );
      }
      afterRemoveFunctions();
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
    $(document).on('change', '#orderTable tr input, #orderTable tr select', function() {
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
     * Calculate Sum Of Total Column(s)
     * */
    function returnSumOfQuantityColumn() {
      var rowCount = getRowCount();
      let sumOfQuantityColumn = 0;
        for (var i = rowCountStartFrom; i < rowCount; i++) {
            if($("#total[" + i + "]")){
                sumOfQuantityColumn += returnDecimalValueByName('quantity['+i+']');
            }
        }
      return sumOfQuantityColumn;
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
        var sumOfQuantityColumn = returnSumOfQuantityColumn();
        //Set Sum Of Total
        $('.sum_of_total').text(_parseFix(sumOfTotalColumn));
        $('.sum_of_quantity').text(_parseFix(sumOfQuantityColumn));
    }

    /**
     * Set bottom records of invoice
     * @set Sutotal
     * @set Tax Total
     * @set Grand Total
     * */
    function setBottomInvoiceTotal(){
        var sumOfTotalColumn = returnSumOfTotalColumn();
        var getRoundOff = getCurrentRoundOffAmount();

        sumOfTotalColumn = parseFloat(sumOfTotalColumn) + parseFloat(getRoundOff);

        //Set Grand Total
        $(".grand_total").val(_parseFix(sumOfTotalColumn));

        autoFillPaymentInputBox();
    }

    function getGrandTotal() {
        return parseFloat($(".grand_total").val());
    }

    function autoFillPaymentInputBox(){
        if(operation == 'save'){
            var grandTotal = getGrandTotal();
            $("input[name='payment_amount[0]']").val(grandTotal);
        }
    }
    /**
     * Rounf-Off Checked or not
     * @return boolean
     * */
    function isCheckboxChecked() {
        return $('#round_off_checkbox').prop('checked');
    }
    /**
     * On-change round off
     * Based on Checkbox
     * Automatic calculation
     * */
    $(document).on('change', '#round_off_checkbox', function(){
        setRoundOffAmount('automatic');
        setBottomInvoiceTotal();
    });
    /**
     * Manuall Round-Off
     * */
    $(document).on('change', '.round_off', function(){
        setRoundOffAmount('manual');
        setBottomInvoiceTotal();
    });


    function setRoundOffAmount(workType='automatic'){
        if(workType=='automatic'){
            var setRoundOffAmount = calculateRoundOff();
        }else{//manual
            var setRoundOffAmount = getCurrentRoundOffAmount();
        }
        setRoundOffAmount = returnZeroIfEmptyyOrisNaN(setRoundOffAmount);

        $(".round_off").val(_parseFix(setRoundOffAmount));
    }

    function getCurrentRoundOffAmount(){
        return $(".round_off").val();
    }
    /**
     * Calculate Round-Off
    */
    function calculateRoundOff(){
        if (isCheckboxChecked()) {
            var sumOfTotalColumn = returnSumOfTotalColumn();

            var afterRoundOff = Math.round(sumOfTotalColumn);

            var roundOffValue = afterRoundOff - sumOfTotalColumn;

            return roundOffValue;

        } else {
            return 0;
        }
    }

    /**
     * Main:
     * Calculate specific row
     * */
    function rowCalculator(rowId){
        rowCalculateTotalUnitPrice(rowId);  // Serial#1
        //rowCalculateDiscountAmount(rowId);  // Serial#2
        //rowCalculateTaxAmount(rowId);       // Serial#3
        rowCalculateTotal(rowId);           // Serial#4
        setBottomOfTableRecords();
    }
    function setBottomOfTableRecords(){
        setSumOfTablefooter();              // Serial#5
        setRoundOffAmount();                // Serial#6
        setBottomInvoiceTotal();              // Serial#7
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
        var rowFinalTotal = returnDecimalValueByName('total_unit_price['+rowId+']');
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

   /**
    * Autocomplete
    * Item Search Input box
    * */
    itemSearchInputBoxId.autocomplete({
        minLength: 1,

        source:     function( request, response ) {
                        $.ajax( {
                            url: baseURL + '/expense/expense-items-master/ajax/get-list',
                            dataType: "json",
                            data: {
                                search: request.term
                            },
                            success: function( data ) {
                                response( data.results );
                            }
                        } );
                    },

        focus:      function( event, ui ) {
                        itemSearchInputBoxId.val( ui.item.text );
                        searchedItemPrice = _parseFix(ui.item.unit_price);
                        return false;
                    },

        select:     function( event, ui ) {
                        itemSearchInputBoxId.val( ui.item.text );
                        return false;
                    },
    })
    .autocomplete( "instance" )._renderItem = function( ul, item ) {
        return $( "<li>" )
            .append( "<div>" + item.text + "</div>" )
            .appendTo( ul );
    };//autocomplete end

    $(document).ready(function(){
        /**
         * Update Opetation
         * */
        if(operation == 'update-expense'){
            updateOperation();
        }
    });

    function updateOperation(){

        var jsonObject = JSON.parse(itemsTableRecords);

        jsonObject.forEach((data, index) => {
                var dataObject = {
                  name: data.item_details.name,
                  description: data.description??'',
                  quantity: _parseQuantity(data.quantity),
                  unit_price: _parseFix(data.unit_price),
                  total_price: _parseFix(data.unit_price),
                };

                addRowToExpenseItemsTable(dataObject);
          });
    }
