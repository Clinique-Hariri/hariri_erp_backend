<?php

namespace App\Models;

use App\Traits\HasGoogleTranslationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use HasFactory, HasGoogleTranslationTrait;

    protected $fillable = [
        'code',
        'name',
        'lat',
        'lon',
    ];

  protected array $translatable = ["name"];


  protected $casts = [
        'name' => 'array',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
