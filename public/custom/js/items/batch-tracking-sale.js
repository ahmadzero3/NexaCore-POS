$(function() {
	"use strict";

	const tableId = $('#batch_number_table');

	var tableBody = tableId.find('tbody');

	var jsonStringifyResponseData;

	const modalName = $("#batchModal");

	const mainForm = $("#invoiceForm");

	

	let rowId = 0;

	let mainTableRowId;

	var batchNumbersArray = [];

	var itemId;

	var warehouseId;

	/**
   * Language
   * */
  const _lang = {
              enterbatchNumber : "Please Enter batch Number!",
              serailNumberAlreadyAdded : "Batch Number Already Exist in Table!",
              enterAtleastOneRecordToSave : "Empty Form! Can't Save!",
              noRecordsToAdd : "No Records on Batch Form!!",
          };

  /**
   * Prepaired JSON data
   * */
  function defaultJsonData() {
      var tempObjectWithData = {
			    batchNo: '', // Assign batchNo as the code
			    mfgDate: '',
			    expDate: '',
			    modelNo: '',
			    mrp: '',
			    color: '',
			    size: '',
			    availableStock: '',
			    saleQuantity: '',
			    readonlyInput: false,
			    itemData: '',
			}

      return tempObjectWithData;
  }

	function addRowToTable(objectData = defaultJsonData()){

		var newRow = `
		<tr id='${rowId}'>
			<td><input type='text' class="form-control batch_no" value='${objectData.batchNo}'></td>
			<td class="${(!itemSettings.enable_mfg_date)?'d-none':''}"><input type='text' class="form-control mfg_date datepicker " placeholder="Pick Date" value='${objectData.mfgDate}'></td>
			<td class="${(!itemSettings.enable_exp_date)?'d-none':''}"><input type='text' class="form-control exp_date datepicker " placeholder="Pick Date" value='${objectData.expDate}'></td>
			<td class="${(!itemSettings.enable_model)?'d-none':''}"><input type='text' class="form-control model_no " value='${objectData.modelNo}'></td>
			<td class="${(!itemSettings.show_mrp)?'d-none':''}"><input type='text' class="form-control mrp cu_numeric " value='${objectData.mrp}'></td>
			<td class="${(!itemSettings.enable_color)?'d-none':''}"><input type='text' class="form-control color " value='${objectData.color}'></td>
			<td class="${(!itemSettings.enable_size)?'d-none':''}"><input type='text' class="form-control size " value='${objectData.size}'></td>
			<td><input type='text' class="form-control available_stock cu_numeric" value='${objectData.availableStock}'></td>
			<td><input type='text' class="form-control sale_quantity cu_numeric border-primary" value='${objectData.saleQuantity}'></td>
			<td>
				<div class="font-18 text-danger cursor-pointer text-center remove_batch_number"><i class="fadeIn animated bx bx-trash"></i></div>
				<input type='hidden' class='hidden-item-data' value='${objectData.itemData}'/>
			</td>
		</tr>
		`;

		tableBody.append(newRow);

		setReadonlyClass(objectData.readonlyInput, rowId);

		resetDatePicker();
		/**
		 * Increment rowId
		 * */
		rowId++;
	}

	function setReadonlyClass(status, rowId) {
		if(!status){
			return true;
		}

		//Set input Box readonly
		tableId.find(`tr#${rowId} input`).not('.sale_quantity').attr('readonly', true).removeClass('datepicker').addClass('cursor-not-allowed');
		tableId.find(`tr#${rowId} div`).removeClass('remove_batch_number text-danger').addClass('cursor-not-allowed text-secondary');
	}

	function resetDatePicker(){
		$(".datepicker").flatpickr({
      dateFormat: dateFormatOfApp, // Set the date format
    });
	}

	/**
	 * Delete Row
	 * */
	$(document).on('click', '.remove_batch_number', function() {
		var arrayId = $(this).closest('tr').attr('id');
		deleteRecordById(arrayId);
	  $(this).closest('tr').remove();
	});

	function deleteRecordById(idToRemove) {
	  batchNumbersArray = $.grep(batchNumbersArray, function(obj) {
		  return parseInt(obj.id) !== parseInt(idToRemove);
		});
	}

	/**
	 * Add Row
	 * */
	$(document).on('click', '.add_row', function() {
		addRowToTable();
	});
	/**
	 * Set The Value
	 * */
	$(".saveBatch").on('click', function(event) {

		const items = [];

	    // Loop through each row's input element
	    $('#batch_number_table tbody tr').each(function() {
		  	const row = $(this); // Get the current row element
		  	const batchNo = row.find('input.batch_no').val().trim() || '';
			  const mfgDate = row.find('input.mfg_date').val().trim() || ''; 
			  const expDate = row.find('input.exp_date').val().trim() || ''; 
			  const modelNo = row.find('input.model_no').val().trim() || '';
			  const mrp = parseFloat(row.find('input.mrp').val().trim()) || 0;
			  const color = row.find('input.color').val().trim() || '';
			  const size = row.find('input.size').val().trim() || '';
			  const availableStock = parseFloat(row.find('input.available_stock').val().trim()) || 0;
			  const itemData = row.find('input.hidden-item-data').val().trim() || '';
			  const saleQuantity = parseFloat(row.find('input.sale_quantity').val().trim()) || 0;

			  /**
			   * Note: These object id's also used in 
			   * App\Services\ItemTransactionService.php
			   * */
			   const item = {
			    batchNo: batchNo, // Assign batchNo as the code
			    mfgDate: mfgDate,
			    expDate: expDate,
			    modelNo: modelNo,
			    mrp: mrp,
			    color: color,
			    size: size,
			    availableStock: availableStock,
			    saleQuantity: saleQuantity,
			    itemData: itemData,
			  };

		    /**
		     * Add item to your data structure (e.g., items array)
		     * Check if at least one value exists
		    **/
			  if (batchNo || mfgDate || expDate || modelNo || mrp || color || size || availableStock) { 
			    items.push(item);
			  }
		});

	    //Save it in variable
	    const jsonString = (items.length > 0) ? JSON.stringify(items) : '';

	    /**
	     * Create tables rows based on selections
	     * */

		//Empty the array
		batchNumbersArray.splice(0, batchNumbersArray.length);

		//Set Total Opening Quantity
    	// const totalOpeningQuantity = items.reduce((acc, item) => acc + item.availableStock, 0);
		// setOpeningQuantity(totalOpeningQuantity);

		createStringfyDataToCreateSaleTableRow(jsonString);

		//Close Model
		closeModal();

	});//save batchBtn
			
		function createStringfyDataToCreateSaleTableRow(stringData){

		if(stringData == ''){
			iziToast.error({title: 'Error', layout: 2, message: _lang.noRecordsToAdd});
			return;
		}
        var jsonObject = JSON.parse(stringData);

        jsonObject.forEach((data, index) => {
        				
    			if (data.saleQuantity <= 0) {
						    return;
						}

				const itemDataArray = JSON.parse(data.itemData);

                var dataObject = {
                    warehouse_id    : warehouseId,
                    id              : itemId,
                    name            : itemDataArray[0].name,
                    tracking_type   : itemDataArray[0].tracking_type,
                    description     : (itemDataArray[0].description != null) ? itemDataArray[0].description : '',
                    sale_price  		: itemDataArray[0].sale_price,
                    is_sale_price_with_tax  :  (itemDataArray[0].tax_type =='inclusive') ? 1 : 0,
                    tax_id          : itemDataArray[0].tax_id,
                    //quantity        : itemDataArray[0].quantity,
                    taxList         : itemDataArray[0].taxList,
                    unitList        : itemDataArray[0].unitList,
                    base_unit_id      : itemDataArray[0].base_unit_id,
                    secondary_unit_id : itemDataArray[0].secondary_unit_id,
                    selected_unit_id  : itemDataArray[0].selected_unit_id,
                    conversion_rate   : itemDataArray[0].conversion_rate,

                    sale_price_discount   : itemDataArray[0].sale_price_discount,
                    discount_type   : itemDataArray[0].discount_type,
                    discount_amount : itemDataArray[0].discount_amount,
                    total_price_after_discount   : 0,
                    tax_amount      : itemDataArray[0].tax_amount,
                    total_price     : 0,

                    current_stock     : data.availableStock,
                    quantity     : data.saleQuantity,
                    quantityToTransfer     : data.saleQuantity, //Used in Stock Transfer

                    serial_numbers  :  '',

                    batch_no        : (data.batchNo!== null) ? data.batchNo : '',
                    mfg_date        : (data.mfgDate!== null) ? data.mfgDate : '',
                    exp_date        : (data.expDate!== null) ? data.expDate : '',
                    mrp        			: (data.mrp!== null) ? data.mrp : '',
                    model_no       	: (data.modelNo!== null) ? data.modelNo : '',
                    color       		: (data.color!== null) ? data.color : '',
                    size       			: (data.size!== null) ? data.size : '',
                    mainTableRowId	: mainTableRowId,
                };

               addRowToInvoiceItemsTableFromBatchTracking(dataObject,true);
          });
    }

		function initiateFirst(){
			getJsonbatchNumbers();
			openModal();
		}
		$(document).on('click', '.batchBtn', function() {
			initiateFirst();
		});

		function getJsonbatchNumbers() {
			//Empty Table tbody row before opening modal
			tableBody.empty();

			const arrayData = JSON.parse(jsonStringifyResponseData);  // Parse the JSON string

			//verify is empty
			if(arrayData.length === 0){
				//Add first empty row
				//addRowToTable();
				return;
			}
			
		  arrayData.forEach(function(item) {
				var itemObject = {
				    batchNo: item.batchNo,
				    mfgDate: item.mfgDate,
				    expDate: item.expDate,
				    modelNo: item.modelNo,
				    mrp: item.mrp,
				    color: item.color,
				    size: item.size,
				    availableStock: item.availableStock,
				    saleQuantity: item.saleQuantity,
				    readonlyInput: true,
				    itemData:JSON.stringify(item.itemData),
				}
			
		    addRowToTable(itemObject);
			});
			
			return true;
		}

		function openModal(){
			modalName.modal('show');
		}

		function closeModal(){
			modalName.modal('hide');
		}

		// function setOpeningQuantity(qty){
		// 	$("input[name='available_stock']").val(qty);
		// }

	window._globalBatchTracking = function(rowId) {
	    /* Only used sale modules*/
	    mainTableRowId = rowId;
	    itemId = $("input[name='item_id["+rowId+"]']").val();
	    warehouseId = $("input[name='warehouse_id["+rowId+"]']").val();
	    getItemBatchStockRecords();
    }

    function beforeCallAjaxRequest(formArray) {
    	showSpinner();
    }
    function afterCallAjaxResponse(formArray) {
    	hideSpinner();
    }
    function afterSeccessOfAjaxRequest(formArray) {
    	jsonStringifyResponseData = JSON.stringify(formArray.response);
    	initiateFirst();
    }

    //Return the data records from the item wise batch stocks
    function getItemBatchStockRecords() {
    	const formArray = {
            csrf: mainForm.find('input[name="_token"]').val(),
            url: baseURL + '/item/batch/stock/ajax/get-list',
            data:{
            	item_id : itemId,
            	warehouse_id : warehouseId,
            },
        };
        ajaxRequest(formArray);
    }

    function ajaxRequest(formArray){
    		var formData = formArray.data;
    		
        var jqxhr = $.ajax({
            type: 'get',
            url: formArray.url,
            data: formData,
            dataType: 'json',
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': formArray.csrf
            },
            beforeSend: function() {
                // Actions to be performed before sending the AJAX request
                if (typeof beforeCallAjaxRequest === 'function') {
                    beforeCallAjaxRequest(formArray);
                }
            },
        });
        jqxhr.done(function(data) {
            formArray.response = data;
            // Actions to be performed after response from the AJAX request
            if (typeof afterSeccessOfAjaxRequest === 'function') {
                afterSeccessOfAjaxRequest(formArray);
            }
        });
        jqxhr.fail(function(response) {
                var message = response.responseJSON.message;
                iziToast.error({title: 'Error', layout: 2, message: message});
        });
        jqxhr.always(function() {
            // Actions to be performed after the AJAX request is completed, regardless of success or failure
            if (typeof afterCallAjaxResponse === 'function') {
                afterCallAjaxResponse(formArray);
            }
        });
    }

});
