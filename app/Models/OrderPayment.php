<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PaymentTypes;
use App\Models\Order;

class OrderPayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_date',
        'order_id',
        'payment_type_id',
        'amount',
        'note',
        'user_id',
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
     * Get Payment Type
     * @return HasOne
     * */
    public function paymentType(): HasOne
    {
        return $this->hasOne(PaymentTypes::class, 'id', 'payment_type_id');
    }

    /**
     * Get Payment Type
     * @return BelongsTo
     * */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

}
