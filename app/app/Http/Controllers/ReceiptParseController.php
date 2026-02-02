<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Services\ReceiptParser;
use Illuminate\Http\Request;

class ReceiptParseController extends Controller
{
    public function parse(Request $request, Receipt $receipt, ReceiptParser $parser)
    {
        $data = $request->validate([
            'text' => ['required', 'string'],
        ]);

        $receipt->ocr_text_edited = $data['text'];
        $receipt->save();

        $parsed = $parser->parse($data['text']);

        return response()->json(['parsed' => $parsed]);
    }
}
