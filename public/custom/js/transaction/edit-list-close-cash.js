$(function () {
    "use strict";

    const form = $("#editCloseCashForm");
    const baseURL = $("#base_url").val();

    form.on("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const url = form.attr("action");
        const method = form.find('input[name="_method"]').val();
        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        $.ajax({
            url: url,
            type: "POST", // Laravel requires POST for PUT/PATCH methods
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "X-HTTP-Method-Override": "PUT", // Simulate PUT request
            },
            success: function (response) {
                iziToast.success({
                    title: "Success",
                    message: response.message,
                    position: "topRight",
                    onClosed: function () {
                        window.location.href =
                            baseURL + "/transaction/close/cash/list";
                    },
                });
            },
            error: function (xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).forEach((error) => {
                        iziToast.error({
                            title: "Error",
                            message: error[0],
                            position: "topRight",
                        });
                    });
                } else {
                    iziToast.error({
                        title: "Error",
                        message:
                            xhr.responseJSON?.message || "An error occurred",
                        position: "topRight",
                    });
                }
            },
        });
    });
});
