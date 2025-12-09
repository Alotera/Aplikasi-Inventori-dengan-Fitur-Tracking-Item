<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemLocation;
use App\Services\StockMovementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ItemLocationController extends Controller
{
    public function __construct(
        private StockMovementService $stockMovementService
    ) {}

    public function create(Item $item): View
    {
        return view('admin.items.locations.create', compact('item'));
    }

    public function store(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'location_name' => 'required|string|max:255',
            'zone' => 'nullable|string|max:100',
            'rack' => 'nullable|string|max:100',
            'shelf' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:0',
        ]);

        // Enforce unique location name per item at application level too
        $exists = ItemLocation::where('item_id', $item->id)
            ->where('location_name', $validated['location_name'])
            ->exists();
        if ($exists) {
            return back()->withErrors(['location_name' => 'Location name already exists for this item.'])->withInput();
        }

        DB::transaction(function () use ($item, $validated): void {
            $location = $item->locations()->create($validated);
            $item->recalculateCurrentStock();
            
            // Record stock movement for location creation
            $this->stockMovementService->recordLocationCreate(
                $item,
                $location->id,
                $validated['quantity'],
                auth()->user(),
                'Location created: ' . $validated['location_name']
            );
        });

        return redirect()->route('admin.items.show', $item)
            ->with('success', 'Location created successfully.');
    }

    public function edit(Item $item, ItemLocation $location): View
    {
        // Ensure the location belongs to the item
        abort_unless($location->item_id === $item->id, 404);
        return view('admin.items.locations.edit', compact('item', 'location'));
    }

    public function update(Request $request, Item $item, ItemLocation $location): RedirectResponse
    {
        abort_unless($location->item_id === $item->id, 404);

        $validated = $request->validate([
            'location_name' => 'required|string|max:255',
            'zone' => 'nullable|string|max:100',
            'rack' => 'nullable|string|max:100',
            'shelf' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:0',
        ]);

        $duplicateName = ItemLocation::where('item_id', $item->id)
            ->where('location_name', $validated['location_name'])
            ->where('id', '!=', $location->id)
            ->exists();
        if ($duplicateName) {
            return back()->withErrors(['location_name' => 'Location name already exists for this item.'])->withInput();
        }

        DB::transaction(function () use ($item, $location, $validated): void {
            $oldQuantity = $location->quantity;
            $location->update($validated);
            $item->recalculateCurrentStock();
            
            // Record stock movement for location update
            $this->stockMovementService->recordLocationUpdate(
                $item,
                $location->id,
                $oldQuantity,
                $validated['quantity'],
                auth()->user(),
                'Location updated: ' . $validated['location_name']
            );
        });

        return redirect()->route('admin.items.show', $item)
            ->with('success', 'Location updated successfully.');
    }

    public function destroy(Item $item, ItemLocation $location): RedirectResponse
    {
        abort_unless($location->item_id === $item->id, 404);

        DB::transaction(function () use ($item, $location): void {
            $deletedQuantity = $location->quantity;
            $locationName = $location->location_name;
            $location->delete();
            $item->recalculateCurrentStock();
            
            // Record stock movement for location deletion
            $this->stockMovementService->recordLocationDelete(
                $item,
                $location->id,
                $deletedQuantity,
                auth()->user(),
                'Location deleted: ' . $locationName
            );
        });

        return redirect()->route('admin.items.show', $item)
            ->with('success', 'Location deleted successfully.');
    }

    public function transfer(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'from_location_id' => 'required|integer|different:to_location_id',
            'to_location_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $from = ItemLocation::where('item_id', $item->id)->findOrFail($validated['from_location_id']);
        $to = ItemLocation::where('item_id', $item->id)->findOrFail($validated['to_location_id']);

        if ($from->id === $to->id) {
            return back()->withErrors(['to_location_id' => 'Destination must be different from source.'])->withInput();
        }

        if ($validated['quantity'] > $from->quantity) {
            return back()->withErrors(['quantity' => 'Quantity exceeds available stock in source location.'])->withInput();
        }

        DB::transaction(function () use ($item, $from, $to, $validated): void {
            $from->quantity -= (int) $validated['quantity'];
            $to->quantity += (int) $validated['quantity'];
            $from->save();
            $to->save();
            $item->recalculateCurrentStock();
            
            // Record stock movement for transfer
            $this->stockMovementService->recordLocationTransfer(
                $item,
                $from->id,
                $to->id,
                (int) $validated['quantity'],
                auth()->user(),
                $validated['notes'] ?? 'Transfer between locations'
            );
        });

        return redirect()->route('admin.items.show', $item)
            ->with('success', 'Quantity transferred successfully.');
    }
}


