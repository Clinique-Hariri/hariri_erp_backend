<?php

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Clinic\Models\Doctor;
use Modules\HRM\Models\Employee;
use Modules\Transactions\Models\Transaction;

class HomeCheckup extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'home_checkup_number',
    'mobile_request_id',
    'patient_fullname',
    'patient_phone',
    'patient_birthdate',
    'requested_patient_number',
    'patient_id',
    'requested_visit_type',
    'visit_type',
    'doctor_id',
    'checkup_id',
    'address',
    'latitude',
    'longitude',
    'preferred_date',
    'preferred_time',
    'scheduled_at',
    'status',
    'base_price',
    'services_total',
    'total_price',
    'coverage_amount',
    'notes',
  ];

  protected $casts = [
    'patient_birthdate' => 'date',
    'preferred_date' => 'date',
    'preferred_time' => 'datetime',
    'scheduled_at' => 'datetime',
    'latitude' => 'decimal:8',
    'longitude' => 'decimal:8',
    'base_price' => 'decimal:2',
    'services_total' => 'decimal:2',
    'total_price' => 'decimal:2',
    'coverage_amount' => 'decimal:2',
  ];

  public function patient(): BelongsTo
  {
    return $this->belongsTo(Patient::class);
  }

  public function doctor(): BelongsTo
  {
    return $this->belongsTo(Doctor::class);
  }

  public function checkup(): BelongsTo
  {
    return $this->belongsTo(Checkup::class);
  }

  public function services(): HasMany
  {
    return $this->hasMany(HomeCheckupService::class);
  }

  public function nurses(): BelongsToMany
  {
    return $this->belongsToMany(Employee::class, 'home_checkup_nurses', 'home_checkup_id', 'nurse_id')
      ->withTimestamps();
  }

  public function transactions(): MorphMany
  {
    return $this->morphMany(Transaction::class, 'accountable');
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($homeCheckup) {
      if (empty($homeCheckup->home_checkup_number)) {
        $homeCheckup->home_checkup_number = self::generateUniqueHomeCheckupNumber();
      }
    });
  }

  public static function generateUniqueHomeCheckupNumber(): string
  {
    do {
      $homeCheckupNumber = now()->format('Ymd') . random_int(1000, 9999);
    } while (self::where('home_checkup_number', $homeCheckupNumber)->exists());

    return $homeCheckupNumber;
  }
}
