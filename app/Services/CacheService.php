<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public static function get($type)
    {
        if(env('INSTALLATION_STATUS')){
            switch ($type) {
                case 'tax':
                    return Cache::remember('tax', now()->addHours(24), function () {
                        return \App\Models\Tax::all();
                    });
                case 'unit':
                    return Cache::remember('unit', now()->addHours(24), function () {
                        return \App\Models\Unit::all();
                    });
                case 'appSetting':
                    return Cache::remember('appSetting', now()->addHours(24), function () {
                        return \App\Models\AppSettings::first();
                    });
                case 'company':
                    return Cache::remember('company', now()->addHours(24), function () {
                        return \App\Models\Company::first();
                    });
                case 'smtpSettings':
                    return Cache::remember('smtpSettings', now()->addHours(24), function () {
                        return \App\Models\SmtpSettings::first() ?? (object) [
                            'host'       => null,
                            'port'       => null,
                            'username'   => null,
                            'password'   => null,
                            'encryption' => null,
                        ];
                    });
                default:
                    throw new \Exception("Invalid cache type: $type");
            }
        }
    }
}
