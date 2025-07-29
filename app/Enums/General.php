<?php
namespace App\Enums;

enum General:string{
    /**
     * Used to record Payments
     * by these code you can identify from where PaymentTransactions are made
     * Like 1. Direct Payment while making invoice,
     * 2. From invoice list
     * 3. From party -> select invoice and divide the payment
     * */
    case INVOICE                = 'INVOICE';
    case INVOICE_LIST           = 'INVOICE_LIST';
    case PARTY_INVOICE_LIST     = 'PARTY_INVOICE_LIST';
    case PARTY_BALANCE_AFTER_ADJUSTMENT     = 'PARTY_BALANCE_AFTER_ADJUSTMENT';

}
