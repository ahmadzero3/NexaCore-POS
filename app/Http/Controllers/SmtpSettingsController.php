<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SmtpSettings;
use App\Http\Requests\SmtpSettingsRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Enums\App;

class SmtpSettingsController extends Controller
{
    protected $smtpSettingsRecordId;

    public function __construct() {
        $this->smtpSettingsRecordId = App::APP_SETTINGS_RECORD_ID->value;
    }

    public function store(SmtpSettingsRequest $request) : JsonResponse{
        $validatedData = $request->validated();

        // Save the application settings
        $settings = SmtpSettings::findOrNew($this->smtpSettingsRecordId);
        $settings->host = $validatedData['host'];
        $settings->port = $validatedData['port'];
        $settings->username = $validatedData['username'];
        $settings->password = $validatedData['password'];
        $settings->encryption = $validatedData['encryption'];
        $settings->status = $validatedData['smtp_status'];
        $settings->save();

        return response()->json([
            'message' => __('app.record_saved_successfully'),
        ]);
    }
}
