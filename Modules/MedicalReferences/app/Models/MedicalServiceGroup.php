<?php

namespace Modules\MedicalReferences\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static findOrFail($groupId)
 */
class MedicalServiceGroup extends Model
{
  protected $fillable = [
    'name',
  ];

  public function services(): HasMany
  {
    return $this->hasMany(MedicalService::class, 'group_id');
  }
}
