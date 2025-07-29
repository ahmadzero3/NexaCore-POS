<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Http\Requests\SmsRequest;
use App\Enums\App;
use App\Models\Company;
use App\Services\SmsService;

class SmsController extends Controller
{
    protected $appSettingsRecordId;
    protected $companyId;
    private $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->appSettingsRecordId = App::APP_SETTINGS_RECORD_ID->value;
        $this->companyId = App::APP_SETTINGS_RECORD_ID->value;
        $this->smsService = $smsService;
    }

    public function create()
    {
        return view('sms.create');
    }
    
    /**
     * Send SMS 
     * 
     * return @return \Illuminate\Http\JsonResponse
     * 
     * */
    public function send(SmsRequest $request) : JsonResponse  {

        $validatedData = $request->validated();

        return $this->smsService->send($validatedData);
    }
    
}
