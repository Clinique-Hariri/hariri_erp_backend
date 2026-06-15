<?php

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Actions\Constants\ActionType;
use Modules\Actions\Models\Action;
use Modules\Clinic\Models\Doctor;
use Modules\Settings\Constants\SettingsKeys;
use Modules\Settings\Models\Setting;
use Modules\Transactions\Models\Transaction;

class Hospitalization extends Model
{
  protected $fillable = [
    'hospitalization_number',
    'admission_date',
    'discharge_date',
    'stay_length',
    'room_number',
    'patient_attendant',
    'initial_price',
    'extension_fees',
    'total_price',
    'status',
    'patient_id',
    'doctor_id',
  ];

  protected $casts = [
    'admission_date' => 'datetime',
    'discharge_date' => 'datetime',
    'stay_length' => 'integer',
    'initial_price' => 'decimal:2',
    'total_price' => 'decimal:2',
    'remaining_amount' => 'decimal:2',
  ];

  public function patient(): BelongsTo
  {
    return $this->belongsTo(Patient::class);
  }

  public function doctor(): BelongsTo
  {
    return $this->belongsTo(Doctor::class);
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
      ->where('action_type', ActionType::HOSPITALIZATION_PAYMENT_ACTION)
      ->latest();
  }

  public function extensionAction(): MorphOne
  {
    return $this->morphOne(Action::class, 'actionable')
      ->where('action_type', ActionType::HOSPITALIZATION_EXTENSION_ACTION)
      ->latest();
  }

    protected static function boot()
  {
    parent::boot();

    static::creating(function ($hospitalization) {
      if (empty($hospitalization->hospitalization_number)) {
        $hospitalization->hospitalization_number = self::generateUniqueHospitalizationNumber();
      }
    });
  }

  public static function generateUniqueHospitalizationNumber(): string
  {
    do {
      // Example format: 202506180001 (date + random number)
      $hospitalization_number = now()->format('Ymd') . random_int(1000, 9999);
    } while (self::where('hospitalization_number', $hospitalization_number)->exists());

    return $hospitalization_number;
  }

  public static function calculateHospitalizationPrice($stayLength): float|int
  {
    // Define your pricing logic here
    $HOSPITALIZATION_DAILY_COST = Setting::where('key', SettingsKeys::HOSPITALIZATION_DAILY_COST)->value('value') ?? 0;
    $HOSPITALIZATION_HOURLY_COST = Setting::where('key', SettingsKeys::HOSPITALIZATION_HOURLY_COST)->value('value') ?? 0;
    $days = intdiv($stayLength, 24);
    $hours = $stayLength % 24;

    return ($days * $HOSPITALIZATION_DAILY_COST) + ($hours * $HOSPITALIZATION_HOURLY_COST);
  }

}
