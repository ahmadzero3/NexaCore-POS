<?php

namespace App\Models\Party;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\User;
use App\Models\Party\PartyTransaction;
use App\Services\PartyService;

class Party extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'party_type',
        'mobile',
        'phone',
        'whatsapp',
        'tax_number',
        'state_id',
        'shipping_address',
        'billing_address',
        'is_set_credit_limit',
        'credit_limit',
        'to_pay',
        'to_receive',
        'status',
        'is_wholesale_customer',
        'default_party',
        'currency_id',
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
     * Define the relationship between Party and User.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Define the relationship between Item Transaction & Items table.
     * Used to save Opening Balance and other payments
     * @return MorphMany
     */
    public function transaction(): MorphMany
    {
        return $this->morphMany(PartyTransaction::class, 'transaction');
    }

    public function getFullName()
    {
        return $this->first_name." ".$this->last_name;
    }

    public function getPartyTotalDueBalance()
    {
        $partyBalance = new PartyService();
        return $partyBalance->getPartyBalance($this->id);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
