@extends('layouts.warehouse-staff')

@section('title', 'Stock History')
@section('page-title', 'Stock History')
@section('page-description', 'Riwayat pergerakan stock barang')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-filter mr-2"></i>
                Filter Stock History
            </h3>
        </div>
        <form method="GET" action="{{ route('warehouse-staff.stock-history') }}" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                    <label for="movement_type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Movement</label>
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
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-4">
                <a href="{{ route('warehouse-staff.stock-history') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-refresh mr-2"></i>
                    Reset
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Stock History Table -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-history mr-2"></i>
                Stock Movement History
            </h3>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
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
                                    @elseif($movement->movement_type === 'CHECKING_RESULT') bg-purple-100 text-purple-800
                                    @elseif($movement->movement_type === 'WI_CONSUMPTION') bg-orange-100 text-orange-800
                                    @elseif($movement->movement_type === 'ADJUSTMENT') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    <i class="fas 
                                        @if($movement->movement_type === 'IN') fa-plus-circle
                                        @elseif($movement->movement_type === 'OUT') fa-minus-circle
                                        @elseif($movement->movement_type === 'CHECKING_RESULT') fa-search
                                        @elseif($movement->movement_type === 'WI_CONSUMPTION') fa-clipboard-list
                                        @elseif($movement->movement_type === 'ADJUSTMENT') fa-exchange-alt
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
                                {{ $movement->user->name }}
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
