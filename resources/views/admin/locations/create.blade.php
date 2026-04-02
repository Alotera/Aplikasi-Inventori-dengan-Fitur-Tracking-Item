@extends('layouts.admin')

@section('title', __('admin.locations.create_title'))
@section('page-title', __('admin.locations.create_title'))
@section('page-description', __('admin.locations.create_page_desc'))

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 flex items-center justify-center rounded-full bg-purple-100">
                        <i class="fas fa-map-marker-alt text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Informasi Lokasi</h3>
                    <p class="text-sm text-gray-500">Masukkan detail lokasi penyimpanan</p>
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.locations.store') }}" class="px-6 py-6">
            @csrf
            <div class="space-y-4 mb-6">
                <div>
                    <label for="location_type" class="block text-sm font-medium text-gray-700 mb-1">
                        Tipe yang ditambahkan <span class="text-red-500">*</span>
                    </label>
                    <select name="location_type" id="location_type" required
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm">
                        <option value="item" {{ old('location_type', 'item') === 'item' ? 'selected' : '' }}>
                            Lokasi item
                        </option>
                        <option value="production_line" {{ old('location_type') === 'production_line' ? 'selected' : '' }}>
                            Line produksi
                        </option>
                    </select>
                    @error('location_type')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div id="location-item-fields" class="space-y-6">
                <!-- Nama Lokasi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-tag mr-1 text-gray-400"></i>
                        Nama Lokasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                           placeholder="Contoh: Gudang A - Rak 01 - Baris A"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm @error('name') border-red-300 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Nama lengkap dan deskriptif untuk lokasi ini</p>
                </div>

                <!-- Zone, Rack, Row -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        <i class="fas fa-sitemap mr-1 text-gray-400"></i>
                        Koordinat Lokasi
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                <i class="fas fa-layer-group mr-1"></i>Zone
                            </label>
                            <input type="text" name="zone" value="{{ old('zone') }}" 
                                   placeholder="A, B, C..."
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm">
                            <p class="mt-1 text-xs text-gray-500">Area gudang</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                <i class="fas fa-boxes-stacked mr-1"></i>Rack
                            </label>
                            <input type="text" name="rack" value="{{ old('rack') }}" 
                                   placeholder="01, 02, 03..."
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm">
                            <p class="mt-1 text-xs text-gray-500">Nomor rak</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                <i class="fas fa-grip-lines mr-1"></i>Row
                            </label>
                            <input type="text" name="row" value="{{ old('row') }}" 
                                   placeholder="A, B, C..."
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm">
                            <p class="mt-1 text-xs text-gray-500">Baris dalam rak</p>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked 
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-3 flex items-center">
                            <i class="fas fa-toggle-on text-green-600 mr-2"></i>
                            <span class="text-sm font-medium text-gray-900">Lokasi Aktif</span>
                        </label>
                    </div>
                    <p class="ml-7 mt-1 text-xs text-gray-500">Lokasi aktif dapat digunakan untuk penyimpanan barang</p>
                </div>
            </div>

            <div id="production-line-fields" class="space-y-6" style="{{ old('location_type', 'item') === 'production_line' ? '' : 'display:none;' }}">
                <!-- Nama Line Produksi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Line Produksi <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="production_line_name"
                           value="{{ old('production_line_name') }}"
                           required
                           placeholder="Contoh: Line A, Assembling 1, Production Line B"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm @error('production_line_name') border-red-300 @enderror">
                    @error('production_line_name')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <input type="checkbox"
                               name="production_line_is_active"
                               id="production_line_is_active"
                               value="1"
                               @checked(old('production_line_is_active', true))
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="production_line_is_active" class="ml-3 flex items-center">
                            <i class="fas fa-toggle-on text-green-600 mr-2"></i>
                            <span class="text-sm font-medium text-gray-900">Line produksi aktif</span>
                        </label>
                    </div>
                    <p class="ml-7 mt-1 text-xs text-gray-500">Line aktif akan muncul di dropdown tujuan pengiriman.</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.locations.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Lokasi
                </button>
            </div>
        </form>
    </div>
</div>
<script>
function toggleLocationType() {
    const type = document.getElementById('location_type')?.value;
    const itemFields = document.getElementById('location-item-fields');
    const productionFields = document.getElementById('production-line-fields');
    if (!type || !itemFields || !productionFields) return;

    if (type === 'production_line') {
        itemFields.style.display = 'none';
        itemFields.querySelectorAll('input,select,textarea,button').forEach(el => el.disabled = true);
        productionFields.style.display = '';
        productionFields.querySelectorAll('input,select,textarea,button').forEach(el => el.disabled = false);
    } else {
        productionFields.style.display = 'none';
        productionFields.querySelectorAll('input,select,textarea,button').forEach(el => el.disabled = true);
        itemFields.style.display = '';
        itemFields.querySelectorAll('input,select,textarea,button').forEach(el => el.disabled = false);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    toggleLocationType();
    document.getElementById('location_type')?.addEventListener('change', toggleLocationType);
});
</script>
@endsection


