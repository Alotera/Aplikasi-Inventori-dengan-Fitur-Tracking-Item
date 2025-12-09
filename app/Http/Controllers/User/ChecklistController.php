<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\WorkInstruction;
use App\Models\ItemLocation;
use App\Models\StockMovement;
use App\Enums\StockMovementType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ChecklistController extends Controller
{
    public function store(Request $request, WorkInstruction $workInstruction, Item $item): RedirectResponse
    {
        $this->authorizeWI($workInstruction);

        $data = $request->validate([
            'type' => ['required', Rule::in(['checking', 'ambil'])],
            'actual_quantity' => ['nullable', 'integer', 'min:0'],
            'condition' => ['nullable', Rule::in(['good', 'not_good'])],
            'confirm_delivery' => ['nullable', 'boolean'],
            'from_location_id' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $pivotRecord = $workInstruction->items()->where('item_id', $item->id)->firstOrFail();
        $pivot = $pivotRecord->pivot;

        if ($data['type'] === 'checking') {
            $request->validate([
                'actual_quantity' => ['required', 'integer', 'min:0'],
                'condition' => ['required', Rule::in(['good', 'not_good'])],
            ]);

            $pivot->actual_quantity = (int) $data['actual_quantity'];
            $pivot->condition = $data['condition'];
            $pivot->status = 'completed';

            // Record movement for checking result (quantity neutral, metadata only)
            StockMovement::create([
                'item_id' => $item->id,
                'movement_type' => StockMovementType::CHECKING_RESULT,
                'quantity' => 0,
                'before_quantity' => $item->current_stock,
                'after_quantity' => $item->current_stock,
                'reference_type' => 'work_instruction',
                'reference_id' => $workInstruction->id,
                'location_id' => null,
                'user_id' => Auth::id(),
                'notes' => $data['notes'] ?? 'Checking result recorded',
                'metadata' => [
                    'required_quantity' => (int) $pivot->required_quantity,
                    'actual_quantity' => (int) $data['actual_quantity'],
                    'condition' => $data['condition'],
                    'wi_number' => $workInstruction->wi_number,
                ],
            ]);
        } else {
            // Additional validation for ambil type
            if (empty($workInstruction->destination_line)) {
                return back()->withErrors(['destination_line' => 'Work Instruction ini belum memiliki tujuan line yang jelas. Hubungi admin untuk melengkapi informasi.'])->withInput();
            }
            
            $request->validate([
                'confirm_delivery' => ['accepted'],
                'actual_quantity' => ['required', 'integer', 'min:1'],
                'from_location_id' => ['nullable', 'integer'],
            ]);

            $consumeQuantity = (int) $request->input('actual_quantity');

            // Optional: consume from a specific location if provided and valid
            $locationId = $request->input('from_location_id');
            $locationUsed = null;
            if (!empty($locationId)) {
                $locationUsed = ItemLocation::where('item_id', $item->id)->findOrFail((int) $locationId);
                if ($consumeQuantity > $locationUsed->quantity) {
                    return back()->withErrors(['actual_quantity' => 'Jumlah melebihi stok di lokasi terpilih.'])->withInput();
                }
                // Decrease location quantity
                $locationUsed->quantity -= $consumeQuantity;
                $locationUsed->save();
                // Recalculate item stock from locations to keep consistency
                $item->recalculateCurrentStock();
            } else {
                // If no location specified, ensure item has enough stock
                if ($consumeQuantity > $item->current_stock) {
                    return back()->withErrors(['actual_quantity' => 'Jumlah melebihi stok item.'])->withInput();
                }
                // Decrease global item stock directly to keep before/after correct
                $before = $item->current_stock;
                $item->current_stock = $before - $consumeQuantity;
                $item->save();
            }

            // Calculate before/after quantities for movement record
            $beforeQuantity = $item->current_stock + $consumeQuantity;
            $afterQuantity = $item->current_stock;

            // Record movement for WI consumption (stock out)
            StockMovement::create([
                'item_id' => $item->id,
                'movement_type' => StockMovementType::WI_CONSUMPTION,
                'quantity' => -$consumeQuantity,
                'before_quantity' => $beforeQuantity,
                'after_quantity' => $afterQuantity,
                'reference_type' => 'work_instruction',
                'reference_id' => $workInstruction->id,
                'location_id' => $locationUsed?->id,
                'user_id' => Auth::id(),
                'notes' => $data['notes'] ?? 'WI ambil - barang dikirim ke ' . ($workInstruction->destination_line ?? 'line produksi'),
                'metadata' => [
                    'wi_number' => $workInstruction->wi_number,
                    'wi_type' => 'ambil',
                    'destination_line' => $workInstruction->destination_line,
                    'dropoff_notes' => $workInstruction->dropoff_notes,
                    'from_location_id' => $locationUsed?->id,
                    'from_location_name' => $locationUsed?->location_name,
                    'delivery_confirmed' => true,
                    'actual_quantity_taken' => $consumeQuantity,
                ],
            ]);

            // Mark pivot completed
            $pivot->status = 'completed';
        }

        if (!empty($data['notes'])) {
            $pivot->notes = $data['notes'];
        }

        $pivot->save();

        $workInstruction->refresh();
        $workInstruction->updateStatus();

        return back()->with('success', 'Checklist berhasil disimpan.');
    }

    protected function authorizeWI(WorkInstruction $wi): void
    {
        if ($wi->assigned_user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengakses WI ini.');
        }
    }
}


