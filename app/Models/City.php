<?php

namespace App\Models;

use App\Traits\HasGoogleTranslationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
  use HasFactory, HasGoogleTranslationTrait;

  protected $fillable = [
    'state_id',
    'post_code',
    'name',
    'lat',
    'lon',
  ];
  protected array $translatable = ["name"];


  public function state(): BelongsTo
  {
    return $this->belongsTo(State::class);
  }

  public function users(): HasMany
  {
    return $this->hasMany(User::class);
  }
}
