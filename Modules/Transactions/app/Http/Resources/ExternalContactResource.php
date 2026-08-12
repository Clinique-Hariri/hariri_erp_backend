<?php

namespace Modules\Transactions\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Transactions\Models\ExternalContact;

/** @mixin ExternalContact */
class ExternalContactResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'fullname' => $this->fullname,
      'phone' => $this->phone,
    ];
  }
}
