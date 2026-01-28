<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));

        return Item::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%');
                $q->with('aliases')->limit(1);
            })->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:items,name'],
            'raw_name' => ['required', 'string', 'max:255', 'unique:item_aliases,alias'],
        ]);

        $item = Item::create([
            'name' => trim($data['name']),
        ]);
        $item->aliases()->create([
            'alias' => $data['raw_name'],
        ]);

        return response()->json($item, 201);
    }
}
