<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemAlias extends Model
{
    protected $fillable = [
        'item_id',
        'alias',
        'normalized_alias',
        'source',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
