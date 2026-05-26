<?php

namespace Modules\HRM\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'base_salary',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'type' => 'string',
    ];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
