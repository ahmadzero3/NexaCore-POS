<?php

namespace App\Services\Communication\Email;
use Illuminate\Http\JsonResponse;
use App\Services\EmailService;
use App\Models\Sale\SaleReturn;
use App\Models\EmailTemplate;
use App\Services\GeneralDataService;
use App\Traits\FormatNumber;

class SaleReturnEmailNotificationService{

    use FormatNumber;

    private $data;
    private $emailService;
    private $messageContent;
    private $keywordReplacer;
    private $emailSubject;
    private $emailTemplateName;

    public function __construct(EmailService $emailService, GeneralDataService $keywordReplacer)
    {
        $this->emailService = $emailService;
        $this->keywordReplacer = $keywordReplacer;
    }


    /**
     * Email
     **/
    public function saleReturnCreatedEmailNotification($returnId)
    {
        //set variable
        $this->emailTemplateName = 'SALE RETURN';

        if(!$this->prepareEmailTemplate()){
            return [
                    'status'    => false,
                    'message'   => __('message.template_not_found'),
                ];
        }else{
            return $this->createdEmailNotification($returnId);
        }
    }

    /**
     * Return Subject & Body/Content
     * */
    public function returnSubjectAndBody($returnId)
    {
        $sale = SaleReturn::with('party')->whereId($returnId)->first();

        if($sale){
            //Get variable
            $content = $this->messageContent;
            $subject = $this->emailSubject;

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
            $readySubject = $this->keywordReplacer->replaceTemplateKeywords($subject, $replacements);
            $readyMessage = $this->keywordReplacer->replaceTemplateKeywords($content, $replacements);

            $emailData = [
                'email'     => $sale->party->email,
                'subject'   => $readySubject,
                'content'   => $readyMessage,
            ];

            return [
                'status' => true,
                'message' => '',
                'data'  => $emailData,
            ];
        }else{
            return [
                    'status'    => false,
                    'message'   => __('app.something_went_wrong'),
                ];
        }
    }
    /**
     * Send Email to party
     * When sale Created
     * */
    public function createdEmailNotification($returnId)
    {
        /**
         * Get Data
         * */
        $response = $this->returnSubjectAndBody($returnId);
        if(!$response['status']){
          throw new \Exception($response['message']);
        }

        return $response;
    }

    /**
     * Get Email Template
     * */
    public function prepareEmailTemplate(){
        $template = EmailTemplate::where('name','=',$this->emailTemplateName)->first();
        if($template){
            //Set variable
            $this->emailSubject = $template->subject;
            $this->messageContent = $template->content;
            return true;
        }else{
            return false;
        }
    }

}
