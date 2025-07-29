"use strict";

    const tableId = $('#itemsTable');

    let originalButtonText;

    let submitButton = 'button[id="submit_form"]';

    let addRowButtonText;

    const rowCountStartFrom = 0;



    const itemSearchInputBoxId = $("#search_item");

    /**
     * Language
     * */
    const _lang = {
                pleaseSelectItem : "Item Name Should not be empty",
                pleaseSelectItemFromSearchBox : "Choose Item from Search Results!!",
                clickTochange : "Click to Change",
                wantToDelete : "Do you want to delete?",
                rowAddedSuccessdully : "Item Added!",
                taxTypeChanged : "Tax type has changed!",
                barcodeNotExist : "This Item Doesn't have Barcode, Please Add it from Item Master!",
            };

    $("#submit_form").on('click', function(event) {
        event.preventDefault();

        /**
         * Payment Validation
         * */
        // if(!validatePaymentAndInvoiceTotal()){
        //     return false;
        // }

        $("#barcodeForm").submit();

    });

    function validatePaymentAndInvoiceTotal(){
        if(_parseFix(calculateTotalPayment()) != getGrandTotal()){
            iziToast.error({title: 'Warning', layout: 2, message: _lang.paymentAndGrandTotalMismatched});
            return false;
        }
        return true;
    }

    $("#barcodeForm").on("submit", function(e) {
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
        //
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
    function addRow(recordObject){
        if((recordObject.item_code).length === 0){
            iziToast.error({title: 'Warning', layout: 2, message: _lang.barcodeNotExist});
            itemSearchInputBoxId.focus();
            return;
        }


        //JSON Data to add row
        addRowToItemsTable(recordObject);

        //Make Input box empty and keep curson on it
        itemSearchInputBoxId.val('').focus();
        //Row Added Message
        rowAddedSuccessdully();
    }

    function rowAddedSuccessdully(){
        //iziToast.success({title: _lang.rowAddedSuccessdully, layout: 2, message: ''});
        itemSearchInputBoxId.autocomplete("close");

        //reset variable
        //searchedItemPrice =0;

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

    function itemInfoIcon(trackingType) {
        var message;
        if(trackingType == 'batch'){
            message = `<b>Tracking Type:</b><br>Batch wise Tracking`;
        }else if(trackingType == 'serial'){
            message = `<b>Tracking Type:</b>:<br>Serial wise Tracking`;
        }else{
            message = `<b>Tracking Type:</b><br>Regular`;
        }
        return `<a tabindex="0" class="text-primary" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="${message}"><i class="fadeIn animated bx bx-info-circle"></i></a>`;
    }
    /**
     * Create Service Table Row
     * Params: record Object
     *
     * autoLoadUpdateOperation parameter is true only when update page autoloaded
     * ex: Sale bill -> Edit
     * */
    function addRowToItemsTable(recordObject, loadedFromUpdateOperation=false){

       //Find service table row count
        var currentRowId = getRowCount();

        var tableBody = tableId.find('tbody');
        var hiddenItemId  = '<input type="hidden" name="item_id['+ currentRowId +']" class="form-control" value="' + recordObject.id + '">';
        var inputItemName  = `<label class="form-label" role="button" name="name[${currentRowId}]">${recordObject.name}</label> ` + itemInfoIcon(recordObject.tracking_type);
        var barcode  = `<label class="form-label" role="button" name="item_code[${currentRowId}]">${recordObject.item_code}</label> `;
        var salePrice  = `<label class="form-label" role="button" name="sale_price[${currentRowId}]">${_parseFix(recordObject.sale_price)}</label> `;
        var inputQuantity   = '<input type="number" name="quantity['+ currentRowId +']" class="form-control" value="' + recordObject.quantity + '">';
        /*Keeping the Scheduled Job Records*/
        var removeClass = (!recordObject.assigned_user_id)? 'remove' : '';
        var inputDeleteButton = '<button type="button" class="btn btn-outline-danger '+removeClass+'"><i class="bx bx-trash me-0"></i></button>';

        var newRow = $('<tr id="'+ currentRowId +'" class="highlight">');
            
            newRow.append('<td>' + hiddenItemId + inputItemName + '</td>');
            newRow.append('<td>' + barcode + '</td>');
            newRow.append('<td>' + salePrice + '</td>');
            newRow.append('<td>' + inputQuantity + '</td>');
            newRow.append('<td>' + inputDeleteButton + '</td>');
            // Add action buttons
            var actionButtonCell = $('<td>');
            // Append new row to the table body
            tableBody.prepend(newRow);
            //Serial #2
            afterAddRowFunctionality(currentRowId);
    }

    
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
     * HTML : After Add Row Functionality
     * */
    function afterAddRowFunctionality(currentRowId){
        //Remove Default existing row if exist
        removeDefaultRowFromTable();

        //Set Row Count
        setRowCount();

        //Reinitiate Tooltip
        setTooltip();
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
      afterRemoveFunctions();
    });


    /**
     * Row: Change events
     * */
    $(document).on('change', '#itemsTable tr input, #itemsTable tr select', function() {
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

    function setSumOfTablefooter(){
        var sumOfQuantityColumn = returnSumOfQuantityColumn();
        //Set Sum Of Total
        $('.sum_of_quantity').text(_parseFix(sumOfQuantityColumn));
    }


    /**
     * Main:
     * Calculate specific row
     * */
    function rowCalculator(rowId){
        setBottomOfTableRecords();
    }
    function setBottomOfTableRecords(){
        setSumOfTablefooter();              // Serial#5
    }


   /**
    * Autocomplete
    * Item Search Input box
    * */

    itemSearchInputBoxId.autocomplete({
        minLength: 1,
        source: function(request, response) {
                    $.ajax({
                        url: baseURL + '/item/ajax/get-list',
                        dataType: "json",
                        data: {
                            search: request.term,
                        },
                        success: function(data) {
                            if (data.length === 1 && data[0].item_code !=='' && (data[0].item_code === request.term || data[0].item_code === request.term)) {
                                // If only one item is returned and its barcode or item_code matches the search term exactly
                                itemSearchInputBoxId.autocomplete("close");
                                itemSearchInputBoxId.autocomplete("option", "select").call(itemSearchInputBoxId[0], null, {
                                    item: data[0]
                                });
                            } else {
                                response(data);
                            }
                        }
                    });
                },

        focus: function(event, ui) {
                    if (ui.item !== undefined) {
                        itemSearchInputBoxId.val(ui.item.name);
                        if (ui.item.sale_price !== undefined) {
                            //searchedItemPrice = _parseFix(ui.item.sale_price);
                        }
                    }
                    return false;
                },

        select: function(event, ui) {
                    if (ui.item !== undefined) {
                        itemSearchInputBoxId.val(ui.item.name);
                        addRow(ui.item);
                    }
                    return false;
                },

        open: function(event, ui) {
                    // Add header after the menu is opened
                    var header = $("<li class='ui-autocomplete-category' style='padding: 5px; border-bottom: 1px solid #ddd; background-color: #f8f9fa;'>" +
                        "<div style='display: flex; font-weight: bold;'>" +
                        "<span style='flex: 3;'>Name</span>" +
                        "<span style='flex: 1;'>Brand</span>" +
                        "<span style='flex: 1; text-align: right;'>Sales Price</span>" +
                        "<span style='flex: 1; text-align: right;'>Purchase Price</span>" +
                        "<span style='flex: 1; text-align: right;'>Stock</span>" +
                        "</div></li>");
                    $(this).autocomplete("widget").prepend(header);
                }
    }).autocomplete("instance")._renderItem = function(ul, item) {
        return $("<li>")
            .attr("style", "padding: 5px; border-bottom: 1px solid #eee;")
            .append(`<div style="display: flex; align-items: center;">
                        <span style="flex: 3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${item.name || 'N/A'}</span>
                        <span style="flex: 1; text-align: left;">${item.brand_name}</span>
                        <span style="flex: 1; text-align: right;">${_parseFix(item.sale_price) || 'N/A'}</span>
                        <span style="flex: 1; text-align: right;">${_parseFix(item.purchase_price) || 'N/A'}</span>
                        <span style="flex: 1; text-align: right; color: ${_parseQuantity(item.current_stock) > 0 ? '#000000' : '#dc3545'};">${_parseQuantity(item.current_stock) || 'N/A'}</span>
                     </div>`)
            .appendTo(ul);
    };


    $(document).ready(function(){
        /**
         * Empty Defalt Row: colspan count
         * */
        $('.default-row').attr('colspan', $('#itemsTable > thead > tr:first > th').not('.d-none').length);
    });

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

    function createLabelData() {
      const records = [];
      const rowCount = getRowCount();

      for (let i = 0; i < rowCount; i++) {
        if (returnDecimalValueByName('quantity[' + i + ']') > 0) {
          const quantity = returnDecimalValueByName('quantity[' + i + ']');
          const barcode = $('label[name="item_code[' + i + ']"]').text();
          const itemName = $('label[name="name[' + i + ']"]').text();
          const price = $('label[name="sale_price[' + i + ']"]').text();
          const companyName = appCompanyName;

          records.push({
            companyName,
            itemName,
            barcode,
            price,
            quantity
          });
        }
      }

      return records;
    }

    $('#generate').on('click', function() {
        var barcodeType = $("#barcode_type").val();
        var size = $("#size").val();

        const data = {
            barcode_type: barcodeType,
            size: size,
            itemData: JSON.stringify(createLabelData())
        };

        // Sending data to the iframe using postMessage
        const iframe = document.getElementById('barcodeIframe');
        iframe.contentWindow.postMessage(data, '*');


    });

    $(".printIFrame").on('click', function(){
        var iframe = document.getElementById('barcodeIframe');
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage('print', '*');
        }
    });
