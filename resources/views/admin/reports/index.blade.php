@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', 'Reports')
@section('page-description', 'Laporan dan analisis sistem inventory')

@section('content')
<div class="space-y-6">
    <!-- Stock Report -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow">
            <div class="p-8">
                <div class="text-center">
                    <div class="flex justify-center mb-6">
                        <i class="fas fa-chart-line text-blue-500 text-6xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Stock Movement Report</h2>
                    <p class="text-lg text-gray-600 mb-6">Laporan lengkap pergerakan stock dari warehouse staff dengan analytics dan filtering yang komprehensif</p>
                    <a href="{{ route('admin.reports.stock') }}" class="inline-flex items-center px-8 py-3 border border-transparent text-lg font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                        <i class="fas fa-eye mr-3"></i>
                        View Stock Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-chart-bar mr-2"></i>
                Quick Statistics
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ \App\Models\StockMovement::count() }}</div>
                    <div class="text-sm text-gray-500">Total Stock Movements</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ \App\Models\Item::where('is_active', true)->count() }}</div>
                    <div class="text-sm text-gray-500">Active Items</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ \App\Models\User::where('role', 'warehouse_staff')->where('is_active', true)->count() }}</div>
                    <div class="text-sm text-gray-500">Warehouse Staff</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
