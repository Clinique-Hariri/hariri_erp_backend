<?php

namespace Modules\MedicalReferences\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Clinic\Models\Doctor;
use Modules\MedicalReferences\Models\InsuranceSociety;

class InsuranceSocietyCheckupPricing extends Model
{
  protected $fillable = [
    'checkup_price',
    'insurance_society_id',
    'doctor_id',
  ];

  public function insuranceSociety(): BelongsTo
  {
    return $this->belongsTo(InsuranceSociety::class);
  }

  public function doctor(): BelongsTo
  {
    return $this->belongsTo(Doctor::class);
  }
}
