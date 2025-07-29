$(function() {
	"use strict";

	const tableId = $('#serial_number_table');

	var tableBody = tableId.find('tbody');

	const serialNumberInputBox = $('#serial_number');

	const serialNumberAddBtn = $('.serial_number_add_btn');

	var hiddenJsonData = $("input[name='serial_number_json']");

	const modalName = $("#serialModal");

	let serialNumberIndex = 0;

	var serialNumbersArray = [];

	var currentRowId;

	var itemId;

	var warehouseId;

	var openingQuantity = $("input[name='opening_quantity']");
	/**
   * Language
   * */
  const _lang = {
              enterSerialNumber : "Please Enter Serial Number!",
              serailNumberAlreadyAdded : "Serial Number Already Exist in Table!",
          };

	/**
	 * Validate input box
	 * */
	function validateBeforeAddingSerailNumber(){
		if(getSerialNumberFromInputBox().length ==0){
	        iziToast.error({title: 'Error', layout: 2, message: _lang.enterSerialNumber});
	        serialNumberInputBox.focus();
	        return false;
	    }
	    /**
	     * Validate is smae serial/imei number exist
	     * */
	    if(serialNumbersArray.some(element => element.value.toLowerCase() === getSerialNumberFromInputBox().toLowerCase())){
	    		iziToast.error({title: 'Error', layout: 2, message: _lang.serailNumberAlreadyAdded});
	        serialNumberInputBox.focus();
	        return false;
	    }



	    return true;
	}

	function getSerialNumberFromInputBox(){
		return serialNumberInputBox.val().trim();
	}

	/**
	 * Main Function to add records
	 * */
	function batchRecordsEntryInTable(){
			if(!validateBeforeAddingSerailNumber()) {
	        return;
	    }
	    /**
	     * Add serial number in array
	     * */
	    addRowToTable();

	    /**
	     * After Success
	     * */
			afterAddingRecordInSerialNumberTable();
	}

	function addRowToTable(serialNumberInput = getSerialNumberFromInputBox()){

		serialNumbersArray.push({'id': serialNumberIndex, 'value': serialNumberInput});

		var newRow = `
		<tr id='${serialNumberIndex}'>
			<td>${serialNumberInput}</td>
			<td><div class="font-18 text-danger cursor-pointer text-center remove_serial_number"><i class="fadeIn animated bx bx-trash"></i></div></td>
		</tr>
		`;
		tableBody.prepend(newRow);

		/**
		 * Increment serialNumberIndex
		 * */
		serialNumberIndex++;
	}

	function afterAddingRecordInSerialNumberTable(){
		/**
		 * Empty the Input Box
		 * */
		serialNumberInputBox.val('').focus();
	}
	/**
	 * Add Row Events
	 * */
	serialNumberInputBox.on("keydown", function(){
		/**
		 * restrictSearchSerialBoxEnterKey defined in sale invoice where no need off add operation when click enter button
		 * 
		 * */
		if (typeof restrictSearchSerialBoxEnterKey === 'function' && restrictSearchSerialBoxEnterKey()) {
				return true;
		}

		if (event.key === "Enter") {
        batchRecordsEntryInTable();
    }
		
	});

	serialNumberAddBtn.on("click", function(){
		batchRecordsEntryInTable();
	});

	/**
	 * Delete Row
	 * */
	$(document).on('click', '.remove_serial_number', function() {
		var arrayId = $(this).closest('tr').attr('id');
		deleteRecordById(arrayId);
	  $(this).closest('tr').remove();
	});

	function deleteRecordById(idToRemove) {
	  serialNumbersArray = $.grep(serialNumbersArray, function(obj) {
		  return parseInt(obj.id) !== parseInt(idToRemove);
		});
	}
	/**
	 * Set The Value
	 * */
	$(".setSerial").on('click', function(event) {
		// Use map to create a new array containing only the values
		const valuesArray = serialNumbersArray.map(function(item) {
		  return item.value;
		});

		// Convert the values array to JSON string
		const jsonString = JSON.stringify(valuesArray);
		setJsonSerialNumbers(jsonString);

		//Empty the array
		makeArrayEmpty();

		setOpeningQuantity(valuesArray.length);

		//Close Model
		closeModal();

		});

		function initiateFirst(){
			getJsonSerialNumbers();
			openModal();
		}

		/**
		 * 
		 * Used in Item Master create/edit pages
		 * */
		$(document).on('click', '.serialBtn', function() {
			initiateFirst();
		});

		/******************************************************
		 * Used in Invoice making tables
		 * */
		$(document).on('click', '.serialBtnForInvoice', function() {
			currentRowId = getCurrentRowId(this);
			_globalSerialTracking(currentRowId);
		});

		window._globalSerialTracking = function(rowId) {
	    hiddenJsonData = $("input[name='serial_numbers["+rowId+"]']");
	    openingQuantity = $("input[name='quantity["+rowId+"]']");

	    /* Only used sale modules*/
	    itemId = $("input[name='item_id["+rowId+"]']").val();

	    warehouseId = $("input[name='warehouse_id["+rowId+"]']").val();

	    initiateFirst();
		};

		/**
		 * Only used sale modules
		 * */
		window._getItemId = function(){
			return itemId;
		}
		window._getWarehouseId = function(){
			return warehouseId;
		}

		function isDublicateSelectedSerialNumber(serialNumber){
			if(serialNumbersArray.some(element => element.value.toLowerCase() === serialNumber.toLowerCase())){
	    		iziToast.error({title: 'Error', layout: 2, message: _lang.serailNumberAlreadyAdded});
	        serialNumberInputBox.focus();
	        return false;
	    }
	    return true;
		}
		//Called from serial-tracking-settings.js
		window._addRowToTable = function(serialNumber){
			if(!isDublicateSelectedSerialNumber(serialNumber)) {
	        return;
	    }
			addRowToTable(serialNumber);

			/**
	     * After Success
	     * */
			afterAddingRecordInSerialNumberTable();
		}
		/******************************************************/

		function getCurrentRowId($this){
			return $($this).closest('tr').attr('id');
		}

		function makeArrayEmpty(){
			serialNumbersArray.splice(0, serialNumbersArray.length);
		}

		function setJsonSerialNumbers(jsonString) {
			hiddenJsonData.val(jsonString);
		}

		function getHiddenJsonData() {
			return hiddenJsonData.val();
		}
		function getJsonSerialNumbers() {
			const jsonString = getHiddenJsonData();
			tableBody.empty();

			//Empty the array
			makeArrayEmpty();

			if(jsonString.length > 0){
				const jsonData = JSON.parse(jsonString);  // Parse the JSON string
			  // Use $.each loop to iterate through the parsed array
			  $.each(jsonData, function(index, value) {
			    addRowToTable(value);
			  });
			}
			return true;
		}

		function openModal(){
			modalName.modal('show');
		}

		function closeModal(){
			modalName.modal('hide');
		}

		function setOpeningQuantity(qty){
			openingQuantity.val(qty).trigger("change");
		}
});
