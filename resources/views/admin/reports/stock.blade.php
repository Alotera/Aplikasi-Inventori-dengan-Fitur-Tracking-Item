@extends('layouts.admin')

@section('title', 'Stock Report')
@section('page-title', 'Stock Movement Report')
@section('page-description', 'Laporan pergerakan stock barang dari warehouse staff')

@section('content')
<div class="space-y-6">
    <!-- Analytics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-line text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Movements</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($analytics['total_movements']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-plus-circle text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Stock IN</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($analytics['stock_in_count']) }} movements</dd>
                            <dd class="text-sm text-gray-500">{{ number_format($analytics['total_stock_in']) }} units</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-minus-circle text-red-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Stock OUT</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($analytics['stock_out_count']) }} movements</dd>
                            <dd class="text-sm text-gray-500">{{ number_format($analytics['total_stock_out']) }} units</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-search text-purple-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Checking Result</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($analytics['checking_count']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Analytics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-boxes text-orange-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">WI Consumption</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($analytics['wi_consumption_count']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-warehouse text-purple-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Staff</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $warehouseStaff->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-bar text-indigo-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Units IN</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($analytics['total_stock_in']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-filter mr-2 text-blue-500"></i>
                        Filter Stock Movements
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Gunakan filter di bawah untuk menyaring data stock movement</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Analytics
                    </span>
                </div>
            </div>
        </div>
        <form method="GET" action="{{ route('admin.reports.stock') }}" class="p-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-search mr-2 text-blue-500"></i>
                    Filter Data
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label for="item_id" class="block text-sm font-medium text-gray-700 mb-2">Item</label>
                        <select id="item_id" name="item_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Semua Item</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }} ({{ $item->item_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="movement_type" class="block text-sm font-medium text-gray-700 mb-2">Movement Type</label>
                        <select id="movement_type" name="movement_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Semua Tipe</option>
                            @foreach($movementTypes as $type => $label)
                                <option value="{{ $type }}" {{ request('movement_type') == $type ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Warehouse Staff</label>
                        <select id="user_id" name="user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Semua Staff</option>
                            @foreach($warehouseStaff as $staff)
                                <option value="{{ $staff->id }}" {{ request('user_id') == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                        <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
            </div>
            
            <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span class="text-sm text-gray-600">Gunakan filter untuk menyaring data stock movement</span>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.reports.stock') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <i class="fas fa-refresh mr-2"></i>
                        Reset
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('admin.reports.stock-export', request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-download mr-2"></i>
                        Export PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Stock Movements Table -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-history mr-2 text-indigo-500"></i>
                        Stock Movement History
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Riwayat lengkap pergerakan stock barang</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        <i class="fas fa-table mr-1"></i>
                        {{ $movements->count() }} records
                    </span>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            @if($movements->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Before</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">After</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($movements as $movement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $movement->item->name }}</div>
                                <div class="text-sm text-gray-500">{{ $movement->item->item_code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($movement->movement_type === 'IN') bg-green-100 text-green-800
                                    @elseif($movement->movement_type === 'OUT') bg-red-100 text-red-800
                                    @elseif($movement->movement_type === 'ADJUSTMENT') bg-blue-100 text-blue-800
                                    @elseif($movement->movement_type === 'CHECKING_RESULT') bg-purple-100 text-purple-800
                                    @elseif($movement->movement_type === 'WI_CONSUMPTION') bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    <i class="fas 
                                        @if($movement->movement_type === 'IN') fa-plus-circle
                                        @elseif($movement->movement_type === 'OUT') fa-minus-circle
                                        @elseif($movement->movement_type === 'ADJUSTMENT') fa-exchange-alt
                                        @elseif($movement->movement_type === 'CHECKING_RESULT') fa-search
                                        @elseif($movement->movement_type === 'WI_CONSUMPTION') fa-clipboard-list
                                        @else fa-question-circle
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
                                {{ number_format($movement->before_quantity) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($movement->after_quantity) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="text-sm font-medium text-gray-900">{{ $movement->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $movement->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $movement->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                {{ $movement->notes ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $movements->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">Tidak ada data stock movement</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
