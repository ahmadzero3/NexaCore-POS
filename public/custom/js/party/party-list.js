$(function() {
	"use strict";

    const tableId = $('#datatable');

    const datatableForm = $("#datatableForm");

    const partyType = $('input[name="party_type"]').val();

    let partyPaymentHistoryModal = $('#partyPaymentHistoryModal');

    /**
     *Server Side Datatable Records
    */
    function loadDatatables(){
        //Delete previous data
        tableId.DataTable().destroy();

        var exportColumns = [2,3,4,5,6,7,8,9,10];//Index Starts from 0

        var table = tableId.DataTable({
            processing: true,
            serverSide: true,
            method:'get',
            ajax: {
                    url: baseURL+'/party/'+partyType+'/datatable-list',
                    data:{
                            is_wholesale_customer : isWholesaleCustomer(),
                        },
                },
            columns: [
                {targets: 0, data:'id', orderable:true, visible:false},
                {
                    data: 'id',
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, full, meta) {
                        return '<input type="checkbox" class="form-check-input row-select" name="record_ids[]" value="' + data + '">';
                      }
                },

                {data: 'name', name: 'name',},
                {data: "mobile", name: "mobile"},
                {data: "whatsapp", name: "whatsapp"},
                {data: "email", name: "email"},
                {data: "balance", name: "balance"},
                {
                    data: 'balance_type',
                    orderable: false,
                    className: '',
                    render: function(data, type, full, meta) {
                        return (data === 'no_balance') 
                            ? '<span class="badge bg-light-transparent text-uppercase text-secondary">No Balance</span>' 
                            : (data === 'you_pay' 
                                ? '<span class="badge bg-light-primary text-uppercase text-primary">Pay</span>' 
                                : '<span class="badge bg-light-danger text-uppercase text-danger">Collect</span>');
                    }
                },


                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, full, meta) {
                        if(data == 1){
                            return '<div class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3">Active</div>';
                        }
                        else{
                            return '<div class="badge rounded-pill text-danger bg-light-danger p-2 text-uppercase px-3">Inactive</div>';
                        }

                    }
                },

                {data: 'username', name: 'username'},
                {data: 'created_at', name: 'created_at'},

                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],

            dom: "<'row' "+
                    "<'col-sm-12' "+
                        "<'float-start' l"+
                            /* card-body class - auto created here */
                        ">"+
                        "<'float-end' fr"+
                            /* card-body class - auto created here */
                        ">"+
                        "<'float-end ms-2'"+
                            "<'card-body ' B >"+
                        ">"+
                    ">"+
                  ">"+
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

            buttons: [
                {
                    className: 'btn btn-outline-danger buttons-copy buttons-html5 multi_delete',
                    text: 'Delete',
                    action: function ( e, dt, node, config ) {
                        //Confirm user then trigger submit event
                       requestDeleteRecords();
                    }
                },
                // Apply exportOptions only to Copy button
                {
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: exportColumns
                    }
                },
                // Apply exportOptions only to Excel button
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: exportColumns
                    }
                },
                // Apply exportOptions only to CSV button
                {
                    extend: 'csvHtml5',
                    exportOptions: {
                        columns: exportColumns
                    }
                },
                // Apply exportOptions only to PDF button
                {
                    extend: 'pdfHtml5',
                    orientation: 'portrait',//or "landscape"
                    exportOptions: {
                        columns: exportColumns,
                    },
                },

            ],

            select: {
                style: 'os',
                selector: 'td:first-child'
            },
            order: [[0, 'desc']]


        });

        table.on('click', '.deleteRequest', function () {
              let deleteId = $(this).attr('data-delete-id');

              deleteRequest(deleteId);

        });

        //Adding Space on top & bottom of the table attributes
        $('.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate').wrap("<div class='card-body py-3'>");
    }

    // Handle header checkbox click event
    tableId.find('thead').on('click', '.row-select', function() {
        var isChecked = $(this).prop('checked');
        tableId.find('tbody .row-select').prop('checked', isChecked);
    });

    /**
     * @return count
     * How many checkbox are checked
    */
   function countCheckedCheckbox(){
        var checkedCount = $('input[name="record_ids[]"]:checked').length;
        return checkedCount;
   }

   /**
    * Validate checkbox are checked
    */
   async function validateCheckedCheckbox(){
        const confirmed = await confirmAction();//Defined in ./common/common.js
        if (!confirmed) {
            return false;
        }
        if(countCheckedCheckbox() == 0){
            iziToast.error({title: 'Warning', layout: 2, message: "Please select at least one record to delete"});
            return false;
        }
        return true;
   }
    /**
     * Caller:
     * Function to single delete request
     * Call Delete Request
    */
    async function deleteRequest(id) {
        const confirmed = await confirmAction();//Defined in ./common/common.js
        if (confirmed) {
            deleteRecord(id);
        }
    }

    /**
     * Create Ajax Request:
     * Multiple Data Delete
    */
   async function requestDeleteRecords(){
        //validate checkbox count
        const confirmed = await confirmAction();//Defined in ./common/common.js
        if (confirmed) {
            //Submit delete records
            datatableForm.trigger('submit');
        }
   }
    datatableForm.on("submit", function(e) {
        e.preventDefault();

            //Form posting Functionality
            const form = $(this);
            const formArray = {
                formId: form.attr("id"),
                csrf: form.find('input[name="_token"]').val(),
                _method: form.find('input[name="_method"]').val(),
                url: form.closest('form').attr('action'),
                formObject : form,
                formData : new FormData(document.getElementById(form.attr("id"))),
            };
            ajaxRequest(formArray); //Defined in ./common/common.js

    });

    /**
     * Create AjaxRequest:
     * Single Data Delete
    */
    function deleteRecord(id){
        const form = datatableForm;
        const formArray = {
            formId: form.attr("id"),
            csrf: form.find('input[name="_token"]').val(),
            _method: form.find('input[name="_method"]').val(),
            url: form.closest('form').attr('action'),
            formObject : form,
            formData: new FormData() // Create a new FormData object
        };
        // Append the 'id' to the FormData object
        formArray.formData.append('record_ids[]', id);
        ajaxRequest(formArray); //Defined in ./common/common.js
    }

    /**
    * Ajax Request
    */
    function ajaxRequest(formArray){
        var jqxhr = $.ajax({
            type: formArray._method,
            url: formArray.url,
            data: formArray.formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': formArray.csrf
            },
            beforeSend: function() {
                // Actions to be performed before sending the AJAX request
                if (typeof beforeCallAjaxRequest === 'function') {
                    // Action Before Proceeding request
                }
            },
        });
        jqxhr.done(function(data) {
            
            iziToast.success({title: 'Success', layout: 2, message: data.message});
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

    function afterCallAjaxResponse(formObject){
        loadDatatables();
    }

    $(document).ready(function() {
        //Load Datatable
        loadDatatables();
	} );

    $(document).on('click', '.party-payment-history', function() {
        var partyId = $(this).attr('data-party-id');
        var url = baseURL + `/party/payment-history/`;
        ajaxGetRequest(url ,partyId, 'party-payment-history');
    });

    $(document).on('click', '.party-delete-payment', function() {
        var paymentId = $(this).closest('tr').attr('id');
        deletePaymentRequest(paymentId);
    });

    /**
     * Caller:
     * Function to single delete request
     * Call Delete Request
    */
    async function deletePaymentRequest(paymentId) {
        const confirmed = await confirmAction();//Defined in ./common/common.js
        if (confirmed) {
            var url = baseURL + `/party/payment-delete/`;
            ajaxGetRequest(url ,paymentId, 'delete-party-payment');
        }
    }

    function ajaxGetRequest(url, id, _from) {
          $.ajax({
            url: url + id,
            type: 'GET',
            headers: {
              'X-CSRF-TOKEN': datatableForm.find('input[name="_token"]').val(),
            },
            beforeSend: function() {
              showSpinner();
            },
            success: function(response) {
              if(_from == 'delete-party-payment'){
                handlePartyPaymentDeleteResponse(response);
              }
              else if (_from == 'party-payment-history') {
                handlePartyPaymentHistoryResponse(response);
              } else {
                //
              }
            },
            error: function(response) {
               var message = response.responseJSON.message;
               iziToast.error({title: 'Error', layout: 2, message: message});
            },
            complete: function() {
              hideSpinner();
            },
          });
    }

    function handlePartyPaymentHistoryResponse(response, showModel = true) {
        $("#party-name").text(response.party_name);
        $("#balance-amount").text(_parseFix(response.balance));

        let totalAmount = 0;
        
        var table = $('#payment-history-table tbody');

        table.empty(); // Clear existing rows
        
        $.each(response.partyPayments, function(index, payment) {
            totalAmount += parseFloat(payment.amount);
            var newRow = `
                <tr id="${payment.payment_id}">
                    <td>${payment.transaction_date}</td>
                    <td>${payment.payment_direction}</td>
                    <td>${payment.reference_no}</td>
                    <td>${payment.payment_type}</td>
                    <td class="text-end text-${payment.color}">${payment.amount}</td>
                    <td>
                        <div class="d-flex order-actions justify-content-center">
                            <a href="${baseURL}/party/payment-receipt/print/${payment.payment_id}" target="_blank" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Print"><i class="bx bxs-printer"></i></a>
                            <a href="${baseURL}/party/payment-receipt/pdf/${payment.payment_id}" target="_blank" class="ms-1 text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="PDF"><i class="bx bxs-file-pdf"></i></a>
                            <a href="javascript:;" role="button" class="ms-1 party-delete-payment text-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Delete"><i class="bx bxs-trash"></i></a>
                        </div>
                    </td>
                </tr>
            `;

            table.append(newRow);
        });

        //show only if not shown, in delete payment condition no need to show modal
        if(showModel){
            partyPaymentHistoryModal.modal('show');
        }

        setTooltip();
    }

    function handlePartyPaymentDeleteResponse(response) {
        iziToast.success({title: 'Success', layout: 2, message: response.message});
        partyPaymentHistoryModal.modal('hide');
        loadDatatables();
    }

    function isWholesaleCustomer() {

        if(partyType != 'customer'){
            return 0;//0 retail
        }
        
        /**
         * @return 0 if wholesaler else 0 for retailer
         * */
        return $("#customer_type").val();
    }

    $(document).on("change", '#customer_type', function function_name() {
        loadDatatables();
    });
});
