<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'name',
    ];

    public function aliases(): HasMany
    {
        return $this->hasMany(ItemAlias::class);
    }
}
