<?php

namespace Modules\Patients\Models;

use App\Models\User;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Clinic\Models\Doctor;

class Prescription extends Model
{
  protected $fillable = [
    'checkup_id',
    'doctor_id',
  ];

  public function checkup(): BelongsTo
  {
    return $this->belongsTo(Checkup::class);
  }

  public function doctor(): BelongsTo
  {
    return $this->belongsTo(Doctor::class);
  }

  public function medicines(): HasMany
  {
    return $this->hasMany(PrescriptionMedicine::class);
  }
}
