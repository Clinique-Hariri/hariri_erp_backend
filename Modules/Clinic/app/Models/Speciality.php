<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Clinic\Models\Doctor;
use App\Traits\HasGoogleTranslationTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\database\factories\SpecialityFactory;

class Speciality extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
  ];

  public function doctors(): HasMany
  {
    return $this->hasMany(Doctor::class);
  }

  public static function newFactory()
  {
    return SpecialityFactory::new();
  }
}
