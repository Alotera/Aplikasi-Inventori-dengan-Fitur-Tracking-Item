@extends('layouts.admin')

@section('title', __('admin.items.edit_title'))
@section('page-title', __('admin.items.edit_title'))

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('admin.items.update', $item) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="item_code" class="block text-sm font-medium text-gray-700">Item Code *</label>
                            <input type="text" name="item_code" id="item_code" value="{{ old('item_code', $item->item_code) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm 
                                   @error('item_code') border-red-300 @enderror">
                            @error('item_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Item Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $item->name) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm 
                                   @error('name') border-red-300 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm 
                                  @error('description') border-red-300 @enderror">{{ old('description', $item->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <input type="text" name="category" id="category" value="{{ old('category', $item->category) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm 
                                   @error('category') border-red-300 @enderror">
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="unit" class="block text-sm font-medium text-gray-700">Unit *</label>
                            <select name="unit" id="unit" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm 
                                    @error('unit') border-red-300 @enderror">
                                <option value="pcs" {{ old('unit', $item->unit) === 'pcs' ? 'selected' : '' }}>Pieces</option>
                                <option value="kg" {{ old('unit', $item->unit) === 'kg' ? 'selected' : '' }}>Kilogram</option>
                                <option value="g" {{ old('unit', $item->unit) === 'g' ? 'selected' : '' }}>Gram</option>
                                <option value="l" {{ old('unit', $item->unit) === 'l' ? 'selected' : '' }}>Liter</option>
                                <option value="ml" {{ old('unit', $item->unit) === 'ml' ? 'selected' : '' }}>Milliliter</option>
                                <option value="m" {{ old('unit', $item->unit) === 'm' ? 'selected' : '' }}>Meter</option>
                                <option value="cm" {{ old('unit', $item->unit) === 'cm' ? 'selected' : '' }}>Centimeter</option>
                                <option value="box" {{ old('unit', $item->unit) === 'box' ? 'selected' : '' }}>Box</option>
                                <option value="set" {{ old('unit', $item->unit) === 'set' ? 'selected' : '' }}>Set</option>
                            </select>
                            @error('unit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Location Field (Dropdown + Advanced Filter) -->
                    <div x-data="locationPicker()">
                        <label for="location_select" class="block text-sm font-medium text-gray-700">Location *</label>
                        <div class="flex items-center justify-between">
                            <select id="location_select" x-ref="select" @change="syncHidden()" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></select>
                            <button type="button" @click="advanced = !advanced" class="ml-3 px-2 py-1 text-xs border rounded">Advanced</button>
                        </div>
                        <div x-show="advanced" class="mt-3 grid grid-cols-1 md:grid-cols-4 gap-3">
                            <input type="text" placeholder="Nama" x-model="filters.name" @input="applyFilters()" 
                            class="border-gray-300 rounded-md shadow-sm sm:text-sm">
                            <input type="text" placeholder="Zone" x-model="filters.zone" @input="applyFilters()" 
                            class="border-gray-300 rounded-md shadow-sm sm:text-sm">
                            <input type="text" placeholder="Rack" x-model="filters.rack" @input="applyFilters()" 
                            class="border-gray-300 rounded-md shadow-sm sm:text-sm">
                            <input type="text" placeholder="Row"  x-model="filters.row"  @input="applyFilters()" 
                            class="border-gray-300 rounded-md shadow-sm sm:text-sm">
                        </div>
                        <input type="hidden" name="location" id="location" value="{{ old('location', 
                        $item->itemLocations->first()->location_name ?? '') }}">
                        <p class="mt-1 text-xs text-gray-500">Pilih lokasi dari master. Nilai yang disimpan akan mengisi lokasi item.</p>
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <script>
                        document.addEventListener('alpine:init', () => {
                            Alpine.data('locationPicker', () => ({
                                advanced: false,
                                filters: { name: '', zone: '', rack: '', row: '' },
                                all: [],
                                async init() {
                                    this.all = @json($locations);
                                    this.renderOptions();
                                    // Preselect current hidden value if exists
                                    const current = document.getElementById('location').value;
                                    if (current) {
                                        const sel = this.$refs.select;
                                        // Find matching option by location_name
                                        const matchingLocation = this.all.find(loc => loc.location_name === current);
                                        if (matchingLocation) {
                                            sel.value = matchingLocation.label;
                                        }
                                    }
                                    // Sync hidden input after preselect
                                    this.syncHidden();
                                },
                                renderOptions() {
                                    const sel = this.$refs.select;
                                    sel.innerHTML = '';
                                    const filtered = this.filtered();
                                    filtered.forEach(o => {
                                        const opt = document.createElement('option');
                                        opt.value = o.label;
                                        opt.textContent = o.label;
                                        sel.appendChild(opt);
                                    });
                                },
                                filtered() {
                                    const f = this.filters;
                                    return this.all.filter(o =>
                                        (f.name === '' || (o.name||'').toLowerCase().includes(f.name.toLowerCase())) &&
                                        (f.zone === '' || (o.zone||'').toLowerCase().includes(f.zone.toLowerCase())) &&
                                        (f.rack === '' || (o.rack||'').toLowerCase().includes(f.rack.toLowerCase())) &&
                                        (f.row  === '' || (o.row ||'').toLowerCase().includes(f.row.toLowerCase()))
                                    );
                                },
                                applyFilters() { this.renderOptions(); this.syncHidden(); },
                                syncHidden() {
                                    const sel = this.$refs.select;
                                    const selectedLabel = sel.value || '';
                                    // Find the location object that matches the selected label
                                    const selectedLocation = this.all.find(loc => loc.label === selectedLabel);
                                    const locationName = selectedLocation ? selectedLocation.location_name : '';
                                    document.getElementById('location').value = locationName;
                                }
                            }))
                        })
                        </script>
                    </div>

                    <div>
                        <label for="minimum_stock" class="block text-sm font-medium text-gray-700">Minimum Stock *</label>
                        <input type="number" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock) }}" 
                        min="0" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm 
                               @error('minimum_stock') border-red-300 @enderror">
                        @error('minimum_stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $item->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Active Item
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.items.show', $item) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm 
                    text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md 
                        text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection