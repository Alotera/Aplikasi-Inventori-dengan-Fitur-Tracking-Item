<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StockMovementService
{
    public function recordMovement(
        Item $item,
        StockMovementType $movementType,
        int $quantity,
        User $user,
        ?string $notes = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $locationId = null,
        ?array $metadata = null
    ): StockMovement {
        return DB::transaction(function () use (
            $item, $movementType, $quantity, $user, $notes, 
            $referenceType, $referenceId, $locationId, $metadata
        ) {
            $beforeQuantity = $item->current_stock;
            $afterQuantity = $beforeQuantity + $quantity;

            // Update item stock
            $item->current_stock = $afterQuantity;
            $item->save();

            // Create movement record
            return StockMovement::create([
                'item_id' => $item->id,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'before_quantity' => $beforeQuantity,
                'after_quantity' => $afterQuantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'location_id' => $locationId,
                'user_id' => $user->id,
                'notes' => $notes,
                'metadata' => $metadata,
            ]);
        });
    }

    public function recordLocationTransfer(
        Item $item,
        int $fromLocationId,
        int $toLocationId,
        int $quantity,
        User $user,
        ?string $notes = null
    ): array {
        return DB::transaction(function () use (
            $item, $fromLocationId, $toLocationId, $quantity, $user, $notes
        ) {
            $fromLocation = $item->locations()->findOrFail($fromLocationId);
            $toLocation = $item->locations()->findOrFail($toLocationId);

            // Update location quantities
            $fromLocation->quantity -= $quantity;
            $toLocation->quantity += $quantity;
            $fromLocation->save();
            $toLocation->save();

            // Recalculate item stock
            $item->recalculateCurrentStock();

            // Record transfer movement (neutral quantity) as ADJUSTMENT per simplified enum
            $movement = StockMovement::create([
                'item_id' => $item->id,
                'movement_type' => StockMovementType::ADJUSTMENT,
                'quantity' => 0, // Net zero for transfers
                'before_quantity' => $item->current_stock,
                'after_quantity' => $item->current_stock,
                'reference_type' => 'transfer',
                'reference_id' => null,
                'location_id' => $toLocationId,
                'user_id' => $user->id,
                'notes' => $notes,
                'metadata' => [
                    'from_location_id' => $fromLocationId,
                    'to_location_id' => $toLocationId,
                    'quantity_transferred' => $quantity,
                    'from_location_name' => $fromLocation->location_name,
                    'to_location_name' => $toLocation->location_name,
                ],
            ]);

            return [
                'movement' => $movement,
                'from_location' => $fromLocation,
                'to_location' => $toLocation,
            ];
        });
    }

    public function recordLocationCreate(
        Item $item,
        int $locationId,
        int $quantity,
        User $user,
        ?string $notes = null
    ): StockMovement {
        return DB::transaction(function () use ($item, $locationId, $quantity, $user, $notes) {
            // Recalculate item stock
            $item->recalculateCurrentStock();

            $location = $item->locations()->findOrFail($locationId);

            return StockMovement::create([
                'item_id' => $item->id,
                'movement_type' => StockMovementType::ADJUSTMENT,
                'quantity' => $quantity,
                'before_quantity' => $item->current_stock - $quantity,
                'after_quantity' => $item->current_stock,
                'reference_type' => 'location',
                'reference_id' => $locationId,
                'location_id' => $locationId,
                'user_id' => $user->id,
                'notes' => $notes,
                'metadata' => [
                    'location_name' => $location->location_name,
                    'zone' => $location->zone,
                    'rack' => $location->rack,
                    'shelf' => $location->shelf,
                ],
            ]);
        });
    }

    public function recordLocationUpdate(
        Item $item,
        int $locationId,
        int $oldQuantity,
        int $newQuantity,
        User $user,
        ?string $notes = null
    ): StockMovement {
        return DB::transaction(function () use (
            $item, $locationId, $oldQuantity, $newQuantity, $user, $notes
        ) {
            $quantityDifference = $newQuantity - $oldQuantity;

            // Recalculate item stock
            $item->recalculateCurrentStock();

            $location = $item->locations()->findOrFail($locationId);

            return StockMovement::create([
                'item_id' => $item->id,
                'movement_type' => StockMovementType::ADJUSTMENT,
                'quantity' => $quantityDifference,
                'before_quantity' => $item->current_stock - $quantityDifference,
                'after_quantity' => $item->current_stock,
                'reference_type' => 'location',
                'reference_id' => $locationId,
                'location_id' => $locationId,
                'user_id' => $user->id,
                'notes' => $notes,
                'metadata' => [
                    'location_name' => $location->location_name,
                    'old_quantity' => $oldQuantity,
                    'new_quantity' => $newQuantity,
                ],
            ]);
        });
    }

    public function recordLocationDelete(
        Item $item,
        int $locationId,
        int $deletedQuantity,
        User $user,
        ?string $notes = null
    ): StockMovement {
        return DB::transaction(function () use (
            $item, $locationId, $deletedQuantity, $user, $notes
        ) {
            // Recalculate item stock
            $item->recalculateCurrentStock();

            return StockMovement::create([
                'item_id' => $item->id,
                'movement_type' => StockMovementType::ADJUSTMENT,
                'quantity' => -$deletedQuantity,
                'before_quantity' => $item->current_stock + $deletedQuantity,
                'after_quantity' => $item->current_stock,
                'reference_type' => 'location',
                'reference_id' => $locationId,
                'location_id' => null, // Location is deleted
                'user_id' => $user->id,
                'notes' => $notes,
                'metadata' => [
                    'deleted_location_id' => $locationId,
                    'deleted_quantity' => $deletedQuantity,
                ],
            ]);
        });
    }

    public function getMovementHistory(Item $item, int $limit = 50)
    {
        return StockMovement::byItem($item->id)
            ->with(['user', 'location'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getMovementSummary(Item $item, int $days = 30)
    {
        $movements = StockMovement::byItem($item->id)
            ->recent($days)
            ->get();

        return [
            'total_movements' => $movements->count(),
            'total_in' => $movements->where('movement_type', StockMovementType::IN)->sum('quantity'),
            'total_out' => abs($movements->where('movement_type', StockMovementType::OUT)->sum('quantity')),
            'net_change' => $movements->sum('quantity'),
            'movements_by_type' => $movements->groupBy('movement_type')->map->count(),
        ];
    }
}
