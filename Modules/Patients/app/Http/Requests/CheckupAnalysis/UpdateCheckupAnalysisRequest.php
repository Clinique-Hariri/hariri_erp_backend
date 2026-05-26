<?php

namespace Modules\Patients\Http\Requests\CheckupAnalysis;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCheckupAnalysisRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
      'notes' => ['nullable', 'string', 'max:1000'],
      'orientation' => ['nullable', 'string', 'max:1000'],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
