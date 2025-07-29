$(function() {
    "use strict";

    let originalButtonText;

    const $attachment = $('#attachment');

    const $removeBtn = $('#removeBtn');
    
    /**
     * Quil Text-editor
     * 
     * */
    var quill = new Quill('#editor', {theme: 'snow'});

    function setupEditor(){
        // Set the height you want (in pixels)
        var newHeight = 150;

        // Get the editor container element
        var editorContainer = document.querySelector('#editor');

        // Use the setHeight method to update the height
        quill.container.style.height = newHeight + 'px';
        quill.root.style.height = newHeight + 'px';
        editorContainer.style.height = newHeight + 'px';

    }

    $(document).ready(function() {
        setupEditor();
    });

    

    $("#emailForm").on("submit", function(e) {
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
    function afterSeccessOfAjaxRequest(formObject){
        formAdjustIfSaveOperation(formObject);
    }

    function ajaxRequest(formArray){
        // Get the Quill editor content
        var content = quill.root.innerHTML;

        var formData = new FormData(document.getElementById(formArray.formId));

        // Append the Quill editor content to the FormData
        formData.append('content', content);

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
        jqxhr.done(function(data) {
            iziToast.success({title: 'Success', layout: 2, message: data.message});
            // Actions to be performed after response from the AJAX request
            if (typeof afterSeccessOfAjaxRequest === 'function') {
                afterSeccessOfAjaxRequest(formArray.formObject);
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

    function formAdjustIfSaveOperation(formObject){
        const _method = formObject.find('input[name="_method"]').val();
        /* Only if Save Operation called*/
        if(_method.toUpperCase() == 'POST' ){
            var formId = formObject.attr("id");
            $("#"+formId)[0].reset();
        }
    }

    /**
     * Email File Attachment code
     * */
    $attachment.on('change', function() {
        if (this.files.length > 0) {
            $removeBtn.prop('disabled', false)
                      .removeClass('btn-outline-secondary')
                      .addClass('btn-outline-danger');
        } else {
            resetAttachment();
        }
    });

    $removeBtn.on('click', resetAttachment);

    function resetAttachment() {
        $attachment.val('');
        $removeBtn.prop('disabled', true)
                  .removeClass('btn-outline-danger')
                  .addClass('btn-outline-secondary');
    }


});
