@extends('layouts.user')

@section('title', __('user.report.title'))
@section('page-title', __('user.report.heading', ['number' => $wi->wi_number]))
@section('page-description', '')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg p-6">
        <form method="post" action="{{ route('user.work-instructions.report.store', $wi) }}" class="space-y-6">
            @csrf
            <div>
                <label for="summary" class="block text-sm font-medium text-gray-700 mb-2">{{ __('user.report.summary') }}</label>
                <textarea id="summary" name="summary" rows="5" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
            </div>
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">{{ __('user.report.notes') }}</label>
                <textarea id="notes" name="notes" rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('user.work-instructions.show', $wi) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    {{ __('user.report.cancel') }}
                </a>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    {{ __('user.report.submit') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
