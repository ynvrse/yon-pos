<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Product extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function itemTransactions(): HasMany
    {
        return $this->hasMany(ItemTransaction::class);
    }
}
