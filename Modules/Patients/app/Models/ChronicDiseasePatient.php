<?php

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\MedicalReferences\Models\ChronicDisease;

class ChronicDiseasePatient extends Model
{
  protected $table = 'chronic_disease_patient';

  protected $fillable = [
    'notes',
    'patient_id',
    'chronic_disease_id',
  ];

  protected $casts = [
    'notes' => 'string',
  ];

  public function patient(): BelongsTo
  {
    return $this->belongsTo(Patient::class);
  }

  public function chronicDisease(): BelongsTo
  {
    return $this->belongsTo(ChronicDisease::class);
  }
}
