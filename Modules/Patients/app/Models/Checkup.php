<?php

namespace Modules\Patients\Models;

use App\Models\User;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Actions\Constants\ActionType;
use Modules\Actions\Models\Action;
use Modules\Clinic\Models\Doctor;
use Modules\Patients\Constants\CheckupAnalysisStatus;
use Modules\Transactions\Constants\Status;
use Modules\Transactions\Models\Transaction;

class Checkup extends Model
{
  protected $fillable = [
    'checkup_number',
    'initial_price',
    'total_price',
    'original_price',
    'coverage_amount',
    'date',
    'time',
    'status',
    'doctor_id',
    'patient_id',
    'reason',
    'weight',
    'height',
    'temperature',
    'systolic_pressure',
    'diastolic_pressure',
    'SPO2',
    'FC',
    'symptoms',
    'type'
  ];

  protected $casts = [
    'initial_price' => 'decimal:2',
    'total_price' => 'decimal:2',
    'coverage_amount' => 'decimal:2',
    'date' => 'date',
    'time' => 'datetime',
    'status' => 'string',
  ];

  public function doctor(): BelongsTo
  {
    return $this->belongsTo(Doctor::class);
  }

  public function ticket(): HasOne
  {
      return $this->hasOne(CheckupTicket::class, 'checkup_id');
  }

  public function patient(): BelongsTo
  {
    return $this->belongsTo(Patient::class);
  }

  public function checkupAnalyses(): HasMany
  {
    return $this->hasMany(CheckupAnalysis::class);
  }

  public function unpaidCheckupAnalyses(): HasMany
  {
    return $this->hasMany(CheckupAnalysis::class)->where('status', CheckupAnalysisStatus::DRAFT);
  }

  public function paidCheckupAnalyses(): HasMany
  {
    return $this->hasMany(CheckupAnalysis::class)->whereNot('status', CheckupAnalysisStatus::DRAFT);
  }

  public function prescriptions(): HasMany
  {
    return $this->hasMany(Prescription::class);
  }
  public function transactions(): MorphMany
  {
    return $this->morphMany(Transaction::class, 'transactionable');
  }

  public function actions(): MorphMany
  {
    return $this->morphMany(Action::class, 'actionable');
  }

  public function paymentAction(): MorphOne
  {
    return $this->morphOne(Action::class, 'actionable')
      ->where('action_type', ActionType::CHECKUP_PAYMENT_ACTION)
      ->latest();
  }

protected static function boot()
{
    parent::boot();

    static::creating(function ($checkup) {
        if (empty($checkup->checkup_number)) {
            $checkup->checkup_number = self::generateUniqueCheckupNumber();
        }
    });
}

public static function generateUniqueCheckupNumber(): string
{
    do {
        // Example format: 202506180001 (date + random number)
        $checkupNumber = now()->format('Ymd') . random_int(1000, 9999);
    } while (self::where('checkup_number', $checkupNumber)->exists());

    return $checkupNumber;
}

}
