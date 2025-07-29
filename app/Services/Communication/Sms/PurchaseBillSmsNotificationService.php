<?php

namespace App\Services\Communication\Sms;
use Illuminate\Http\JsonResponse;
use App\Services\SmsService;
use App\Models\Purchase\Purchase;
use App\Models\SmsTemplate;
use App\Services\GeneralDataService;
use App\Traits\FormatNumber;

class PurchaseBillSmsNotificationService{

    use FormatNumber;

    private $data;
    private $smsService;
    private $smsTemplateName;
    private $messageContent;
    private $keywordReplacer;

    public function __construct(SmsService $smsService, GeneralDataService $keywordReplacer)
    {
    	$this->smsService = $smsService;
        $this->keywordReplacer = $keywordReplacer;
    }

    public function purchaseCreatedSmsNotification($purchaseId)
    {
        //set variable
        $this->smsTemplateName = 'PURCHASE BILL';

        if(!$this->prepareSmsTemplate()){
            return [
                    'status'    => false,
                    'message'   => __('message.template_not_found'),
                ];
        }else{
            return $this->createdSmsNotification($purchaseId);
        }
    }


    /**
     * Send SMS to party
     * When purchase Created
     * */
    public function createdSmsNotification($purchaseId)
    {
        $purchase = Purchase::with('party')->whereId($purchaseId)->first();
        if($purchase){
            //Get variable
            $content = $this->messageContent;

            $replacements = [
                '[Supplier Name]'      =>  $purchase->party->getFullName(),
                '[Bill Number]'         =>  $purchase->purchase_code,
                '[Purchase Date]'           =>  $purchase->formatted_purchase_date,
                '[Total Amount]'        =>  $this->formatWithPrecision($purchase->grand_total),
                '[Paid Amount]'        =>  $this->formatWithPrecision($purchase->paid_amount),
                '[Balance Amount]'        =>  $this->formatWithPrecision($purchase->grand_total - $purchase->paid_amount),
                '[Your Email Address]'  =>  app('company')['email'],
                '[Your Mobile Number]'  =>  app('company')['mobile'],
                '[Your Company Name]'  =>  app('company')['name'],
            ];

            /**
             * Call helper
             * @return string|statement
             * */
            $readyMessage = $this->keywordReplacer->replaceTemplateKeywords($content, $replacements);

            $smsData = [
                'mobile'    => $purchase->party->mobile,
                'content'   => $readyMessage,
            ];

            return [
                'status' => true,
                'message' => '',
                'data'  => $smsData,
            ];


        }else{
            return [
                    'status'    => false,
                    'message'   => __('app.something_went_wrong'),
                ];
        }
    }

    /**
     * Get SMS Template
     * */
    public function prepareSmsTemplate(){
        $template = SmsTemplate::where('name','=',$this->smsTemplateName)->first();
        if($template){
            //Set variable
            $this->messageContent = $template->content;
            return true;
        }else{
            return false;
        }
    }


}
