<?php

namespace Modules\Patients\Http\Requests\CheckupAnalysis;

use Illuminate\Validation\Rule;
use Modules\Patients\Models\CheckupAnalysis;

class SendAnalysisResultNotificationRequest
{
  public static function rules(): array
  {
    $checkupAnalysisId = request()->integer('checkup_analysis_id');

    $hasResultAttachment = false;
    if (!empty($checkupAnalysisId)) {
      $analysis = CheckupAnalysis::with('media')->find($checkupAnalysisId);
      $hasResultAttachment = filled($analysis?->getFirstMediaUrl(CheckupAnalysis::RESULT_ATTACHMENT));
    }

    return [
      'checkup_analysis_id' => ['required', 'integer', 'exists:checkup_analyses,id'],
      'report' => [Rule::requiredIf(!$hasResultAttachment), 'nullable', 'file', 'mimes:pdf', 'max:10240'],
    ];
  }
}
