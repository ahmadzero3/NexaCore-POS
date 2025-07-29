<?php

namespace App\Services\Communication\Sms;
use Illuminate\Http\JsonResponse;
use App\Services\SmsService;
use App\Models\Purchase\PurchaseReturn;
use App\Models\SmsTemplate;
use App\Services\GeneralDataService;
use App\Traits\FormatNumber;

class PurchaseReturnSmsNotificationService{

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
    
    public function purchaseReturnCreatedSmsNotification($returnId)
    {
        //set variable
        $this->smsTemplateName = 'PURCHASE RETURN';
     
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
     * When purchase Created
     * */
    public function createdSmsNotification($returnId)
    {
        $return = PurchaseReturn::with('party')->whereId($returnId)->first();
        if($return){
            //Get variable
            $content = $this->messageContent;

            $replacements = [
                '[Supplier Name]'      =>  $return->party->getFullName(),
                '[Return Number]'     =>  $return->return_code,
                '[Return Date]'           =>  $return->formatted_return_date,
                '[Total Amount]'        =>  $this->formatWithPrecision($return->grand_total),
                '[Return Amount]'        =>  $this->formatWithPrecision($return->paid_amount),
                '[Balance Amount]'        =>  $this->formatWithPrecision($return->grand_total - $return->paid_amount),
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
                'mobile'    => $return->party->mobile,
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
