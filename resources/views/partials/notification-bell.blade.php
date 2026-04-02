@php
    $user = auth()->user();
    $recentNotifications = $user->notifications()->latest()->limit(15)->get();
    $unreadCount = $user->unreadNotifications()->count();
@endphp
<div class="relative" x-data="{ notifOpen: false }" @keydown.escape.window="notifOpen = false">
    <button
        type="button"
        @click="notifOpen = !notifOpen"
        class="relative p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500"
        aria-expanded="false"
        :aria-expanded="notifOpen"
        aria-label="{{ __('notifications.bell.aria') }}"
    >
        <i class="fas fa-bell text-lg"></i>
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full min-w-[1.25rem]">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div
        x-show="notifOpen"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        @click.outside="notifOpen = false"
        class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-50 max-h-[min(24rem,70vh)] flex flex-col"
        style="display: none;"
    >
        <div class="px-3 py-2 border-b border-gray-100 flex items-center justify-between gap-2">
            <span class="text-sm font-semibold text-gray-800">{{ __('notifications.bell.aria') }}</span>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                        {{ __('notifications.bell.mark_all_read') }}
                    </button>
                </form>
            @endif
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($recentNotifications as $n)
                @php
                    $data = $n->data ?? [];
                    $titleKey = $data['title_key'] ?? '';
                    $bodyKey = $data['body_key'] ?? '';
                    $replace = $data['replace'] ?? [];
                    $actionUrl = $data['action_url'] ?? null;
                @endphp
                <div class="border-b border-gray-50 last:border-0 {{ $n->read_at ? '' : 'bg-blue-50/50' }}">
                    <div class="px-3 py-2.5">
                        @if($actionUrl)
                            <a href="{{ $actionUrl }}" class="block hover:opacity-90" @click="notifOpen = false">
                                <p class="text-sm font-medium text-gray-900">{{ $titleKey ? __($titleKey, $replace) : '' }}</p>
                                <p class="text-xs text-gray-600 mt-0.5 line-clamp-3">{{ $bodyKey ? __($bodyKey, $replace) : '' }}</p>
                                <p class="text-[10px] text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                            </a>
                        @else
                            <p class="text-sm font-medium text-gray-900">{{ $titleKey ? __($titleKey, $replace) : '' }}</p>
                            <p class="text-xs text-gray-600 mt-0.5 line-clamp-3">{{ $bodyKey ? __($bodyKey, $replace) : '' }}</p>
                            <p class="text-[10px] text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                        @endif
                        @if(!$n->read_at)
                            <form method="POST" action="{{ route('notifications.read', $n->id) }}" class="mt-1.5">
                                @csrf
                                <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">{{ __('notifications.bell.mark_read') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="px-3 py-6 text-sm text-gray-500 text-center">{{ __('notifications.bell.empty') }}</p>
            @endforelse
        </div>
    </div>
</div>
