<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreSchema;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreController extends Controller
{
    public function index()
    {
        return Inertia::render('stores/Index', [
            'stores' => Store::withCount('receipts')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        return Inertia::render('stores/Create', [
            'columnTypes' => [
                'qty',
                'name',
                'unit_price',
                'total_price',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'unique:stores,name'],
            'columns' => ['required', 'array', 'min:2'],
            'columns.*' => ['in:qty,name,unit_price,total_price'],
        ]);

        if (collect($data['columns'])->filter(fn ($c) => $c === 'name')->count() !== 1) {
            return back()->withErrors([
                'columns' => 'Exactly one "name" column is required.',
            ]);
        }

        $store = Store::create([
            'name' => $data['name'],
        ]);

        foreach ($data['columns'] as $index => $type) {
            StoreSchema::create([
                'store_id' => $store->id,
                'position' => $index + 1,
                'type' => $type,
            ]);
        }

        return redirect()->route('stores.index')
            ->with('success', 'Store created successfully.');
    }

    public function destroy(Store $store)
    {
        $store->delete();

        return redirect()
            ->route('stores.index')
            ->with('success', 'Store deleted.');
    }
}
