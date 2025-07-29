<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FormatsDateInputs;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StatusHistory extends Model
{
    use FormatsDateInputs;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'status_date',
        'note',
        'created_by',
        'updated_by',
    ];

    /**
     * Insert & update User Id's
     * */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id();
            $model->updated_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }

    /**
     * This defines the polymorphic relationship,
     * allowing you to easily access the model that this status history belongs to.
     */
    public function statusable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * This method calling the Trait FormatsDateInputs
     * @return null or string
     * Use it as formated_status_date
     * */
    public function getFormatedStatusDateAttribute()
    {
        return $this->toUserDateFormat($this->status_date); // Call the trait method
    }

    /**
     * Define the relationship between Order and User.
     *
     * @return BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
