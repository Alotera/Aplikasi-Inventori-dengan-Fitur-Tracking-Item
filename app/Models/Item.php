<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_code',
        'name',
        'description',
        'category',
        'current_stock',
        'minimum_stock',
        'unit',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function itemLocations(): HasMany
    {
        return $this->hasMany(ItemLocation::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(ItemLocation::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function workInstructions(): BelongsToMany
    {
        return $this->belongsToMany(WorkInstruction::class, 'work_instruction_items')
                    ->withPivot(['required_quantity', 'actual_quantity', 'condition', 'status', 'notes'])
                    ->withTimestamps();
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    /**
     * Recalculate and persist current_stock based on sum of all item locations quantities.
     */
    public function recalculateCurrentStock(): void
    {
        $totalQuantityAcrossLocations = (int) $this->itemLocations()->sum('quantity');
        if ($this->current_stock !== $totalQuantityAcrossLocations) {
            $this->current_stock = $totalQuantityAcrossLocations;
            $this->save();
        }
    }

    /**
     * Update stock and create stock movement record
     */
    public function updateStock(int $newQuantity, StockMovementType $type = StockMovementType::CHECKING_RESULT
    , ?int $workInstructionId = null, ?int $userId = null, ?string $reason = null, ?string $notes = null): void
    {
        $oldQuantity = $this->current_stock;
        $quantityChange = $newQuantity - $oldQuantity;

        // Update current stock
        $this->current_stock = $newQuantity;
        $this->save();

        // Update item location quantity
        $itemLocation = $this->itemLocations()->first();
        if ($itemLocation) {
            $itemLocation->update(['quantity' => $newQuantity]);
        }

        // Create stock movement record
        StockMovement::create([
            'item_id' => $this->id,
            'movement_type' => $type->value,
            'quantity' => $quantityChange,
            'before_quantity' => $oldQuantity,
            'after_quantity' => $newQuantity,
            'reference_type' => $workInstructionId ? 'work_instruction' : 'manual_adjustment',
            'reference_id' => $workInstructionId,
            'user_id' => $userId ?? auth()->id(),
            'notes' => $notes ?? $reason,
        ]);
    }
}