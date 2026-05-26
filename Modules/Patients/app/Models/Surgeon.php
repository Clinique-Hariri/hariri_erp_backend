<?php

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Modules\Clinic\Models\Doctor;

class Surgeon extends Model
{
  protected $fillable = [
    'doctor_commission_percentage',
    'doctor_id',
    'operation_id',
  ];

  protected $casts = [
    'doctor_commission_percentage' => 'decimal:2',
    'doctor_id' => 'integer',
    'operation_id' => 'integer',
  ];

  public function operation()
  {
    return $this->belongsTo(Operation::class);
  }

  public function doctor()
  {
    return $this->belongsTo(Doctor::class, 'doctor_id');
  }
}
