<?php

namespace App\Services;
use Illuminate\Http\JsonResponse;
use App\Services\SmsService;
use App\Services\EmailService;
use App\Models\Order;
use App\Models\SmsTemplate;
use App\Models\EmailTemplate;
use App\Services\GeneralDataService;

class OrderNotificationService{

    private $data;
    private $smsService;
    private $emailService;
    private $smsTemplateName;
    private $messageContent;
    private $keywordReplacer;
    private $emailSubject;

    public function __construct(SmsService $smsService, EmailService $emailService, GeneralDataService $keywordReplacer)
    {
    	$this->smsService = $smsService;
        $this->emailService = $emailService;
        $this->keywordReplacer = $keywordReplacer;
    }
   
    
    public function orderCreatedSmsNotification($orderId)
    {
        //set variable
        $this->smsTemplateName = 'ORDER CREATED';
        $this->emailTemplateName = 'ORDER CREATED';

        if(!$this->prepareSmsTemplate()){
            return [
                    'status'    => false,
                    'message'   => __('message.template_not_found'),
                ];
        }else{
            return $this->createdSmsNotification($orderId);
        }
    }

    
    /**
     * Send SMS to customer
     * When Order Created
     * */
    public function createdSmsNotification($orderId)
    {
        $messageArray = [];
        $order = Order::with('customer')->whereId($orderId)->first();
        if($order){
            //Get variable
            $content = $this->messageContent;

            $replacements = [
                'customer_first_name'      =>  $order->customer->first_name,
                'customer_last_name'       =>  $order->customer->last_name,
                'order_id'                 =>  $order->order_code,
                'company_name'             =>  app('company')['name'],
                'order_date'               =>  $order->order_date,
            ];

            /**
             * Call helper
             * @return string|statement
             * */
            $readyMessage = $this->keywordReplacer->replaceTemplateKeywords($content, $replacements);

            $smsData = [
                'mobile_numbers'    => $order->customer->mobile,
                'message'   => $readyMessage,
            ];

            /**
             * @return Json Response
             * */
            $smsResponse = $this->smsService->send($smsData);

            /**
             * Get data from Response->json
             * @return array
             * */
            $responseArray = $smsResponse->original;

            if($responseArray['status'] == true){
                return [
                    'status'    => true,
                    'message'   => __('message.message_sent_successfully'),
                ];
            }else{
                return [
                    'status'    => false,
                    'message'   => $responseArray['message'],
                ];
            }
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

    /**
     * Email
     **/
    public function orderCreatedEmailNotification($orderId)
    {
        //set variable
        $this->emailTemplateName = 'ORDER CREATED';

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
     * Send Email to customer
     * When Order Created
     * */
    public function createdEmailNotification($orderId)
    {
        $messageArray = [];
        $order = Order::with('customer')->whereId($orderId)->first();
        if($order){
            /**
             * Verify is customer has email id
             * */
            if(!$order->customer->email){
                return [
                    'status'    => false,
                    'message'   => __('customer.empty_email'),
                ];
            }
            //Get variable
            $content = $this->messageContent;
            $subject = $this->emailSubject;

            $replacements = [
                'customer_first_name'      =>  $order->customer->first_name,
                'customer_last_name'       =>  $order->customer->last_name,
                'order_id'                 =>  $order->order_code,
                'company_name'             =>  app('company')['name'],
                'order_date'               =>  $order->order_date,
            ];

            /**
             * Call helper
             * @return string|statement
             * */
            $readySubject = $this->keywordReplacer->replaceTemplateKeywords($subject, $replacements);
            $readyMessage = $this->keywordReplacer->replaceTemplateKeywords($content, $replacements);

            $emailData = [
                'email'     => $order->customer->email,
                'subject'   => $readySubject,
                'content'   => $readyMessage,
            ];

            /**
             * @return Json Response
             * */
            $emailResponse = $this->emailService->send($emailData);

            /**
             * Get data from Response->json
             * @return array
             * */
            $responseArray = $emailResponse->original;

            if($responseArray['status'] == true){
                return [
                    'status'    => true,
                    'message'   => __('message.message_sent_successfully'),
                ];
            }else{
                return [
                    'status'    => false,
                    'message'   => $responseArray['message'],
                ];
            }
        }else{
            return [
                    'status'    => false,
                    'message'   => __('app.something_went_wrong'),
                ];
        }
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
