<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ItemController extends Controller
{
    public function index(): View
    {
        $items = Item::with('itemLocations')->paginate(15);
        return view('admin.items.index', compact('items'));
    }

    public function create(): View
    {
        // Get active locations for dropdown
        $locations = Location::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'zone', 'rack', 'row'])
            ->map(function($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'zone' => $location->zone,
                    'rack' => $location->rack,
                    'row' => $location->row,
                    'label' => $location->display_name ?? $location->name,
                ];
            });
        
        return view('admin.items.create', compact('locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_code' => 'required|string|max:50|unique:items',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'minimum_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'location' => 'required|string|max:255',
        ]);

        // Create item with current_stock set to 0
        $item = Item::create([
            'item_code' => $validated['item_code'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'current_stock' => 0,
            'minimum_stock' => $validated['minimum_stock'],
            'unit' => $validated['unit'],
        ]);

        // Create item location with quantity 0
        ItemLocation::create([
            'item_id' => $item->id,
            'location_name' => $validated['location'],
            'quantity' => 0,
            'notes' => 'Default location for ' . $item->name,
        ]);

        return redirect()->route('admin.items.index')
                        ->with('success', 'Item berhasil dibuat!');
    }

    public function show(Item $item): View
    {
        $item->load('itemLocations');
        return view('admin.items.show', compact('item'));
    }

    public function edit(Item $item): View
    {
        $item->load('itemLocations');
        
        // Get active locations for dropdown
        $locations = Location::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'zone', 'rack', 'row'])
            ->map(function($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'zone' => $location->zone,
                    'rack' => $location->rack,
                    'row' => $location->row,
                    'label' => $location->display_name ?? $location->name,
                    'location_name' => $location->display_name ?? $location->name, // Add this for form compatibility
                ];
            });
        
        return view('admin.items.edit', compact('item', 'locations'));
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'item_code' => 'required|string|max:50|unique:items,item_code,' . $item->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'minimum_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'location' => 'required|string|max:255',
        ]);

        $item->update([
            'item_code' => $validated['item_code'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'minimum_stock' => $validated['minimum_stock'],
            'unit' => $validated['unit'],
        ]);

        // Update or create item location (keep current quantity)
        $itemLocation = $item->itemLocations()->first();
        if ($itemLocation) {
            $itemLocation->update([
                'location_name' => $validated['location'],
                // Keep the existing quantity, don't update it
            ]);
        } else {
            ItemLocation::create([
                'item_id' => $item->id,
                'location_name' => $validated['location'],
                'quantity' => 0, // Default to 0 if creating new location
                'notes' => 'Updated location for ' . $item->name,
            ]);
        }

        return redirect()->route('admin.items.index')
                        ->with('success', 'Item berhasil diperbarui!');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();
        return redirect()->route('admin.items.index')
                        ->with('success', 'Item berhasil dihapus!');
    }
}