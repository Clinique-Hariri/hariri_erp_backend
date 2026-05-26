<?php

namespace Modules\HRM\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CareerChange extends Model implements HasMedia
{
  use HasFactory, InteractsWithMedia;

  const string FILE = 'file';

  protected $fillable = [
    'employee_id',
    'old_contract_id',
    'new_contract_id',
    'type',
    'notes',
  ];

  protected $casts = [
    'type' => 'string',
    'notes' => 'string',
  ];

  // Relationships
  public function employee(): BelongsTo
  {
    return $this->belongsTo(Employee::class);
  }

  public function oldContract(): BelongsTo
  {
    return $this->belongsTo(Contract::class, 'old_contract_id');
  }

  public function newContract(): BelongsTo
  {
    return $this->belongsTo(Contract::class, 'new_contract_id');
  }

  public function registerMediaCollections(): void
  {
    $this->addMediaCollection(self::FILE)
      ->singleFile()
      ->useDisk('public');
  }
}
