<?php

namespace Modules\Inventory\Http\Requests\ItemsCategory;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Inventory\Constants\ItemsCategory\Status;

class StoreItemsCategoryRequest extends FormRequest {
    public static function rules() {
        return [
          'name' => ['required', 'string', 'unique:items_categories,name'],
        ];
    }
}
