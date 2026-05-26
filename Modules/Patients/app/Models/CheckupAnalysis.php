<?php

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\Actions\Constants\ActionType;
use Modules\Clinic\Models\Doctor;
use Modules\Actions\Models\Action;
use Modules\Transactions\Models\Transaction;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CheckupAnalysis extends Model implements HasMedia
{
  use InteractsWithMedia;
  const string RESULT_ATTACHMENT = 'result_attachment';

  protected $fillable = [
    'checkup_analysis_number',
    'type',
    'coverage_amount',
    'original_price',
    'total_price',
    'notes',
    'orientation',
    'doctor_interpretation',
    'status',
    'checkup_id',
  ];

  protected $casts = [
    'coverage_amount' => 'decimal:2',
    'total_price' => 'decimal:2',
  ];

  public function checkup(): BelongsTo
  {
    return $this->belongsTo(Checkup::class);
  }

public function patient(): HasOneThrough
{
    return $this->hasOneThrough(
        Patient::class,
        Checkup::class, 
        'id',            
        'id',            
        'checkup_id',    
        'patient_id'     
    );
}

public function doctor(): HasOneThrough
{
    return $this->hasOneThrough(
        Doctor::class,
        Checkup::class, 
        'id',           
        'id',          
        'checkup_id',  
        'doctor_id'     
    );
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
      ->where('action_type', ActionType::ANALYSIS_PAYMENT_ACTION)
      ->latest();
  }

  public function resultAction(): MorphOne
  {
    return $this->morphOne(Action::class, 'actionable')
      ->where('action_type', ActionType::ANALYSIS_RESULT_ACTION)
      ->latest();
  }

  public function services(): HasMany
  {
    return $this->hasMany(CheckupAnalysisService::class);
  }

  public function registerMediaCollections(): void
  {
    $this->addMediaCollection(self::RESULT_ATTACHMENT)
      ->singleFile();
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($checkupAnalysis) {
      if (empty($checkupAnalysis->checkup_analysis_number)) {
        $checkupAnalysis->checkup_analysis_number = self::generateUniqueCheckupAnalysisNumber();
      }
    });
  }

  public static function generateUniqueCheckupAnalysisNumber(): string
  {
    do {
      // Example format: 202506180001 (date + random number)
      $checkupAnalysisNumber = now()->format('Ymd') . random_int(1000, 9999);
    } while (self::where('checkup_analysis_number', $checkupAnalysisNumber)->exists());

    return $checkupAnalysisNumber;
  }
}
