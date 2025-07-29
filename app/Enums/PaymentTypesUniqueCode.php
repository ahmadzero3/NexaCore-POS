<?php
namespace App\Enums;

enum PaymentTypesUniqueCode:string{
    case CASH                     = 'CASH';
    case CHEQUE                   = 'CHEQUE';
    case BANK                     = 'BANK';
}
