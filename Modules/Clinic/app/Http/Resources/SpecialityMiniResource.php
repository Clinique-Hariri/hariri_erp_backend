<?php

namespace Modules\Clinic\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Models\Speciality;

/** @mixin Speciality */
class SpecialityMiniResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
    ];
  }
}
