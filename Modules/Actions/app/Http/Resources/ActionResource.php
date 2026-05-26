<?php

namespace Modules\Actions\Http\Resources;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Actions\Constants\ActionType;
use Modules\Actions\Models\Action;

/** @mixin Action */
class ActionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'action_type' => ActionType::get_resource($this->action_type),
            'from_status' => $this->from_status,
            'to_status'   => $this->to_status,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,

            'user_id' => $this->user_id,
            'user'    => new UserResource($this->whenLoaded('user')),
        ];
    }
}
