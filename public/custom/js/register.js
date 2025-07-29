$(function() {
    "use strict";

    /*Password show & hide js*/
    $(document).ready(function () {

      $("#show_hide_password a").on('click', function(event) {
        show_hide_password('show_hide_password');
      });

      $("#show_hide_confirm_password a").on('click', function() {
        show_hide_password('show_hide_confirm_password');
      });

    });

    function show_hide_password(id) {

      var input = $('#' + id + ' input');
      var icon = $('#' + id + ' i');

      if (input.attr("type") === "text") {
        input.attr('type', 'password');
        icon.removeClass("bx-hide").addClass("bx-show");
      } else if (input.attr("type") === "password") {
        input.attr('type', 'text');
        icon.removeClass("bx-show").addClass("bx-hide");
      }
    }


    //Form Validation & Submit through Ajax
    $("#registerForm").on("submit",function (e) {
        e.preventDefault();

        var jqxhr = $.ajax({
            type: "POST",
            url: 'register',
            data: $('#registerForm').serialize(),
            dataType: 'json',
          });
          jqxhr.done(function(data) {
            iziToast.success({title: 'Success', layout: 2, message: data.message});
            setTimeout(function() {
                location.href = 'dashboard';
              }, 1000);
              });
          jqxhr.fail(function(data) {
           var message = data.responseJSON.message;
            iziToast.error({title: 'Error', layout: 2, message: message});
          });

    });
  });//jQqury default

