<?php

namespace Database\Seeders\Updates;

use App\Models\EmailTemplate;
use App\Models\Prefix;
use App\Models\SmsTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Version145Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        echo "Version145Seeder Running...";
        $this->updateRecords();
        $this->addNewRecords();

        echo "\Version145Seeder Completed!!\n";
    }

    public function updateRecords()
    {
        Prefix::query()->update(['quotation' => 'QT/']);

    }

    public function addNewRecords()
    {
        /**
         * Quotation
         *
         * */
        $content = <<<EOT
Dear [Customer Name],

Please find attached the details of Quotation.

Quotation Details:

    Quotation Number: [Quotation Number]

    Quotation Date: [Quotation Date]

    Total: [Total Amount]


If you have any questions or require further assistance, please don't hesitate to contact us at [Your Email Address] or [Your Mobile Number].

Thank you for your business.

Sincerely,

[Your Company Name]

[Your Mobile Number]
EOT;

$keys = <<<EOT
[Quotation Number]

[Customer Name]

[Quotation Date]

[Total Amount]

[Balance Amount]

[Your Email Address]

[Your Mobile Number]

[Your Company Name]
EOT;

        EmailTemplate::firstOrCreate([
            'name'                  => 'QUOTATION',
            'subject'               => 'Quotation #[Quotation Number] - [Customer Name]',
            'content'               => $content,
            'keys'                  => $keys,
            'delete_flag'           => 1,
        ]);



/**
         * QUOTATION SMS
         *
         * */
        $content = <<<EOT
Dear [Customer Name],

Your Quotation details for [Quotation Number] are attached.

Total: [Total Amount]

Contact us at [Your Mobile Number] or [Your Email Address] for questions.

Thanks,
[Your Company Name]
EOT;

$keys = <<<EOT
[Quotation Number]

[Customer Name]

[Quotation Date]

[Total Amount]

[Balance Amount]

[Your Email Address]

[Your Mobile Number]

[Your Company Name]
EOT;

        SmsTemplate::firstOrCreate([
            'name'                  => 'QUOTATION',
            'content'               => $content,
            'keys'                  => $keys,
            'delete_flag'           => 1,
        ]);
}
}
