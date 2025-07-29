<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\OrderedProduct;

class JobOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'order_id',
        'ordered_product_id',
        'user_id',
        'note',
        'status',
    ];

    /**
     * Job Order has Ordered Product
     *
     * @return BelongsTo
     */
    public function orderedProducts()
    {
        return $this->belongsTo(OrderedProduct::class, 'ordered_product_id');
    }

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
}
