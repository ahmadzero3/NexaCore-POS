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
    $.getJSON(baseURL+'/schedule/get_service_order_records', {order_id:  orderId}, function(jsonResponse, textStatus) {
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
