<style>
       body {
            width: 1000px;
            font-family: 'DejaVu Sans', sans-serif;
            margin: 3px auto;
            padding: 2px;
            box-sizing: border-box;
        }
        .invoice {
            border: 1px solid #999797;
            padding-top: 0px;
            padding-left: 10px;
            padding-right: 10px;
            padding-bottom: 0px;
            width: 980px;
            margin: 0 auto;
        }
        table {
            width: 980px;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #ccc;
            padding: 5px;
            font-size: 12px;
        }
        .table-bordered th,{
            text-align: {{$appDirection == 'ltr' ? 'left' : 'right'}};

        }
        th, td {
            /*text-align: left;*/
            text-align: {{$appDirection == 'ltr' ? 'left' : 'right'}};/* 01-01-2025 */
            vertical-align: top;
        }
        th {
            background-color: #e6e6fa;
        }
        .bank-details {
            margin-top: 20px;
            font-size: 12px;
        }
        .bank-details {
            text-align: {{$appDirection == 'ltr' ? 'left' : 'right'}};
        }
        .signature {
            margin-top: 40px;
            font-size: 12px;
        }
        .signature {
            text-align: right;
        }
        .header {
            width: 100%;
            table-layout: fixed;
        }
        .company-info {
            text-align: center;
            width: 50%;
            vertical-align: middle;
        }
        [dir="ltr"] .bill-info {
            text-align: right;
        }
        .logo {
            text-align: left;
        }
        [dir="rtl"] .logo {
            text-align: right;
        }

        .company-logo {
            width: 120px;
            height: 120px;
        }
        .addresses {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 3px;
        }
        .address {
            width: 50%;
            background-color: #e6e6fa;
            padding: 10px;
            vertical-align: top;
            border: 1px solid #ccc;
            padding: 15px;
            font-size: 16px;
            line-height: 26px;
        }

        .terms-and-conditions, .address {
            text-align: {{$appDirection == 'ltr' ? 'left' : 'right'}};
        }

        .tfoot-first-td{
            text-align: {{$appDirection == 'ltr' ? 'left' : 'right'}};
        }
        .header td, .addresses td {
            border: none;
        }

        .invoice-container {
            text-align: center;
        }

        .invoice-name {
            display: block;

            font-size: 18px;
        }

        .ltr {
            direction: ltr;
            text-align: left;
        }

        .rtl {
            direction: rtl;
            text-align: right;
        }

        .bill-number {
            font-size: 24px;
            font-weight: bold;
        }

        .custom-table td {
            font-size: 14px;
        }

        .table-compact td, .table-compact th {
            padding: 0.25rem;
            font-size: 0.875rem;
            line-height: 1.2;
        }

        .terms-and-conditions {
            background-color: #e6e6fa;
            padding: 15px;
            margin-top: 20px;

        }

        .cu-fs-1 {
            font-size: 13px;
            line-height: 1;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .signature {

            font-size: 16px;
        }
        [dir="ltr"] .signature {
            text-align: right;
        }
        [dir="rtl"] .signature {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-end {
            text-align: right;
        }

        .fw-bold{
            font-weight: bold;
        }

        .cu-fs-18{
            font-size: 18px;
        }
        .invoice-note{
            font-weight: bold;
        }

        .cu-fs-14 {
          font-size: 14px;
          line-height: 1;
        }
        .cu-fs-16{
            font-size: 16px;
            line-height: 2;
        }
        .cu-fs-16-only{
            font-size: 16px;
        }

        .company-contact{
            font-size: 16px;
            line-height: 1.5;
        }

        .company-name{
            font-size: 2rem;
            font-weight: bold;
        }

        .d-none{
            display: none;
        }

        @if($isPdf)
                        /*Code for PDF ONLY : Start*/
                        .address{
                            font-size: 14px;
                            line-height: 20px;
                        }
                        .cu-fs-18{
                            font-size: 16px;
                        }
                        .company-name{
                            font-size: 20px;
                            font-weight: bold;
                        }

                        [dir="ltr"] .company-contact{
                            font-size: 14px;
                            line-height: 1.5;
                            text-align: right;
                        }
                        [dir="rtl"] .company-contact{
                            font-size: 14px;
                            line-height: 1.5;
                        }
                        [dir="ltr"] .company-info{
                            text-align: left;
                            padding-left: 10px;
                        }
                        [dir="rtl"] .company-info {
                            text-align: right;
                            padding-left: 10px;
                        }

                        .cu-fs-14 {
                            font-size: 10;
                        }

                        [dir="rtl"] .terms-and-conditions {
                            text-align: right;
                        }
                        /*Code for PDF ONLY : End*/
        @endif
    </style>
