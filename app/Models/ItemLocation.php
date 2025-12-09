<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'location_name',
        'zone',
        'rack',
        'shelf',
        'quantity',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}