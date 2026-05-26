<?php

namespace Modules\HRM\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'salary_id',
        'loan_installment_id',
        'type',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'type' => 'string',
    ];

    // Relationships
    public function salary(): BelongsTo
    {
        return $this->belongsTo(Salary::class);
    }

    public function loanInstallment(): BelongsTo
    {
        return $this->belongsTo(LoanInstallment::class);
    }
}
