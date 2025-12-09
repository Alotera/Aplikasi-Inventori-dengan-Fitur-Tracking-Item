<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'movement_type',
        'quantity',
        'before_quantity',
        'after_quantity',
        'reference_type',
        'reference_id',
        'location_id',
        'user_id',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'before_quantity' => 'integer',
        'after_quantity' => 'integer',
        'quantity' => 'integer',
        'metadata' => 'array',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function workInstruction(): BelongsTo
    {
        return $this->belongsTo(WorkInstruction::class, 'reference_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(ItemLocation::class);
    }

    public function getFormattedQuantityAttribute(): string
    {
        $sign = $this->quantity >= 0 ? '+' : '';
        return $sign . number_format($this->quantity);
    }

    public function getMovementTypeLabelAttribute(): string
    {
        return match($this->movement_type) {
            'checking' => 'Stock Check',
            'CHECKING_RESULT' => 'Checking',
            'IN' => 'Stock In',
            'OUT' => 'Stock Out',
            'ADJUSTMENT' => 'Manual Adjustment',
            'WI_CONSUMPTION' => 'Work Instruction',
            'adjustment' => 'Manual Adjustment',
            'transfer' => 'Transfer',
            'return' => 'Return',
            'damage' => 'Damage',
            default => 'Unknown',
        };
    }

    public function getMovementTypeColorAttribute(): string
    {
        return match($this->movement_type) {
            'checking' => 'bg-blue-100 text-blue-800',
            'CHECKING_RESULT' => 'bg-purple-100 text-purple-800',
            'IN' => 'bg-green-100 text-green-800',
            'OUT' => 'bg-red-100 text-red-800',
            'ADJUSTMENT' => 'bg-yellow-100 text-yellow-800',
            'WI_CONSUMPTION' => 'bg-orange-100 text-orange-800',
            'adjustment' => 'bg-yellow-100 text-yellow-800',
            'transfer' => 'bg-green-100 text-green-800',
            'return' => 'bg-purple-100 text-purple-800',
            'damage' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}