<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
                        ->with('success', 'User berhasil dibuat!');
    }

    public function show(User $user): View
    {
        $user->loadCount(['workInstructions as assigned_wi_count' => function($query) use ($user) {
            $query->where('assigned_user_id', $user->id);
        }]);

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

        return view('admin.users.show', compact('user', 'recentWorkInstructions', 'wiStats'));
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
            return back()->withErrors(['is_active' => 'Anda tidak dapat menonaktifkan akun sendiri.']);
        }

        // Prevent changing own role
        if ($user->id === auth()->id() && $validated['role'] !== $user->role) {
            return back()->withErrors(['role' => 'Anda tidak dapat mengubah role akun sendiri.']);
        }

        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');

        $user->update($validated);

        return redirect()->route('admin.users.index')
                        ->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(User $user): RedirectResponse
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun sendiri.']);
        }

        // Check if user has work instructions
        if ($user->workInstructions()->count() > 0) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus user yang memiliki work instructions.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                        ->with('success', 'User berhasil dihapus!');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        // Prevent admin from deactivating themselves
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menonaktifkan akun sendiri.']);
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return back()->with('success', "User berhasil {$status}!");
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $user->update(['password' => Hash::make('password123')]);

        return back()->with('success', 'Password user berhasil direset ke "password123"');
    }
}
