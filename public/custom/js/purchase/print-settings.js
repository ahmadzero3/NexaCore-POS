$(document).ready(function () {  
    var colspan =  $('#printInvoice > thead > tr:first > th').not('.d-none').length - 1
    $('.tfoot-first-td').attr('colspan', colspan);
});