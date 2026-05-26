<?php

namespace App\Http\Resources\User;

use App\Constants\Statuses\UserStatus;
use App\Models\User;
use App\Support\Enum\UserTypes;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MedicalReferences\Http\Resources\InsuranceSocietyResource;
use Modules\MedicalReferences\Models\InsuranceSociety;

/** @mixin User */
class UserResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'fullname' => $this->fullname,
      'email' => $this->email,
      'phone' => $this->phone,
      'gender' => $this->gender,
      'full_address' => $this->full_address,
      'avatar' => $this->avatar_url,
      'birthdate' => $this->birthdate,
      'type' => UserTypes::get_resource($this->type),
      'doctor_id' => $this->doctor?->id??null,
      'status' => UserStatus::get_resource($this->status),
      'next_statuses' => UserStatus::get_next_statuses($this->status),
      'role' => $this->roles()->first()->name,
      'insurance_societies' => InsuranceSocietyResource::collection($this->whenLoaded('insuranceSocieties')),
      'permissions' => $this->getAllPermissions()->pluck('name'),
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,

    ];
  }
}
