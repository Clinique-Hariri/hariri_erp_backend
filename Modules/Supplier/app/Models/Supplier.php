<?php

namespace Modules\Supplier\Models;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Inventory\Models\InventoryTransaction;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Supplier extends Model implements HasMedia {
    use HasFactory, InteractsWithMedia;

    const string IMAGE = 'image';
    protected $fillable = [
        'name',
        'phone',
    ];

    // Relationships
    public function inventoryTransactions(): HasMany {
        return $this->hasMany(InventoryTransaction::class);
    }
}
