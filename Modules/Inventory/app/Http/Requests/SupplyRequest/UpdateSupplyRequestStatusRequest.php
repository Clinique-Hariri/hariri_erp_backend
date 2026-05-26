<?php

namespace Modules\Inventory\Http\Requests\SupplyRequest;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Inventory\Constants\SupplyRequest\Status;

class UpdateSupplyRequestStatusRequest extends FormRequest {
    public static function rules() {
        return [
          'status' => ['required', 'in:' . implode(',', Status::all())],
        ];
    }
}
