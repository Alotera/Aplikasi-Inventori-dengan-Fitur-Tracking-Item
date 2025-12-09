<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\WorkInstruction;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\ItemLocation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('admin.reports.index');
    }

    public function stock(Request $request): View
    {
        $query = StockMovement::with(['item', 'user', 'location'])
            ->orderBy('created_at', 'desc');

        // Apply filters
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

        $movements = $query->paginate(20);

        // Get filter options
        $items = Item::where('is_active', true)->orderBy('name')->get();
        $warehouseStaff = User::where('role', 'warehouse_staff')->where('is_active', true)->orderBy('name')->get();
        $movementTypes = [
            'IN' => 'Stock IN',
            'OUT' => 'Stock OUT',
            'CHECKING_RESULT' => 'Checking',
            'WI_CONSUMPTION' => 'Ambil',
        ];

        // Analytics data
        $analytics = $this->getStockAnalytics($request);
        
        return view('admin.reports.stock', compact('movements', 'items', 'warehouseStaff', 'movementTypes', 'analytics'));
    }


    private function getStockAnalytics(Request $request): array
    {
        $query = StockMovement::query();

        // Apply same filters as main query
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

        return [
            'total_movements' => $movements->count(),
            'stock_in_count' => $movements->where('movement_type', 'IN')->count(),
            'stock_out_count' => $movements->where('movement_type', 'OUT')->count(),
            'checking_count' => $movements->where('movement_type', 'CHECKING_RESULT')->count(),
            'adjustment_count' => $movements->where('movement_type', 'ADJUSTMENT')->count(),
            'wi_consumption_count' => $movements->where('movement_type', 'WI_CONSUMPTION')->count(),
            'total_stock_in' => $movements->where('movement_type', 'IN')->sum('quantity'),
            'total_stock_out' => abs($movements->where('movement_type', 'OUT')->sum('quantity')),
        ];
    }

    public function stockExport(Request $request): Response
    {
        $query = StockMovement::with(['item', 'user', 'location'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as main query
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
        $analytics = $this->getStockAnalytics($request);

        $pdf = Pdf::loadView('admin.reports.stock-export', compact('movements', 'analytics'));
        return $pdf->download('stock-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
