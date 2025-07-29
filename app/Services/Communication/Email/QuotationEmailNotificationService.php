<?php

namespace App\Services\Communication\Email;
use Illuminate\Http\JsonResponse;
use App\Services\EmailService;
use App\Models\Sale\Quotation;
use App\Models\EmailTemplate;
use App\Services\GeneralDataService;
use App\Traits\FormatNumber;

class QuotationEmailNotificationService{

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
    public function quotationCreatedEmailNotification($quotationId)
    {
        //set variable
        $this->emailTemplateName = 'QUOTATION';

        if(!$this->prepareEmailTemplate()){
            return [
                    'status'    => false,
                    'message'   => __('message.template_not_found'),
                ];
        }else{
            return $this->createdEmailNotification($quotationId);
        }
    }

    /**
     * Return Subject & Body/Content
     * */
    public function returnSubjectAndBody($quotationId)
    {
        $quotation = Quotation::with('party')->whereId($quotationId)->first();

        if($quotation){
            //Get variable
            $content = $this->messageContent;
            $subject = $this->emailSubject;

            $replacements = [
                '[Customer Name]'           =>  $quotation->party->getFullName(),
                '[Quotation Number]'        =>  $quotation->quotation_code,
                '[Quotation Date]'          =>  $quotation->formatted_quotation_date,
                '[Total Amount]'            =>  $this->formatWithPrecision($quotation->grand_total),
                '[Paid Amount]'             =>  $this->formatWithPrecision($quotation->paid_amount),
                '[Balance Amount]'          =>  $this->formatWithPrecision($quotation->grand_total - $quotation->paid_amount),
                '[Your Email Address]'      =>  app('company')['email'],
                '[Your Mobile Number]'      =>  app('company')['mobile'],
                '[Your Company Name]'       =>  app('company')['name'],
            ];

            /**
             * Call helper
             * @return string|statement
             * */
            $readySubject = $this->keywordReplacer->replaceTemplateKeywords($subject, $replacements);
            $readyMessage = $this->keywordReplacer->replaceTemplateKeywords($content, $replacements);

            $emailData = [
                'email'     => $quotation->party->email,
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
    public function createdEmailNotification($quotationId)
    {
        /**
         * Get Data
         * */
        $response = $this->returnSubjectAndBody($quotationId);
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
