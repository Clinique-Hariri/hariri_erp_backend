<?php

namespace Modules\HRM\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HRM\Models\Contract;
use Modules\Inventory\Models\SupplyRequest;

class Department extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
  ];

  public function supplyRequests(): HasMany
  {
    return $this->hasMany(SupplyRequest::class);
  }

  public function contracts(): HasMany
  {
    return $this->hasMany(Contract::class);
  }
}
