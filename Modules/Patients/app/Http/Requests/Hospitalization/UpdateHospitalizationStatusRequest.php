<?php

namespace Modules\Patients\Http\Requests\Hospitalization;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Patients\Constants\HospitalizationStatus;

class UpdateHospitalizationStatusRequest extends FormRequest
{
    public static function rules(): array
    {
        return [
//          'payment_amount' => ['required_if:status,' . HospitalizationStatus::ACCEPTED, 'numeric', 'min:0'],
          'status' => 'required|in:' . implode(',', HospitalizationStatus::all()),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
