<?php
namespace App\Services;

use App\Models\Item;
use App\Models\ItemAlias;
use Illuminate\Support\Collection;

class ReceiptParser
{
    /**
     * @param  string  $text
     * @param  Collection  $schemas  // ordered StoreSchema collection
     * @return array
     */
    public function parse(string $text, Collection $schemas): array
    {
        $lines = preg_split('/\R/', $text);



        return collect($lines)
            ->map(fn($line) => trim($line))
            ->filter(fn($line) => $line !== '')
            ->map(fn($line) => $this->parseLine($line, $schemas))
            ->values()
            ->all();
    }

    /**
     * Parse a single OCR line using positional schema.
     */
    private function parseLine(string $line, Collection $schemas): array
    {
        $tokens = explode(';', $line);
        $result = [
            'name' => trim($tokens[0]),
            'qty' => (float) str_replace(',', '.', $tokens[1]),
            'unit_price' => (float) str_replace(',', '.', $tokens[2]),
            'total_price' => (float) str_replace(',', '.', $tokens[3]),
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
