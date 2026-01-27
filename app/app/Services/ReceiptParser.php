<?php
namespace App\Services;

use App\Models\Item;

class ReceiptParser
{
    /**
     * @param  string  $text
     * @return array
     */
    public function parse(string $text): array
    {
        $lines = preg_split('/\R/', $text);

        return collect($lines)
            ->map(fn($line) => trim($line))
            ->filter(fn($line) => $line !== '')
            ->map(fn($line) => $this->parseLine($line))
            ->values()
            ->all();
    }

    /**
     * Parse a single OCR line using positional schema.
     */
    private function parseLine(string $line): array
    {
        $tokens = explode(';', $line);
        $name = $tokens[0] ?? '';
        $qty = $tokens[1] ?? '';
        $unit = $tokens[2] ?? '';
        $total = $tokens[3] ?? '';
        $result = [
            'name' => trim($name),
            'qty' => (float) str_replace(',', '.', trim($qty)),
            'unit_price' => (float) str_replace(',', '.', trim($unit)),
            'total_price' => (float) str_replace(',', '.', trim($total)),
        ];

        /** @var Item | null $alias*/
        $item = Item::whereHas(
            'aliases',
            fn($query) => $query->where('alias', 'like', "%{$result['name']}%")
        )->first();

        $result['item_id'] = null;
        $result['item_name'] = null;

        if ($item) {
            $result['item_id'] = $item->id;
            $result['item_name'] = $item->name;
        }

        return $result;
    }

    /**
     * Fallback when parsing fails.
     */
    private function rawFallback(string $line): array
    {
        return [
            'raw_line' => $line,
            'qty' => null,
            'name' => $line,
            'unit_price' => null,
            'total_price' => null,
            'item_id' => null,
            'item_name' => null,
        ];
    }
}
