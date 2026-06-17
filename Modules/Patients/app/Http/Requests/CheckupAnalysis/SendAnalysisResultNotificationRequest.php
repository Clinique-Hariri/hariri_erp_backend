<?php

namespace Modules\Patients\Http\Requests\CheckupAnalysis;

class SendAnalysisResultNotificationRequest
{
  public static function rules(): array
  {
    return [
      'checkup_analysis_id' => ['required', 'integer', 'exists:checkup_analyses,id'],
      'report' => ['required', 'file', 'mimes:pdf', 'max:10240'],
    ];
  }
}
