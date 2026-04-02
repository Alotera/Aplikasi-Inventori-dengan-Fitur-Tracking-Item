<div class="flex items-center gap-1 rounded-lg bg-gray-200/80 p-0.5 text-xs font-semibold" role="group" aria-label="{{ __('nav.language') }}">
    <form method="POST" action="{{ route('locale.switch') }}" class="inline">
        @csrf
        <input type="hidden" name="locale" value="id">
        <button type="submit" class="px-2.5 py-1 rounded-md transition {{ app()->getLocale() === 'id' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">ID</button>
    </form>
    <form method="POST" action="{{ route('locale.switch') }}" class="inline">
        @csrf
        <input type="hidden" name="locale" value="en">
        <button type="submit" class="px-2.5 py-1 rounded-md transition {{ app()->getLocale() === 'en' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">EN</button>
    </form>
</div>
