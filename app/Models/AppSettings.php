<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
//use Stancl\Tenancy\Database\Concerns\CentralConnection;

class AppSettings extends Model
{
    use HasFactory;
    //use CentralConnection;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'application_name',
        'footer_text',
        'colored_logo',
        'light_logo',
        'active_sms_api',
        'language_id',
    ];

    /**
     * Insert & update User Id's
     * */
    protected static function boot()
    {
        parent::boot();
        /**
         * created
         * updated
         * cache created in App\Services\CacheService.php
         * */
        static::created(function ($appSetting) {
            Cache::forget('appSetting');
        });
        static::updated(function ($appSetting) {
            Cache::forget('appSetting');
        });
        static::deleted(function ($appSetting) {
            Cache::forget('appSetting');
        });
    }
}
