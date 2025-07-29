$(function() {
	"use strict";

	

	const serialSearchInputBoxId = $("#serial_number");

	$(document).ready(function() {
		//Change the Serial/IMEI search bar name
		serialSearchInputBoxId.attr({
			placeholder: 'Search Serial/IMEI',
		});
		//Remove Add Button from the HTML code
		$(".serial_number_add_btn").remove();
	});

	/**
    * Autocomplete
    * Item Search Input box
    * */

    serialSearchInputBoxId.autocomplete({
        minLength: 1,
        source: function(request, response) {
                    $.ajax({
                        url: baseURL + '/item/serial/stock/ajax/get-list',
                        dataType: "json",
                        data: {
                            search: request.term,
                            item_id: _getItemId(),
                            warehouse_id: _getWarehouseId(),
                        },
                        success: function(data) {
                        	if (data.length === 0) {
                        		showNoRecordsFound();
                        	}else{
                            response(data);
                        	}
                        }
                    });
                },

        focus: function(event, ui) {
                    if (ui.item !== undefined) {
                    	//
                    }
                    return false;
                },

        select: function(event, ui) {
                    if (ui.item !== undefined) {
                         _addRowToTable(ui.item.name);
                    }
                    $(".ui-autocomplete-loading").removeClass('ui-autocomplete-loading');
                    return false;
                },
                
        open: function(event, ui) {
                    // Add header after the menu is opened
                    var header = $("<li class='ui-autocomplete-category' style='padding: 5px; border-bottom: 1px solid #ddd; background-color: #f8f9fa;'>" +
                        "<div style='display: flex; font-weight: bold;'>" +
                        "<span style='flex: 3;'>Serial/IMEI</span>" +
                        "<span style='flex: 1;'>Item Name</span>" +
                        "</div></li>");
                    $(this).autocomplete("widget").prepend(header);
                }
    }).autocomplete("instance")._renderItem = function(ul, item) {
        return $("<li>")
            .attr("style", "padding: 5px; border-bottom: 1px solid #eee;")
            .append(`<div style="display: flex; align-items: center;">
                        <span style="flex: 3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${item.name || 'N/A'}</span>
                        <span style="flex: 1;">${item.item_name}</span>
                     </div>`)
            .appendTo(ul);
    };

    serialSearchInputBoxId.autocomplete( "option", "appendTo", "#serialModal" );
	
	serialSearchInputBoxId.on("keydown", function(){
		if (event.key === "Enter") {
				return false;
	        //batchRecordsEntryInTable();
	    }
	});


	function showNoRecordsFound() {
        $(".no-records-found-lable").html('');
        let label = $('<label class="text-danger fst-italic no-records-found-lable">No Records Found</label>');
	    $('#serial_number').parent().after(label); // Append after the parent div

	    setTimeout(() => {
	       label.remove();
	    }, 2000); // Remove the label after 2 seconds
        
        $(".ui-autocomplete-loading").removeClass('ui-autocomplete-loading');
  }
});

function restrictSearchSerialBoxEnterKey() {
	return true;
}