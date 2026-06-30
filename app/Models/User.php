<?php

namespace App\Models;

use App\Support\Enum\UserTypes;
use Modules\HRM\Models\Employee;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Modules\Clinic\Models\Doctor;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Modules\Transactions\Models\Transaction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements HasMedia
{
  use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */

  const string CONTRACT = 'contract';

  protected $fillable = [
    'fullname',
    'email',
    'phone',
    'avatar',
    'password',
    'type',
    'gender',
    'birthdate',
    'full_address',
    'status',
    'device_token',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'birthdate' => 'date',
    'email_verified_at' => 'datetime',
    'is_insured' => 'boolean',
    'insurance_date' => 'date',
  ];

  public function employee()
  {
    return $this->hasOne(Employee::class)->oldestOfMany();
  }

  public function doctor()
  {
    return $this->hasOne(Doctor::class)->oldestOfMany();
  }


  public function insuranceSocieties()
  {
    return $this->belongsToMany(\Modules\MedicalReferences\Models\InsuranceSociety::class, 'insurance_society_managers');
  }

  public function transactions(): MorphMany
  {
    return $this->morphMany(Transaction::class, 'accountable');
  }

  public function notifications(): MorphMany
  {
    return $this->morphMany(\App\Models\Notification::class, 'notifiable');
  }

  public function unreadNotifications()
  {
    return $this->notifications()->whereNull('read_at');
  }

  public function getAvatarUrlAttribute(): ?string
  {
    if ($this->avatar) {
      return asset('storage/' . $this->avatar);
    }
    return null;
  }

  public function scopeAdmins($query)
  {
    return $query->where('type', UserTypes::ADMIN);
  }

  public function visits()
  {
    return $this->hasMany(Visit::class);
  }

  public static function generateUniqueInsuranceNumber(): string
  {
    do {
      // Example format: 202506180001 (date + random number)
      $insuranceNumber = now()->format('Ymd') . random_int(1000, 9999);
    } while (self::where('insurance_number', $insuranceNumber)->exists());

    return $insuranceNumber;
  }


  public function registerMediaCollections(): void
  {
    $this->addMediaCollection(self::CONTRACT)
      ->singleFile()
      ->useDisk('public');
  }
}
