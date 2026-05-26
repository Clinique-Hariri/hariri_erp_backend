<?php

namespace Modules\Settings\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Settings\Constants\SettingsKeys;
use Modules\Settings\Models\Setting;

/** @mixin Setting */
class SettingResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'key' => $this->key,
      'label' => SettingsKeys::get_name($this->key),
      'value' => $this->value,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}
