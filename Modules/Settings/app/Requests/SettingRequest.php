<?php

namespace Modules\Settings\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
  public static function rules($id): array
  {
    return [
      'key' => ['required', 'string', 'unique:settings,key,' . $id],
      'value' => ['nullable'],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
