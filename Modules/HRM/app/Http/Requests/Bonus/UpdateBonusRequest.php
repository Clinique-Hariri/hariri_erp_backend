<?php

namespace Modules\HRM\Http\Requests\Bonus;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBonusRequest extends FormRequest
{
  public function rules()
  {
    return [
      'name' => ['required', 'string', 'max:255'],
      'value' => ['required', 'numeric', 'min:0'],
    ];
  }

  // Override to disable automatic validation
  public function validateResolved()
  {
  }
}
