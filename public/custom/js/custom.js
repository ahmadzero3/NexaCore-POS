/**
 * Input Box clear button with Focus
 * ex: <span class="input-group-text " role="button" id="clear-input"><i class="bx bx-x"></i></span>
 * */
$(document).on('click', '#clear-input-with-focus', function(event) {
  event.preventDefault();
  // Access the input element directly
  const input = $(this).prev();
  input.val('');
  input.focus();
});

/**
 * Input Box clear button without Focus
 *
 * */
$(document).on('click', '#clear-input-without-focus', function(event) {
  event.preventDefault();
  // Access the input element directly
  const input = $(this).prev();
  input.val('');
});
/**
 * Input Box button with Near Focus
 * */
$(document).on('click', '#input-near-focus', function(event) {
  event.preventDefault();
  // Access the input element directly
  const input = $(this).prev();
  input.focus();
});
/**
 * Custom decimals to toFixed()
 * */
function _parseQuantity(value){
  return parseFloat(value).toFixed(quantityPrecision);
}
function _parseFix(value){
  return parseFloat(value).toFixed(numberPrecision);
}
/**
 * return Inclusive & Exclusive Tax value
 * */
function returnTaxValue(taxType, taxRate, totalPriceAfterDiscount){
  var taxValue = 0;
  var taxIntoAmount = taxRate * totalPriceAfterDiscount;
  if(taxType == 'inclusive'){
      taxValue = ((taxIntoAmount)/(100+taxRate));
  }else{
      taxValue = (taxIntoAmount/100);
  }
  return parseFloat(taxValue);
}
/**
 * Format Number
 * using numbro.min.js
 *
 * */
numbro.setDefaults({
    thousandSeparated: true,
    mantissa: numberPrecision,
});
function _formatNumber(inputNumber){
  var outputString = numbro(inputNumber).format();
  return outputString;
}
function _unFormatNumber(InputString){
  var outputNumber = numbro.unformat(InputString);
  return outputNumber;//typeof number
}
/**
 * return 0
 * if empty or isNaN
 * */
function returnZeroIfEmptyyOrisNaN(inputValue) {
  return (inputValue === "" || isNaN(inputValue)) ? 0 : inputValue;
}

/**
 * Allow only numeric input
 * Allow only positive value
 * */
$(document).on('input keypress paste', '.cu_numeric', function(event) {
  const value = $(this).val();
  const key = event.key;

  // Check if adding another dot or minus would violate the format
  if ((key === '.' && value.indexOf('.') !== -1) ||
      (key === '-' && $(this).hasClass('cu_numeric') && value.length !== 0)) {
    event.preventDefault();
    return;
  }

  $(this).val(value.replace(/[^0-9\-.]/g, ''));
});


/**
 * Theme Mode change
 * */

$(document).on("click", '.theme-mode', function(){

    var baseUrl = $(this).attr("data-base-url");

    var themeMode = $('html').attr('class');

    $.get(baseUrl+'/theme/switch/'+themeMode, function(data) {});
});

$(document).ready(function() {
  if($('html').hasClass('light-theme')) {
     $(".dark-mode-icon i").attr("class", "bx bx-moon");
  } else {
     $(".dark-mode-icon i").attr("class", "bx bx-sun");
  }
});

/**
 * Seletion box like App Settings Sub menu-bar
 * */
$('.list-group-item').click(function(){
    $('.list-group-item').removeClass('active text-white');
    $(this).addClass('active text-white');
});

/**
 * Generate Numeric Random Numbers
 * */
function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    // The maximum is exclusive and the minimum is inclusive
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

/**
 * Custom Page Loader: Show
 * */
function showSpinner() {
  const spinnerOverlay = document.getElementById('spinner-overlay');
  spinnerOverlay.style.display = 'flex';
}

/**
 * Custom Page Loader: Hide
 * */
function hideSpinner() {
  const spinnerOverlay = document.getElementById('spinner-overlay');
  spinnerOverlay.style.display = 'none';
}

/**
 * Return 1 if no-tax else 0
 * */
function noTaxFlag(argument) {
  return parseInt((appTaxType == 'no-tax')?1:0);
}
