@extends('layouts.admin')

@section('title', __('admin.stock_movements.show_title'))
@section('page-title', __('admin.stock_movements.show_title'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-medium text-gray-900">Movement #{{ $stockMovement->id }}</h2>
            <p class="text-sm text-gray-600">{{ $stockMovement->created_at->format('M d, Y H:i:s') }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.stock-movements.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
            <a href="{{ route('admin.stock-movements.by-item', $stockMovement->item) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-list mr-2"></i>
                Item History
            </a>
        </div>
    </div>

    <!-- Movement Information -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Movement Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Movement Type</label>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $stockMovement->movement_type_color }}-100 text-{{ $stockMovement->movement_type_color }}-800">
                            {{ $stockMovement->movement_type_label }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Quantity Change</label>
                    <p class="mt-1 text-sm {{ $stockMovement->quantity >= 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                        {{ $stockMovement->formatted_quantity }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Stock Before</label>
                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ number_format($stockMovement->before_quantity) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Stock After</label>
                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ number_format($stockMovement->after_quantity) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Location</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $stockMovement->location?->location_name ?? 'No specific location' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Performed By</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $stockMovement->user->name }}</p>
                </div>
            </div>
            
            @if($stockMovement->notes)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-500">Notes</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $stockMovement->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Item Information -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Item Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Item Code</label>
                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $stockMovement->item->item_code }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Item Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $stockMovement->item->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Category</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $stockMovement->item->category ?? 'No category' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Current Stock</label>
                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ number_format($stockMovement->item->current_stock) }} {{ $stockMovement->item->unit }}</p>
                </div>
            </div>
            
            <div class="mt-6">
                <a href="{{ route('admin.items.show', $stockMovement->item) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-eye mr-2"></i>
                    View Item Details
                </a>
            </div>
        </div>
    </div>

    <!-- Location Details (if applicable) -->
    @if($stockMovement->location)
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Location Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Location Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $stockMovement->location->location_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Zone</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $stockMovement->location->zone ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Rack</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $stockMovement->location->rack ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Shelf</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $stockMovement->location->shelf ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Metadata (if available) -->
    @if($stockMovement->metadata)
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
            <div class="bg-gray-50 rounded-md p-4">
                <pre class="text-sm text-gray-700">{{ json_encode($stockMovement->metadata, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    </div>
    @endif

    <!-- Reference Information (if applicable) -->
    @if($stockMovement->reference_type && $stockMovement->reference)
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reference Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Reference Type</label>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $stockMovement->reference_type)) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Reference ID</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $stockMovement->reference_id }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
