@extends('layouts.admin')

@section('title', 'Edit Lokasi')
@section('page-title', 'Edit Lokasi')
@section('page-description', 'Ubah informasi lokasi penyimpanan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 flex items-center justify-center rounded-full bg-purple-100">
                            <i class="fas fa-edit text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Edit Informasi Lokasi</h3>
                        <p class="text-sm text-gray-500">Perbarui detail lokasi penyimpanan</p>
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    ID: <span class="font-medium">{{ $location->id }}</span>
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.locations.update', $location) }}" class="px-6 py-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Nama Lokasi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-tag mr-1 text-gray-400"></i>
                        Nama Lokasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $location->name) }}" required 
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
                            <input type="text" name="zone" value="{{ old('zone', $location->zone) }}" 
                                   placeholder="A, B, C..."
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm">
                            <p class="mt-1 text-xs text-gray-500">Area gudang</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                <i class="fas fa-boxes-stacked mr-1"></i>Rack
                            </label>
                            <input type="text" name="rack" value="{{ old('rack', $location->rack) }}" 
                                   placeholder="01, 02, 03..."
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm">
                            <p class="mt-1 text-xs text-gray-500">Nomor rak</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                <i class="fas fa-grip-lines mr-1"></i>Row
                            </label>
                            <input type="text" name="row" value="{{ old('row', $location->row) }}" 
                                   placeholder="A, B, C..."
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm">
                            <p class="mt-1 text-xs text-gray-500">Baris dalam rak</p>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" 
                               @checked(old('is_active', $location->is_active))
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-3 flex items-center">
                            <i class="fas fa-toggle-on text-green-600 mr-2"></i>
                            <span class="text-sm font-medium text-gray-900">Lokasi Aktif</span>
                        </label>
                    </div>
                    <p class="ml-7 mt-1 text-xs text-gray-500">Lokasi aktif dapat digunakan untuk penyimpanan barang</p>
                </div>

                <!-- Info Update -->
                @if($location->updated_at)
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Terakhir diubah: <span class="font-medium">{{ $location->updated_at->format('d M Y H:i') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                @endif
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
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


