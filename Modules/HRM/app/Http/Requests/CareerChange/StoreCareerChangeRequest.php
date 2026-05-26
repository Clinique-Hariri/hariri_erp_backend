<?php

namespace Modules\HRM\Http\Requests\CareerChange;

use Illuminate\Foundation\Http\FormRequest;
use Modules\HRM\Constants\CareerChangeType;

class StoreCareerChangeRequest extends FormRequest
{
  public function rules()
  {
    $type = $this->input('type');

    $rules = [
      'employee_id' => ['required', 'exists:employees,id'],
      'type' => ['required', 'string', 'in:' . implode(',', CareerChangeType::all())],
      'notes' => ['nullable', 'string'],
    ];

    switch ($type) {
      case CareerChangeType::TRANSFER:
        $rules['department_id'] = ['required', 'exists:departments,id'];
        $rules['file'] = ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'];
        break;

      case CareerChangeType::RAISE:
        $rules['basic_salary'] = ['required', 'numeric', 'min:0'];
        $rules['file'] = ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'];
        break;

      case CareerChangeType::PROMOTION:
        $rules['department_id'] = ['required', 'exists:departments,id'];
        $rules['designation_id'] = ['required', 'exists:designations,id'];
        $rules['basic_salary'] = ['required', 'numeric', 'min:0'];
        $rules['file'] = ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'];
        break;

      case CareerChangeType::RENEWAL:
        $rules['department_id'] = ['required', 'exists:departments,id'];
        $rules['designation_id'] = ['required', 'exists:designations,id'];
        $rules['basic_salary'] = ['required', 'numeric', 'min:0'];
        $rules['end_date'] = ['required', 'date'];
        $rules['file'] = ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'];
        break;

      case CareerChangeType::TERMINATION:
        //$rules['start_date'] = ['required', 'date'];
        $rules['file'] = ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'];
        break;
    }

    return $rules;
  }

  // Override to disable automatic validation
  public function validateResolved()
  {
  }
}
