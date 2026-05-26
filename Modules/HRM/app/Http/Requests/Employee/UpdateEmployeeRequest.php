<?php

namespace Modules\HRM\Http\Requests\Employee;

use App\Constants\Gender;
use App\Support\Enum\UserRoles;
use App\Support\Enum\UserTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
  public static function rules($employee)
  {

    $rules = [
      'fullname' => ['required', 'string', 'max:255'],
      'phone' => ['required', 'string'],
      'email' => ['required', 'string', 'email', 'max:255'],
      'gender' => ['required', 'string', 'in:' . implode(',', Gender::all())],
      'address' => ['required', 'string', 'max:255'],
      'birth_date' => ['required', 'date'],
      //'hire_date' => ['required', 'date'],
      'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240'],

      // Optional user creation fields
      'create_user' => ['boolean'],
      'user_type' => ['required_if:create_user,true', 'in:' . implode(',', UserTypes::all())],
      'user_role' => [
        'required_if:create_user,true',
        'exists:roles,name',
//        'in:' . implode(',', UserRoles::all())
      ],
      'insurance_society_ids' => [
        'required_if:user_role,' . UserRoles::INSURANCE_SOCIETY_MANAGER,
        'array',
        'min:1',
      ],
      'insurance_society_ids.*' => ['exists:insurance_societies,id'],
      //'password' => ['required_if:create_user,true', 'string', 'min:8', 'confirmed'],

      'speciality_id' => [
        Rule::requiredIf(function () use ($employee) {
          return $employee->user?->doctor;
        }),
        'exists:specialities,id'
      ],
      'checkup_price' => [
        Rule::requiredIf(function () use ($employee) {
          return $employee->user?->doctor;
        }),
        'numeric',
        'min:0'
      ],
      'commission_percentages' => [
        Rule::requiredIf(function () use ($employee) {
          return $employee->user?->doctor;
        }),
        'array',
      ],
      'commission_percentages.checkup' => [
        Rule::requiredIf(function () use ($employee) {
          return $employee->user?->doctor;
        }),
        'numeric',
        'min:0',
        'max:100',
      ],
      'commission_percentages.analysis' => [
        Rule::requiredIf(function () use ($employee) {
          return $employee->user?->doctor;
        }),
        'numeric',
        'min:0',
        'max:100',
      ],
      'commission_percentages.hospitalization' => [
        Rule::requiredIf(function () use ($employee) {
          return $employee->user?->doctor;
        }),
        'numeric',
        'min:0',
        'max:100',
      ],
      /* 'commission_percentages.operation' => [
        Rule::requiredIf(function () use ($employee) {
          return $employee->user?->doctor;
        }),
        'numeric',
        'min:0',
        'max:100',
      ], */
    ];

    // Add unique validation for email and phone if creating a user
    if (request()->input('create_user')) {
      $rules['phone'] = ['unique:users,phone,' . ($employee->user_id ?? 'NULL')];
      $rules['email'] = ['unique:users,email,' . ($employee->user_id ?? 'NULL')];
    }

    return $rules;
  }

  public function validateResolved()
  {
  }
}
