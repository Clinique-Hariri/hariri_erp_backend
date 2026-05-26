<?php

namespace Modules\HRM\Http\Requests\Loan;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
  public function rules()
  {
    return [
      'employee_id' => ['required', 'exists:employees,id'],
      'amount' => ['required', 'numeric', 'min:0'],
      'installment_amount' => ['required', 'numeric', 'min:0'],
      'total_installments' => ['required', 'integer', 'min:1'],
      'deduction_date' => ['required','date', 'after_or_equal:today']
    ];
  }

  // Override to disable automatic validation
  public function validateResolved()
  {
  }
}
