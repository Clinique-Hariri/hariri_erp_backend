<?php

namespace Modules\Supplier\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Supplier\Constants\Status;

class UpdateSupplierRequest extends FormRequest {
    public static function rules() {
        return [
          'name' => ['required', 'string'],
          'phone' => ['nullable', 'string'],
          'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:10240'],
        ];
    }
}
