<?php

namespace App\Services\Communication\Email;
use Illuminate\Http\JsonResponse;

use App\Services\EmailService;
use App\Models\Sale\Sale;
use App\Models\EmailTemplate;
use App\Services\GeneralDataService;
use App\Traits\FormatNumber;

class SaleEmailNotificationService{

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
    public function saleCreatedEmailNotification($saleId)
    {
        //set variable
        $this->emailTemplateName = 'SALE INVOICE';

        if(!$this->prepareEmailTemplate()){
            return [
                    'status'    => false,
                    'message'   => __('message.template_not_found'),
                ];
        }else{
            return $this->createdEmailNotification($saleId);
        }
    }

    /**
     * Return Subject & Body/Content
     * */
    public function returnSubjectAndBody($saleId)
    {
        $sale = Sale::with('party')->whereId($saleId)->first();

        if($sale){
            //Get variable
            $content = $this->messageContent;
            $subject = $this->emailSubject;

            $replacements = [
                '[Customer Name]'      =>  $sale->party->getFullName(),
                '[Invoice Number]'     =>  $sale->sale_code,
                '[Due Date]'            =>  $sale->formatted_due_date,
                '[Sale Date]'           =>  $sale->formatted_sale_date,
                '[Total Amount]'        =>  $this->formatWithPrecision($sale->grand_total),
                '[Paid Amount]'        =>  $this->formatWithPrecision($sale->paid_amount),
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
    public function createdEmailNotification($saleId)
    {
        /**
         * Get Data
         * */
        $response = $this->returnSubjectAndBody($saleId);
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
