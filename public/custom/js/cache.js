$(function() {
	"use strict";

    let originalButtonText;

    $("#clearCache").on("click", function(e) {
        e.preventDefault();
        ajaxRequest();
    });

    function disableSubmitButton(form) {
        showSpinner();
    }

    function enableSubmitButton(form) {
        hideSpinner();
    }

    function beforeCallAjaxRequest(){
        disableSubmitButton();
    }
    function afterCallAjaxResponse(){
        enableSubmitButton();
    }

    function ajaxRequest(){
        var jqxhr = $.ajax({
            type: "POST",
            url: baseURL + '/settings/app/clear_cache',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': _csrf_token,  // Laravel's CSRF token
            },
            contentType: false,
            processData: false,
            beforeSend: function() {
                // Actions to be performed before sending the AJAX request
                if (typeof beforeCallAjaxRequest === 'function') {
                    beforeCallAjaxRequest();
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
                afterCallAjaxResponse();
            }
        });
    }



});
