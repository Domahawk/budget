<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Services\ReceiptParser;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReceiptParseController extends Controller
{
    public function parse(Request $request, ReceiptParser $parser)
    {
        $data = $request->validate([
            'text' => ['required', 'string'],
        ]);

        $parsed = $parser->parse($data['text']);

        return response()->json($parsed);
    }
}
