<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $limit = $request->query('limit', 10);

        $stores = Store::where('name', 'like', '%'.$search.'%')->limit($limit)->get();

        return response()->json([
            'stores' => $stores,
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
