@extends('layouts.admin')

@section('title', 'Item Details')
@section('page-title', 'Item Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-medium text-gray-900">{{ $item->item_code }}</h2>
            <p class="text-sm text-gray-600">{{ $item->name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.items.edit', $item) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
            <a href="{{ route('admin.items.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
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
                    <label class="block text-sm font-medium text-gray-500">Item Code</label>
                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $item->item_code }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Category</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->category ?? 'No category' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Current Stock</label>
                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ number_format($item->current_stock) }} {{ $item->unit }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Minimum Stock</label>
                    <p class="mt-1 text-sm text-gray-900">{{ number_format($item->minimum_stock) }} {{ $item->unit }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Unit</label>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst($item->unit) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <i class="fas {{ $item->is_active ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Stock Status</label>
                    <p class="mt-1">
                        @if($item->isLowStock())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Low Stock
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Normal
                            </span>
                        @endif
                    </p>
                </div>
            </div>
            
            @if($item->description)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-500">Description</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Location Information -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Location Information</h3>
            @if($item->itemLocations->count() > 0)
                <div class="space-y-4">
                    @foreach($item->itemLocations as $location)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="text-lg font-medium text-gray-900">{{ $location->location_name }}</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        Location
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Quantity at this location:</span>
                                        <span class="ml-1 font-medium text-gray-900">{{ number_format($location->quantity) }} {{ $item->unit }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Created:</span>
                                        <span class="ml-1 font-medium text-gray-900">{{ $location->created_at->format('d M Y H:i') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Updated:</span>
                                        <span class="ml-1 font-medium text-gray-900">{{ $location->updated_at->format('d M Y H:i') }}</span>
                                    </div>
                                </div>
                                
                                @if($location->notes)
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <span class="text-gray-500 text-sm">Notes:</span>
                                        <p class="text-sm text-gray-900 mt-1">{{ $location->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-map-marker-alt text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No locations set for this item</p>
                    <p class="text-sm text-gray-400 mt-1">Edit the item to add location information</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection