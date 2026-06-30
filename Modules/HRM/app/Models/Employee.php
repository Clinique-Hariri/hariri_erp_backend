<?php

namespace Modules\HRM\Models;

use Carbon\Carbon;
use App\Models\User;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HRM\Constants\ContractStatus;
use Modules\Transactions\Models\Transaction;

class Employee extends Model implements HasMedia
{
  use HasFactory, InteractsWithMedia;

  const string IMAGE = 'image';

  protected $fillable = [
    'user_id',
    'employee_code',
    'fullname',
    'phone',
    'email',
    'gender',
    'address',
    'birth_date',
    'hire_date',
  ];

  protected $casts = [
    'fullname' => 'string',
    'address' => 'string',
    'birth_date' => 'date',
    'hire_date' => 'date',
  ];

  // Relationships
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function getDoctorAttribute()
  {
    return $this->user?->doctor;
  }

  public function contracts(): HasMany
  {
    return $this->hasMany(Contract::class);
  }
  public function latestContract()
  {
    return $this->hasOne(Contract::class)->latestOfMany();
  }
  public function contract($date = null)
  {
    $date = $date ?? now();
    return $this->contracts()
      ->whereDate('start_date', '<=', $date)
      ->whereDate('end_date', '>=', $date)
      ->orderBy('created_at', 'ASC');
  }

  public function getContractAttribute()
  {
    if ($this->relationLoaded('contracts')) {
      $now = now();
      return $this->contracts->first(function ($contract) use ($now) {
        return $contract->start_date <= $now && $contract->end_date >= $now;
      });
    }
    return $this->contract()->first();
  }

  public function hasContractAt($date = null)
  {
    return $this->contract($date)->exists();
  }

  public function scopeWhereHasContractAt($query, $date = null)
  {
    $date = $date ?? now();

    return $query->whereHas('contracts', function ($q) use ($date) {
      $q->whereDate('start_date', '<=', $date)
        ->whereDate('end_date', '>=', $date);
    });
  }

  public function employeeBonuses(): HasMany
  {
    return $this->hasMany(EmployeeBonus::class);
  }

  public function bonuses()
  {
    return $this->hasManyThrough(
      Bonus::class,
      EmployeeBonus::class,
      'employee_id',
      'id',
      'id',
      'bonus_id'
    );
  }

  public function careerChanges(): HasMany
  {
    return $this->hasMany(CareerChange::class);
  }

  public function attendances(): HasMany
  {
    return $this->hasMany(Attendance::class);
  }

  public function attendance($date = null)
  {

    $date = $date ?? now();
    return $this->attendances()
      ->whereDate('date', $date)
      ->orderBy('created_at', 'ASC');
  }

  public function getAttendanceAttribute()
  {
    return $this->attendance()->first();
  }

  public function activeShift()
  {
    return $this->attendances()
      ->whereNull('check_out_time')
      ->whereNotNull('check_in_time')
      ->orderBy('date', 'ASC');
  }

  public function getActiveShiftAttribute()
  {
    return $this->activeShift()->first();
  }

  public function getHasActiveShiftAttribute()
  {
    return $this->activeShift()->exists();
  }

  public function loans(): HasMany
  {
    return $this->hasMany(Loan::class);
  }

  public function salaries(): HasMany
  {
    return $this->hasMany(Salary::class);
  }

  public function transactions(): MorphMany
  {
    return $this->morphMany(Transaction::class, 'accountable');
  }

  public function getWorkMonthsAttribute()
  {
    return (int) Carbon::parse($this->hire_date)->diffInMonths(now());
  }

  public function getLoansCountAttribute()
  {
    return $this->loans()->count();
  }

  public function getContractStatusAttribute()
  {

    if ($this->relationLoaded('contracts')) {
      $contractsCount = $this->contracts->count();

      if ($contractsCount == 0) {
        return ContractStatus::NONE;
      }

      $hasActiveContract = $this->contracts->some(function ($contract) {
        $now = now();
        return $contract->start_date <= $now && $contract->end_date >= $now;
      });

      return $hasActiveContract ? ContractStatus::ACTIVE : ContractStatus::INACTIVE;
    }


    if ($this->contracts()->count() == 0) {
      return ContractStatus::NONE;
    }

    if ($this->hasContractAt(now())) {
      return ContractStatus::ACTIVE;
    } else {
      return ContractStatus::INACTIVE;
    }
  }

  public function scopeFilterByContractStatus($query, $status)
  {
    switch ($status) {
      case ContractStatus::NONE:
        return $query->whereDoesntHave('contracts');

      case ContractStatus::ACTIVE:
        return $query->whereHas('contracts', function ($q) {
          $q->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now());
        });

      case ContractStatus::INACTIVE:
        return $query->whereHas('contracts')
          ->whereDoesntHave('contracts', function ($q) {
            $q->whereDate('start_date', '<=', now())
              ->whereDate('end_date', '>=', now());
          });

      default:
        return $query;
    }
  }
  public function registerMediaCollections(): void
  {
    $this->addMediaCollection(self::IMAGE)
      ->singleFile()
      ->useDisk('public');
  }
}
