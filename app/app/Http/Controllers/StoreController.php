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
        return response()->json([
            'stores' => Store::withCount('receipts')->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'unique:stores,name'],
        ]);

        $store = Store::create([
            'name' => $data['name'],
        ]);

        return response()->json([
            'store' => $store,
        ]);
    }

    public function destroy(Store $store)
    {
        $store->delete();

        return response()->json([
            'message' => 'Successfully deleted store',
        ]);
    }
}
