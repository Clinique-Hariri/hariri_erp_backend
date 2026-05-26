<?php

namespace Modules\Patients\Http\Requests\Operation;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Patients\Constants\OperationStatus;

class UpdateOperationStatusRequest extends FormRequest
{
    public static function rules(): array
    {
        return [
          'status' => 'required|in:' . implode(',', OperationStatus::all()),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
