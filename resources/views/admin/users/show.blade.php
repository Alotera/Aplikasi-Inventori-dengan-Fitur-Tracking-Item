@extends('layouts.admin')

@section('title', __('admin.users.show_title'))
@section('page-title', __('admin.users.show_title'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-medium text-gray-900">{{ $user->name }}</h2>
            <p class="text-sm text-gray-600">{{ $user->email }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i>
                Edit User
            </a>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- User Information -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">User Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Full Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Email Address</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">User ID</label>
                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $user->id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Role</label>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            <i class="fas {{ $user->role === 'admin' ? 'fa-crown' : 'fa-user' }} mr-1"></i>
                            {{ ucfirst($user->role) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <p class="mt-1">
                        @if($user->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>
                                Inactive
                            </span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Account Created</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Last Login</label>
                    <p class="mt-1 text-sm text-gray-900">
                        @if($user->last_login_at)
                            {{ \Carbon\Carbon::parse($user->last_login_at)->format('d M Y H:i') }}
                            <span class="text-gray-500">({{ \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() }})</span>
                        @else
                            <span class="text-gray-400">Never logged in</span>
                        @endif
                    </p>
                </div>
                @if($user->role === 'admin')
                <div>
                    <label class="block text-sm font-medium text-gray-500">Last WI Created</label>
                    <p class="mt-1 text-sm text-gray-900">
                        @if($adminWiStats['last_wi_created_at'])
                            {{ \Carbon\Carbon::parse($adminWiStats['last_wi_created_at'])->format('d M Y H:i') }}
                            <span class="text-gray-500">({{ \Carbon\Carbon::parse($adminWiStats['last_wi_created_at'])->diffForHumans() }})</span>
                        @else
                            <span class="text-gray-400">No WI activity yet</span>
                        @endif
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($user->role === 'warehouse_staff')
    <!-- Stock Activity Statistics -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Stock Activity Statistics</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $stockStats['total_movements'] }}</div>
                    <div class="text-sm text-gray-500">Total Movements</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $stockStats['movements_30_days'] }}</div>
                    <div class="text-sm text-gray-500">Last 30 Days</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $stockStats['stock_in_30_days'] }}</div>
                    <div class="text-sm text-gray-500">Stock IN (30 Days)</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $stockStats['stock_out_30_days'] }}</div>
                    <div class="text-sm text-gray-500">Stock OUT (30 Days)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Activity Summary -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Operational Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-500">Last Stock Activity</p>
                    <p class="mt-1 text-sm text-gray-900">
                        @if($stockStats['last_activity_at'])
                            {{ \Carbon\Carbon::parse($stockStats['last_activity_at'])->format('d M Y H:i') }}
                            <span class="text-gray-500">({{ \Carbon\Carbon::parse($stockStats['last_activity_at'])->diffForHumans() }})</span>
                        @else
                            <span class="text-gray-400">No activity yet</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Today's Activities</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $stockStats['movements_today'] }} movements</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">IN/OUT Ratio (30 Days)</p>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ $stockStats['stock_in_30_days'] }} : {{ $stockStats['stock_out_30_days'] }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Stock Movements -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Stock Movements</h3>

            @if($recentStockMovements->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Before -> After</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentStockMovements as $movement)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $movement->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $movement->item?->name ?? '-' }}</div>
                                    <div class="text-sm text-gray-500">{{ $movement->item?->item_code ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $movement->movement_type_color }}">
                                        {{ $movement->movement_type_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $movement->quantity >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movement->formatted_quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($movement->before_quantity ?? 0) }} -> {{ number_format($movement->after_quantity ?? 0) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                    {{ $movement->notes ?: '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">Belum ada aktivitas stok dari user ini</p>
                </div>
            @endif
        </div>
    </div>
    @elseif($user->role === 'admin')
    <!-- Admin Management Statistics -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Admin Management Statistics</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $adminWiStats['total_wi'] }}</div>
                    <div class="text-sm text-gray-500">Total Work Instructions</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $adminWiStats['wi_30_days'] }}</div>
                    <div class="text-sm text-gray-500">Created (30 Days)</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $adminWiStats['completed'] }}</div>
                    <div class="text-sm text-gray-500">Completed</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $adminWiStats['overdue'] }}</div>
                    <div class="text-sm text-gray-500">Overdue</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Managed Work Instructions -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Managed Work Instructions</h3>

            @if($recentManagedWorkInstructions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">WI Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentManagedWorkInstructions as $wi)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <a href="{{ route('admin.work-instructions.show', $wi) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $wi->wi_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $wi->type === 'checking' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        <i class="fas {{ $wi->type === 'checking' ? 'fa-search' : 'fa-hand-holding' }} mr-1"></i>
                                        {{ ucfirst($wi->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $wi->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $wi->assignedUser?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $wi->deadline->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($wi->status === 'completed') bg-green-100 text-green-800
                                        @elseif($wi->status === 'overdue') bg-red-100 text-red-800
                                        @elseif($wi->status === 'in_progress') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        <i class="fas
                                            @if($wi->status === 'completed') fa-check-circle
                                            @elseif($wi->status === 'overdue') fa-exclamation-triangle
                                            @elseif($wi->status === 'in_progress') fa-clock
                                            @else fa-pause-circle
                                            @endif mr-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $wi->status)) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">Belum ada work instructions untuk dimonitor</p>
                </div>
            @endif
        </div>
    </div>
    @else
    <!-- Work Instruction Statistics -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Work Instruction Statistics</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $wiStats['total'] }}</div>
                    <div class="text-sm text-gray-500">Total Assigned</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $wiStats['completed'] }}</div>
                    <div class="text-sm text-gray-500">Completed</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $wiStats['in_progress'] }}</div>
                    <div class="text-sm text-gray-500">In Progress</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $wiStats['overdue'] }}</div>
                    <div class="text-sm text-gray-500">Overdue</div>
                </div>
            </div>

            @if($wiStats['total'] > 0)
                <div class="mt-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Completion Rate</span>
                        <span>{{ round(($wiStats['completed'] / $wiStats['total']) * 100) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($wiStats['completed'] / $wiStats['total']) * 100 }}%"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Work Instructions -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Work Instructions</h3>

            @if($recentWorkInstructions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">WI Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentWorkInstructions as $wi)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <a href="{{ route('admin.work-instructions.show', $wi) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $wi->wi_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $wi->type === 'checking' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        <i class="fas {{ $wi->type === 'checking' ? 'fa-search' : 'fa-hand-holding' }} mr-1"></i>
                                        {{ ucfirst($wi->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $wi->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-alt mr-1 text-gray-400"></i>
                                        {{ $wi->deadline->format('d M Y H:i') }}
                                        @if($wi->isOverdue())
                                            <i class="fas fa-exclamation-triangle ml-2 text-red-500" title="Overdue"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($wi->status === 'completed') bg-green-100 text-green-800
                                        @elseif($wi->status === 'overdue') bg-red-100 text-red-800
                                        @elseif($wi->status === 'in_progress') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        <i class="fas
                                            @if($wi->status === 'completed') fa-check-circle
                                            @elseif($wi->status === 'overdue') fa-exclamation-triangle
                                            @elseif($wi->status === 'in_progress') fa-clock
                                            @else fa-pause-circle
                                            @endif mr-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $wi->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-box mr-1"></i>
                                        {{ $wi->items->count() }} items
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No work instructions assigned to this user</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    @if($user->id !== auth()->id())
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="flex flex-wrap gap-3">
                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white {{ $user->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                        <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }} mr-2"></i>
                        {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                    </button>
                </form>
                
                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="inline" onsubmit="return confirm('Reset password user ini ke \"password123\"?')">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                        <i class="fas fa-key mr-2"></i>
                        Reset Password
                    </button>
                </form>
                
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i>
                        Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
