<?php

namespace Modules\Inventory\Models;

use Carbon\Carbon;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'barcode',
        'category_id',
        'unit_price',
        'current_stock',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'current_stock' => 'integer',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(ItemsCategory::class);
    }

    public function supplyRequestItems(): HasMany
    {
        return $this->hasMany(SupplyRequestItem::class, 'item_id');
    }
    public function inventoryTransactionItems(): HasMany
    {
        return $this->hasMany(InventoryTransactionItem::class, 'item_id');
    }

}
