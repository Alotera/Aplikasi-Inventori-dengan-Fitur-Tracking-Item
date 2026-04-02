<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\WorkInstruction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::withCount(['workInstructions as assigned_wi_count' => function($query) {
            $query->where('assigned_user_id', '!=', null);
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user,warehouse_staff',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        User::create($validated);

        return redirect()->route('admin.users.index')
                        ->with('success', __('messages.user.created'));
    }

    public function show(User $user): View
    {
        $user->loadCount(['workInstructions as assigned_wi_count' => function($query) use ($user) {
            $query->where('assigned_user_id', $user->id);
        }]);

        $recentWorkInstructions = collect();
        $wiStats = [
            'total' => 0,
            'completed' => 0,
            'not_started' => 0,
            'overdue' => 0,
            'in_progress' => 0,
        ];

        $recentStockMovements = collect();
        $stockStats = [
            'total_movements' => 0,
            'movements_30_days' => 0,
            'stock_in_30_days' => 0,
            'stock_out_30_days' => 0,
            'movements_today' => 0,
            'last_activity_at' => null,
        ];

        $recentManagedWorkInstructions = collect();
        $adminWiStats = [
            'total_wi' => 0,
            'wi_30_days' => 0,
            'completed' => 0,
            'overdue' => 0,
            'last_wi_created_at' => null,
        ];

        if ($user->role === 'warehouse_staff') {
            $thirtyDaysAgo = now()->subDays(30);

            $recentStockMovements = StockMovement::with('item')
                ->where('user_id', $user->id)
                ->latest()
                ->take(10)
                ->get();

            $stockStats['total_movements'] = StockMovement::where('user_id', $user->id)->count();
            $stockStats['movements_30_days'] = StockMovement::where('user_id', $user->id)
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->count();
            $stockStats['stock_in_30_days'] = StockMovement::where('user_id', $user->id)
                ->where('movement_type', 'IN')
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->count();
            $stockStats['stock_out_30_days'] = StockMovement::where('user_id', $user->id)
                ->where('movement_type', 'OUT')
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->count();
            $stockStats['movements_today'] = StockMovement::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->count();
            $stockStats['last_activity_at'] = StockMovement::where('user_id', $user->id)
                ->latest('created_at')
                ->value('created_at');
        } elseif ($user->role === 'admin') {
            $thirtyDaysAgo = now()->subDays(30);

            // No creator column exists on work instructions; use system-wide WI management metrics for admin role.
            $allManagedWorkInstructions = WorkInstruction::all();

            $recentManagedWorkInstructions = WorkInstruction::with(['assignedUser', 'items'])
                ->latest('created_at')
                ->take(10)
                ->get();

            $adminWiStats = [
                'total_wi' => $allManagedWorkInstructions->count(),
                'wi_30_days' => $allManagedWorkInstructions->filter(fn($wi) => $wi->created_at >= $thirtyDaysAgo)->count(),
                'completed' => $allManagedWorkInstructions->filter(fn($wi) => $wi->getMainStatus() === 'completed')->count(),
                'overdue' => $allManagedWorkInstructions->filter(fn($wi) => $wi->getMainStatus() === 'overdue')->count(),
                'last_wi_created_at' => $allManagedWorkInstructions->sortByDesc('created_at')->first()?->created_at,
            ];
        } else {
            $recentWorkInstructions = WorkInstruction::where('assigned_user_id', $user->id)
                ->with('items')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $workInstructions = WorkInstruction::where('assigned_user_id', $user->id)->get();

            $wiStats = [
                'total' => $workInstructions->count(),
                'completed' => $workInstructions->filter(fn($wi) => $wi->getMainStatus() === 'completed')->count(),
                'not_started' => $workInstructions->filter(fn($wi) => $wi->getMainStatus() === 'not_started')->count(),
                'overdue' => $workInstructions->filter(fn($wi) => $wi->getMainStatus() === 'overdue')->count(),
                'in_progress' => $workInstructions->filter(fn($wi) => $wi->getProgressionStatus() === 'in_progress')->count(),
            ];
        }

        return view('admin.users.show', compact(
            'user',
            'recentWorkInstructions',
            'wiStats',
            'recentStockMovements',
            'stockStats',
            'recentManagedWorkInstructions',
            'adminWiStats'
        ));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,user,warehouse_staff',
            'is_active' => 'boolean',
        ]);

        // Prevent admin from deactivating themselves
        if ($user->id === auth()->id() && !$request->has('is_active')) {
            return back()->withErrors(['is_active' => __('messages.user.cannot_deactivate_self')]);
        }

        // Prevent changing own role
        if ($user->id === auth()->id() && $validated['role'] !== $user->role) {
            return back()->withErrors(['role' => __('messages.user.cannot_change_own_role')]);
        }

        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');

        $user->update($validated);

        return redirect()->route('admin.users.index')
                        ->with('success', __('messages.user.updated'));
    }

    public function destroy(User $user): RedirectResponse
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => __('messages.user.cannot_delete_self')]);
        }

        // Check if user has work instructions
        if ($user->workInstructions()->count() > 0) {
            return back()->withErrors(['error' => __('messages.user.cannot_delete_has_wi')]);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                        ->with('success', __('messages.user.deleted'));
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        // Prevent admin from deactivating themselves
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => __('messages.user.cannot_deactivate_self')]);
        }

        $user->update(['is_active' => !$user->is_active]);
        $user->refresh();

        return back()->with('success', __('messages.user.status_changed', [
            'state' => $user->is_active ? __('messages.user.activated') : __('messages.user.deactivated'),
        ]));
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $user->update(['password' => Hash::make('password123')]);

        return back()->with('success', __('messages.user.password_reset'));
    }
}
