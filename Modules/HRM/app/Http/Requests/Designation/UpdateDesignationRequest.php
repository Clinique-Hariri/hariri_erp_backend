<?php

namespace Modules\HRM\Http\Requests\Designation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDesignationRequest extends FormRequest
{
    public static function rules()
    {
        return [
          'name' => ['required', 'string', 'max:255'],
          'base_salary' => ['required', 'numeric', 'min:0'],
        ];
    }
}
