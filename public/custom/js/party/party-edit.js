$(function() {
   "use strict";
  
   $(document).ready(function() {

    /**
     * variables: _opening_balance_type
     * File: Variables defined in views/party/edit.blade.php 
     * */
    setOpeningBalanceTypeRadio(_opening_balance_type);

    /**
     * variables: _isWholesaleCustomer
     * File: Variables defined in views/party/edit.blade.php 
     * */
    setWholesaleCustomerRadio(_isWholesaleCustomer);

   });
   /**
    * Set Tracking Type Radio button
    * */
   function setOpeningBalanceTypeRadio(_opening_balance_type) {
       if(_opening_balance_type == 'to_pay'){
        $("#to_pay").attr('checked', true);
       }else{
        $("#to_receive").attr('checked', true);
       }
   }

   /**
    * Set isWholesaleCustomer Radio input
    * */
   function setWholesaleCustomerRadio(_isWholesaleCustomer) {
      /**
       * Verify first if party_type not a customer then return true
       * */
      if($("input[name='party_type']").val() != 'customer'){
         return true;
      }

      /**
       * Set Radio
       * */
      if(_isWholesaleCustomer == 1){
         $("#wholesaler").attr('checked', true).trigger('change');
      }
      else{
         $("#retailer").attr('checked', true).trigger('change');
      }
      
   }

});
