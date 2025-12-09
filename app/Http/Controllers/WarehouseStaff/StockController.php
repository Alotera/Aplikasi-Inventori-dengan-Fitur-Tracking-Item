<?php

namespace App\Http\Controllers\WarehouseStaff;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockMovement;
use App\Services\StockMovementService;
use App\Enums\StockMovementType;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockController extends Controller
{
    public function __construct(
        private StockMovementService $stockMovementService
    ) {}

    public function dashboard(): View
    {
        // Get statistics
        $totalItems = Item::where('is_active', true)->count();
        $lowStockItems = Item::where('is_active', true)
            ->whereRaw('current_stock <= minimum_stock')
            ->count();
        
        $recentMovements = StockMovement::with(['item', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $todayMovements = StockMovement::whereDate('created_at', today())
            ->count();

        $stats = [
            'total_items' => $totalItems,
            'low_stock_items' => $lowStockItems,
            'today_movements' => $todayMovements,
        ];

        return view('warehouse-staff.dashboard', compact('stats', 'recentMovements'));
    }

    public function stockInForm(): View
    {
        $items = Item::where('is_active', true)->orderBy('name')->get();
        
        return view('warehouse-staff.stock-in', compact('items'));
    }

    public function stockIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ], [
            'item_id.required' => 'Item harus dipilih.',
            'quantity.required' => 'Jumlah harus diisi.',
            'quantity.min' => 'Jumlah minimal 1.',
            'reason.required' => 'Alasan stock in harus diisi.',
        ]);

        try {
            DB::beginTransaction();

            $item = Item::findOrFail($validated['item_id']);
            $user = auth()->user();

            // Record stock movement
            $movement = $this->stockMovementService->recordMovement(
                $item,
                StockMovementType::IN,
                $validated['quantity'],
                $user,
                $validated['notes'] ?? null,
                'warehouse_stock_in',
                null,
                null,
                [
                    'reason' => $validated['reason'],
                    'movement_date' => now()->toDateString(),
                ]
            );

            DB::commit();

            return redirect()->route('warehouse-staff.dashboard')
                ->with('success', "Stock IN berhasil! {$item->name} ditambah {$validated['quantity']} {$item->unit}");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function stockOutForm(): View
    {
        $items = Item::where('is_active', true)->orderBy('name')->get();
        
        return view('warehouse-staff.stock-out', compact('items'));
    }

    public function stockOut(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ], [
            'item_id.required' => 'Item harus dipilih.',
            'quantity.required' => 'Jumlah harus diisi.',
            'quantity.min' => 'Jumlah minimal 1.',
            'reason.required' => 'Alasan stock out harus diisi.',
        ]);

        try {
            DB::beginTransaction();

            $item = Item::findOrFail($validated['item_id']);
            $user = auth()->user();

            // Check if sufficient stock
            if ($item->current_stock < $validated['quantity']) {
                return redirect()->back()
                    ->with('error', "Stock tidak mencukupi! Stock tersedia: {$item->current_stock} {$item->unit}")
                    ->withInput();
            }

            // Record stock movement (negative quantity for OUT)
            $movement = $this->stockMovementService->recordMovement(
                $item,
                StockMovementType::OUT,
                -$validated['quantity'], // Negative for OUT
                $user,
                $validated['notes'] ?? null,
                'warehouse_stock_out',
                null,
                null,
                [
                    'reason' => $validated['reason'],
                    'movement_date' => now()->toDateString(),
                ]
            );

            DB::commit();

            return redirect()->route('warehouse-staff.dashboard')
                ->with('success', "Stock OUT berhasil! {$item->name} dikurangi {$validated['quantity']} {$item->unit}");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function stockHistory(Request $request): View
    {
        $query = StockMovement::with(['item', 'user'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->paginate(20);

        // Get filter options
        $items = Item::where('is_active', true)->orderBy('name')->get();
        $movementTypes = [
            'IN' => 'Stock IN',
            'OUT' => 'Stock OUT',
           
        ];

        return view('warehouse-staff.stock-history', compact('movements', 'items', 'movementTypes'));
    }
}
