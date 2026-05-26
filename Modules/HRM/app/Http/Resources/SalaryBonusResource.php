<?php

namespace Modules\HRM\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRM\Models\SalaryBonus;

/** @mixin SalaryBonus */
class SalaryBonusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'salary_id' => $this->salary_id,
            'bonus_id' => $this->bonus_id,
            'name' => $this->name,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
