<?php

namespace Modules\Transactions\Http\Requests\Patient;

use App\Constants\Gender;
use App\Constants\BloodType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Modules\Patients\Constants\PatientStatus;

class UpdateTransactionsRequest extends FormRequest
{
    public static function rules($id): array
    {
        return [
            'details' => ['nullable', 'string', 'max:255'],
        ];
    }
}
