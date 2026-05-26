<?php

namespace Modules\Inventory\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplyRequestItem extends Model {
    use HasFactory;

    protected $fillable = [
        'supply_request_id',
        'item_id',
        'requested_quantity',
    ];

    protected $casts = [
        'requested_quantity' => 'integer',
    ];

    // Relationships
    public function supplyRequest(): BelongsTo
    {
        return $this->belongsTo(SupplyRequest::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

}
