<?php

namespace App\Services\Communication\Sms;
use Illuminate\Http\JsonResponse;
use App\Services\SmsService;
use App\Models\Sale\Quotation;
use App\Models\SmsTemplate;
use App\Services\GeneralDataService;
use App\Traits\FormatNumber;

class QuotationSmsNotificationService{

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
   public function quotationCreatedSmsNotification($quotationId)
    {
        //set variable
        $this->smsTemplateName = 'QUOTATION';

        if(!$this->prepareSmsTemplate()){
            return [
                    'status'    => false,
                    'message'   => __('message.template_not_found'),
                ];
        }else{
            return $this->createdSmsNotification($quotationId);
        }
    }


    /**
     * Send SMS to party
     * When sale Created
     * */
    public function createdSmsNotification($quotationId)
    {
        $quotation = Quotation::with('party')->whereId($quotationId)->first();
        if($quotation){
            //Get variable
            $content = $this->messageContent;

            $replacements = [
                '[Customer Name]'      =>  $quotation->party->getFullName(),
                '[Quotation Number]'     =>  $quotation->quotation_code,
                '[Quotation Date]'           =>  $quotation->formatted_quotation_date,
                '[Total Amount]'        =>  $this->formatWithPrecision($quotation->grand_total),
                '[Paid Amount]'        =>  $this->formatWithPrecision($quotation->paid_amount),
                '[Balance Amount]'        =>  $this->formatWithPrecision($quotation->grand_total - $quotation->paid_amount),
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
                'mobile'    => $quotation->party->mobile,
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
