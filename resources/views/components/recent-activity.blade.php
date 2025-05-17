<!-- resources/views/components/recent-activity.blade.php -->
@props(['title', 'viewAllRoute' => null])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $title }}</h3>
        @if($viewAllRoute)
            <a href="{{ $viewAllRoute }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View all</a>
        @endif
    </div>
    <div class="p-4">
        <div class="flow-root">
            <ul role="list" class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                {{ $slot }}
            </ul>
        </div>
    </div>
</div>