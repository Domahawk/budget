<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receipt extends Model
{
    protected $fillable = [
        'image_path',
        'status',
        'ocr_text_raw',
        'ocr_text_edited',
        'parsed_total',
        'confirmed_total',
        'store_id',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ReceiptItem::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
