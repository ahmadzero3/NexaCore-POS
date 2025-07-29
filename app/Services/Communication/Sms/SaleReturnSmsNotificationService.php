<?php

namespace App\Services\Communication\Sms;
use Illuminate\Http\JsonResponse;
use App\Services\SmsService;
use App\Models\Sale\SaleReturn;
use App\Models\SmsTemplate;
use App\Services\GeneralDataService;
use App\Traits\FormatNumber;

class SaleReturnSmsNotificationService{

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
    
    public function saleReturnCreatedSmsNotification($returnId)
    {
        //set variable
        $this->smsTemplateName = 'SALE RETURN';
     
        if(!$this->prepareSmsTemplate()){
            return [
                    'status'    => false,
                    'message'   => __('message.template_not_found'),
                ];
        }else{
            return $this->createdSmsNotification($returnId);
        }
    }

    
    /**
     * Send SMS to party
     * When sale Created
     * */
    public function createdSmsNotification($returnId)
    {
        $sale = SaleReturn::with('party')->whereId($returnId)->first();
        if($sale){
            //Get variable
            $content = $this->messageContent;

            $replacements = [
                '[Customer Name]'      =>  $sale->party->getFullName(),
                '[Return Number]'     =>  $sale->return_code,
                '[Return Date]'           =>  $sale->formatted_return_date,
                '[Total Amount]'        =>  $this->formatWithPrecision($sale->grand_total),
                '[Return Amount]'        =>  $this->formatWithPrecision($sale->paid_amount),
                '[Balance Amount]'        =>  $this->formatWithPrecision($sale->grand_total - $sale->paid_amount),
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
                'mobile'    => $sale->party->mobile,
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
