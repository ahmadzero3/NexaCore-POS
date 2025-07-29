<?php
namespace App\Enums;

enum AccountUniqueCode:string{
    /**
     * Account Groups
     * It has its own parents groups as well
     * */
    case ASSETS                     = 'ASSETS';
    case FIXED_ASSETS               = 'FIXED_ASSETS';
        case CURRENT_ASSETS             = 'CURRENT_ASSETS';
            case CASH_ACCOUNT             = 'CASH_ACCOUNT';
                case CASH_IN_HAND             = 'CASH_IN_HAND';
            case OTHER_CASH_ACCOUNT       = 'OTHER_CASH_ACCOUNT';
            case BANK_ACCOUNT             = 'BANK_ACCOUNT';
            case INPUT_DUTIES_AND_TAXES   = 'INPUT_DUTIES_AND_TAXES';
                case INPUT_TAX                  = 'INPUT_TAX';
        case OTHER_CURRENT_ASSETS             = 'OTHER_CURRENT_ASSETS';
            
        case SUNDRY_DEBTORS             = 'SUNDRY_DEBTORS';

    case EQUITIES_AND_LIABILITIES   = 'EQUITIES_AND_LIABILITIES';
        case CAPITAL_ACCOUNT                        = 'CAPITAL_ACCOUNT';
            case OWNERS_EQUITY                        = 'OWNERS_EQUITY';
                case OPENING_BALANCE_EQUITY                        = 'OPENING_BALANCE_EQUITY';
            case RESERVES_AND_SURPLUS                        = 'RESERVES_AND_SURPLUS';
                case NET_INCOME_OR_PROFIT                        = 'NET_INCOME_OR_PROFIT';
        case SUNDRY_CREDITORS                        = 'SUNDRY_CREDITORS';
        case OUTWARD_DUTIES_AND_TAXES                = 'OUTWARD_DUTIES_AND_TAXES';
            case OUTPUT_TAX                             = 'OUTPUT_TAX';
        case OTHER_CURRENT_LIABILITIES               = 'OTHER_CURRENT_LIABILITIES';
            case UNWITHDRAWN_CHEQUES               = 'UNWITHDRAWN_CHEQUES';
        case LONG_TERM_EQUITIES_AND_LIABILITIES      = 'LONG_TERM_EQUITIES_AND_LIABILITIES';
        case CURRENT_LIABILITIES                     = 'CURRENT_LIABILITIES';

    case EXPENSES   = 'EXPENSES';
        case PURCHASE_ACCOUNTS                        = 'PURCHASE_ACCOUNTS';
            case PURCHASES                        = 'PURCHASES';
        case DIRECT_EXPENSES                        = 'DIRECT_EXPENSES';
           // case DIRECT_EXPENSE_CONSTRAINT                        = 'DIRECT_EXPENSE_CONSTRAINT';//Show all direct expense categories of table "expense_category"
        case INDIRECT_EXPENSES                        = 'INDIRECT_EXPENSES';
           // case INDIRECT_EXPENSE_CONSTRAINT                        = 'INDIRECT_EXPENSE_CONSTRAINT';//Show all in-direct expense categories of table "expense_category"
    /**
     * Accounts
     * It is a childer of Account Groups
     * */
    case STOCK_IN_HAND              = 'STOCK_IN_HAND';

    case INPUT_SGST                 = 'INPUT_SGST';
    case INPUT_CGST                 = 'INPUT_CGST';
    case INPUT_TAX_ALL                 = 'INPUT_TAX_ALL';

    case OUTPUT_CGST                = 'OUTPUT_CGST';
    case OUTPUT_SGST                = 'OUTPUT_SGST';
    case OUTPUT_TAX_ALL                = 'OUTPUT_TAX_ALL';

    case OPENING_STOCK_BALANCE      = 'OPENING_STOCK_BALANCE';
    case SUNDRY_DEBTORS_LIST        = 'SUNDRY_DEBTORS_LIST';
    case SUNDRY_CREDITORS_LIST        = 'SUNDRY_CREDITORS_LIST';
    case PARTY_OPENING_BALANCE        = 'PARTY_OPENING_BALANCE';
    case ADVANCE_PAID_FOR_PURCHASE_ORDER             = 'ADVANCE_PAID_FOR_PURCHASE_ORDER';
}
