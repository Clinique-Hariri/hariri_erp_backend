<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorWorkingPeriod extends Model
{
  use HasFactory;

  protected $fillable = [
    'start_time',
    'end_time',
    'doctor_schedule_id',
  ];

  protected $casts = [
    'start_time' => 'datetime:H:i',
    'end_time' => 'datetime:H:i',
  ];

  public function schedule(): BelongsTo
  {
    return $this->belongsTo(DoctorSchedule::class);
  }
}
