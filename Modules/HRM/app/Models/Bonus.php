<?php

namespace Modules\HRM\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
    ];

    protected $casts = [
        'value' => 'decimal:2',
    ];

    public function employeeBonuses(): HasMany
    {
        return $this->hasMany(EmployeeBonus::class);
    }

    public function salaryBonuses(): HasMany
    {
        return $this->hasMany(SalaryBonus::class);
    }
}
