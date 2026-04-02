@extends('layouts.user')

@section('title', __('user.dashboard.title'))
@section('page-title', __('user.dashboard.page_title'))
@section('page-description', __('user.dashboard.page_desc'))

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 rounded-full bg-blue-100">
                            <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-500 truncate">{{ __('user.dashboard.total_wi') }}</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $workInstructions->count() }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 rounded-full bg-yellow-100">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-500 truncate">{{ __('user.dashboard.not_started') }}</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $workInstructions->filter(fn($wi) => $wi->getMainStatus() === 'not_started')->count() }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 rounded-full bg-green-100">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-500 truncate">{{ __('user.dashboard.completed') }}</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $workInstructions->filter(fn($wi) => $wi->getMainStatus() === 'completed')->count() }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 rounded-full bg-red-100">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-500 truncate">{{ __('user.dashboard.overdue') }}</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $workInstructions->filter(fn($wi) => $wi->getMainStatus() === 'overdue')->count() }}</dd>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Work Instructions List -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('user.dashboard.actual_wi') }}</h3>
            
            @if($workInstructions->count() > 0)
                <div class="space-y-4">
                    @foreach($workInstructions->take(5) as $workInstruction)
                    <div class="border border-gray-200 rounded-lg p-4 {{ $workInstruction->getMainStatus() === 'overdue' ? 'bg-red-50 border-red-200' : 'bg-gray-50' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="text-lg font-medium text-gray-900">{{ $workInstruction->wi_number }}</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $workInstruction->type === 'checking' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        <i class="fas {{ $workInstruction->type === 'checking' ? 'fa-search' : 'fa-hand-holding' }} mr-1"></i>
                                        {{ __('user.wi_type.'.$workInstruction->type) }}
                                    </span>
                                    @if($workInstruction->getMainStatus() === 'overdue')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            {{ __('user.dashboard.overdue_badge') }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ $workInstruction->title }}</p>
                                
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">{{ __('user.dashboard.status_label') }}:</span>
                                        <div class="flex flex-col space-y-1 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                @if($workInstruction->getMainStatus() === 'completed') bg-green-100 text-green-800
                                                @elseif($workInstruction->getMainStatus() === 'overdue') bg-red-100 text-red-800
                                                @else bg-blue-100 text-blue-800
                                                @endif">
                                                <i class="fas 
                                                    @if($workInstruction->getMainStatus() === 'completed') fa-check-circle
                                                    @elseif($workInstruction->getMainStatus() === 'overdue') fa-exclamation-triangle
                                                    @else fa-clock
                                                    @endif mr-1"></i>
                                                {{ $workInstruction->getStatusLabel() }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                @if($workInstruction->getProgressionStatus() === 'completed') bg-green-50 text-green-700
                                                @elseif($workInstruction->getProgressionStatus() === 'in_progress') bg-yellow-50 text-yellow-700
                                                @else bg-gray-50 text-gray-700
                                                @endif">
                                                <i class="fas 
                                                    @if($workInstruction->getProgressionStatus() === 'completed') fa-check
                                                    @elseif($workInstruction->getProgressionStatus() === 'in_progress') fa-spinner
                                                    @else fa-pause
                                                    @endif mr-1"></i>
                                                {{ $workInstruction->getProgressionLabel() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">{{ __('user.dashboard.deadline') }}:</span>
                                        <span class="ml-1 font-medium {{ $workInstruction->getMainStatus() === 'overdue' ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $workInstruction->deadline->format('d M Y H:i') }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">{{ __('user.dashboard.items_label') }}:</span>
                                        <span class="ml-1 font-medium text-gray-900">{{ __('user.dashboard.items_count', ['count' => $workInstruction->items->count()]) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">{{ __('user.dashboard.progress') }}:</span>
                                        <span class="ml-1 font-medium text-gray-900">
                                            {{ __('user.dashboard.completed_of_total', [
                                                'done' => $workInstruction->items->where('pivot.status', 'completed')->count(),
                                                'total' => $workInstruction->items->count(),
                                            ]) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Items Preview with Locations -->
                                @if($workInstruction->items->count() > 0)
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <h5 class="text-xs font-medium text-gray-700 mb-2">{{ $workInstruction->type === 'checking' ? __('user.dashboard.items_to_check') : __('user.dashboard.items_to_take') }}:</h5>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-1">
                                            @foreach($workInstruction->items->take(3) as $item)
                                                @php($pivotKey = $item->pivot->status)
                                                <div class="flex items-center justify-between text-xs bg-white rounded px-2 py-1">
                                                    <div class="flex-1">
                                                        <span class="font-medium text-gray-900">{{ $item->name }}</span>
                                                        <span class="text-gray-500 ml-1">({{ $item->item_code }})</span>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-gray-500">
                                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                                            {{ $item->itemLocations->first()->location_name ?? __('user.dashboard.no_location') }}
                                                        </span>
                                                        <span class="{{ $item->pivot->status === 'completed' ? 'text-green-600' : ($item->pivot->status === 'not_found' ? 'text-red-600' : 'text-yellow-600') }}">
                                                            {{ __('user.item_pivot.'.$pivotKey) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if($workInstruction->items->count() > 3)
                                                <div class="text-xs text-gray-500 col-span-full text-center">
                                                    {{ __('user.dashboard.more_items', ['count' => $workInstruction->items->count() - 3]) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex flex-col space-y-2">
                                <a href="{{ route('user.work-instructions.show', $workInstruction) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-eye mr-2"></i>
                                    {{ __('user.dashboard.view') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @if($workInstructions->count() > 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('user.work-instructions.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            {{ __('user.dashboard.view_all_wi') }}
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">{{ __('user.dashboard.no_wi_assigned') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
