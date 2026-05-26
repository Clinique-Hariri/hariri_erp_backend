<?php

namespace Modules\MedicalReferences\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicine extends Model
{
  protected $fillable = [
    'name',
    'description',
  ];
}
