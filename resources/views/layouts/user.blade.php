<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('user.dashboard.title')) - {{ __('app.name') }}</title>
    @include('partials.favicon')
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex">
        <!-- Mobile overlay -->
        <div
            class="fixed inset-0 bg-black/50 z-30 md:hidden"
            x-show="sidebarOpen"
            x-transition.opacity
            @click="sidebarOpen = false"
            style="display: none;"
        ></div>

        <!-- Sidebar -->
        <div
            class="fixed inset-y-0 left-0 w-64 bg-gray-800 text-white flex flex-col z-40 transform transition-transform duration-200 md:sticky md:top-0 md:translate-x-0 h-dvh"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="p-4 border-b border-gray-700">
                <h2 class="text-xl font-bold">
                    <i class="fas fa-user mr-2 text-green-400"></i>
                    {{ __('app.name') }}
                </h2>
                <p class="text-gray-400 text-sm mt-1">{{ __('nav.user_panel') }}</p>
                <div class="mt-2 text-xs text-green-300">{{ auth()->user()->name }}</div>
            </div>
            
            <nav class="flex-1 mt-4 px-2 overflow-y-auto pb-4">
                <a href="{{ route('user.dashboard') }}" 
                   class="flex items-center px-4 py-2.5 mb-1 text-gray-300 hover:bg-gray-700 rounded-md transition-colors {{ request()->routeIs('user.dashboard') ? 'bg-green-600 text-white' : '' }}">
                    <i class="fas fa-tachometer-alt mr-3 w-5"></i>
                    {{ __('nav.dashboard') }}
                </a>
                <a href="{{ route('user.work-instructions.index') }}" 
                   class="flex items-center px-4 py-2.5 mb-1 text-gray-300 hover:bg-gray-700 rounded-md transition-colors {{ request()->routeIs('user.work-instructions.*') ? 'bg-green-600 text-white' : '' }}">
                    <i class="fas fa-clipboard-list mr-3 w-5"></i>
                    {{ __('nav.work_instructions') }}
                </a>
            </nav>
            
            <div class="p-4 border-t border-gray-700 mt-auto space-y-3">
                <div class="flex justify-center">
                    @include('partials.language-switch')
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-red-600 hover:text-white rounded-md transition-colors">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        {{ __('nav.logout') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="px-4 sm:px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <button
                                type="button"
                                class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50"
                                @click="sidebarOpen = true"
                                aria-label="{{ __('layout.open_menu') }}"
                            >
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="min-w-0">
                            <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 break-words">@yield('page-title', __('user.dashboard.page_title'))</h1>
                            <p class="text-xs sm:text-sm text-gray-500 mt-1 break-words">@yield('page-description', __('layout.default_desc_user'))</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            @include('partials.notification-bell')
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                {{ now()->format('d M Y H:i') }}
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6">
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