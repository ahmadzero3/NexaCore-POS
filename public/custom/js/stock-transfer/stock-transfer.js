"use strict";

    const tableId = $('#stockTransferItemsTable');

    let originalButtonText;

    let submitButton = 'button[id="submit_form"]';

    let addRowButtonText;

    const rowCountStartFrom = 0;



    const operation = $('#operation').val();

    const currentWarehouse = $('#warehouse_id');
    const toWarehouse = $('#to_warehouse_id');

    const itemSearchInputBoxId = $("#search_item");

    var searchedItemPrice = 0;

    var buttonId = $("#add_row");

    /**
     * Language
     * */
    const _lang = {
                pleaseSelectItem : "Item Name Should not be empty",
                pleaseSelectItemFromSearchBox : "Choose Item from Search Results!!",
                clickTochange : "Click to Change",
                clickToChangeTaxType : "Click to Change Tax Type",
                clickToSelectSerial : "Click to Select Serial Numbers",
                enterValidNumber : "Please enter a valid number",
                wantToDelete : "Do you want to delete?",
                paymentAndGrandTotalMismatched : "Total Payment Amount Should be equal to Grand Total!",
                rowAddedSuccessdully : "Item Added!",
                taxTypeChanged : "Tax type has changed!",
                selectWarehouse : "Please select Warehouse(s)",
                toWarehouseShouldNotBeSame : "You can't transfer to the same warehouse!",
            };

    $("#submit_form").on('click', function(event) {
        event.preventDefault();

        $("#stockTransferForm").submit();
    });


    $("#stockTransferForm").on("submit", function(e) {
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
        if(formObject.response.id !== undefined){
            redirectTo = '/stock-transfer/details/'+formObject.response.id;
        }else{
            redirectTo = '/stock-transfer/list';
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
            //iziToast.info({title: 'Info', layout: 2, message: _lang.pleaseSelectItemFromSearchBox});
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
    function addRow(recordObject){
        if(Object.keys(recordObject).length === 0){
            iziToast.error({title: 'Warning', layout: 2, message: _lang.pleaseSelectItem});
            itemSearchInputBoxId.focus();
            return;
        }

        //Disable the Button
        disableAddRowButton(buttonId);
        //JSON Data to add row
        addRowToInvoiceItemsTable(recordObject);
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
    function addRowToInvoiceItemsTable(recordObject, loadedFromUpdateOperation=false){

       //Find service table row count
        var currentRowId = getRowCount();

        var tableBody = tableId.find('tbody');
        var warehouseId = (parseInt(recordObject.warehouse_id) > 0) ? recordObject.warehouse_id : currentWarehouse.val();
        var toWarehouseId = (parseInt(recordObject.to_warehouse_id) > 0) ? recordObject.to_warehouse_id : toWarehouse.val();
        var hiddenWarehouseId  = '<input type="hidden" name="warehouse_id['+ currentRowId +']" class="form-control" value="' + warehouseId + '">';
        var hiddenToWarehouseId  = '<input type="hidden" name="to_warehouse_id['+ currentRowId +']" class="form-control" value="' + toWarehouseId + '">';
        var hiddenItemId  = '<input type="hidden" name="item_id['+ currentRowId +']" class="form-control" value="' + recordObject.id + '">';
        var inputItemName  = `<label class="form-label" role="button">${recordObject.name}</label> ` + itemInfoIcon(recordObject.tracking_type);

        var serialTracking = `<i class="fadeIn animated bx bx-list-ol text-primary serialBtnForInvoice" role='button' data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="${_lang.clickToSelectSerial}"></i>`;
        var _serialNumbers = (recordObject.serial_numbers !== undefined) ? recordObject.serial_numbers : '';
        var hiddenSerialNumbers  = `<input type="hidden" name='serial_numbers[${currentRowId}]' class='form-control' value='${_serialNumbers}'>`;

        var _batchNumber = (recordObject.batch_no !== undefined) ? recordObject.batch_no : '';
        var inputBatchNumber   = '<input type="text" name="batch_no['+ currentRowId +']" class="form-control batch-group" value="'+_batchNumber+'">';

        var _mfgDate = (recordObject.mfg_date !== undefined) ? recordObject.mfg_date : '';
        var mfgDate   = `<input type='text' class="form-control datepicker batch-group" name="mfg_date[${currentRowId}]" placeholder="Pick Date" value='${_mfgDate}'></td>`;

        var _expDate = (recordObject.exp_date !== undefined) ? recordObject.exp_date : '';
        var expDate   = `<input type='text' class="form-control datepicker batch-group" name="exp_date[${currentRowId}]" placeholder="Pick Date" value='${_expDate}'></td>`;

        var _modelNo = (recordObject.model_no !== undefined) ? recordObject.model_no : '';
        var modelNo   = '<input type="text" name="model_no['+ currentRowId +']" class="form-control batch-group" value="'+_modelNo+'">';

        var _mrp = (recordObject.mrp !== undefined && (parseFloat(recordObject.mrp)>=0)) ? _parseFix(recordObject.mrp) : '';
        var mrp  = '<input type="text" name="mrp['+ currentRowId +']" class="form-control" value="'+_mrp+'">';

        var _color = (recordObject.color !== undefined) ? recordObject.color : '';
        var color  = '<input type="text" name="color['+ currentRowId +']" class="form-control batch-group" value="'+_color+'">';

        var _size = (recordObject.size !== undefined) ? recordObject.size : '';
        var size  = '<input type="text" name="size['+ currentRowId +']" class="form-control batch-group" value="'+_size+'">';

        var _quantityToTransfer = (recordObject.quantityToTransfer !== undefined) ? recordObject.quantityToTransfer : 1;
        var _trackingTypeMode = (recordObject.tracking_type === 'serial') ? 'readonly' : '';
        var inputQuantity   = '<input type="text" name="current_stock['+ currentRowId +']" class="form-control cursor-not-allowed" readonly value="' + _parseQuantity(recordObject.current_stock) + '">';
        var quantityToTransfer   = '<input type="number" name="quantity['+ currentRowId +']" class="form-control" '+_trackingTypeMode+' value="'+_quantityToTransfer+'">';

        var unitSelectionBox = `<input type="hidden" name='conversion_rate[${currentRowId}]' data-base-unit-id='${recordObject.base_unit_id}' data-base-price='${recordObject.sale_price}' value='${recordObject.conversion_rate}'>`;
            unitSelectionBox += `<input type="hidden" id='selected_unit_id[${currentRowId}]' value='${recordObject.selected_unit_id}'>`;
            unitSelectionBox +=generateUnitSelectionBox(recordObject.unitList, currentRowId, recordObject.selected_unit_id);


        if(loadedFromUpdateOperation == false){
            //Loaded from item selection box
            var taxType = (recordObject.is_sale_price_with_tax) ? "exclusive" : 'exclusive';
        }else{
            var taxType = (recordObject.is_sale_price_with_tax) ? "inclusive" : 'exclusive';
        }
        var hiddenTaxType  = '<input type="hidden" name="tax_type['+ currentRowId +']" class="form-control" value="' + taxType + '">';

        var inputTaxAmount  = '<input type="text" name="tax_amount['+ currentRowId +']" class="form-control text-end form-control-plaintext" readonly  value="' + (recordObject.tax_amount ?? '') + '">';

        /*Keeping the Scheduled Job Records*/
        var removeClass = (!recordObject.assigned_user_id)? 'remove' : '';
        var inputDeleteButton = '<button type="button" class="btn btn-outline-danger '+removeClass+'"><i class="bx bx-trash me-0"></i></button>';

        var newRow = $('<tr id="'+ currentRowId +'" class="highlight">');
            newRow.append('<td>' + hiddenWarehouseId + hiddenToWarehouseId + hiddenItemId + inputItemName + '</td>');
            newRow.append(`<td class="${(!itemSettings.enable_serial_tracking)?'d-none':''}">` + serialTracking + hiddenSerialNumbers + '</td>');
            newRow.append(`<td class="${(!itemSettings.enable_batch_tracking)?'d-none':''}">` + inputBatchNumber + '</td>');
            newRow.append(`<td class="${(!itemSettings.enable_mfg_date)?'d-none':''}">` + mfgDate + '</td>');
            newRow.append(`<td class="${(!itemSettings.enable_exp_date)?'d-none':''}">` + expDate + '</td>');
            newRow.append(`<td class="${(!itemSettings.enable_model)?'d-none':''}">` + modelNo + '</td>');
            newRow.append(`<td class="${(!itemSettings.enable_color)?'d-none':''}">` + color + '</td>');
            newRow.append(`<td class="${(!itemSettings.enable_size)?'d-none':''}">` + size + '</td>');
            newRow.append('<td>' + inputQuantity + '</td>');
            newRow.append('<td>' + unitSelectionBox + '</td>');
            newRow.append('<td>' + quantityToTransfer + '</td>');
            newRow.append('<td>' + inputDeleteButton + '</td>');

            // Add action buttons
            var actionButtonCell = $('<td>');

            // Append new row to the table body
            tableBody.prepend(newRow);

            //Serial #1
            loadTrackingDataFromAjax(recordObject, currentRowId, loadedFromUpdateOperation);

            //Serial #2
            afterAddRowFunctionality(currentRowId);
    }

    function showNoItemsMessageIfEmpty() {
        var $tbody = $('#invoiceItemsTable tbody');
        // Count only rows that are not the default "no items" row
        var itemRows = $tbody.find('tr').filter(function() {
            return !$(this).hasClass('default-row');
        });
        if (itemRows.length === 0) {
            $tbody.html(
                '<tr>' +
                '<td colspan="12" class="text-center fw-light fst-italic default-row">' +
                'No items are added yet!!' +
                '</td>' +
                '</tr>'
            );
        }
    }

    $(document).on('click', '.remove', function() {
      $(this).closest('tr').remove();
      afterRemoveFunctions();
      showNoItemsMessageIfEmpty();
    });

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
        // const options = Object.values(taxList)
        //     .map(tax => `<option value="${tax.id}" data-tax-rate="${tax.rate}" ${selectId === tax.id ? 'selected' : ''}>${tax.name}</option>`)
        //     .join('');

        // return `<select class="form-select" name="tax_id[${currentRowId}]">${options}</select>`;
    }

    /**
     * Generate Unit Selection Box
     * */
    function generateUnitSelectionBox(unitList, currentRowId, selectId = null) {
        const options = Object.values(unitList)
            .map(unit => `<option value="${unit.id}" ${selectId === unit.id ? 'selected' : ''}>${unit.name}</option>`)
            .join('');

        return `<select class="form-select" name="unit_id[${currentRowId}]">${options}</select>`;
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
     * Row: Change events
     * */
    $(document).on('change', '#stockTransferItemsTable tr input, #stockTransferItemsTable tr select', function() {
      const rowId = $(this).closest('tr').attr('id');
      rowCalculator(rowId);
    });

    /**
     * Calculate Sum Of Total Column(s)
     * */
    function returnSumOfTotalColumn() {
      // var rowCount = getRowCount();
      // let sumOfTotalColumn = 0;
      //   for (var i = rowCountStartFrom; i < rowCount; i++) {
      //       if($("#total[" + i + "]")){
      //           sumOfTotalColumn += returnDecimalValueByName('total['+i+']');
      //       }
      //   }
      // return sumOfTotalColumn;
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
        //var sumOfTotalColumn = returnSumOfTotalColumn();
        var sumOfQuantityColumn = returnSumOfQuantityColumn();
        //Set Sum Of Total
        //$('.sum_of_total').text(_parseFix(sumOfTotalColumn));

        $('.sum_of_quantity').text(_parseFix(sumOfQuantityColumn));
    }


    /**
     * Calculate Price Corversation rate wise
     * example: Base Unit 1, Secondary unit 10
     * EachUnit = BaseUnit/SecondaryUnit
     * */
    function calculatePriceWithConversationRate(rowId){
        var baseUnitId = $("input[name='conversion_rate["+rowId+"]'").attr('data-base-unit-id');
        var baseUnitPrice = $("input[name='sale_price["+rowId+"]'").val();
        var conversionRate = returnDecimalValueByName('conversion_rate['+rowId+']');

        var _newSelectedUnitId = $('select[name="unit_id[' + rowId + ']"] option:selected').val()
        var _oldSelectedUnitId = $("input[id='selected_unit_id["+rowId+"]'").val();

        if(_newSelectedUnitId === _oldSelectedUnitId){
            $("input[name='sale_price["+rowId+"]'").val(_parseFix(baseUnitPrice));
        }
        else if(_newSelectedUnitId !== _oldSelectedUnitId && _newSelectedUnitId !==baseUnitId){
            $("input[name='sale_price["+rowId+"]'").val(_parseFix(baseUnitPrice/conversionRate));
        }else{
            $("input[name='sale_price["+rowId+"]'").val(_parseFix(baseUnitPrice * conversionRate));
        }
        //Update selected ID
        $("input[id='selected_unit_id["+rowId+"]'").val(_newSelectedUnitId);
    }

    /**
     * Main:
     * Calculate specific row
     * */
    function rowCalculator(rowId){
        calculatePriceWithConversationRate(rowId); // Serial#0
        setBottomOfTableRecords();
    }
    function setBottomOfTableRecords(){
        setSumOfTablefooter();              // Serial#5

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
        source: function(request, response) {
                        // Check if warehouse_id is empty
                    var warehouseId = currentWarehouse.val();
                    var toWarehouseId = toWarehouse.val();
                    if (!warehouseId || !toWarehouseId || warehouseId === toWarehouseId ) {
                        // If empty, show an alert or some other message
                        $(".ui-autocomplete-loading").removeClass('ui-autocomplete-loading');
                        var message = (warehouseId === toWarehouseId) ? _lang.toWarehouseShouldNotBeSame : _lang.selectWarehouse;
                        iziToast.error({title: '', layout: 2, message: message});
                        itemSearchInputBoxId.val('');
                        if(!warehouseId){
                            currentWarehouse.select2('open');
                        }else{
                            toWarehouse.select2('open');
                        }
                        return false; // Prevent the search from happening
                    }
                    $.ajax({
                        url: baseURL + '/item/ajax/get-list',
                        dataType: "json",
                        data: {
                            search: request.term,
                            warehouse_id : currentWarehouse.val(),
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
                        },
                        complete: function() {
                            // Remove loading class after the request completes
                            $(".ui-autocomplete-loading").removeClass('ui-autocomplete-loading');
                        }
                    });
                },

        focus: function(event, ui) {
                    if (ui.item !== undefined) {
                        itemSearchInputBoxId.val(ui.item.name);
                        if (ui.item.sale_price !== undefined) {
                            searchedItemPrice = _parseFix(ui.item.sale_price);
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
         * Toggle the sidebar of the template
         * */
        toggleSidebar();

        /**
         * Empty Defalt Row: colspan count
         * */
        $('.default-row').attr('colspan', $('#stockTransferItemsTable > thead > tr:first > th').not('.d-none').length);

        /**
         * Set colspan of the table bottom
         * */
        $('.tfoot-first-td').attr('colspan', $('#stockTransferItemsTable > thead > tr:first > th').not('.d-none').length - 1);

        /**
         * Update Opetation
         * */
        if(operation == 'update'){
            updateOperation(itemsTableRecords);
        }
    });

    /**
     * Used this in sale-order -> operation -> create
     *
     * */
    window.addRowToInvoiceItemsTableFromBatchTracking = function(dataObject){
        /**
         * Remove already added row while selecting batch
         * */
        tableId.find(`tr#${dataObject.mainTableRowId}`).remove();

        /**
         * @argument (object, true)
         * Dynamically adding row then no need to show pop up so it's second argument is true
         * */
        addRowToInvoiceItemsTable(dataObject,true);
    }

    function updateOperation(stringData){

        var jsonObject = JSON.parse(stringData);
        jsonObject.forEach((data, index) => {

                var dataObject = {
                    warehouse_id    : data.warehouse_id,
                    to_warehouse_id    : data.item_stock_transfer.to_warehouse_id,
                    id              : data.item_id,
                    name            : data.item.name,
                    tracking_type   : data.tracking_type,
                    description     : (data.description != null) ? data.description : '',
                    sale_price  : data.unit_price,
                    is_sale_price_with_tax  :  (data.tax_type =='inclusive') ? 1 : 0,
                    tax_id          : data.tax_id,
                    current_stock        : _parseQuantity(data.currentStock),
                    quantityToTransfer        : _parseQuantity(data.quantity),
                    //taxList         : taxList,
                    unitList        : data.unitList,
                    base_unit_id      : data.item.base_unit_id,
                    secondary_unit_id : data.item.secondary_unit_id,
                    selected_unit_id  : data.unit_id,
                    //conversion_rate   : data.item.conversion_rate,
                    //sale_price_discount   : data.discount,
                    //discount_type   : data.discount_type,
                    //discount_amount : data.discount_amount,
                    //total_price_after_discount   : 0,
                    //tax_amount      : data.tax_amount,
                    //total_price     : data.total,

                    serial_numbers  : (data.tracking_type == 'serial') ? JSON.stringify(data.itemSerialTransactions.map(item => item.serial_code)) : '',

                    batch_no        : (data.tracking_type == 'batch' && data.batch.item_batch_master.batch_no!== null) ? data.batch.item_batch_master.batch_no : '',
                    mfg_date        : (data.tracking_type == 'batch' && data.batch.item_batch_master.mfg_date!== null) ? data.batch.item_batch_master.mfg_date : '',
                    exp_date        : (data.tracking_type == 'batch' && data.batch.item_batch_master.exp_date!== null) ? data.batch.item_batch_master.exp_date : '',
                    mrp             : (data.tracking_type == 'batch' && data.batch.item_batch_master.mrp!== null) ? data.batch.item_batch_master.mrp : data.mrp,
                    model_no        : (data.tracking_type == 'batch' && data.batch.item_batch_master.model_no!== null) ? data.batch.item_batch_master.model_no : '',
                    color           : (data.tracking_type == 'batch' && data.batch.item_batch_master.color!== null) ? data.batch.item_batch_master.color : '',
                    size            : (data.tracking_type == 'batch' && data.batch.item_batch_master.size!== null) ? data.batch.item_batch_master.size : '',
                };

                addRowToInvoiceItemsTable(dataObject,true);
          });
    }

    function loadTrackingDataFromAjax(recordObject, rowId, loadedFromUpdateOperation) {
        if(recordObject.tracking_type == 'serial' && loadedFromUpdateOperation==false){
            /**
             * Show TrackingType Data
             * POP UP Selection
             * */
            _globalSerialTracking(rowId);

        }
        else if(recordObject.tracking_type == 'batch' && loadedFromUpdateOperation==false){
            /**
             * Show Batch Tracking
             * loadedFromUpdateOperation = false because don't show pop up
             * */
            _globalBatchTracking(rowId);
        }
        else{
            //General
        }
        autoSetClassOfTableRow(recordObject.tracking_type, rowId);

    }

    /**
     * Table Class adjustments
     * */
    function autoSetClassOfTableRow(trackingType, rowId){
        if(trackingType == 'serial'){
            /**
             * Disabling Datepicker by removing datepicker class name
             * */
            $(`input[name='mfg_date[${rowId}]']`).removeClass('datepicker').prop('readonly', true).attr('placeholder', '');
            $(`input[name='exp_date[${rowId}]']`).removeClass('datepicker').prop('readonly', true).attr('placeholder', '');

            /**
             * Set all batch group readonly
             * */
             $(`#stockTransferItemsTable tr#${rowId} input.batch-group`).prop('readonly', true).addClass('cursor-not-allowed');
             $(`#stockTransferItemsTable tr#${rowId} input.serial-group`).prop('readonly', false);
        }
        else if(trackingType == 'batch'){
            $(`#stockTransferItemsTable tr#${rowId} input.batch-group`).prop('readonly', false);
            $(`input[name='batch_no[${rowId}]']`).addClass('border custom-border-primary');

            $(`#stockTransferItemsTable tr#${rowId} i.serialBtnForInvoice`)
                    .removeClass('serialBtnForInvoice')
                    .addClass('serialBtnForInvoice-disabled')
                    .addClass('text-muted')
                    .addClass('cursor-not-allowed').attr('data-bs-toggle', '');

            //$(".serialBtnForInvoice").removeClass('serialBtnForInvoice').addClass('serialBtnForInvoice-disabled');
        }else{
            $(`input[name='mfg_date[${rowId}]']`).removeClass('datepicker').prop('readonly', true).attr('placeholder', '');
            $(`input[name='exp_date[${rowId}]']`).removeClass('datepicker').prop('readonly', true).attr('placeholder', '');
            $(`#stockTransferItemsTable tr#${rowId} input.batch-group`).prop('readonly', true).addClass('cursor-not-allowed');
            $(`#stockTransferItemsTable tr#${rowId} i.serialBtnForInvoice`)
                    .removeClass('serialBtnForInvoice')
                    .addClass('serialBtnForInvoice-disabled')
                    .addClass('text-muted')
                    .addClass('cursor-not-allowed').attr('data-bs-toggle', '');
        }
    }
