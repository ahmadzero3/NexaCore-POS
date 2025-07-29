<?php

namespace App\Services\Communication\Email;
use Illuminate\Http\JsonResponse;
use App\Services\EmailService;
use App\Models\Purchase\PurchaseReturn;
use App\Models\EmailTemplate;
use App\Services\GeneralDataService;
use App\Traits\FormatNumber;

class PurchaseReturnEmailNotificationService{

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
    public function purchaseReturnCreatedEmailNotification($returnId)
    {
        //set variable
        $this->emailTemplateName = 'PURCHASE RETURN';

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
        $return = PurchaseReturn::with('party')->whereId($returnId)->first();

        if($return){
            //Get variable
            $content = $this->messageContent;
            $subject = $this->emailSubject;

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
            $readySubject = $this->keywordReplacer->replaceTemplateKeywords($subject, $replacements);
            $readyMessage = $this->keywordReplacer->replaceTemplateKeywords($content, $replacements);

            $emailData = [
                'email'     => $return->party->email,
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
     * When purchase Created
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
