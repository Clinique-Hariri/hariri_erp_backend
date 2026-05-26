<?php

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\MedicalReferences\Models\MedicalService;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CheckupAnalysisService extends Model implements HasMedia
{
  use InteractsWithMedia;
  const string RESULT_ATTACHMENT = 'result_attachment';
  protected $fillable = [
    'service_price',
    'result',
    'checkup_analysis_id',
    'medical_service_id',
  ];

  protected $casts = [
    'service_price' => 'decimal:2',
    'result' => 'array',
  ];

  public function checkupAnalysis(): BelongsTo
  {
    return $this->belongsTo(CheckupAnalysis::class);
  }

  public function medicalService(): BelongsTo
  {
    return $this->belongsTo(MedicalService::class);
  }

  public function registerMediaCollections(): void
  {
    $this->addMediaCollection(self::RESULT_ATTACHMENT)
         ->singleFile();
  }
}
