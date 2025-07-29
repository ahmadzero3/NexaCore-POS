$(function() {
   "use strict";
  
   $(document).ready(function() {
    /**
     * variables: _baseUnitId, _secondaryUnitId, _conversionRate
     * File: Variables defined in views/items/item/edit.blade.php
     * 
     * function : autoSetDefaultUnits()
     * File: function in modals\unit\unit.js
     * */
    autoSetDefaultUnits(_baseUnitId, _secondaryUnitId, _conversionRate);

    /**
     * function : setUnits()
     * Defined in modals\unit\unit.js
     * 
     * */
    setUnits();   

    /**
     * variables: _trackingType
     * File: Variables defined in views/items/item/edit.blade.php 
     * */
    setTrackingTypeRadio(_trackingType);

   });
   /**
    * Set Tracking Type Radio button
    * */
   function setTrackingTypeRadio(_trackingType) {
       if(_trackingType == 'batch'){
        $("#batch_tracking").attr('checked', true).trigger('change');
       }
       else if(_trackingType == 'serial'){
        $("#serial_tracking").attr('checked', true).trigger('change');
       }else{
        $("#general_tracking").attr('checked', true).trigger('change');
       }
   }
});
