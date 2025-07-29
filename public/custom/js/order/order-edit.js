"use strict";

var orderId;

/**
 * After Form Loading
 * */
$(document).ready(function() {
    //Order ID, from input Hidden Box
    orderId = $("input[name='order_id'").val();

    //Load Order Services List
    orderEdit();
});
/**
 * Auto load when form appeared to edit
 * @return JSON
 * */
function orderEdit(){
    /*If Service ID exist, get the service details*/
    $.getJSON(baseURL+'/order/get_service_order_records', {order_id:  orderId}, function(jsonResponse, textStatus) {
        if(jsonResponse.status){
            jsonResponse.data.forEach(record => {
             addRowToServiceTable(record);
            });
        }
        else{
            iziToast.error({title: 'Error', layout: 2, message: jsonResponse.message});
        }
    });
}

/**
 * Delete Paid Payment in order edit form
 * @return JSON
 * */
$(document).on('click', '.payment-delete', function() {
   var _this = $(this);
   const paymentId = _this.data('record-id');
   if(confirm(_lang.wantToDelete)){
       paymentDelete(paymentId, _this);
   }
});
/**
 * Delete Payment : Ajax Request
 * @return JSON
 * 
 * */
function paymentDelete(paymentId, _this){
    /*If Service ID exist, get the service details*/
    $.getJSON(baseURL+'/order/payment/delete', {payment_id:  paymentId}, function(jsonResponse, textStatus) {
        if(jsonResponse.status){
            iziToast.success({title: 'Success', layout: 2, message: jsonResponse.message});
            _this.closest('tr').remove();
        }
        else{
            iziToast.error({title: 'Error', layout: 2, message: jsonResponse.message});
        }
    });
}