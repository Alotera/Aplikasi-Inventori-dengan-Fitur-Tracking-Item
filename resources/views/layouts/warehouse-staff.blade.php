<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Warehouse Staff Dashboard') - Inventory App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-4 border-b border-gray-700">
                <h2 class="text-xl font-bold">
                    <i class="fas fa-warehouse mr-2 text-blue-400"></i>
                    Inventory App
                </h2>
                <p class="text-gray-400 text-sm mt-1">Warehouse Staff</p>
                <div class="mt-2 text-xs text-blue-300">{{ auth()->user()->name }}</div>
            </div>
            
            <nav class="flex-1 mt-4 px-2">
                <a href="{{ route('warehouse-staff.dashboard') }}" 
                   class="flex items-center px-4 py-2.5 mb-1 text-gray-300 hover:bg-gray-700 rounded-md transition-colors {{ request()->routeIs('warehouse-staff.dashboard') ? 'bg-blue-600 text-white' : '' }}">
                    <i class="fas fa-tachometer-alt mr-3 w-5"></i>
                    Dashboard
                </a>
                <a href="{{ route('warehouse-staff.stock-in') }}" 
                   class="flex items-center px-4 py-2.5 mb-1 text-gray-300 hover:bg-gray-700 rounded-md transition-colors {{ request()->routeIs('warehouse-staff.stock-in*') ? 'bg-blue-600 text-white' : '' }}">
                    <i class="fas fa-plus-circle mr-3 w-5"></i>
                    Stock IN
                </a>
                <a href="{{ route('warehouse-staff.stock-out') }}" 
                   class="flex items-center px-4 py-2.5 mb-1 text-gray-300 hover:bg-gray-700 rounded-md transition-colors {{ request()->routeIs('warehouse-staff.stock-out*') ? 'bg-blue-600 text-white' : '' }}">
                    <i class="fas fa-minus-circle mr-3 w-5"></i>
                    Stock OUT
                </a>
                <a href="{{ route('warehouse-staff.stock-history') }}" 
                   class="flex items-center px-4 py-2.5 mb-1 text-gray-300 hover:bg-gray-700 rounded-md transition-colors {{ request()->routeIs('warehouse-staff.stock-history') ? 'bg-blue-600 text-white' : '' }}">
                    <i class="fas fa-history mr-3 w-5"></i>
                    Stock History
                </a>
            </nav>
            
            <div class="p-4 border-t border-gray-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-red-600 hover:text-white rounded-md transition-colors">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                            <p class="text-sm text-gray-500 mt-1">@yield('page-description', 'Manage warehouse stock movements')</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                {{ now()->format('d M Y H:i') }}
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        {{ session('warning') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
