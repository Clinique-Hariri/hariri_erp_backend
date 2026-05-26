<?php

namespace Modules\Patients\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Patients\Models\CheckupTicket;

/** @mixin CheckupTicket */
class CheckupTicketResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'ticket_number' => $this->ticket_number,
      'active_previous_tickets_count' => CheckupTicket::activeBeforeCurrent($this->resource)->count(),
      'date' => $this->date?->format('Y-m-d'),
//      'status' => $this->status,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}
