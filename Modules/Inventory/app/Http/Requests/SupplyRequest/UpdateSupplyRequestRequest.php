<?php

namespace Modules\Inventory\Http\Requests\SupplyRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Inventory\Constants\SupplyRequest\Status;

class UpdateSupplyRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public static function rules(): array
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'requested_by' => 'required|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:inventory_items,id',
            'items.*.requested_quantity' => 'required|integer|min:1',
        ];
    }
}
