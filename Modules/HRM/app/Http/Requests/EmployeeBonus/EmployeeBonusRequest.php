<?php

namespace Modules\HRM\Http\Requests\EmployeeBonus;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeBonusRequest extends FormRequest
{
  public function rules()
  {
    return [
      'employee_id' => ['required', 'exists:employees,id'],
      'bonuses' => ['required', 'nullable', 'array'],
      'bonuses.*' => ['required', 'exists:bonuses,id'],
    ];
  }

  // Override to disable automatic validation
  public function validateResolved()
  {
  }
}
