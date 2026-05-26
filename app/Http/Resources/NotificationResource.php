<?php

namespace App\Http\Resources;

use App\Models\Notification;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Notification */
class NotificationResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'type' => class_basename($this->type),
      'title' => app()->isLocale('fr') ? $this->data['title']['fr'] ?? ''
        : (app()->isLocale('ar') ? $this->data['title']['ar'] : $this->data['title']['en'] ?? ''),
      'body' => app()->isLocale('fr') ? $this->data['body']['fr'] ?? ''
        : (app()->isLocale('ar') ? $this->data['body']['ar'] : $this->data['body']['en'] ?? ''),
      'data' => $this->data['data'] ?? [],
      'read' => !is_null($this->read_at),
      'created_at' => $this->created_at->toDateTimeString(),
    ];
  }
}
