<?php

namespace Modules\Patients\Models;

use App\Support\Enum\UserRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Modules\MedicalReferences\Models\InsuranceSocietyBranch;
use Modules\Transactions\Models\Transaction;

class Patient extends Model
{
  use Notifiable;

  protected $fillable = [
    'patient_number',
    'fullname',
    'insured_name',
    'gender',
    'blood_type',
    'birthdate',
    'age',
    'birth_place',
    'full_address',
    'avatar',
    'passport_number',
    'phone',
    'whatsapp_number',
    'email',
    'insurance_number',
    'status',
    'external_patient_id',
    'insurance_society_branch_id',
  ];

  protected $casts = [
    'birthdate' => 'date',
  ];

  public function insuranceSocietyBranch(): BelongsTo
  {
    return $this->belongsTo(InsuranceSocietyBranch::class, 'insurance_society_branch_id');
  }

  public function checkups(): HasMany
  {
    return $this->hasMany(Checkup::class);
  }

  public function chronicDiseases(): HasMany
  {
    return $this->hasMany(ChronicDiseasePatient::class);
  }

  public function Hospitalizations(): HasMany
  {
    return $this->hasMany(Hospitalization::class);
  }

  public function accountableTransactions(): MorphMany
  {
    return $this->morphMany(Transaction::class, 'accountable');
  }

  public function scopeFilterByInsuranceSociety($query)
  {
    $user = Auth::user();

    if ($user && $user->hasRole(UserRoles::INSURANCE_SOCIETY_MANAGER)) {
      return $query->whereHas('insuranceSocietyBranch', function ($q) use ($user) {
        $q->whereIn('insurance_society_id', $user->insuranceSocieties()->pluck('insurance_societies.id')->toArray());
      });
    }

    return $query;
  }

  public function getAvatarUrlAttribute(): ?string
  {
    if ($this->avatar) {
      return asset('storage/' . $this->avatar);
    }
    return null;
  }

  public function routeNotificationForMail(): ?string
  {
    return filled($this->email) ? trim($this->email) : null;
  }

  public function routeNotificationForWhatsApp(): ?string
  {
    $number = $this->whatsapp_number;

    return filled($number) ? trim($number) : null;
  }

  public function routeNotificationForSms(): ?string
  {
    $number = filled($this->phone) ? $this->phone : $this->whatsapp_number;

    return filled($number) ? trim($number) : null;
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($patient) {
      if (empty($patient->patient_number)) {
        $patient->patient_number = self::generateUniquePatientNumber();
      }
    });
  }

  public static function generateUniquePatientNumber(): string
  {
    do {
      // Example format: 202506180001 (date + random number)
      $patientNumber = now()->format('Ymd') . random_int(1000, 9999);
    } while (self::where('patient_number', $patientNumber)->exists());

    return $patientNumber;
  }
}

