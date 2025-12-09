@extends('layouts.user')

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
            @if($workInstruction->getProgressionStatus() === 'in_progress')
                <button type="button" onclick="scrollToItems()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-edit mr-2"></i>
                    Update Progress
                </button>
                <form method="POST" action="{{ route('user.work-instructions.complete', $workInstruction) }}" class="inline" onsubmit="return confirm('Are you sure you want to complete this work instruction?')">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-check mr-2"></i>
                        Complete Work Instruction
                    </button>
                </form>
            @endif
            <a href="{{ route('user.work-instructions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
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
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($workInstruction->getMainStatus() === 'completed') bg-green-100 text-green-800
                                @elseif($workInstruction->getMainStatus() === 'overdue') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                <i class="fas 
                                    @if($workInstruction->getMainStatus() === 'completed') fa-check-circle
                                    @elseif($workInstruction->getMainStatus() === 'overdue') fa-exclamation-triangle
                                    @else fa-clock
                                    @endif mr-1"></i>
                                {{ $workInstruction->getStatusLabel() }}
                            </span>
                            
                            <!-- Progression Status -->
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($workInstruction->getProgressionStatus() === 'completed') bg-green-50 text-green-700
                                @elseif($workInstruction->getProgressionStatus() === 'in_progress') bg-yellow-50 text-yellow-700
                                @else bg-gray-50 text-gray-700
                                @endif">
                                <i class="fas 
                                    @if($workInstruction->getProgressionStatus() === 'completed') fa-check
                                    @elseif($workInstruction->getProgressionStatus() === 'in_progress') fa-spinner
                                    @else fa-pause
                                    @endif mr-1"></i>
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
                                        Ambil barang dari gudang dan kirim ke tujuan yang telah ditentukan, kemudian konfirmasi pengiriman.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif($workInstruction->type === 'checking')
                <div class="md:col-span-2 lg:col-span-3">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-search text-blue-400"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-medium text-blue-800 mb-2">
                                    <i class="fas fa-search mr-1"></i>
                                    Informasi Pengecekan - Work Instruction Tipe "Checking"
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-blue-700 mb-1">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            Lokasi Pengecekan
                                        </label>
                                        <p class="text-sm font-semibold text-blue-900 bg-white px-3 py-2 rounded border">
                                            Lihat lokasi per item di bawah
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-blue-700 mb-1">
                                            <i class="fas fa-clipboard-check mr-1"></i>
                                            Tugas Pengecekan
                                        </label>
                                        <p class="text-sm text-blue-900 bg-white px-3 py-2 rounded border min-h-[40px]">
                                            Cek kondisi fisik dan hitung quantity setiap item sesuai dengan yang tertera. Stock akan diupdate otomatis.
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-t border-blue-200">
                                    <p class="text-xs text-blue-600">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Pergi ke lokasi item dan lakukan pengecekan fisik, catat quantity actual dan kondisi item. Stock akan diupdate otomatis.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Deadline</label>
                    <p class="mt-1 text-sm text-gray-900 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                        {{ $workInstruction->deadline->format('d M Y H:i') }}
                        @if($workInstruction->getMainStatus() === 'overdue')
                            <i class="fas fa-exclamation-triangle ml-2 text-red-500" title="Terlambat"></i>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Created</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $workInstruction->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Items Count</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $workInstruction->items->count() }} items</p>
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

    <!-- Items List with Type-Specific Interface -->
    <div id="items-section" class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Items in this Work Instruction</h3>
            
            @if($workInstruction->items->count() > 0)
                <div class="space-y-4">
                    @foreach($workInstruction->items as $item)
                    <div class="border border-gray-200 rounded-lg p-4 {{ $item->pivot->status === 'completed' ? 'bg-green-50' : ($item->pivot->status === 'not_good' ? 'bg-orange-50' : 'bg-gray-50') }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-3">
                                    <h4 class="text-lg font-medium text-gray-900">{{ $item->name }}</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $item->item_code }}
                                    </span>
                                    @if($item->pivot->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Completed
                                        </span>
                                    @elseif($item->pivot->status === 'not_good')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Discrepancy
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Pending
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Location</label>
                                        <p class="text-sm font-semibold text-gray-900 flex items-center">
                                            <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                                            {{ $item->itemLocations->first()->location_name ?? 'No location set' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Required Quantity</label>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ number_format($item->pivot->required_quantity) }} {{ $item->unit }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Type-Specific Interface -->
                                @if($item->pivot->status === 'pending')
                                    @if($workInstruction->type === 'ambil')
                                        <!-- AMBIL: Simple Checklist Interface -->
                                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                                            <h5 class="text-sm font-medium text-gray-900 mb-3">
                                                <i class="fas fa-hand-holding mr-1 text-green-500"></i>
                                                Checklist Pengambilan
                                            </h5>
                                            
                                            <form method="POST" action="{{ route('user.work-instructions.update-item', $workInstruction) }}" class="space-y-4">
                                                @csrf
                                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                <input type="hidden" name="actual_quantity" value="{{ $item->pivot->required_quantity }}">
                                                <input type="hidden" name="condition" value="good">
                                                <input type="hidden" name="status" value="completed">
                                                
                                                <div class="flex items-center space-x-4">
                                                    <div class="flex items-center">
                                                        
                                                        
                                                    </div>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 gap-4">
                                                    <div>
                                                        <label for="notes_{{ $item->id }}" class="block text-sm font-medium text-gray-700 mb-1">Catatan Pengiriman</label>
                                                        <input type="text" name="notes" id="notes_{{ $item->id }}" 
                                                               value="{{ $item->pivot->notes }}" 
                                                               placeholder="Optional notes..."
                                                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                                    </div>
                                                </div>
                                                
                                                <div class="flex justify-end">
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                                        <i class="fas fa-hand-holding mr-2"></i>
                                                        Konfirmasi Pengambilan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @else
                                        <!-- CHECKING: Form Interface -->
                                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                                            <h5 class="text-sm font-medium text-gray-900 mb-3">
                                                <i class="fas fa-search mr-1 text-blue-500"></i>
                                                Update Checking Progress
                                            </h5>
                                            
                                            <form method="POST" action="{{ route('user.work-instructions.update-item', $workInstruction) }}" class="space-y-4">
                                                @csrf
                                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label for="actual_quantity_{{ $item->id }}" class="block text-sm font-medium text-gray-700 mb-1">Actual Quantity *</label>
                                                        <input type="number" name="actual_quantity" id="actual_quantity_{{ $item->id }}" 
                                                               value="{{ $item->pivot->actual_quantity }}" min="0" required
                                                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                               onchange="updateStatusBasedOnQuantity({{ $item->id }}, {{ $item->pivot->required_quantity }})"
                                                               oninput="updateStatusBasedOnQuantity({{ $item->id }}, {{ $item->pivot->required_quantity }})">
                                                    </div>
                                                    
                                                    <div>
                                                        <label for="condition_{{ $item->id }}" class="block text-sm font-medium text-gray-700 mb-1">Condition *</label>
                                                        <select name="condition" id="condition_{{ $item->id }}" required
                                                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                            <option value="">Select Condition</option>
                                                            <option value="good" {{ $item->pivot->condition === 'good' ? 'selected' : '' }}>Good</option>
                                                            <option value="not_good" {{ $item->pivot->condition === 'not_good' ? 'selected' : '' }}>Discrepancy</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div>
                                                        <label for="status_{{ $item->id }}" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                                                        <select name="status" id="status_{{ $item->id }}" required readonly
                                                                class="block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed sm:text-sm"
                                                                style="pointer-events: none;">
                                                            <option value="completed" {{ $item->pivot->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                            <option value="not_good" {{ $item->pivot->status === 'not_good' ? 'selected' : '' }}>Discrepancy</option>
                                                        </select>
                                                        <p class="mt-1 text-xs text-gray-500">Status ditentukan otomatis berdasarkan quantity</p>
                                                    </div>
                                                    
                                                    <div>
                                                        <label for="notes_{{ $item->id }}" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                                        <input type="text" name="notes" id="notes_{{ $item->id }}" 
                                                               value="{{ $item->pivot->notes }}" 
                                                               placeholder="Optional notes..."
                                                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                    </div>
                                                </div>
                                                
                                                <div class="flex justify-end">
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                                        <i class="fas fa-check mr-2"></i>
                                                        Mark as Checked
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                @elseif($item->pivot->status === 'not_good')
                                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>
                                                <span class="text-sm font-medium text-orange-800">
                                                    @if($workInstruction->type === 'checking')
                                                        Item has been checked (Quantity mismatch)
                                                    @else
                                                        Item has been taken (Condition not good)
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xs text-orange-600">
                                                    <span class="font-medium">Actual Qty:</span> {{ number_format($item->pivot->actual_quantity ?? 0) }} {{ $item->unit }}
                                                </div>
                                                <div class="text-xs text-orange-600">
                                                    <span class="font-medium">Condition:</span> {{ ucfirst(str_replace('_', ' ', $item->pivot->condition ?? 'N/A')) }}
                                                </div>
                                            </div>
                                        </div>
                                        @if($item->pivot->notes)
                                            <p class="text-xs text-orange-600 mt-1">{{ $item->pivot->notes }}</p>
                                        @endif
                                    </div>
                                @elseif($item->pivot->status === 'completed')
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                                <span class="text-sm font-medium text-green-800">
                                                    @if($workInstruction->type === 'checking')
                                                        Item has been checked successfully
                                                    @else
                                                        Item has been taken and delivered
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xs text-green-600">
                                                    <span class="font-medium">Actual Qty:</span> {{ number_format($item->pivot->actual_quantity ?? 0) }} {{ $item->unit }}
                                                </div>
                                                <div class="text-xs text-green-600">
                                                    <span class="font-medium">Condition:</span> {{ ucfirst(str_replace('_', ' ', $item->pivot->condition ?? 'N/A')) }}
                                                </div>
                                            </div>
                                        </div>
                                        @if($item->pivot->notes)
                                            <p class="text-xs text-green-600 mt-1">{{ $item->pivot->notes }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
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
                    <div class="text-2xl font-bold text-green-600">{{ $workInstruction->items->where('pivot.status', '!=', 'pending')->count() }}</div>
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
                        <span>{{ $workInstruction->calculateProgressPercentage() }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $workInstruction->calculateProgressPercentage() }}%"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function scrollToItems() {
    document.getElementById('items-section').scrollIntoView({ behavior: 'smooth' });
}

function updateStatusBasedOnQuantity(itemId, requiredQuantity) {
    const actualQuantityInput = document.getElementById('actual_quantity_' + itemId);
    const statusSelect = document.getElementById('status_' + itemId);
    
    if (actualQuantityInput && statusSelect) {
        const actualQuantity = parseInt(actualQuantityInput.value) || 0;
        
        if (actualQuantity === requiredQuantity) {
            statusSelect.value = 'completed';
        } else {
            statusSelect.value = 'not_good';
        }
        
        // Update visual feedback
        if (actualQuantity === requiredQuantity) {
            statusSelect.style.backgroundColor = '#dcfce7'; // green background
        } else {
            statusSelect.style.backgroundColor = '#fef2f2'; // red background
        }
    }
}
</script>
@endsection