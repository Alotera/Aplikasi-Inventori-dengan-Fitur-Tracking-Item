@extends('layouts.admin')

@section('title', __('admin.wi.index_title'))
@section('page-title', __('admin.wi.index_title'))

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-medium text-gray-900">{{ __('admin.wi.heading') }}</h2>
            <p class="text-sm text-gray-600">{{ __('admin.wi.subtitle') }}</p>
        </div>
        <a href="{{ route('admin.work-instructions.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>
            {{ __('admin.wi.create_button') }}
        </a>
    </div>

    <div class="bg-white shadow sm:rounded-lg p-4 sm:p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">
            <i class="fas fa-filter mr-2 text-gray-500"></i>{{ __('admin.wi.filter_heading') }}
        </h3>
        <form method="GET" action="{{ route('admin.work-instructions.index') }}" class="flex flex-col lg:flex-row lg:flex-wrap lg:items-end gap-4">
            <div class="w-full sm:w-auto sm:min-w-[12rem]">
                <label for="filter_assigned_user_id" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.wi.filter_assigned_user') }}</label>
                <select id="filter_assigned_user_id" name="assigned_user_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">{{ __('admin.wi.filter_all_users') }}</option>
                    @foreach($filterUsers as $u)
                        <option value="{{ $u->id }}" {{ (string) request('assigned_user_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto sm:min-w-[10rem]">
                <label for="filter_type" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.wi.filter_type') }}</label>
                <select id="filter_type" name="type" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">{{ __('admin.wi.filter_all_types') }}</option>
                    <option value="checking" {{ request('type') === 'checking' ? 'selected' : '' }}>{{ __('user.wi_type.checking') }}</option>
                    <option value="ambil" {{ request('type') === 'ambil' ? 'selected' : '' }}>{{ __('user.wi_type.ambil') }}</option>
                </select>
            </div>
            <div class="w-full sm:w-auto sm:min-w-[10rem]">
                <label for="filter_status" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.wi.filter_status') }}</label>
                <select id="filter_status" name="status" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">{{ __('admin.wi.filter_all_statuses') }}</option>
                    <option value="not_started" {{ request('status') === 'not_started' ? 'selected' : '' }}>{{ __('user.wi_status.main.not_started') }}</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('user.wi_status.main.completed') }}</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>{{ __('user.wi_status.main.overdue') }}</option>
                </select>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>{{ __('admin.wi.filter_apply') }}
                </button>
                <a href="{{ route('admin.work-instructions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    {{ __('admin.wi.filter_reset') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Work Instructions Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="overflow-x-auto">
            <table class="w-full table-fixed divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">{{ __('admin.wi.wi_number') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">{{ __('admin.wi.filter_type') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.wi.title_label') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">{{ __('admin.wi.table_destination') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">{{ __('admin.dashboard.assigned_to') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-44">{{ __('admin.dashboard.deadline') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-44">{{ __('admin.wi.filter_status') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">{{ __('admin.wi.table_items') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">{{ __('admin.wi.table_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($workInstructions as $wi)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <div class="max-w-[8rem] sm:max-w-[10rem] truncate" title="{{ $wi->wi_number }}">
                                {{ $wi->wi_number }}
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $wi->type === 'checking' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}"
                                  title="{{ $wi->getTypeExplanation() }}">
                                <i class="fas {{ $wi->type === 'checking' ? 'fa-search' : 'fa-hand-holding' }} mr-1"></i>
                                {{ ucfirst($wi->type) }}
                            </span>
                        </td>
                        <td class="px-3 sm:px-6 py-4 align-top">
                            <div class="text-sm font-medium text-gray-900 break-words">{{ $wi->title }}</div>
                            @if($wi->description)
                                <div class="text-sm text-gray-500 break-words">{{ Str::limit($wi->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-4 text-sm text-gray-900 align-top break-words">
                            @if($wi->type === 'ambil')
                                <div>{{ $wi->destination_line ?? '-' }}</div>
                                @if($wi->dropoff_notes)
                                    <div class="text-xs text-gray-500">{{ Str::limit($wi->dropoff_notes, 40) }}</div>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-4 text-sm text-gray-900 break-words">
                            {{ $wi->assignedUser->name }}
                        </td>
                        <td class="px-3 sm:px-6 py-4 text-sm text-gray-500">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt mr-1 text-gray-400"></i>
                                <span class="break-words">{{ $wi->deadline->format('d M Y H:i') }}</span>
                                @if($wi->getMainStatus() === 'overdue')
                                    <i class="fas fa-exclamation-triangle ml-2 text-red-500" title="Terlambat"></i>
                                @endif
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4">
                            <div class="space-y-1">
                                <!-- Main Status -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($wi->getMainStatus() === 'completed') bg-green-100 text-green-800
                                    @elseif($wi->getMainStatus() === 'overdue') bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800
                                    @endif"
                                      title="{{ $wi->getMainStatusExplanation() }}">
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
                                    @endif"
                                      title="{{ $wi->getProgressionStatusExplanation() }}">
                                    <i class="fas 
                                        @if($wi->getProgressionStatus() === 'completed') fa-check
                                        @elseif($wi->getProgressionStatus() === 'in_progress') fa-spinner
                                        @else fa-pause
                                        @endif mr-1"></i>
                                    {{ $wi->getProgressionLabel() }}
                                </span>
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-box mr-1"></i>
                                {{ __('admin.wi.items_count', ['count' => $wi->items->count()]) }}
                            </span>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
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
                        <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                            {{ __('admin.dashboard.no_wi') }} <a href="{{ route('admin.work-instructions.create') }}" class="text-blue-600 hover:text-blue-800">{{ __('admin.wi.create_first') }}</a>
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
