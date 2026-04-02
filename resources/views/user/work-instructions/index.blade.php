@extends('layouts.user')

@section('title', __('user.wi_index.title'))
@section('page-title', __('user.wi_index.page_title'))

@section('content')
<div class="space-y-6">
    <div class="bg-white shadow sm:rounded-lg p-4 sm:p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">
            <i class="fas fa-filter mr-2 text-gray-500"></i>{{ __('user.wi_index.filter_heading') }}
        </h3>
        <form method="GET" action="{{ route('user.work-instructions.index') }}" class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-4">
            <div class="w-full sm:w-auto sm:min-w-[10rem]">
                <label for="user_wi_filter_type" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.wi.filter_type') }}</label>
                <select id="user_wi_filter_type" name="type" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
                    <option value="">{{ __('admin.wi.filter_all_types') }}</option>
                    <option value="checking" {{ request('type') === 'checking' ? 'selected' : '' }}>{{ __('user.wi_type.checking') }}</option>
                    <option value="ambil" {{ request('type') === 'ambil' ? 'selected' : '' }}>{{ __('user.wi_type.ambil') }}</option>
                </select>
            </div>
            <div class="w-full sm:w-auto sm:min-w-[10rem]">
                <label for="user_wi_filter_status" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.wi.filter_status') }}</label>
                <select id="user_wi_filter_status" name="status" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
                    <option value="">{{ __('admin.wi.filter_all_statuses') }}</option>
                    <option value="not_started" {{ request('status') === 'not_started' ? 'selected' : '' }}>{{ __('user.wi_status.main.not_started') }}</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('user.wi_status.main.completed') }}</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>{{ __('user.wi_status.main.overdue') }}</option>
                </select>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-search mr-2"></i>{{ __('admin.wi.filter_apply') }}
                </button>
                <a href="{{ route('user.work-instructions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    {{ __('admin.wi.filter_reset') }}
                </a>
            </div>
        </form>
    </div>

    @if($workInstructions->count() > 0)
        <div class="grid gap-6">
            @foreach($workInstructions as $workInstruction)
            <div class="bg-white shadow rounded-lg overflow-hidden {{ $workInstruction->getMainStatus() === 'overdue' ? 'ring-2 ring-red-200' : '' }}">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-medium text-gray-900">{{ $workInstruction->wi_number }}</h3>
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
                            <p class="text-sm text-gray-600 mb-3">{{ $workInstruction->title }}</p>
                            
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
                                        {{ __('user.wi_index.progress_line', [
                                            'done' => $workInstruction->items->where('pivot.status', '!=', 'pending')->count(),
                                            'total' => $workInstruction->items->count(),
                                        ]) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col space-y-2">
                            <a href="{{ route('user.work-instructions.show', $workInstruction) }}" 
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-eye mr-2"></i>
                                {{ __('user.wi_index.view_details') }}
                            </a>
                        </div>
                    </div>
                    
                    @if($workInstruction->items->count() > 0)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $workInstruction->type === 'checking' ? __('user.dashboard.items_to_check') : __('user.dashboard.items_to_take') }}:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach($workInstruction->items->take(4) as $item)
                                    @php($pivotKey = $item->pivot->status)
                                    <div class="flex items-center justify-between text-sm bg-gray-50 rounded px-3 py-2">
                                        <div class="flex-1">
                                            <span class="font-medium text-gray-900">{{ $item->name }}</span>
                                            <span class="text-gray-500 ml-2">({{ $item->item_code }})</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-500">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $item->itemLocations->first()->location_name ?? __('user.dashboard.no_location') }}
                                            </span>
                                            <span class="text-xs {{ $item->pivot->status === 'completed' ? 'text-green-600' : ($item->pivot->status === 'not_good' ? 'text-red-600' : 'text-yellow-600') }}">
                                                {{ __('user.item_pivot.'.$pivotKey) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                                @if($workInstruction->items->count() > 4)
                                    <div class="text-xs text-gray-500 col-span-full text-center">
                                        {{ __('user.dashboard.more_items', ['count' => $workInstruction->items->count() - 4]) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-clipboard-list text-gray-400 text-6xl mb-4"></i>
            @if(request()->filled('type') || request()->filled('status'))
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('user.wi_index.no_wi_title') }}</h3>
                <p class="text-gray-500">{{ __('user.wi_index.no_wi_filtered') }}</p>
            @else
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('user.wi_index.no_wi_title') }}</h3>
                <p class="text-gray-500">{{ __('user.wi_index.no_wi_body') }}</p>
            @endif
        </div>
    @endif
</div>
@endsection
