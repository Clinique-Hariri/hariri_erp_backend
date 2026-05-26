<?php

namespace Modules\MedicalReferences\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChronicDisease extends Model
{
  protected $fillable = [
    'name',
    'description',
  ];
}
