$(function() {
	"use strict";

    let originalButtonText;

    let openModal = $('#unitModal');

    let baseUnit = $('select[name="base_unit_id"]');

    let secondaryUnit = $('select[name="secondary_unit_id"]');

    let conversionRate = $("input[name='conversion_rate']");
    
    //let initialValue = 1;

    /**
     * Language
     * */
    const _lang = {
                pleaseSelectBaseUnit : "Please Select Base Unit!",
                pleaseSelectSecondaryUnit : "Please Select Secondary Unit!",
                enterConvertionRate : "Please Enter Conversion Rate!",
            };
    window.autoSetDefaultUnits = function(_baseUnitId = '', _secondaryUnitId = '', _conversionRate = 1) {
        if(_baseUnitId != ''){
            baseUnit.val(_baseUnitId);
        }
        if(_secondaryUnitId != ''){
            secondaryUnit.val(_secondaryUnitId);
        }
        if(_conversionRate != ''){
            conversionRate.val(_conversionRate);
        }
    };

    function getBaseUnitData(){
        return baseUnit.find("option:selected").text();
    }

    function getSecondaryUnitData(){
        return secondaryUnit.find("option:selected").text();
    }
    function showUnitData() {
        $("#base-text").text(`1 ${getBaseUnitData()} =`);
        conversionRate.val(conversionRate.val());
        $("#secondary-text").text(` ${getSecondaryUnitData()}`);
    }
    function functionSetLabel(){
        var label ='';
        label += $("#base-text").text();
        label += conversionRate.val();
        label += $("#secondary-text").text();
        $(".unit-label").text(label);
    }
    $(document).ready(function() {
        autoSetDefaultUnits();
        showUnitData();
        functionSetLabel();
    });
    $(document).on("change", "select[name='base_unit_id'], select[name='secondary_unit_id']", function(){
        showUnitData();
    });

    function validateUnitsAndConversionRate() {
        if(baseUnit.val().length === 0){
            iziToast.error({title: 'Error', layout: 2, message: _lang.pleaseSelectBaseUnit});
            return false;
        }
        if(secondaryUnit.val().length === 0){
            iziToast.error({title: 'Error', layout: 2, message: _lang.pleaseSelectSecondaryUnit});
            return false;
        }
        if(conversionRate.val().trim().length === 0 || parseFloat(conversionRate.val()) <=0 ){
            iziToast.error({title: 'Error', layout: 2, message: _lang.enterConvertionRate});
            return false;
        }
        return true;
    }
    $(".setUnits").on('click', function() {
        setUnits();
    });
    window.setUnits = function(){
        let validated = validateUnitsAndConversionRate();
        if(validated){
            openModal.modal('hide');
            showUnitData();
            functionSetLabel();
        }
    }

});//main function
