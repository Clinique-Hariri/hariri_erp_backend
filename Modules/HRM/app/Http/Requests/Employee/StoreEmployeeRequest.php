<?php

namespace Modules\HRM\Http\Requests\Employee;

use App\Constants\Gender;
use App\Support\Enum\UserRoles;
use App\Support\Enum\UserTypes;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
  public function rules()
  {
    $rules = [
      // Employee data
      'fullname' => ['required', 'string', 'max:255'],
      'phone' => ['required', 'string'],
      'email' => ['required', 'string', 'email', 'max:255'],
      'gender' => ['required', 'string', 'in:' . implode(',', Gender::all())],
      'address' => ['required', 'string', 'max:255'],
      'birth_date' => ['required', 'date'],
      'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240'],

      // optional doctor data
      'create_doctor' => ['required', 'boolean'],
      'speciality_id' => ['required_if:create_doctor,true', 'exists:specialities,id'],
      'checkup_price' => ['required_if:create_doctor,true', 'numeric', 'min:0'],
      'commission_percentages' => ['required_if:create_doctor,true', 'array'],
      'commission_percentages.checkup' => ['required_if:create_doctor,true', 'numeric', 'min:0', 'max:100'],
      'commission_percentages.analysis' => ['required_if:create_doctor,true', 'numeric', 'min:0', 'max:100'],
      'commission_percentages.hospitalization' => ['required_if:create_doctor,true', 'numeric', 'min:0', 'max:100'],
      //'commission_percentages.operation' => ['required_if:create_doctor,true', 'numeric', 'min:0', 'max:100'],

      // Optional user data
      'create_user' => ['required', 'boolean', 'accepted_if:create_doctor,true'],
      'user_type' => [
        'required_if:create_user,true',
        'in:' . implode(',', UserTypes::all()),
      ],
      'user_role' => [
        'required_if:create_user,true',
        'exists:roles,name',
//        'in:' . implode(',', UserRoles::all()),
      ],
      'insurance_society_ids' => [
        'required_if:user_role,' . UserRoles::INSURANCE_SOCIETY_MANAGER,
        'array',
        'min:1',
      ],
      'insurance_society_ids.*' => ['exists:insurance_societies,id'],
      'password' => [
        'required_if:create_user,true',
        'string',
        'min:8',
        'confirmed',
      ],

      // Contract data
      'create_contract' => ['boolean'],
      'hire_date' => [
        'required_if:create_contract,true',
        'date',
      ],
      'department_id' => [
        'required_if:create_contract,true',
        'exists:departments,id',
      ],
      'designation_id' => [
        'required_if:create_contract,true',
        'exists:designations,id',
      ],
      'end_date' => [
        'required_if:create_contract,true',
        'date',
        'after:hire_date',
      ],
      'basic_salary' => [
        'required_if:create_contract,true',
        'numeric',
        'min:0',
      ],
    ];

    if ($this->boolean('create_user')) {
      $rules['phone'][] = 'unique:users,phone';
      $rules['email'][] = 'unique:users,email';
    }

    return $rules;
  }

  // Override to disable automatic validation
  public function validateResolved()
  {
  }
}
