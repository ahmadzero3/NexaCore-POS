<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Twilio extends Model
{
    use HasFactory;

    protected $table = 'twilio';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sid',
        'auth_token',
        'twilio_number',
    ];
}
