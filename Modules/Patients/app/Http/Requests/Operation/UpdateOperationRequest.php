<?php

namespace Modules\Patients\Http\Requests\Operation;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Patients\Constants\OperationStatus;

class UpdateOperationRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
      'operation_date' => 'sometimes|date',
      'price' => 'sometimes|numeric|min:0',
      'description' => 'nullable|string',
      'patient_id' => 'sometimes|exists:patients,id',
      'surgeons' => 'sometimes|array',
      'surgeons.*.doctor_id' => 'sometimes|exists:doctors,id',
      'surgeons.*.doctor_commission_percentage' => 'sometimes|numeric|min:0|max:100',
    ];
  }
}
