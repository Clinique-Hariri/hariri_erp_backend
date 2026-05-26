<?php

namespace Modules\MedicalReferences\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalService extends Model
{
  protected $fillable = [
    'name',
    'type',
    'price',
    'result_type',
    'min_normal_value',
    'max_normal_value',
    'normal_values',
    'unit',
    'group_id',
  ];

  protected $casts = [
    'price' => 'decimal:2',
    'result_type' => 'integer',
  ];

  public function group(): BelongsTo
  {
    return $this->belongsTo(MedicalServiceGroup::class, 'group_id');
  }
}
