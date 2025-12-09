@extends('layouts.warehouse-staff')

@section('title', 'Stock OUT')
@section('page-title', 'Stock OUT')
@section('page-description', 'Kurangi stock barang dari inventory')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-minus-circle text-red-600 mr-2"></i>
                Form Stock OUT
            </h3>
            <p class="text-sm text-gray-600 mt-1">Masukkan detail barang yang akan dikurangi dari inventory</p>
        </div>
        
        <form method="POST" action="{{ route('warehouse-staff.stock-out') }}" class="p-6 space-y-6">
            @csrf
            
            <!-- Item Selection -->
            <div>
                <label for="item_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-box mr-2"></i>
                    Pilih Item *
                </label>
                <select id="item_id" 
                        name="item_id" 
                        required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">-- Pilih Item --</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->name }} ({{ $item->item_code }}) - Stock: {{ $item->current_stock }} {{ $item->unit }}
                        </option>
                    @endforeach
                </select>
                @error('item_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quantity -->
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-hashtag mr-2"></i>
                    Jumlah *
                </label>
                <input type="number" 
                       id="quantity" 
                       name="quantity" 
                       min="1" 
                       required
                       value="{{ old('quantity') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reason -->
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-comment mr-2"></i>
                    Alasan Stock OUT *
                </label>
                <select id="reason" 
                        name="reason" 
                        required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">-- Pilih Alasan --</option>
                    <option value="Penjualan" {{ old('reason') == 'Penjualan' ? 'selected' : '' }}>Penjualan</option>
                    <option value="Produksi" {{ old('reason') == 'Produksi' ? 'selected' : '' }}>Produksi</option>
                    <option value="Retur ke Supplier" {{ old('reason') == 'Retur ke Supplier' ? 'selected' : '' }}>Retur ke Supplier</option>
                    <option value="Transfer ke Gudang Lain" {{ old('reason') == 'Transfer ke Gudang Lain' ? 'selected' : '' }}>Transfer ke Gudang Lain</option>
                    <option value="Kerusakan" {{ old('reason') == 'Kerusakan' ? 'selected' : '' }}>Kerusakan</option>
                    <option value="Hilang" {{ old('reason') == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                    <option value="Stock Opname" {{ old('reason') == 'Stock Opname' ? 'selected' : '' }}>Stock Opname</option>
                    <option value="Lainnya" {{ old('reason') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-2"></i>
                    Catatan Tambahan
                </label>
                <textarea id="notes" 
                          name="notes" 
                          rows="3"
                          placeholder="Masukkan catatan tambahan jika diperlukan..."
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('warehouse-staff.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i class="fas fa-minus mr-2"></i>
                    Kurangi Stock
                </button>
            </div>
        </form>
    </div>

    <!-- Help Section -->
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Peringatan Stock OUT</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Stock OUT akan mengurangi jumlah stock barang yang dipilih</li>
                        <li>Sistem akan memeriksa ketersediaan stock sebelum melakukan pengurangan</li>
                        <li>Pastikan alasan stock OUT diisi dengan benar untuk audit trail</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
