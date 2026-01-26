<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreSchema extends Model
{
    protected $fillable = [
        'store_id',
        'position',
        'type',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
