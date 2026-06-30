<?php

namespace Modules\MedicalReferences\Models;

use App\Models\User;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Patients\Models\Patient;
use Modules\Transactions\Models\Transaction;

class InsuranceSociety extends Model
{
  protected $fillable = [
    'name',
  ];

  public function patients(): HasMany
  {
    return $this->hasMany(Patient::class);
  }

  public function checkupPricings(): HasMany
  {
    return $this->hasMany(InsuranceSocietyCheckupPricing::class);
  }

  public function medicalServicePricings(): HasMany
  {
    return $this->hasMany(InsuranceSocietyServicePricing::class, 'insurance_society_id');
  }

  public function branches(): HasMany
  {
    return $this->hasMany(InsuranceSocietyBranch::class, 'insurance_society_id');
  }

  public function managers()
  {
    return $this->belongsToMany(User::class, 'insurance_society_managers');
  }

  public function transactions(): MorphMany
  {
    return $this->morphMany(Transaction::class, 'accountable');
  }
}
