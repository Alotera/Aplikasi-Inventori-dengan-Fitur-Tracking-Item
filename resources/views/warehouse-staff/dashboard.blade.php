@extends('layouts.warehouse-staff')

@section('title', 'Warehouse Staff Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Overview stock management dan aktivitas warehouse')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 rounded-full bg-blue-100">
                            <i class="fas fa-boxes text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Items</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['total_items'] }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 rounded-full bg-yellow-100">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-500 truncate">Low Stock Items</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['low_stock_items'] }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 rounded-full bg-green-100">
                            <i class="fas fa-exchange-alt text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-500 truncate">Today's Movements</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['today_movements'] }}</dd>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('warehouse-staff.stock-in') }}" 
                   class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-plus text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-semibold text-gray-800">Stock IN</h4>
                        <p class="text-sm text-gray-600">Tambah stock barang</p>
                    </div>
                </a>

                <a href="{{ route('warehouse-staff.stock-out') }}" 
                   class="flex items-center p-4 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                    <div class="p-3 bg-red-100 rounded-full">
                        <i class="fas fa-minus text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-semibold text-gray-800">Stock OUT</h4>
                        <p class="text-sm text-gray-600">Kurangi stock barang</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Stock Movements -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Recent Stock Movements</h3>
                <a href="{{ route('warehouse-staff.stock-history') }}" 
                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            @if($recentMovements->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentMovements as $movement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $movement->item->name }}</div>
                                <div class="text-sm text-gray-500">{{ $movement->item->item_code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($movement->movement_type === 'IN') bg-green-100 text-green-800
                                    @elseif($movement->movement_type === 'OUT') bg-red-100 text-red-800
                                    @elseif($movement->movement_type === 'CHECKING_RESULT') bg-purple-100 text-purple-800
                                    @elseif($movement->movement_type === 'WI_CONSUMPTION') bg-orange-100 text-orange-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    <i class="fas 
                                        @if($movement->movement_type === 'IN') fa-plus-circle
                                        @elseif($movement->movement_type === 'OUT') fa-minus-circle
                                        @elseif($movement->movement_type === 'CHECKING_RESULT') fa-search
                                        @elseif($movement->movement_type === 'WI_CONSUMPTION') fa-clipboard-list
                                        @else fa-exchange-alt
                                        @endif mr-1"></i>
                                    {{ $movement->movement_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="font-medium
                                    @if($movement->movement_type === 'IN') text-green-600
                                    @elseif($movement->movement_type === 'OUT') text-red-600
                                    @else text-gray-600
                                    @endif">
                                    {{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity) }}
                                </span>
                                <span class="text-gray-500">{{ $movement->item->unit }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $movement->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $movement->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">Belum ada stock movement</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
