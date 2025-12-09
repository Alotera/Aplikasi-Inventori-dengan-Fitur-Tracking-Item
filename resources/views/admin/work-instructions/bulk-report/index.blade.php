@extends('layouts.admin')

@section('title', 'Work Instruction Bulk Report')
@section('page-title', 'Work Instruction Bulk Report')
@section('page-description', 'Generate laporan PDF untuk Work Instruction dengan filter yang komprehensif')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-file-pdf mr-2 text-red-500"></i>
                        Generate Work Instruction Report PDF
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Pilih filter untuk menghasilkan laporan PDF Work Instruction</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        PDF Report
                    </span>
                </div>
            </div>
        </div>
        <div class="p-6">
            
            <form method="POST" action="{{ route('admin.work-instructions.bulk-report.generate') }}" class="space-y-8">
                @csrf
                
                <!-- Date Range Section -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                        Rentang Tanggal
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Mulai
                            </label>
                            <input type="date" 
                                   id="date_from" 
                                   name="date_from" 
                                   value="{{ old('date_from') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('date_from')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Akhir
                            </label>
                            <input type="date" 
                                   id="date_to" 
                                   name="date_to" 
                                   value="{{ old('date_to') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('date_to')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Type and Status Section -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                        <i class="fas fa-filter mr-2 text-green-500"></i>
                        Filter Work Instruction
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipe Work Instruction
                            </label>
                            <select id="type" 
                                    name="type" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="all" {{ old('type') == 'all' ? 'selected' : '' }}>Semua Tipe</option>
                                <option value="checking" {{ old('type') == 'checking' ? 'selected' : '' }}>Checking</option>
                                <option value="ambil" {{ old('type') == 'ambil' ? 'selected' : '' }}>Ambil</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>
                            <select id="status" 
                                    name="status" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="all" {{ old('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="not_started" {{ old('status') == 'not_started' ? 'selected' : '' }}>Belum Dikerjakan</option>
                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="overdue" {{ old('status') == 'overdue' ? 'selected' : '' }}>Terlambat</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- User and Progress Section -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                        <i class="fas fa-users mr-2 text-purple-500"></i>
                        Filter Tambahan
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                                User yang Ditugaskan
                            </label>
                            <select id="user_id" 
                                    name="user_id" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Semua User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Range Progress (%)
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" 
                                       id="progress_min" 
                                       name="progress_min" 
                                       min="0" 
                                       max="100" 
                                       placeholder="Min"
                                       value="{{ old('progress_min') }}"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <input type="number" 
                                       id="progress_max" 
                                       name="progress_max" 
                                       min="0" 
                                       max="100" 
                                       placeholder="Max"
                                       value="{{ old('progress_max') }}"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            @error('progress_min')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('progress_max')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <span class="text-sm text-gray-600">Pastikan filter sudah sesuai sebelum generate PDF</span>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.work-instructions.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Generate Report PDF
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Help Section -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-blue-800 mb-3">Informasi Filter</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-700">
                    <div class="space-y-2">
                        <div class="flex items-start">
                            <i class="fas fa-calendar-alt mr-2 mt-1 text-blue-500"></i>
                            <div>
                                <strong>Tanggal:</strong> Kosongkan untuk mengambil semua data
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-tags mr-2 mt-1 text-blue-500"></i>
                            <div>
                                <strong>Tipe:</strong> Pilih "Checking" atau "Ambil" untuk filter spesifik
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-flag mr-2 mt-1 text-blue-500"></i>
                            <div>
                                <strong>Status:</strong> Filter berdasarkan status work instruction
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-start">
                            <i class="fas fa-user mr-2 mt-1 text-blue-500"></i>
                            <div>
                                <strong>User:</strong> Filter berdasarkan user yang ditugaskan
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-percentage mr-2 mt-1 text-blue-500"></i>
                            <div>
                                <strong>Progress:</strong> Filter berdasarkan persentase progress (0-100%)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
