@extends('layouts.admin')

@section('title', 'Work Instructions Management')
@section('page-title', 'Work Instructions Management')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-medium text-gray-900">Work Instructions</h2>
            <p class="text-sm text-gray-600">Manage work instructions for users</p>
        </div>
        <a href="{{ route('admin.work-instructions.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>
            Create New WI
        </a>
    </div>

    <!-- Work Instructions Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">WI Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($workInstructions as $wi)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $wi->wi_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $wi->type === 'checking' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                <i class="fas {{ $wi->type === 'checking' ? 'fa-search' : 'fa-hand-holding' }} mr-1"></i>
                                {{ ucfirst($wi->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $wi->title }}</div>
                            @if($wi->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($wi->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($wi->type === 'ambil')
                                <div>{{ $wi->destination_line ?? '-' }}</div>
                                @if($wi->dropoff_notes)
                                    <div class="text-xs text-gray-500">{{ Str::limit($wi->dropoff_notes, 40) }}</div>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $wi->assignedUser->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt mr-1 text-gray-400"></i>
                                {{ $wi->deadline->format('d M Y H:i') }}
                                @if($wi->getMainStatus() === 'overdue')
                                    <i class="fas fa-exclamation-triangle ml-2 text-red-500" title="Terlambat"></i>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="space-y-1">
                                <!-- Main Status -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($wi->getMainStatus() === 'completed') bg-green-100 text-green-800
                                    @elseif($wi->getMainStatus() === 'overdue') bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    <i class="fas 
                                        @if($wi->getMainStatus() === 'completed') fa-check-circle
                                        @elseif($wi->getMainStatus() === 'overdue') fa-exclamation-triangle
                                        @else fa-clock
                                        @endif mr-1"></i>
                                    {{ $wi->getStatusLabel() }}
                                </span>
                                
                                <!-- Progression Status -->
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($wi->getProgressionStatus() === 'completed') bg-green-50 text-green-700
                                    @elseif($wi->getProgressionStatus() === 'in_progress') bg-yellow-50 text-yellow-700
                                    @else bg-gray-50 text-gray-700
                                    @endif">
                                    <i class="fas 
                                        @if($wi->getProgressionStatus() === 'completed') fa-check
                                        @elseif($wi->getProgressionStatus() === 'in_progress') fa-spinner
                                        @else fa-pause
                                        @endif mr-1"></i>
                                    {{ $wi->getProgressionLabel() }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-box mr-1"></i>
                                {{ $wi->items->count() }} items
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.work-instructions.show', $wi) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.work-instructions.edit', $wi) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.work-instructions.destroy', $wi) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this work instruction?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                            No work instructions found. <a href="{{ route('admin.work-instructions.create') }}" class="text-blue-600 hover:text-blue-800">Create your first work instruction</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($workInstructions->hasPages())
        <div class="mt-4">
            {{ $workInstructions->links() }}
        </div>
    @endif
</div>
@endsection
