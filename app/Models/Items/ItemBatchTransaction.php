<?php

namespace App\Models\Items;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\Items\ItemBatchMaster;
use App\Models\Items\ItemTransaction;
use App\Models\Items\Item;
use App\Models\Warehouse;

class ItemBatchTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_transaction_id',
        'item_batch_master_id',
        'warehouse_id',
        'item_id',
        'quantity',
        'unique_code',
    ];

    public function itemBatchMaster():BelongsTo
    {
        return $this->belongsTo(ItemBatchMaster::class, 'item_batch_master_id');
    }
    
    public function item():BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function itemTransaction():BelongsTo
    {
        return $this->belongsTo(ItemTransaction::class, 'item_transaction_id');
    }

    /**
     * ItemTransaction item has item id
     * 
     * @return BelongsTo
     * */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

}
