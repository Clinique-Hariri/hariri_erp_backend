<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Modules\Settings\Models\Setting;
use Modules\Settings\Resources\SettingResource;
use Throwable;

class SettingsController extends Controller
{
  use ApiResponseTrait;
  public function index(Request $request)
  {
    $this->authorizePermission(PermissionNames::SETTINGS_VIEW);

    try {
      $model = Setting::get();

      return $this->successResponse(
        data: SettingResource::collection($model),
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function multiUpdate(Request $request)
  {
    $this->authorizePermission(PermissionNames::SETTINGS_UPDATE);

    $this->validateRequest($request, [
      'settings' => ['required', 'array'],
      'settings.*.key' => ['required', 'string', 'exists:settings,key'],
      'settings.*.value' => ['required', 'string', 'max:255'],
    ]);

    $updatedSettings = [];
    try {
      foreach ($request->settings as $settingData) {
        $setting = Setting::where('key', $settingData['key'])->first();
        if ($setting) {
          $setting->update([
            'value' => $settingData['value'],
          ]);
          $updatedSettings[] = new SettingResource($setting);
        }
      }

      return $this->successResponse(
        data: $updatedSettings,
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
