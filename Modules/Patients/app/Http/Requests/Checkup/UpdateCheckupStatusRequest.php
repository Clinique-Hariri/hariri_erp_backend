<?php

namespace Modules\Patients\Http\Requests\Checkup;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Patients\Constants\CheckupStatus;

class UpdateCheckupStatusRequest extends FormRequest {
    public static function rules() {
        return [
          'status' => ['required', 'in:' . implode(',', CheckupStatus::all())],
        ];
    }
}
