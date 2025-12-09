<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\User;
use App\Enums\StockMovementType;
use App\Services\StockMovementService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class StockMovementController extends Controller
{
    public function __construct(
        private StockMovementService $stockMovementService
    ) {}

    public function index(Request $request): View
    {
        $query = StockMovement::with(['item', 'user', 'location'])
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->paginate(50);

        // Get filter options
        $items = Item::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $movementTypes = StockMovementType::cases();

        return view('admin.stock-movements.index', compact(
            'movements', 'items', 'users', 'movementTypes'
        ));
    }

    public function show(StockMovement $stockMovement): View
    {
        $stockMovement->load(['item', 'user', 'location', 'reference']);
        
        return view('admin.stock-movements.show', compact('stockMovement'));
    }

    public function byItem(Item $item, Request $request): View
    {
        $query = $item->stockMovements()
            ->with(['user', 'location'])
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->paginate(30);

        // Get movement summary
        $summary = $this->stockMovementService->getMovementSummary($item, 30);
        $movementTypes = StockMovementType::cases();

        return view('admin.stock-movements.by-item', compact(
            'item', 'movements', 'summary', 'movementTypes'
        ));
    }

    public function export(Request $request): JsonResponse
    {
        $query = StockMovement::with(['item', 'user', 'location'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->get();

        $data = $movements->map(function ($movement) {
            return [
                'Date' => $movement->created_at->format('Y-m-d H:i:s'),
                'Item Code' => $movement->item->item_code,
                'Item Name' => $movement->item->name,
                'Movement Type' => $movement->movement_type_label,
                'Quantity' => $movement->formatted_quantity,
                'Before Stock' => number_format($movement->before_quantity),
                'After Stock' => number_format($movement->after_quantity),
                'Location' => $movement->location?->location_name ?? '-',
                'User' => $movement->user->name,
                'Notes' => $movement->notes ?? '-',
            ];
        });

        return response()->json([
            'data' => $data,
            'filename' => 'stock_movements_' . now()->format('Y-m-d_H-i-s') . '.json'
        ]);
    }

    public function dashboard(): View
    {
        // Recent movements
        $recentMovements = StockMovement::with(['item', 'user', 'location'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Statistics
        $stats = [
            'total_movements_today' => StockMovement::whereDate('created_at', today())->count(),
            'total_movements_week' => StockMovement::where('created_at', '>=', now()->subWeek())->count(),
            'total_movements_month' => StockMovement::where('created_at', '>=', now()->subMonth())->count(),
            'stock_ins_today' => StockMovement::whereDate('created_at', today())
                ->where('movement_type', StockMovementType::IN)
                ->sum('quantity'),
            'stock_outs_today' => abs(StockMovement::whereDate('created_at', today())
                ->where('movement_type', StockMovementType::OUT)
                ->sum('quantity')),
        ];

        // Top active items (by movement count)
        $topActiveItems = StockMovement::selectRaw('item_id, COUNT(*) as movement_count')
            ->where('created_at', '>=', now()->subWeek())
            ->groupBy('item_id')
            ->orderBy('movement_count', 'desc')
            ->limit(10)
            ->with('item')
            ->get();

        // Movement types distribution
        $movementTypesStats = StockMovement::where('created_at', '>=', now()->subMonth())
            ->selectRaw('movement_type, COUNT(*) as count')
            ->groupBy('movement_type')
            ->get()
            ->pluck('count', 'movement_type');

        return view('admin.stock-movements.dashboard', compact(
            'recentMovements', 'stats', 'topActiveItems', 'movementTypesStats'
        ));
    }
}
