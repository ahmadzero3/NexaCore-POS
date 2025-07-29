<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\Items\ItemTransaction;
use App\Models\Items\ItemStockTransfer;
use App\Traits\FormatsDateInputs;
use App\Traits\FormatTime;
use App\Models\User;

class StockTransfer extends Model
{
    use HasFactory;

    use FormatsDateInputs;

    use FormatTime;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transfer_date',
        'prefix_code',
        'count_id',
        'transfer_code',
        'unit_price',
        'total',
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
     * Use it as formatted_transfer_date
     * */
    public function getFormattedTransferDateAttribute()
    {
        return $this->toUserDateFormat($this->transfer_date); // Call the trait method
    }

    /**
     * This method calling the Trait FormatTime
     * @return null or string
     * Use it as format_created_time
     * */
    public function getFormatCreatedTimeAttribute()
    {
        return $this->toUserTimeFormat($this->created_at); // Call the trait method
    }

    /**
     * Define the relationship between Item Transaction & Sale Ordeer table.
     *
     * @return MorphMany
     */
    public function itemTransaction(): MorphMany
    {
        return $this->morphMany(ItemTransaction::class, 'transaction');
    }

    /**
     * Define the relationship between Item Transaction & Sale Ordeer table.
     *
     * @return MorphMany
     */
    public function itemStockTransfer(): HasMany
    {
        return $this->hasMany(ItemStockTransfer::class);
    }

    public function getTableCode(){
        return $this->id;
    }
}
