$(document).ready(function () {  
    var form = '#printForm',  

    a4 = [595.28, 841.89]; // for a4 size paper width and height  

    $('#generate_pdf').on('click', function () {  
        generatePDF();
    });  

    function generatePDF(){
            const { jsPDF } = window.jspdf;
            var doc = new jsPDF('l', 'mm', [1200, 1210]);

            var pdfjs = document.querySelector(form);

            // Convert HTML to PDF in JavaScript
            doc.html(pdfjs, {
                callback: function(doc) {
                    doc.save("order-receipt.pdf");
                },
                x: 10,
                y: 10
            });

    }

    $("#printButton").click(function(){
        $(form).printThis({
            importCSS: true,
            importStyle: true, 
            canvas: true,
        });
    });
});