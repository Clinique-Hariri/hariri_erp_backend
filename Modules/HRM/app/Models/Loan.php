<?php

namespace Modules\HRM\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HRM\Constants\InstallmentStatus;
use Modules\HRM\Constants\LoanStatus;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'amount',
        'installment_amount',
        'total_installments',
        'status',
        'deduction_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'total_installments' => 'integer',
        'status' => 'string',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(LoanInstallment::class);
    }

    public function getPaidAmountAttribute(): string
    {
        if ($this->relationLoaded('installments')) {
            $sum = $this->installments
                ->where('status', InstallmentStatus::PAID)
                ->sum(function ($inst) { return (float) $inst->amount; });
        } else {
            $sum = (float) $this->installments()
                ->where('status', InstallmentStatus::PAID)
                ->sum('amount');
        }
        return number_format($sum, 2, '.', '');
    }

    public function getRemainingAmountAttribute(): string
    {
        if ($this->relationLoaded('installments')) {
            $sum = $this->installments
                ->where('status', '!=', InstallmentStatus::PAID)
                ->sum(function ($inst) { return (float) $inst->amount; });
        } else {
            $sum = (float) $this->installments()
                ->where('status', '!=', InstallmentStatus::PAID)
                ->sum('amount');
        }
        return number_format($sum, 2, '.', '');
    }

    public function getPaidInstallmentsAttribute(): int
    {
        if ($this->relationLoaded('installments')) {
            return $this->installments->where('status', InstallmentStatus::PAID)->count();
        }
        return $this->installments()->where('status', InstallmentStatus::PAID)->count();
    }

    public function getRemainingInstallmentsAttribute(): int
    {
        if ($this->relationLoaded('installments')) {
            return $this->installments->where('status', '!=', InstallmentStatus::PAID)->count();
        }
        return $this->installments()->where('status', '!=', InstallmentStatus::PAID)->count();
    }

    public function getStatusAttribute(): string
    {
        $paid = $this->paid_installments;
        $total = $this->total_installments;

        if ($paid == 0) {
            return LoanStatus::UNPAID;
        }

        if ($paid == $total) {
            return LoanStatus::PAID;
        }

        return LoanStatus::IN_PROGRESS;
    }
}
