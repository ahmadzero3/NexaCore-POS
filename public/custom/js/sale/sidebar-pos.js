document.addEventListener("DOMContentLoaded", function () {
    var sidebar = document.getElementById("sidebar-pos");
    var closeBtn = document.getElementById("sidebar-pos-close");
    var openBtn = document.querySelector(".btn.btn-primary.rounded-circle");
    var overlay = document.getElementById("sidebar-pos-overlay");

    if (openBtn) {
        openBtn.addEventListener("click", function () {
            sidebar.classList.add("open");
            if (overlay) overlay.classList.add("active");
            if (overlay) overlay.style.display = "block";
        });
    }
    if (closeBtn) {
        closeBtn.addEventListener("click", function () {
            sidebar.classList.remove("open");
            if (overlay) overlay.classList.remove("active");
            if (overlay) overlay.style.display = "none";
        });
    }
    if (overlay) {
        overlay.addEventListener("click", function () {
            sidebar.classList.remove("open");
            overlay.classList.remove("active");
            overlay.style.display = "none";
        });
    }

    // Handle Finish button click
    document
        .querySelectorAll(".sidebar-pos-action-btn.finish-invoice-btn")
        .forEach(function (btn) {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                var saleId = this.getAttribute("data-sale-id");
                if (!saleId) return;

                fetch("/sale/invoice/update-status", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: JSON.stringify({
                        sale_id: saleId,
                        status: "finished",
                    }),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            iziToast.success({
                                title: "Success",
                                message: "Invoice status updated successfully!",
                                position: "topRight",
                                timeout: 2000,
                                onClosed: function () {
                                    location.reload();
                                },
                            });
                        } else {
                            iziToast.error({
                                title: "Error",
                                message: "Failed to update invoice status.",
                                position: "topRight",
                            });
                        }
                    })
                    .catch(() => {
                        iziToast.error({
                            title: "Error",
                            message: "Error updating invoice status.",
                            position: "topRight",
                        });
                    });
            });
        });

    document
        .querySelectorAll('.sidebar-pos-action-btn[title="Delete"]')
        .forEach(function (btn) {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                var saleId = this.getAttribute("data-sale-id");
                if (!saleId) return;

                iziToast.question({
                    timeout: false,
                    close: false,
                    overlay: true,
                    displayMode: "once",
                    zindex: 10000,
                    title: "Confirm Deletion",
                    message:
                        "Are you sure you want to delete this invoice permanently?",
                    position: "center",
                    buttons: [
                        [
                            "<button><b>Yes</b></button>",
                            function (instance, toast) {
                                instance.hide(
                                    { transitionOut: "fadeOut" },
                                    toast,
                                    "button"
                                );
                                // Proceed with deletion
                                fetch("/sale/invoice/delete", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": document
                                            .querySelector(
                                                'meta[name="csrf-token"]'
                                            )
                                            .getAttribute("content"),
                                    },
                                    body: JSON.stringify({
                                        sale_id: saleId,
                                    }),
                                })
                                    .then((response) => response.json())
                                    .then((data) => {
                                        if (data.success) {
                                            iziToast.success({
                                                title: "Deleted",
                                                message:
                                                    "Invoice deleted successfully!",
                                                position: "topRight",
                                                timeout: 2000,
                                                onClosed: function () {
                                                    location.reload();
                                                },
                                            });
                                        } else {
                                            iziToast.error({
                                                title: "Error",
                                                message:
                                                    "Failed to delete invoice.",
                                                position: "topRight",
                                            });
                                        }
                                    })
                                    .catch(() => {
                                        iziToast.error({
                                            title: "Error",
                                            message: "Error deleting invoice.",
                                            position: "topRight",
                                        });
                                    });
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
                                // Do nothing, just close the toast
                            },
                        ],
                    ],
                    onOpening: function (instance, toast) {
                        // Optionally, add custom styling or focus
                    },
                });
            });
        });
});
