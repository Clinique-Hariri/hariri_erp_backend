<?php

namespace Modules\Patients\Http\Requests\Operation;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Patients\Constants\OperationStatus;

class StoreOperationRequest extends FormRequest
{
    public static function rules(): array
    {
        return [
            'operation_date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'patient_id' => 'required|exists:patients,id',
            'surgeons' => 'sometimes|array',
            'surgeons.*.doctor_id' => 'required|exists:doctors,id',
            'surgeons.*.doctor_commission_percentage' => 'required|numeric|min:0|max:100',
        ];
    }
}
