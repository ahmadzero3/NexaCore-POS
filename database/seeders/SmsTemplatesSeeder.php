<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SmsTemplate;

class SmsTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*SmsTemplate::create([
            'name'                  => 'ORDER CREATED',
            'content'               => 'Dear :customer_first_name :customer_last_name , service order is created. Your order id is :order_id. Thank you',
            'keys'                  => ':customer_first_name
:customer_last_name
:order_id
:company_name
:order_date',
            'delete_flag'           => 1,
        ]);*/

        /**
         * 
         * SALE INVOICE
         * */
        $content = <<<EOT
Dear [Customer Name],

Your invoice for [Invoice Number] is attached.

Total: [Total Amount]
Paid: [Paid Amount]
Due: [Balance Amount]

Contact us at [Your Mobile Number] or [Your Email Address] for questions.

Thanks,
[Your Company Name]
EOT;
        $keys = <<<EOT
[Invoice Number]

[Customer Name]

[Sale Date]

[Due Date]

[Total Amount]

[Paid Amount]

[Balance Amount]

[Your Email Address]

[Your Mobile Number]

[Your Company Name]
EOT;

        SmsTemplate::create([
            'name'                  => 'SALE INVOICE',
            'content'               => $content,
            'keys'                  => $keys,
            'delete_flag'           => 1,
        ]);

        /**
         * SALE ORDER
         * 
         * */
        $content = <<<EOT
Dear [Customer Name],

Your sale order details for [Order Number] are attached.

Total: [Total Amount]
Paid: [Paid Amount]
Due: [Balance Amount]

Contact us at [Your Mobile Number] or [Your Email Address] for questions.

Thanks,
[Your Company Name]
EOT;

$keys = <<<EOT
[Order Number]

[Customer Name]

[Order Date]

[Due Date]

[Total Amount]

[Paid Amount]

[Balance Amount]

[Your Email Address]

[Your Mobile Number]

[Your Company Name]
EOT;

        SmsTemplate::create([
            'name'                  => 'SALE ORDER',
            'content'               => $content,
            'keys'                  => $keys,
            'delete_flag'           => 1,
        ]);

        /**
         * SALE RETURN
         * 
         * */
        $content = <<<EOT
Dear [Customer Name],

Your sale return details for [Return Number] are attached.

Total: [Total Amount]
Returned: [Return Amount]
Due: [Balance Amount]

Contact us at [Your Mobile Number] or [Your Email Address] for questions.

Thanks,
[Your Company Name]
EOT;

$keys = <<<EOT
[Return Number]

[Customer Name]

[Return Date]

[Total Amount]

[Return Amount]

[Balance Amount]

[Your Email Address]

[Your Mobile Number]

[Your Company Name]
EOT;

        SmsTemplate::create([
            'name'                  => 'SALE RETURN',
            'content'               => $content,
            'keys'                  => $keys,
            'delete_flag'           => 1,
        ]);


        /**
         * 
         * PURCHASE BILL
         * */
        $content = <<<EOT
Dear [Supplier Name],

Please find attached the invoice for your recent purchase.

Bill Details:

   Bill Number: [Bill Number]
   Purchase Date: [Purchase Date]
   Total: [Total Amount]
   Paid Amount: [Paid Amount]
   Bill Balance: [Balance Amount]

If you have any questions or require further assistance, please don't hesitate to contact us at [Your Email Address] or [Your Mobile Number].

Thank you for your business.

Sincerely,
[Your Company Name]
[Your Mobile Number]
EOT;
        $keys = <<<EOT
[Bill Number]

[Supplier Name]

[Purchase Date]

[Total Amount]

[Paid Amount]

[Balance Amount]

[Your Email Address]

[Your Mobile Number]

[Your Company Name]
EOT;

        SmsTemplate::create([
            'name'                  => 'PURCHASE BILL',
            'content'               => $content,
            'keys'                  => $keys,
            'delete_flag'           => 1,
        ]);


/**
         * PURCHASE ORDER
         * 
         * */
        $content = <<<EOT
Dear [Supplier Name],

Your Purchase order details for [Order Number] are attached.

Total: [Total Amount]
Paid: [Paid Amount]
Due: [Balance Amount]

Contact us at [Your Mobile Number] or [Your Email Address] for questions.

Thanks,
[Your Company Name]
EOT;

$keys = <<<EOT
[Order Number]

[Customer Name]

[Order Date]

[Due Date]

[Total Amount]

[Paid Amount]

[Balance Amount]

[Your Email Address]

[Your Mobile Number]

[Your Company Name]
EOT;

        SmsTemplate::create([
            'name'                  => 'PURCHASE ORDER',
            'content'               => $content,
            'keys'                  => $keys,
            'delete_flag'           => 1,
        ]);

/**
         * PURCHASE RETURN
         * 
         * */
        $content = <<<EOT
Dear [Supplier Name],

Your purchase return details for [Return Number] are attached.

Total: [Total Amount]
Returned: [Return Amount]
Due: [Balance Amount]

Contact us at [Your Mobile Number] or [Your Email Address] for questions.

Thanks,
[Your Company Name]
EOT;

$keys = <<<EOT
[Return Number]

[Supplier Name]

[Return Date]

[Total Amount]

[Return Amount]

[Balance Amount]

[Your Email Address]

[Your Mobile Number]

[Your Company Name]
EOT;

        SmsTemplate::create([
            'name'                  => 'PURCHASE RETURN',
            'content'               => $content,
            'keys'                  => $keys,
            'delete_flag'           => 1,
        ]);


    }
}
