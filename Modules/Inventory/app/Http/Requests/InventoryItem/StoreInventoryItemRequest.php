<?php

namespace Modules\Inventory\Http\Requests\InventoryItem;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryItemRequest extends FormRequest
{
    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'barcode' => 'required|string|max:50|unique:inventory_items,barcode',
            'category_id' => 'required|exists:items_categories,id',
            'current_stock' => 'nullable|integer|min:0',
        ];
    }
}
