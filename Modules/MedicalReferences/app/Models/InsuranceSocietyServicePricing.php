<?php

namespace Modules\MedicalReferences\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsuranceSocietyServicePricing extends Model
{
  protected $fillable = [
    'medical_service_price',
    'insurance_society_id',
    'medical_service_id',
  ];

  public function insuranceSociety(): BelongsTo
  {
    return $this->belongsTo(InsuranceSociety::class);
  }

  public function medicalService(): BelongsTo
  {
    return $this->belongsTo(MedicalService::class);
  }
}
