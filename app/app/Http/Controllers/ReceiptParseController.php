<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Services\ReceiptParser;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReceiptParseController extends Controller
{
    public function parse(Request $request, Receipt $receipt, ReceiptParser $parser)
    {
        $data = $request->validate([
            'text' => ['required', 'string'],
        ]);

        $store = $receipt->store;

        $schemas = $store->schemas()->orderBy('position')->get();

        if ($schemas->isEmpty()) {
            return response()->json([
                'error' => 'Store has no schema defined.',
            ], 422);
        }

        $parsed = $parser->parse($data['text'], $schemas);

        return Inertia::render('receipts/Show', ['parsed' => $parsed]);
    }
}
