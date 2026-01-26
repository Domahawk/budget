<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessReceiptOcr;
use App\Models\Receipt;
use App\Models\Store;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReceiptController extends Controller
{
    public function index()
    {
        return Inertia::render('receipts/Index', [
            'receipts' => Receipt::withSum('items', 'total_price')->get()->load('store'),
        ]);
    }

    public function show(Receipt $receipt)
    {
        return Inertia::render('receipts/Show', [
            'receipt' => $receipt->load(['items.item']),
        ]);
    }

    public function create()
    {
        return Inertia::render('receipts/Create', [
            'stores' => Store::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => ['required', 'image'],
            'store_id' => ['required', 'exists:stores,id'],
        ]);

        $path = $data['image']->store('receipts', 'public');

        $receipt = Receipt::create([
            'image_path' => $path,
            'store_id' => $data['store_id'],
            'status' => 'uploaded',
        ]);

        ProcessReceiptOcr::dispatch($receipt->id);

        return redirect()->route('receipts.show', $receipt);
    }
}
