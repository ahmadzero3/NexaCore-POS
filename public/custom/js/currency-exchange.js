/**
     * ************************************************************
     * Currency Exchange Related code
     * :START
     */
$(document).ready(function () {
    //Auto Load when page is ready
    //|| operation=='convert' || operation =='update'

    if(operation == 'save'){
        setExchangeRateOnInputBox(true);
    }else{
        setExchangeRateOnInputBox(false);
    }
});

//Manually changed invoice_currency_id
$(document).on('change', `#invoice_currency_id`, function() {
    setExchangeRateOnInputBox(true);
});

function setExchangeRateOnInputBox(defaultExchangeRate = false){

    if(!isEnableSecondaryCurrency){
        return true;
    }
    //get current exchange rate from invoice_currency_id. which has data-exchange-rate attribute
    var exchangeRate = $("#invoice_currency_id option:selected").data('exchange-rate');
    if(defaultExchangeRate){
        $("input[name='exchange_rate']").val(_parseFix(exchangeRate));

    }
    //Update Lang
    var exchangeLang = $(".exchange-lang").data('exchange-lang');
    var currencyCode = $("#invoice_currency_id option:selected").data('code');
    currencyCode = `<a href="javascript:void(0);" class="text-primary show-currency">${currencyCode}</a>`;
    $(".exchange-lang").html(exchangeLang+'-'+currencyCode);

    convertToExchangeCurrencyAmount();
}

$(document).on('click', '.show-currency', function(){
    //focus on input box when click on currency code
    $("input[name='exchange_rate']").focus();
});

$(document).on('keyup', `input[name='exchange_rate']`, function() {
    convertToExchangeCurrencyAmount()
});

function convertToExchangeCurrencyAmount(){
    if(!isEnableSecondaryCurrency){
        return true;
    }
    var exchangeRate = returnDecimalValueByName('exchange_rate');
    var grandTotal = getGrandTotal();
    var convertedAmount = grandTotal * exchangeRate;
    $(".converted_amount").val(_parseFix(convertedAmount));
}

$('#party_id').on('select2:select', function(e) {
    if(!isEnableSecondaryCurrency){
        return true;
    }

    var selectedData = e.params.data; // Get selected data object
    $("#invoice_currency_id").val(selectedData.currency_id).trigger('change');
});

/**
 * :END
 * ************************************************************
 */
