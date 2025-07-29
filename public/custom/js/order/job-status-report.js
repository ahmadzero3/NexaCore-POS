$(function() {
	"use strict";

    let originalButtonText;

    const tableId = $('#orderReport');

    let taxSelectionBox = $('select[name="customer_id"]')

    /**
     * Language
     * */
    const _lang = {
                total : "Total",
            };

    $("#reportForm").on("submit", function(e) {
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
    function afterSeccessOfAjaxRequest(formObject, response){
        formAdjustIfSaveOperation(response);
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
        jqxhr.done(function(response) {
            // Actions to be performed after response from the AJAX request
            if (typeof afterSeccessOfAjaxRequest === 'function') {
                afterSeccessOfAjaxRequest(formArray.formObject, response);
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

    function formAdjustIfSaveOperation(response){
        
        var tableBody = tableId.find('tbody');

        var id = 1;
        var tr = "";
        
        var sum_total_amount = 0;
        var sum_paid_amount = 0;
        
        $.each(response.data, function(index, item) {
            var total_amount = parseFloat(item.total_amount);
            var paid_amount = parseFloat(item.paid_amount);
            var balance = parseFloat(item.total_amount - item.paid_amount);
             tr += "<tr>"
             tr += "<td>" + id++ + "</td>";
             tr += "<td>" + item.order_date + "</td>";
             tr += "<td>" + item.customer_name     + "</td>";
             tr += "<td>" + item.order_code + "</td>";
             tr += "<td>" + item.job_code + "</td>";
             tr += "<td>" + item.service_name + "</td>";
             tr += "<td>" + item.start_date + ' ' + item.start_time  + "</td>";
             tr += "<td>" + item.end_date + ' ' + item.end_time  + "</td>";
             tr += "<td>" + item.assigned_user + "</td>";
             tr += "<td>" + item.user_status + "</td>";
             tr += "</tr>"
             sum_total_amount+=total_amount;
             sum_paid_amount+=paid_amount;
        });

        // Clear existing rows:
        tableBody.empty();
        tableBody.append(tr);
    }

    /** 
     * 
     * Table Exporter
     * PDF, SpreadSheet
     * */
    $(document).on("click", '#generate_pdf', function() {
        tableId.tableExport({type:'pdf',escape:'false'});
    });

    $(document).on("click", '#generate_excel', function() {
        tableId.tableExport({type:'xlsx',escape:'false'});
    });
    
});//main function
