<?php

namespace Modules\Transactions\Http\Requests\Patient;

use App\Constants\Gender;
use App\Constants\BloodType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Modules\Patients\Constants\PatientStatus;

class StoreTransactionRequest extends FormRequest
{
    public static function rules(): array
    {
      return [
        'amount' => ['required', 'numeric', 'min:0'],
        'details' => ['nullable', 'string', 'max:255'],
        'type' => ['required', 'string', 'in:' . implode(',', \Modules\Transactions\Constants\Type::all())],
      ];
    }
}
