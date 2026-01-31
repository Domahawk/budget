<?php

namespace App\Http\Controllers;

use App\Enums\ReceiptStatus;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use Illuminate\Http\Request;

class ReceiptItemController extends Controller
{
    public function store(Request $request, Receipt $receipt)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.raw_name' => ['required', 'string'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'numeric'],
            'items.*.unit_price' => ['required', 'numeric'],
            'items.*.total_price' => ['required', 'numeric'],
            'items.*.id' => ['integer'],
        ]);

        $itemIds = $receipt->items->mapWithKeys(function ($item) {
            return [$item->id => $item];
        });

        $newItems = [];

        foreach ($data['items'] as $item) {
            $receiptItem = new ReceiptItem([
                'item_id' => $item['item_id'],
                'raw_name' => $item['raw_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price'],
            ]);

            if (!isset($item['id'])) {
                $newItems[] = $receiptItem;
                continue;
            }

            $hasKey = $itemIds->has($item['id']);

            if ($hasKey) {
                $itemIds[$item['id']]->update($item);
                $itemIds->forget($item['id']);
            }

            if (!$hasKey) {
                $newItems[] = $receiptItem;
            }
        }

        foreach ($itemIds as $item) {
            $item->delete();
        }

        $receipt->items()->saveMany($newItems);
        $receipt->update([
            'status' => $receipt->items->count() < 1 ? ReceiptStatus::NEEDS_REVIEW->value : ReceiptStatus::CONFIRMED->value,
        ]);

        return response()->json($receipt, 201);
    }
}
