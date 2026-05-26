<?php

namespace Modules\Inventory\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Patients\Constants\PatientStatus;

class UpdatePatientStatusRequest extends FormRequest
{
  public static function rules()
  {
    return [
      'status' => ['required', 'in:' . implode(',', PatientStatus::all())],
    ];
  }
}
