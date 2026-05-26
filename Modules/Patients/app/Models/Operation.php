<?php

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\Actions\Constants\ActionType;
use Modules\Actions\Models\Action;
use Modules\Clinic\Models\Doctor;
use Modules\Patients\Constants\OperationStatus;
use Modules\Patients\Models\Patient;
use Modules\Patients\Models\Surgeon;
use Modules\Transactions\Models\Transaction;

class Operation extends Model
{
  protected $fillable = [
    'operation_number',
    'operation_date',
    'price',
    'description',
    'status',
    'patient_id',
  ];

  protected $casts = [
    'operation_date' => 'datetime',
    'price' => 'decimal:2',
  ];

  public function patient()
  {
    return $this->belongsTo(Patient::class);
  }

  public function surgeons()
  {
    return $this->hasMany(Surgeon::class);
  }

  public function doctors(): HasManyThrough
  {
    return $this->hasManyThrough(Doctor::class, Surgeon::class, 'operation_id', 'id', 'id', 'doctor_id');
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
      ->where('action_type', ActionType::OPERATION_PAYMENT_ACTION)
      ->latest();
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($operation) {
      if (empty($operation->operation_number)) {
        $operation->operation_number = self::generateUniqueOperationNumber();
      }
    });
  }

  public static function generateUniqueOperationNumber(): string
  {
    do {
      // Example format: 202506180001 (date + random number)
      $operation_number = now()->format('Ymd') . random_int(1000, 9999);
    } while (self::where('operation_number', $operation_number)->exists());

    return $operation_number;
  }

}
