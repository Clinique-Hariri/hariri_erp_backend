<?php

namespace Modules\Inventory\Http\Requests\InventoryItem;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public static function rules($id): array
    {
        $itemId = $id;

        return [
            'name' => 'required|string|max:100',
            'barcode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('inventory_items', 'barcode')->ignore($itemId)
            ],
            'category_id' => 'required|exists:items_categories,id',
            'current_stock' => 'nullable|integer|min:0',
        ];
    }
}
