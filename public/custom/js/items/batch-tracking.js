$(function() {
	"use strict";

	const tableId = $('#batch_number_table');

	var tableBody = tableId.find('tbody');

	var hiddenJsonData = $("input[name='batch_details_json']");

	const modalName = $("#batchModal");

	let rowId = 0;

	var batchNumbersArray = [];

	/**
   * Language
   * */
  const _lang = {
              enterbatchNumber : "Please Enter batch Number!",
              serailNumberAlreadyAdded : "Batch Number Already Exist in Table!",
              enterAtleastOneRecordToSave : "Empty Form! Can't Save!",
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
			    openingQuantity: '',
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
			<td><input type='text' class="form-control opening_quantity cu_numeric" value='${objectData.openingQuantity}'></td>
			<td><div class="font-18 text-danger cursor-pointer text-center remove_batch_number"><i class="fadeIn animated bx bx-trash"></i></div></td>
		</tr>
		`;
		tableBody.append(newRow);

		resetDatePicker();
		/**
		 * Increment rowId
		 * */
		rowId++;
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
			  const openingQuantity = parseFloat(row.find('input.opening_quantity').val().trim()) || 0;

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
			    openingQuantity: openingQuantity,
			  };

		    /**
		     * Add item to your data structure (e.g., items array)
		     * Check if at least one value exists
		    **/
			  if (batchNo || mfgDate || expDate || modelNo || mrp || color || size || openingQuantity) { 
			    items.push(item);
			  }
		});

    //Save it in hidden input box
    const jsonString = (items.length > 0) ? JSON.stringify(items) : '';
		hiddenJsonData.val(jsonString);	

		//Empty the array
		batchNumbersArray.splice(0, batchNumbersArray.length);

		//Set Total Opening Quantity
    const totalOpeningQuantity = items.reduce((acc, item) => acc + item.openingQuantity, 0);
		setOpeningQuantity(totalOpeningQuantity);

		//Close Model
		closeModal();

		});//save batchBtn

		function initiateFirst(){
			getJsonbatchNumbers();
			openModal();
		}
		$(document).on('click', '.batchBtn', function() {
			initiateFirst()
		});

		function getJsonbatchNumbers() {
			//Empty Table tbody row before opening modal
			tableBody.empty();

			//Get Hidden input box data
			var jsonString = hiddenJsonData.val();
			
			//verify is empty
			if(jsonString.length === 0){
				//Add first empty row
				addRowToTable();
				return;
			}
			
			const arrayData = JSON.parse(jsonString);  // Parse the JSON string

		  arrayData.forEach(function(item) {
				var itemObject = {
				    batchNo: item.batchNo,
				    mfgDate: item.mfgDate,
				    expDate: item.expDate,
				    modelNo: item.modelNo,
				    mrp: item.mrp,
				    color: item.color,
				    size: item.size,
				    openingQuantity: item.openingQuantity,
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

		function setOpeningQuantity(qty){
			$("input[name='opening_quantity']").val(qty);
		}

});
