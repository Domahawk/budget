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
            'items.*.raw_name' => ['required', 'string'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'numeric'],
            'items.*.unit_price' => ['required', 'numeric'],
            'items.*.total_price' => ['required', 'numeric'],
            'items.*.position' => ['required', 'integer'],
        ]);

        $receipt->items()->delete();

        foreach ($data['items'] as $item) {
            $receiptItem = $receipt->items()->create([
                'item_id'     => $item['item_id'],
                'raw_name'    => $item['raw_name'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'total_price' => $item['total_price'],
                'position'    => $item['position'],
            ]);
        }

        $receipt->update([
            'status' => 'confirmed',
        ]);

        return response()->json($receipt, 201);
    }
}
