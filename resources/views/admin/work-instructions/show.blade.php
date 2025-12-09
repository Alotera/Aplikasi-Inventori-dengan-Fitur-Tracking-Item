@extends('layouts.admin')

@section('title', 'Work Instruction Details')
@section('page-title', 'Work Instruction Details')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-medium text-gray-900">{{ $workInstruction->wi_number }}</h2>
            <p class="text-sm text-gray-600">{{ $workInstruction->title }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.work-instructions.report-pdf', $workInstruction) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                <i class="fas fa-file-pdf mr-2"></i>
                Generate Report PDF
            </a>
            <a href="{{ route('admin.work-instructions.edit', $workInstruction) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
            <a href="{{ route('admin.work-instructions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Basic Information -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">WI Number</label>
                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $workInstruction->wi_number }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Type</label>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $workInstruction->type === 'checking' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            <i class="fas {{ $workInstruction->type === 'checking' ? 'fa-search' : 'fa-hand-holding' }} mr-1"></i>
                            {{ ucfirst($workInstruction->type) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <p class="mt-1">
                        <div class="space-y-2">
                            <!-- Main Status -->
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($workInstruction->getMainStatus() === 'completed') bg-green-100 text-green-800
                                @elseif($workInstruction->getMainStatus() === 'overdue') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                <i class="fas 
                                    @if($workInstruction->getMainStatus() === 'completed') fa-check-circle
                                    @elseif($workInstruction->getMainStatus() === 'overdue') fa-exclamation-triangle
                                    @else fa-clock
                                    @endif mr-2"></i>
                                {{ $workInstruction->getStatusLabel() }}
                            </span>
                            
                            <!-- Progression Status -->
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($workInstruction->getProgressionStatus() === 'completed') bg-green-50 text-green-700
                                @elseif($workInstruction->getProgressionStatus() === 'in_progress') bg-yellow-50 text-yellow-700
                                @else bg-gray-50 text-gray-700
                                @endif">
                                <i class="fas 
                                    @if($workInstruction->getProgressionStatus() === 'completed') fa-check
                                    @elseif($workInstruction->getProgressionStatus() === 'in_progress') fa-spinner
                                    @else fa-pause
                                    @endif mr-2"></i>
                                {{ $workInstruction->getProgressionLabel() }}
                            </span>
                        </div>
                    </p>
                </div>
                @if($workInstruction->type === 'ambil')
                <div class="md:col-span-2 lg:col-span-3">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-truck text-green-400"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-medium text-green-800 mb-2">
                                    <i class="fas fa-hand-holding mr-1"></i>
                                    Informasi Pengiriman - Work Instruction Tipe "Ambil"
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-green-700 mb-1">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            Tujuan Line Produksi
                                        </label>
                                        <p class="text-sm font-semibold text-green-900 bg-white px-3 py-2 rounded border">
                                            {{ $workInstruction->destination_line ?? 'Tidak ditentukan' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-green-700 mb-1">
                                            <i class="fas fa-sticky-note mr-1"></i>
                                            Catatan Pengiriman
                                        </label>
                                        <p class="text-sm text-green-900 bg-white px-3 py-2 rounded border min-h-[40px]">
                                            {{ $workInstruction->dropoff_notes ?? 'Tidak ada catatan tambahan' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-t border-green-200">
                                    <p class="text-xs text-green-600">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        User akan mengambil barang dan mengirimkannya ke tujuan yang telah ditentukan, kemudian mengonfirmasi pengiriman melalui checklist.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-500">Assigned To</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $workInstruction->assignedUser->name }}</p>
                    <p class="text-xs text-gray-500">{{ $workInstruction->assignedUser->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Deadline</label>
                    <p class="mt-1 text-sm text-gray-900 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                        {{ $workInstruction->deadline->format('d M Y H:i') }}
                        @if($workInstruction->isOverdue())
                            <i class="fas fa-exclamation-triangle ml-2 text-red-500" title="Overdue"></i>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Created</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $workInstruction->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
            
            @if($workInstruction->description)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-500">Description</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $workInstruction->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Items List -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Items in this Work Instruction</h3>
            
            @if($workInstruction->items->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Required Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actual Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($workInstruction->items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item->item_code }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->category ?? 'No category' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($item->pivot->required_quantity) }} {{ $item->unit }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($item->pivot->actual_quantity)
                                        {{ number_format($item->pivot->actual_quantity) }} {{ $item->unit }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->pivot->condition)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->pivot->condition === 'good' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            <i class="fas {{ $item->pivot->condition === 'good' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                            {{ ucfirst($item->pivot->condition) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($item->pivot->status === 'completed') bg-green-100 text-green-800
                                        @elseif($item->pivot->status === 'not_good') bg-orange-100 text-orange-800
                                        @elseif($item->pivot->status === 'not_found') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        <i class="fas 
                                            @if($item->pivot->status === 'completed') fa-check-circle
                                            @elseif($item->pivot->status === 'not_good') fa-exclamation-triangle
                                            @elseif($item->pivot->status === 'not_found') fa-times-circle
                                            @else fa-clock
                                            @endif mr-1"></i>
                                        {{ $item->pivot->status === 'not_good' ? 'Discrepancy' : ucfirst(str_replace('_', ' ', $item->pivot->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $item->pivot->notes ?? '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-box text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No items assigned to this work instruction</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Progress Summary -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Progress Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-{{ $workInstruction->type === 'checking' ? '4' : '3' }} gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $workInstruction->items->count() }}</div>
                    <div class="text-sm text-gray-500">Total Items</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $workInstruction->items->where('pivot.status', 'completed')->count() }}</div>
                    <div class="text-sm text-gray-500">Completed</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $workInstruction->items->where('pivot.status', 'pending')->count() }}</div>
                    <div class="text-sm text-gray-500">Pending</div>
                </div>
                @if($workInstruction->type === 'checking')
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $workInstruction->items->where('pivot.status', 'not_good')->count() }}</div>
                    <div class="text-sm text-gray-500">Discrepancy</div>
                </div>
                @endif
            </div>
            
            @if($workInstruction->items->count() > 0)
                <div class="mt-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Progress</span>
                        <span>{{ round(($workInstruction->items->where('pivot.status', 'completed')->count() / $workInstruction->items->count()) * 100) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($workInstruction->items->where('pivot.status', 'completed')->count() / $workInstruction->items->count()) * 100 }}%"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
