<?php

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrescriptionMedicine extends Model
{
  protected $fillable = [
    'medicine_name',
    'dosage',
    'instructions',
    'prescription_id',
  ];

  public function prescription(): BelongsTo
  {
    return $this->belongsTo(Prescription::class);
  }
}
