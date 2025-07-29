$(function () {
    "use strict";

    const tableId = $("#datatable");

    const datatableForm = $("#datatableForm");

    /**
     * Server Side Datatable Records
     */
    window.loadDatatables = function () {
        tableId.DataTable().destroy();

        var exportColumns = [1, 2, 3, 4, 5];

        var table = tableId.DataTable({
            processing: true,
            serverSide: true,
            method: "get",
            ajax: {
                url: baseURL + "/transaction/close-cash/datatable-list",
                data: {
                    from_date: $('input[name="from_date"]').val(),
                    to_date: $('input[name="to_date"]').val(),
                },
            },
            columns: [
                { targets: 0, data: "id", orderable: true, visible: false },
                { data: "opening_balance", name: "opening_balance" },
                { data: "today_income", name: "today_income" },
                { data: "total_income", name: "total_income" },
                { data: "today_expenses", name: "today_expenses" },
                { data: "balance", name: "balance" },
                { data: "created_by", name: "created_by" },
                { data: "created_at", name: "created_at" },
                { data: "updated_at", name: "updated_at" },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false,
                },
            ],

            dom:
                "<'row' " +
                "<'col-sm-12' " +
                "<'float-start' l" +
                ">" +
                "<'float-end' fr" +
                ">" +
                "<'float-end ms-2'" +
                "<'card-body ' B >" +
                ">" +
                ">" +
                ">" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

            buttons: [
                {
                    className:
                        "btn btn-outline-danger buttons-copy buttons-html5 multi_delete",
                    text: "Delete",
                    action: function (e, dt, node, config) {
                        // Confirm user then trigger submit event
                        requestDeleteRecords();
                    },
                },
                // Apply exportOptions only to Copy button
                {
                    extend: "copyHtml5",
                    exportOptions: {
                        columns: exportColumns,
                    },
                },
                // Apply exportOptions only to Excel button
                {
                    extend: "excelHtml5",
                    exportOptions: {
                        columns: exportColumns,
                    },
                },
                // Apply exportOptions only to CSV button
                {
                    extend: "csvHtml5",
                    exportOptions: {
                        columns: exportColumns,
                    },
                },
                // Apply exportOptions only to PDF button
                {
                    extend: "pdfHtml5",
                    orientation: "portrait", // or "landscape"
                    exportOptions: {
                        columns: exportColumns,
                    },
                },
            ],

            select: {
                style: "os",
                selector: "td:first-child",
            },
            order: [[0, "desc"]],
        });

        table.on("click", ".deleteRequest", function () {
            let deleteId = $(this).attr("data-delete-id");
            deleteRequest(deleteId);
        });

        // Adding Space on top & bottom of the table attributes
        $(
            ".dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate"
        ).wrap("<div class='card-body py-3'>");
    };

    // Handle header checkbox click event
    tableId.find("thead").on("click", ".row-select", function () {
        var isChecked = $(this).prop("checked");
        tableId.find("tbody .row-select").prop("checked", isChecked);
    });

    /**
     * @return count
     * How many checkbox are checked
     */
    function countCheckedCheckbox() {
        var checkedCount = $('input[name="record_ids[]"]:checked').length;
        return checkedCount;
    }

    /**
     * Validate checkbox are checked
     */
    async function validateCheckedCheckbox() {
        const confirmed = await confirmAction(); // Defined in ./common/common.js
        if (!confirmed) {
            return false;
        }
        if (countCheckedCheckbox() == 0) {
            iziToast.error({
                title: "Warning",
                layout: 2,
                message: "Please select at least one record to delete",
            });
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
        const confirmed = await confirmAction(); // Defined in ./common/common.js
        if (confirmed) {
            deleteRecord(id);
        }
    }

    /**
     * Create Ajax Request:
     * Multiple Data Delete
     */
    async function requestDeleteRecords() {
        // Validate checkbox count
        const confirmed = await confirmAction(); // Defined in ./common/common.js
        if (confirmed) {
            // Submit delete records
            datatableForm.trigger("submit");
        }
    }

    datatableForm.on("submit", function (e) {
        e.preventDefault();

        // Form posting Functionality
        const form = $(this);
        const formArray = {
            formId: form.attr("id"),
            csrf: form.find('input[name="_token"]').val(),
            _method: form.find('input[name="_method"]').val(),
            url: form.closest("form").attr("action"),
            formObject: form,
            formData: new FormData(document.getElementById(form.attr("id"))),
        };
        ajaxRequest(formArray); // Defined in ./common/common.js
    });

    /**
     * Create AjaxRequest:
     * Single Data Delete
     */
    function deleteRecord(id) {
        const url = `${baseURL}/transaction/close-cash/delete/${id}`;
        const csrfToken = $('meta[name="csrf-token"]').attr("content"); // Ensure CSRF token is available

        $.ajax({
            type: "DELETE",
            url: url,
            headers: {
                "X-CSRF-TOKEN": csrfToken,
            },
            success: function (response) {
                iziToast.success({
                    title: "Success",
                    layout: 2,
                    message: response.message,
                });
                loadDatatables(); // Refresh the DataTable
            },
            error: function (response) {
                const message =
                    response.responseJSON?.message || "An error occurred";
                iziToast.error({
                    title: "Error",
                    layout: 2,
                    message: message,
                });
            },
        });
    }

    function afterSeccessOfAjaxRequest(formObject, response) {
        // It is from cash-in-hand.js
        setCashInHandValue(response.cashInHand);
    }

    /**
     * Ajax Request
     */
    function ajaxRequest(formArray) {
        var jqxhr = $.ajax({
            type: formArray._method,
            url: formArray.url,
            data: formArray.formData,
            dataType: "json",
            contentType: false,
            processData: false,
            headers: {
                "X-CSRF-TOKEN": formArray.csrf,
            },
            beforeSend: function () {
                // Actions to be performed before sending the AJAX request
                if (typeof beforeCallAjaxRequest === "function") {
                    // Action Before Proceeding request
                }
            },
        });
        jqxhr.done(function (response) {
            iziToast.success({
                title: "Success",
                layout: 2,
                message: response.message,
            });
            if (typeof afterSeccessOfAjaxRequest === "function") {
                afterSeccessOfAjaxRequest(formArray.formObject, response);
            }
        });
        jqxhr.fail(function (response) {
            var message = response.responseJSON.message;
            iziToast.error({ title: "Error", layout: 2, message: message });
        });
        jqxhr.always(function () {
            // Actions to be performed after the AJAX request is completed, regardless of success or failure
            if (typeof afterCallAjaxResponse === "function") {
                afterCallAjaxResponse(formArray.formObject);
            }
        });
    }

    function afterCallAjaxResponse(formObject) {
        loadDatatables();
    }

    $(document).ready(function () {
        // Load Datatable
        loadDatatables();

        /**
         * Modal payment type, reinitiate initSelect2PaymentType() for modal
         * Call because modal won't support ajax search input box cursor.
         * by this code it works
         */
        initSelect2PaymentType({ dropdownParent: $("#invoicePaymentModal") });
    });

    $(document).on(
        "change",
        'input[name="from_date"], input[name="to_date"]',
        function function_name(e) {
            loadDatatables();
        }
    );

    tableId.on("click", ".view-details", function () {
        const id = $(this).data("id");
        window.location.href = baseURL + "/print/list-close-cash/details/" + id;
    });
});
