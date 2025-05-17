
<!-- resources/views/components/activity-item.blade.php -->
@props(['route', 'icon', 'iconColor' => 'text-gray-400', 'iconBg' => 'bg-gray-100', 'time'])

<li class="py-3">
    <div class="flex items-start space-x-4">
        <div class="flex-shrink-0">
            <span class="inline-flex items-center justify-center h-10 w-10 rounded-md {{ $iconBg }} dark:bg-gray-700">
                <svg class="h-5 w-5 {{ $iconColor }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    {!! $icon !!}
                </svg>
            </span>
        </div>
        <div class="min-w-0 flex-1">
            <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                <a href="{{ $route }}" class="hover:underline">
                    {{ $slot }}
                </a>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $time }}
            </p>
        </div>
    </div>
</li>