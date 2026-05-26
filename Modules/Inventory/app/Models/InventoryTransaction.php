<?php

namespace Modules\Inventory\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HRM\Models\Department;
use Modules\Supplier\Models\Supplier;

class InventoryTransaction extends Model {
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'transaction_number',
        'employee_name',
        'total_amount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];
    // Relationships
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inventoryTransactionItems(): HasMany
    {
        return $this->hasMany(InventoryTransactionItem::class, 'transaction_id');
    }

    // Accessors
    public function getTotalItemsAttribute(): int
    {
        return $this->inventoryTransactionItems->sum('quantity');
    }

}
