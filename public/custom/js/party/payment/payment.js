$(function() {
	"use strict";

    let originalButtonText;

    const makePaymentForm = $("#paymentForm");

    const paymentFor = $("#payment_for").val();//purchase, purchase_return, sale, sale return

    let paying = $("input[name='payment']");

    makePaymentForm.on("submit", async function(e) {
        e.preventDefault();
        const form = $(this);
        // Use async confirmation
        const confirmed = await confirmAction('Save it?'); // confirmAction is defined in ./common/common.js
        if (confirmed) {
            const formArray = {
                formId: form.attr("id"),
                csrf: form.find('input[name="_token"]').val(),
                url: form.closest('form').attr('action'),
                formObject: form,
            };
            ajaxRequest(formArray);
        }
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
        closeModalAndAddOption(response);
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
            iziToast.success({title: 'Success', layout: 2, message: response.message});
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
        window.open(baseURL + '/party/payment-receipt/print/' + response.id, '_blank', 'width=800,height=600');
        
        setTimeout(function() {
           location.href = location.href;
        }, 1000);
    }

    function closeModalAndAddOption(response){
        /*Close the Model*/
    }

    $(document).on('click', '.load-records', function() {
        if($("#adjustment").val() == 'none'){
            return false;
        }
        var partyId = $('#party_id').val();
        var url = baseURL + `/party/due-payment/get-records/`;
        ajaxGetRequest(url, partyId, 'load-records');
    });

    $("#adjustment").on('change', function(){
        if($(this).val() == 'none'){
            $(".load-records").addClass('cursor-not-allowed').removeClass('btn-success').addClass('btn-secondary');;
            //Make Table Empty
            var table = $('#duePaymentsRecordsTable tbody');

            table.empty(); // Clear existing rows

        }else{
            $(".load-records").removeClass('cursor-not-allowed').removeClass('btn-secondary').addClass('btn-success');
        }
    });

    function ajaxGetRequest(url, id, _from) {
          $.ajax({
            url: url + id,
            type: 'GET',
            headers: {
              'X-CSRF-TOKEN': makePaymentForm.find('input[name="_token"]').val(),
            },
            beforeSend: function() {
              showSpinner();
            },
            success: function(response) {
              if(_from == 'load-records'){
                handleDuePaymentRecords(response);
              }
              else {
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

    function handleDuePaymentRecords(response) {
        
        let totalGrandTotal = 0;
        let totalPaidAmount = 0;
        let id=1;
        var table = $('#duePaymentsRecordsTable tbody');

        table.empty(); // Clear existing rows

        $.each(response.data, function(index, record) {
            totalGrandTotal += parseFloat(record.grand_total);
            totalPaidAmount += parseFloat(record.paid_amount);
            
            var newRow = `
                 <tr>
                    <td>${id++}</td>
                    <td>${record.transaction_date}</td>
                    <td>${record.invoice_or_bill_code}</td>
                    <td class='text-end' data-tableexport-celltype="number" >${_formatNumber(record.grand_total)}</td>
                    <td class='text-end' data-tableexport-celltype="number" >${_formatNumber(record.paid_amount)}</td>
                    <td class='text-end' data-tableexport-celltype="number" >${_formatNumber(record.balance)}</td>
                    <td><input type='text' class='form-control cu_numeric text-end' name='record[${record.id}]'></td>
                </tr>
            `;

            table.append(newRow);
        });

        // Create a new row for total amount
        const totalRow = `
            <tr>
                <th colspan="3" class="text-end">Total Amount:</th>
                <th class="text-end">${_parseFix(totalGrandTotal)}</th>
                <th class="text-end">${_parseFix(totalPaidAmount)}</th>
                <th class="text-end">${_parseFix(totalGrandTotal-totalPaidAmount)}</th>
                <th class="text-end balance-minus-adjusted">0</th>
            </tr>
        `;
        table.append(totalRow);
        setTooltip();
        showBalanceMinusAdjusted();
    }

    function showBalanceMinusAdjusted() {
        var inputAdjustedAmount = sumOfRecordInputs();
        var inputPaymentAmount = getPaying();

        var bottomTotal = inputAdjustedAmount + ' / ' + inputPaymentAmount;
        $(".balance-minus-adjusted").text(bottomTotal);

        if (parseFloat(inputAdjustedAmount) > parseFloat(inputPaymentAmount)) {
            // Danger
            $(".balance-minus-adjusted").removeClass('text-success').addClass('text-danger');
        } else {
            // Success
            $(".balance-minus-adjusted").removeClass('text-danger').addClass('text-success');
        }
    }

    function getPaying() {
        var payment = parseFloat(paying.val()) || 0;
        return _parseFix(payment);
    }
    
    /**
     * sum of adjusted record invoice/bill's
     * */
    function sumOfRecordInputs() {
        let totalSum = 0;
        
        // Select all input elements with names that match 'record[...]'
        $("input[name^='record']").each(function() {
            let inputValue = parseFloat($(this).val()) || 0; // Parse the input value or default to 0 if empty/invalid
            totalSum += inputValue;
        });
        
        return _parseFix(totalSum);
    }

    /**
     * Input Payment Box
     * */
    paying.on('input', function(){
        showBalanceMinusAdjusted();
    });

    /**
     * On each record invoice/bill adjusted
     * */
    $(document).on('input', "input[name^='record']", function() {
        showBalanceMinusAdjusted();
    });

});//main function
