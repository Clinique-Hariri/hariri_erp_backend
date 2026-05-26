<?php

namespace Modules\HRM\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
  public static function rules()
  {
    return [
      'name' => ['required', 'string', 'max:255'],
    ];
  }
}
