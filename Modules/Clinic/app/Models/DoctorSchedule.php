<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorSchedule extends Model
{
  use HasFactory;

  protected $fillable = [
    'day_of_week',
    'doctor_id',
  ];

  public function doctor(): BelongsTo
  {
    return $this->belongsTo(Doctor::class);
  }

  public function workingPeriods(): HasMany
  {
    return $this->hasMany(DoctorWorkingPeriod::class)->orderBy('start_time');
  }
}
