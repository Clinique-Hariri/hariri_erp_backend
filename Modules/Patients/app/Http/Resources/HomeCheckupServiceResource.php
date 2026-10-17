<?php

namespace Modules\Patients\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Patients\Models\HomeCheckupService;

/** @mixin HomeCheckupService */
class HomeCheckupServiceResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'service_name' => $this->service_name,
      'service_price' => $this->service_price,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}
