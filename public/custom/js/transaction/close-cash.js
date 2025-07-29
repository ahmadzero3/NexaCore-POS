document.addEventListener("DOMContentLoaded", function () {
    // Set default date inputs to today's date
    const today = new Date().toISOString().split("T")[0];
    document.querySelector('input[name="from_date"]').value = today;
    document.querySelector('input[name="to_date"]').value = today;

    // Example: Add event listener for opening balance input
    const openingBalanceInput = document.querySelector(
        'input[name="opening_balance"]'
    );

    // Add event listeners for timepicker inputs
    const timepickerInputs = document.querySelectorAll(".timepicker-edit");
    timepickerInputs.forEach((input) => {
        input.addEventListener("focus", function () {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            $(this).timepicker(
                "setTime",
                `${hours}:${minutes < 10 ? "0" : ""}${minutes}`
            );
            $(this).timepicker("showWidget");
        });
    });

    // Add event listeners for timepicker icons
    const timepickerIcons = document.querySelectorAll(".input-group-text");
    timepickerIcons.forEach((icon) => {
        icon.addEventListener("click", function () {
            const input = $(this).siblings(".timepicker-edit");
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            input.timepicker(
                "setTime",
                `${hours}:${minutes < 10 ? "0" : ""}${minutes}`
            );
            input.timepicker("showWidget");
        });
    });

    const printLink = document.querySelector('a[href*="close-cash-print"]');
    if (printLink) {
        printLink.addEventListener("click", function (event) {
            event.preventDefault(); // Prevent the default link behavior
            window.open(
                this.href,
                "_blank",
                "scrollbars=1,resizable=1,height=300,width=450"
            );
        });
    }
    // Add event listener for the close cash button
    const closeCashButton = document.querySelector(".btn-danger.close");
    if (closeCashButton) {
        closeCashButton.addEventListener("click", function () {
            const today = new Date().toLocaleDateString();
            iziToast.question({
                timeout: 20000,
                close: false,
                overlay: true,
                displayMode: "once",
                id: "question",
                zindex: 999,
                title: "Confirmation",
                message: `Are You Sure to Close Your Cash Today (${today})?`,
                position: "center",
                buttons: [
                    [
                        "<button>Yes</button>",
                        function (instance, toast) {
                            instance.hide(
                                { transitionOut: "fadeOut" },
                                toast,
                                "button"
                            );

                            // Gather data from the table in Card Three
                            const openingBalanceElement =
                                document.querySelector(
                                    ".table-3-tr-1 td:nth-child(2)"
                                );
                            const todayIncomeElement = document.querySelector(
                                ".table-3-tr-2 td:nth-child(2)"
                            );
                            const totalIncomeElement = document.querySelector(
                                ".bg-green td:nth-child(2)"
                            );
                            const todayExpensesElement = document.querySelector(
                                ".bg-red td:nth-child(2)"
                            );
                            const balanceElement = document.querySelector(
                                ".bg-blue td:nth-child(2)"
                            );

                            const csrfTokenElement = document.querySelector(
                                'meta[name="csrf-token"]'
                            );
                            const csrfToken = csrfTokenElement
                                ? csrfTokenElement.getAttribute("content")
                                : null;

                            if (
                                openingBalanceElement &&
                                todayIncomeElement &&
                                totalIncomeElement &&
                                todayExpensesElement &&
                                balanceElement &&
                                csrfToken
                            ) {
                                const data = {
                                    opening_balance: parseFloat(
                                        openingBalanceElement.innerText.replace(
                                            /,/g,
                                            ""
                                        )
                                    ),
                                    today_income: parseFloat(
                                        todayIncomeElement.innerText.replace(
                                            /,/g,
                                            ""
                                        )
                                    ),
                                    total_income: parseFloat(
                                        totalIncomeElement.innerText.replace(
                                            /,/g,
                                            ""
                                        )
                                    ),
                                    today_expenses: parseFloat(
                                        todayExpensesElement.innerText.replace(
                                            /,/g,
                                            ""
                                        )
                                    ),
                                    balance: parseFloat(
                                        balanceElement.innerText.replace(
                                            /,/g,
                                            ""
                                        )
                                    ),
                                    created_by: parseInt(
                                        document.getElementById("created_by")
                                            .value
                                    ),
                                };

                                fetch("/transaction/close-cash/insert", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": csrfToken,
                                    },
                                    body: JSON.stringify(data),
                                })
                                    .then((response) => response.json())
                                    .then((data) => {
                                        if (data.status === "success") {
                                            iziToast.success({
                                                title: "Success",
                                                message: data.message,
                                                position: "topRight",
                                            });
                                            window.open(
                                                "/transaction/apply-close-cash-print",
                                                "_blank",
                                                "scrollbars=1,resizable=1,height=300,width=450"
                                            );
                                        }
                                    });
                            } else {
                                console.error(
                                    "One or more table elements or CSRF token are missing."
                                );
                            }
                        },
                        true,
                    ],
                    [
                        "<button>No</button>",
                        function (instance, toast) {
                            instance.hide(
                                { transitionOut: "fadeOut" },
                                toast,
                                "button"
                            );
                        },
                    ],
                ],
            });
        });
    }
});

$(document).on("click", ".collapse-btn", function () {
    const $btn = $(this);
    const $card = $btn.closest(".card");
    const $body = $card.find(".card-body");

    $body.stop(true).slideToggle(250);

    $btn.find("i").toggleClass("bx-minus bx-plus").addClass("close-button");
});

$(document).ready(function () {
    /**
     * Toggle the sidebar of the template
     * */
    toggleSidebar();
});

$(function () {
    "use strict";

    const tableId = $("#saleInvoiceTable");
    const datatableForm = $("#saleInvoiceForm");

    /**
     *Server Side Datatable Records
     */
    window.loadDatatables = function () {
        tableId.DataTable().destroy();

        var exportColumns = [2, 3, 4, 5, 6, 7, 8];

        var table = tableId.DataTable({
            processing: true,
            serverSide: true,
            method: "get",
            ajax: {
                url: baseURL + "/sale/invoice/datatable-list",
                data: {
                    party_id: $("#party_id").val(),
                    user_id: $("#user_id").val(),

                    from_date: $('input[name="from_date"]').val(),
                    to_date: $('input[name="to_date"]').val(),
                },
            },
            columns: [
                { targets: 0, data: "id", orderable: true, visible: false },
                {
                    data: "id",
                    orderable: false,
                    className: "text-center",
                    render: function (data, type, full, meta) {
                        return (
                            '<input type="checkbox" class="form-check-input row-select" name="record_ids[]" value="' +
                            data +
                            '">'
                        );
                    },
                },

                {
                    data: null,
                    name: "sale_code",
                    orderable: false,
                    className: "text-center",
                    render: function (data, type, full, meta) {
                        let orderCode = data.sale_code || ""; // Default if sale_code is null
                        let statusBadge = "";

                        // Check if status is an object and extract data
                        let statusText = data.status?.text || ""; // Get text from status object
                        let statusCode = data.status?.code || ""; // Get sale_order or quotation code
                        let statusUrl = data.status?.url || "";

                        if (statusText === "Converted from Sale Order") {
                            statusBadge = `<div class="badge text-primary bg-light-primary p-2 text-uppercase px-3">
                                                ${statusText} (<a href="${statusUrl}" target="_blank" data-bs-toggle="tooltip"
                                                                  data-bs-placement="top" title="View Sale Order Details">
                                                                  ${statusCode} <i class="fadeIn animated bx bx-link-external bx-tada-hover"></i>
    
                                                              </a>)
                                            </div>`;
                        } else if (statusText === "Converted from Quotation") {
                            statusBadge = `<div class="badge bg-light-success text-success p-2 text-uppercase px-3">
                                                ${statusText} (<a href="${statusUrl}" target="_blank" data-bs-toggle="tooltip"
                                                                  data-bs-placement="top" title="View Quotation Details">
                                                                  ${statusCode} <i class="fadeIn animated bx bx-link-external bx-tada-hover"></i>
    
                                                              </a>)
                                            </div>`;
                        }

                        if (data.is_return_raised?.status === "Return Raised") {
                            let returnLinks = data.is_return_raised.urls
                                .map(
                                    (url, index) =>
                                        `<a href="${url}" target="_blank" data-bs-toggle="tooltip"
                                                                  data-bs-placement="top" title="View Sale Return Details">
                                                                  ${
                                                                      data.is_return_raised.codes.split(
                                                                          ", "
                                                                      )[index]
                                                                  }
                                                                  <i class="fadeIn animated bx bx-link-external bx-tada-hover"></i></a>`
                                )
                                .join(", ");

                            statusBadge += `<div class="badge text-danger bg-light-danger text-uppercase">
                                                    ${data.is_return_raised.status} (${returnLinks})
                                                </div>`;
                        }

                        // Combine order code and status badge
                        return `<div>
                                        <strong>${orderCode}</strong><br>
                                        ${statusBadge}
                                    </div>`;
                    },
                },

                { data: "sale_date", name: "sale_date" },

                {
                    data: "grand_total",
                    name: "grand_total",
                    className: "text-end",
                },
                { data: "balance", name: "balance", className: "text-end" },

                { data: "username", name: "username" },
                { data: "created_at", name: "created_at" },
            ],

            dom:
                "<'row' " +
                "<'col-sm-12' " +
                "<'float-start' l" +
                /* card-body class - auto created here */
                ">" +
                "<'float-end' fr" +
                /* card-body class - auto created here */
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
                        //Confirm user then trigger submit event
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
                    orientation: "portrait", //or "landscape"
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
            drawCallback: function () {
                /**
                 * Initialize Tooltip
                 * */
                setTooltip();
            },
        });

        table.on("click", ".deleteRequest", function () {
            let deleteId = $(this).attr("data-delete-id");

            deleteRequest(deleteId);
        });

        //Adding Space on top & bottom of the table attributes
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
        const confirmed = await confirmAction(); //Defined in ./common/common.js
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
        const confirmed = await confirmAction(); //Defined in ./common/common.js
        if (confirmed) {
            deleteRecord(id);
        }
    }

    /**
     * Create Ajax Request:
     * Multiple Data Delete
     */
    async function requestDeleteRecords() {
        //validate checkbox count
        const confirmed = await confirmAction(); //Defined in ./common/common.js
        if (confirmed) {
            //Submit delete records
            datatableForm.trigger("submit");
        }
    }
    datatableForm.on("submit", function (e) {
        e.preventDefault();

        //Form posting Functionality
        const form = $(this);
        const formArray = {
            formId: form.attr("id"),
            csrf: form.find('input[name="_token"]').val(),
            _method: form.find('input[name="_method"]').val(),
            url: form.closest("form").attr("action"),
            formObject: form,
            formData: new FormData(document.getElementById(form.attr("id"))),
        };
        ajaxRequest(formArray); //Defined in ./common/common.js
    });

    /**
     * Create AjaxRequest:
     * Single Data Delete
     */
    function deleteRecord(id) {
        const form = datatableForm;
        const formArray = {
            formId: form.attr("id"),
            csrf: form.find('input[name="_token"]').val(),
            _method: form.find('input[name="_method"]').val(),
            url: form.closest("form").attr("action"),
            formObject: form,
            formData: new FormData(), // Create a new FormData object
        };
        // Append the 'id' to the FormData object
        formArray.formData.append("record_ids[]", id);
        ajaxRequest(formArray); //Defined in ./common/common.js
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
        jqxhr.done(function (data) {
            iziToast.success({
                title: "Success",
                layout: 2,
                message: data.message,
            });
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
        //Load Datatable
        loadDatatables();

        /**
         * Modal payment type, reinitiate initSelect2PaymentType() for modal
         * Call because modal won't support ajax search input box cursor.
         * by this code it works
         * */
        initSelect2PaymentType({ dropdownParent: $("#invoicePaymentModal") });
    });

    $(document).on(
        "change",
        '#party_id, #user_id, input[name="from_date"], input[name="to_date"]',
        function function_name(e) {
            loadDatatables();
        }
    );
});

$(function () {
    "use strict";

    const tableId = $("#expenseTable");
    const datatableForm = $("#expenseForm");

    /**
     *Server Side Datatable Records
     */
    function loadDatatables() {
        //Delete previous data
        tableId.DataTable().destroy();

        var exportColumns = [2, 3, 4, 5, 6, 7, 8]; //Index Starts from 0

        var table = tableId.DataTable({
            processing: true,
            serverSide: true,
            method: "get",
            ajax: {
                url: baseURL + "/expense/datatable-list",
                type: "GET", // <-- explicitly use type, not 'method'
                data: {
                    expense_category_id: $("#expense_category_id").val(),
                    from_date: $('input[name="from_date"]').val(),
                    to_date: $('input[name="to_date"]').val(),
                },
                error: function (xhr, error, thrown) {
                    console.error("Expense AJAX error:", xhr.responseText);
                    alert(
                        "Expense datatable load error:\n" +
                            (xhr.responseJSON?.message || xhr.responseText)
                    );
                },
            },

            columns: [
                { targets: 0, data: "id", orderable: true, visible: false },
                {
                    data: "id",
                    orderable: false,
                    className: "text-center",
                    render: function (data, type, full, meta) {
                        return (
                            '<input type="checkbox" class="form-check-input row-select" name="record_ids[]" value="' +
                            data +
                            '">'
                        );
                    },
                },

                { data: "expense_number", name: "expense_number" },

                { data: "expense_date", name: "expense_date" },

                { data: "expense_category", name: "expense_category" },
                { data: "paid_amount" },
                {
                    data: "payment_type",
                    name: "payment_type",
                    orderable: false,
                },
                { data: "username", name: "username" },
                { data: "created_at", name: "created_at" },
            ],

            dom:
                "<'row' " +
                "<'col-sm-12' " +
                "<'float-start' l" +
                /* card-body class - auto created here */
                ">" +
                "<'float-end' fr" +
                /* card-body class - auto created here */
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
                        //Confirm user then trigger submit event
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
                    orientation: "portrait", //or "landscape"
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

        //Adding Space on top & bottom of the table attributes
        $(
            ".dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate"
        ).wrap("<div class='card-body py-3'>");
    }

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
        const confirmed = await confirmAction(); //Defined in ./common/common.js
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
        const confirmed = await confirmAction(); //Defined in ./common/common.js
        if (confirmed) {
            deleteRecord(id);
        }
    }

    /**
     * Create Ajax Request:
     * Multiple Data Delete
     */
    async function requestDeleteRecords() {
        //validate checkbox count
        const confirmed = await confirmAction(); //Defined in ./common/common.js
        if (confirmed) {
            //Submit delete records
            datatableForm.trigger("submit");
        }
    }
    datatableForm.on("submit", function (e) {
        e.preventDefault();

        //Form posting Functionality
        const form = $(this);
        const formArray = {
            formId: form.attr("id"),
            csrf: form.find('input[name="_token"]').val(),
            _method: form.find('input[name="_method"]').val(),
            url: form.closest("form").attr("action"),
            formObject: form,
            formData: new FormData(document.getElementById(form.attr("id"))),
        };
        ajaxRequest(formArray); //Defined in ./common/common.js
    });

    /**
     * Create AjaxRequest:
     * Single Data Delete
     */
    function deleteRecord(id) {
        const form = datatableForm;
        const formArray = {
            formId: form.attr("id"),
            csrf: form.find('input[name="_token"]').val(),
            _method: form.find('input[name="_method"]').val(),
            url: form.closest("form").attr("action"),
            formObject: form,
            formData: new FormData(), // Create a new FormData object
        };
        // Append the 'id' to the FormData object
        formArray.formData.append("record_ids[]", id);
        ajaxRequest(formArray); //Defined in ./common/common.js
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
        jqxhr.done(function (data) {
            iziToast.success({
                title: "Success",
                layout: 2,
                message: data.message,
            });
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
        //Load Datatable
        loadDatatables();
    });

    $(document).on(
        "change",
        "#expense_category_id, input[name='from_date'], input[name='to_date']",
        loadDatatables
    );
});
