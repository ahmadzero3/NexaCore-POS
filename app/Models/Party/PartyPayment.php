<?php

namespace App\Models\Party;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FormatsDateInputs;
use App\Models\PaymentTypes;
use App\Models\User;
use App\Models\Party\Party;
use App\Models\Party\PartyPaymentAllocation;
use App\Models\PaymentTransaction;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PartyPayment extends Model
{
    use HasFactory;

    use FormatsDateInputs;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_date',
        'party_id',
        'payment_type_id',
        'payment_direction',
        'amount',
        'reference_no',
        'note',
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
     * Define the relationship between Order and Party.
     *
     * @return BelongsTo
     */
    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'party_id');
    }

    /**
     * Define the relationship between Order and User.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * This method calling the Trait FormatsDateInputs
     * @return null or string
     * Use it as formatted_transaction_date
     * */
    public function getFormattedTransactionDateAttribute()
    {
        return $this->toUserDateFormat($this->transaction_date); // Call the trait method
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
     * @return HasMany
     * */
    public function partyPaymentAllocation(): HasMany
    {
        return $this->hasMany(PartyPaymentAllocation::class);
    }

    /**
     * Define the relationship between Expense Payment Transaction & Expense table.
     *
     * @return MorphMany
     */
    public function paymentTransaction(): MorphMany
    {
        return $this->morphMany(PaymentTransaction::class, 'transaction');
    }

}
