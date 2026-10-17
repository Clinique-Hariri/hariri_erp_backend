<?php

namespace Modules\Patients\Http\Requests\HomeCheckup;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Patients\Constants\HomeCheckupStatus;

class UpdateHomeCheckupStatusRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
      'status' => ['required', 'in:' . implode(',', HomeCheckupStatus::all())],
    ];
  }
}
