<?php

namespace Modules\Patients\Http\Requests\CheckupAnalysis;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Patients\Constants\CheckupAnalysisStatus;

class UpdateCheckupAnalysisStatusRequest
{
  public static function rules(): array
  {
    return [
      'status' => [
        'required',
        'string',
        'in:' . implode(',', CheckupAnalysisStatus::all()),
      ],

      'services' => ['nullable', 'array'],

      'services.*.id' => [
        'required',
        'integer',
        'exists:checkup_analysis_services,id',
      ],

      'services.*.result' => [
        'nullable',
        'array',
      ],

      'services.*.result_attachment' => [
        'nullable',
        'file',
        'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
        'max:10240',
      ],
    ];
  }
}
