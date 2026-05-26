<?php

namespace Modules\HRM\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRM\Models\Bonus;

/** @mixin Bonus */
class BonusResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'value' => $this->value,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}
