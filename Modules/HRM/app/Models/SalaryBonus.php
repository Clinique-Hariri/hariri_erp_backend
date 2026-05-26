<?php

namespace Modules\HRM\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryBonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'salary_id',
        'bonus_id',
        'name',
        'amount',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function salary(): BelongsTo
    {
        return $this->belongsTo(Salary::class);
    }

    public function bonus(): BelongsTo
    {
        return $this->belongsTo(Bonus::class);
    }
}
