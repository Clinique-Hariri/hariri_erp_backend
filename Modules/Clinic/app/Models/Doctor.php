<?php

namespace Modules\Clinic\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Clinic\Models\Speciality;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasGoogleTranslationTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\database\factories\SpecialityFactory;
use Modules\Transactions\Models\Transaction;

class Doctor extends Model
{
  protected $fillable = [
    'user_id',
    'speciality_id',
    'checkup_price',
    'commission_percentages'
  ];

  protected $casts = [
    'checkup_price' => 'decimal:2',
    'commission_percentages' => 'array',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function speciality(): BelongsTo
  {
    return $this->belongsTo(Speciality::class);
  }

  public function schedules(): HasMany
  {
    return $this->hasMany(DoctorSchedule::class);
  }

  public function getEmployeeAttribute()
  {
    return $this->user?->employee;
  }

  public function transactions(): MorphMany
  {
    return $this->morphMany(Transaction::class, 'transactionable');
  }

  public function accountableTransactions(): MorphMany
  {
    return $this->morphMany(Transaction::class, 'accountable');
  }
}
