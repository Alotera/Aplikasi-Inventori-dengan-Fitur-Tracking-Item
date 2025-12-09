@extends('layouts.user')

@section('title', 'My Work Instructions')
@section('page-title', 'My Work Instructions')

@section('content')
<div class="space-y-6">
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
                                    {{ ucfirst($workInstruction->type) }}
                                </span>
                                @if($workInstruction->getMainStatus() === 'overdue')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Terlambat
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mb-3">{{ $workInstruction->title }}</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Status:</span>
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
                                    <span class="text-gray-500">Deadline:</span>
                                    <span class="ml-1 font-medium {{ $workInstruction->getMainStatus() === 'overdue' ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $workInstruction->deadline->format('d M Y H:i') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Items:</span>
                                    <span class="ml-1 font-medium text-gray-900">{{ $workInstruction->items->count() }} items</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Progress:</span>
                                    <span class="ml-1 font-medium text-gray-900">
                                        {{ $workInstruction->items->where('pivot.status', '!=', 'pending')->count() }}/{{ $workInstruction->items->count() }} completed
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col space-y-2">
                            <a href="{{ route('user.work-instructions.show', $workInstruction) }}" 
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-eye mr-2"></i>
                                View Details
                            </a>
                        </div>
                    </div>
                    
                    <!-- Items Preview with Locations -->
                    @if($workInstruction->items->count() > 0)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Items to {{ $workInstruction->type === 'checking' ? 'Check' : 'Take' }}:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach($workInstruction->items->take(4) as $item)
                                    <div class="flex items-center justify-between text-sm bg-gray-50 rounded px-3 py-2">
                                        <div class="flex-1">
                                            <span class="font-medium text-gray-900">{{ $item->name }}</span>
                                            <span class="text-gray-500 ml-2">({{ $item->item_code }})</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-500">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $item->itemLocations->first()->location_name ?? 'No location' }}
                                            </span>
                                            <span class="text-xs {{ $item->pivot->status === 'completed' ? 'text-green-600' : ($item->pivot->status === 'not_good' ? 'text-red-600' : 'text-yellow-600') }}">
                                                {{ ucfirst($item->pivot->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                                @if($workInstruction->items->count() > 4)
                                    <div class="text-xs text-gray-500 col-span-full text-center">
                                        +{{ $workInstruction->items->count() - 4 }} more items
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
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Work Instructions</h3>
            <p class="text-gray-500">You don't have any work instructions assigned yet.</p>
        </div>
    @endif
</div>
@endsection