@extends('layouts.admin')

@section('title', __('admin.wi.create_title'))
@section('page-title', __('admin.wi.create_page_title'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('admin.work-instructions.store') }}" id="wi-form">
                @csrf
                
                <div class="space-y-8">
                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('admin.wi.basic_information') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('admin.wi.wi_number') }}</label>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('admin.wi.wi_number_auto') }}
                                </p>
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">{{ __('admin.wi.type_required') }}</label>
                                <select name="type" id="type" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('type') border-red-300 @enderror">
                                    <option value="">{{ __('admin.wi.select_type') }}</option>
                                    <option value="checking" {{ old('type') === 'checking' ? 'selected' : '' }}>Checking</option>
                                    <option value="ambil" {{ old('type') === 'ambil' ? 'selected' : '' }}>Ambil</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="ambil-extra" class="mt-6" style="display: none;">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle text-blue-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">{{ __('admin.wi.ambil_info_title') }}</h3>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <p>{{ __('admin.wi.ambil_info_desc') }}</p>
                                            <ul class="list-disc list-inside mt-1 space-y-1">
                                                <li>{{ __('admin.wi.ambil_info_point_1') }}</li>
                                                <li>{{ __('admin.wi.ambil_info_point_2') }}</li>
                                                <li>{{ __('admin.wi.ambil_info_point_3') }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="destination_line" class="block text-sm font-medium text-gray-700">
                                        <i class="fas fa-map-marker-alt mr-1 text-blue-500"></i>
                                        {{ __('admin.wi.destination_line_required') }}
                                    </label>
                                    @php($currentDestinationLine = old('destination_line'))
                                    <select name="destination_line" id="destination_line" required
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('destination_line') border-red-300 @enderror">
                                        <option value="">{{ __('admin.wi.select_destination_line') }}</option>
                                        @if(!empty($currentDestinationLine) && ! $productionLines->contains(fn($pl) => $pl->name === $currentDestinationLine))
                                            <option value="{{ $currentDestinationLine }}" selected>{{ $currentDestinationLine }}</option>
                                        @endif
                                        @foreach($productionLines as $pl)
                                            <option value="{{ $pl->name }}" {{ $currentDestinationLine === $pl->name ? 'selected' : '' }}>
                                                {{ $pl->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">{{ __('admin.wi.destination_line_help') }}</p>
                                    @error('destination_line')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="dropoff_notes" class="block text-sm font-medium text-gray-700">
                                        <i class="fas fa-sticky-note mr-1 text-green-500"></i>
                                        {{ __('admin.wi.dropoff_notes') }}
                                    </label>
                                    <textarea name="dropoff_notes" id="dropoff_notes" rows="3"
                                              placeholder="{{ __('admin.wi.dropoff_notes_placeholder') }}"
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('dropoff_notes') border-red-300 @enderror">{{ old('dropoff_notes') }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">{{ __('admin.wi.dropoff_notes_help') }}</p>
                                    @error('dropoff_notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="title" class="block text-sm font-medium text-gray-700">{{ __('admin.wi.title_required') }}</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('title') border-red-300 @enderror">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">{{ __('admin.wi.description') }}</label>
                            <textarea name="description" id="description" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="assigned_user_id" class="block text-sm font-medium text-gray-700">{{ __('admin.wi.assigned_user_required') }}</label>
                                <select name="assigned_user_id" id="assigned_user_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('assigned_user_id') border-red-300 @enderror">
                                    <option value="">{{ __('admin.wi.select_user') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_user_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="deadline" class="block text-sm font-medium text-gray-700">{{ __('admin.wi.deadline_required') }}</label>
                                <input type="datetime-local" name="deadline" id="deadline" value="{{ old('deadline') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('deadline') border-red-300 @enderror">
                                @error('deadline')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Items Selection -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('admin.wi.items_selection') }}</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-sm text-gray-600">{{ __('admin.wi.select_items_for_wi') }}</span>
                                <button type="button" id="add-item" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-plus mr-1"></i>
                                    {{ __('admin.wi.add_item') }}
                                </button>
                            </div>
                            
                            <div id="items-container">
                                <!-- Items will be added here dynamically -->
                            </div>
                            
                            @error('items')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.work-instructions.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('admin.wi.cancel') }}
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('admin.wi.create_submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsContainer = document.getElementById('items-container');
    const addItemBtn = document.getElementById('add-item');
    const items = @json($items);
    const i18n = {
        selectItem: @json(__('admin.wi.select_item')),
        qtyPlaceholder: @json(__('admin.wi.qty_placeholder'))
    };
    const typeSelect = document.getElementById('type');
    const ambilExtra = document.getElementById('ambil-extra');
    let itemCount = 0;

    function toggleAmbilExtra() {
        const isAmbil = typeSelect.value === 'ambil';
        ambilExtra.style.display = isAmbil ? '' : 'none';
        const destInput = document.getElementById('destination_line');
        if (destInput) {
            if (isAmbil) destInput.setAttribute('required', 'required');
            else destInput.removeAttribute('required');
        }
    }

    function addItemRow() {
        const itemRow = document.createElement('div');
        itemRow.className = 'item-row flex items-center space-x-4 mb-4 p-3 bg-white rounded border';
        itemRow.innerHTML = `
            <div class="flex-1">
                <select name="items[${itemCount}][item_id]" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">${i18n.selectItem}</option>
                    ${items.map(item => `<option value="${item.id}">${item.item_code} - ${item.name}</option>`).join('')}
                </select>
            </div>
            <div class="w-32">
                <input type="number" name="items[${itemCount}][required_quantity]" placeholder="${i18n.qtyPlaceholder}" min="1" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div>
                <button type="button" class="remove-item text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        itemsContainer.appendChild(itemRow);
        itemCount++;

        // Add remove functionality
        itemRow.querySelector('.remove-item').addEventListener('click', function() {
            itemRow.remove();
        });
    }

    addItemBtn.addEventListener('click', addItemRow);

    // Add initial item row
    addItemRow();

    // Initialize ambil extra visibility
    toggleAmbilExtra();
    typeSelect.addEventListener('change', toggleAmbilExtra);
});
</script>
@endsection
