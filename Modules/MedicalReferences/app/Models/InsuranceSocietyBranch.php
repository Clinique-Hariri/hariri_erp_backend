<?php

namespace Modules\MedicalReferences\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsuranceSocietyBranch extends Model
{
  protected $fillable = [
    'name',
    'coverage_percentage',
    'insurance_society_id',
  ];

  protected $casts = [
    'coverage_percentage' => 'float',
  ];

  public function insuranceSociety(): BelongsTo
  {
    return $this->belongsTo(InsuranceSociety::class);
  }
}
