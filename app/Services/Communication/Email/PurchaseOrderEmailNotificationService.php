<?php

namespace App\Services\Communication\Email;
use Illuminate\Http\JsonResponse;
use App\Services\EmailService;
use App\Models\Purchase\PurchaseOrder;
use App\Models\EmailTemplate;
use App\Services\GeneralDataService;
use App\Traits\FormatNumber;

class PurchaseOrderEmailNotificationService{

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
    public function purchaseOrderCreatedEmailNotification($orderId)
    {
        //set variable
        $this->emailTemplateName = 'PURCHASE ORDER';

        if(!$this->prepareEmailTemplate()){
            return [
                    'status'    => false,
                    'message'   => __('message.template_not_found'),
                ];
        }else{
            return $this->createdEmailNotification($orderId);
        }
    }

    /**
     * Return Subject & Body/Content
     * */
    public function returnSubjectAndBody($orderId)
    {
        $purchase = PurchaseOrder::with('party')->whereId($orderId)->first();

        if($purchase){
            //Get variable
            $content = $this->messageContent;
            $subject = $this->emailSubject;

            $replacements = [
                '[Supplier Name]'      =>  $purchase->party->getFullName(),
                '[Order Number]'     =>  $purchase->order_code,
                '[Due Date]'            =>  $purchase->formatted_due_date,
                '[Order Date]'           =>  $purchase->formatted_order_date,
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
            $readySubject = $this->keywordReplacer->replaceTemplateKeywords($subject, $replacements);
            $readyMessage = $this->keywordReplacer->replaceTemplateKeywords($content, $replacements);

            $emailData = [
                'email'     => $purchase->party->email,
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
    public function createdEmailNotification($orderId)
    {
        /**
         * Get Data
         * */
        $response = $this->returnSubjectAndBody($orderId);
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
