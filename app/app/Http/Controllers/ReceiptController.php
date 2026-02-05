<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessReceiptOcr;
use App\Models\Receipt;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReceiptController extends Controller
{
    public function index(): JsonResponse
    {
        $receipts = Receipt::withSum('items', 'total_price')
            ->with('store')
            ->get();

        return response()->json($receipts);
    }

    public function show(Receipt $receipt): JsonResponse
    {
        return response()->json(
            $receipt->load(['items.item'])
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'image' => ['required', 'image'],
            'store_id' => ['required', 'exists:stores,id'],
            'group_id' => ['required', 'exists:groups,id'],
        ]);

        $path = $data['image']->store('receipts', 'public');

        $receipt = Receipt::create([
            'image_path' => $path,
            'store_id' => $data['store_id'],
            'group_id' => $data['group_id'],
            'status' => 'uploaded',
        ]);

        ProcessReceiptOcr::dispatch($receipt->id);

        return response()->json([
            'receipt' => $receipt,
        ], 201);
    }
}
