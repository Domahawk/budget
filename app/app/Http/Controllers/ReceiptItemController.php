<?php

namespace App\Http\Controllers;

use App\Models\ItemAlias;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReceiptItemController extends Controller
{
    public function store(Request $request, Receipt $receipt)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.raw_name' => ['nullable', 'string'],
            'items.*.item_id' => ['nullable', 'exists:items,id'],
            'items.*.quantity' => ['nullable', 'numeric'],
            'items.*.unit_price' => ['nullable', 'numeric'],
            'items.*.total_price' => ['nullable', 'numeric'],
            'items.*.position' => ['nullable', 'integer'],
        ]);

        // Replace mode: clear existing items
        $receipt->items()->delete();

        foreach ($data['items'] as $item) {
            $receiptItem = $receipt->items()->create([
                'item_id'     => $item['item_id'] ?? null,
                'raw_name'    => $item['raw_name'] ?? null,
                'quantity'    => $item['quantity'] ?? null,
                'unit_price'  => $item['unit_price'] ?? null,
                'total_price' => $item['total_price'] ?? null,
                'position'    => $item['position'] ?? null,
            ]);

            // Alias auto-learning (only after user confirmation)
            if (!empty($item['raw_name']) && !empty($item['item_id'])) {
                ItemAlias::firstOrCreate([
                    'item_id' => $item['item_id'],
                ], [
                    'alias'  => $item['raw_name'],
                    'source' => 'ocr',
                ]);
            }
        }

        $receipt->update([
            'status' => 'confirmed',
        ]);

        return to_route('receipts.show', $receipt);
    }

    private function normalize(string $value): string
    {
        $value = mb_strtolower($value);
        $value = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return trim($value);
    }
}
