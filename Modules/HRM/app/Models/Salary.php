<?php

namespace Modules\HRM\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Actions\Constants\ActionType;
use Modules\Actions\Models\Action;
use Modules\Transactions\Models\Transaction;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Salary extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    const string REPORT = 'report';

    protected $fillable = [
        'employee_id',
        'month',
        'basic_salary',
        'daily_wage',
        'total_bonuses',
        'total_deduction',
        'work_days',
        'absent_days',
        'net_salary',
        'status',
        'pay_date',
    ];

    protected $casts = [
        'month' => 'date',
        'basic_salary' => 'decimal:2',
        'daily_wage' => 'decimal:2',
        'total_bonuses' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'work_days' => 'integer',
        'absent_days' => 'integer',
        'net_salary' => 'decimal:2',
        'status' => 'string',
        'pay_date' => 'date',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function bonuses(): HasMany
    {
        return $this->hasMany(SalaryBonus::class);
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(SalaryDeduction::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function actions(): MorphMany
    {
        return $this->morphMany(Action::class, 'actionable');
    }

    public function paymentAction(): MorphOne
    {
    return $this->morphOne(Action::class, 'actionable')
      ->where('action_type', ActionType::SALARY_PAYMENT_ACTION)
      ->latest();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::REPORT)
            ->singleFile()
            ->useDisk('public');
    }
}
