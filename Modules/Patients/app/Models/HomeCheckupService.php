<?php

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeCheckupService extends Model
{
  protected $fillable = [
    'service_name',
    'service_price',
    'home_checkup_id',
  ];

  protected $casts = [
    'service_price' => 'decimal:2',
  ];

  public function homeCheckup(): BelongsTo
  {
    return $this->belongsTo(HomeCheckup::class);
  }
}
