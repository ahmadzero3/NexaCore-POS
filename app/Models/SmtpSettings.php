<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SmtpSettings extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'status',
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
        static::created(function ($smtpSettings) {
            Cache::forget('smtpSettings');
        });
        static::updated(function ($smtpSettings) {
            Cache::forget('smtpSettings');
        });
        static::deleted(function ($smtpSettings) {
            Cache::forget('smtpSettings');
        });
    }

}
