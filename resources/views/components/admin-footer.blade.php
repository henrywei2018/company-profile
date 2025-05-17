<!-- resources/views/components/admin-footer.blade.php -->
<footer class="mt-auto py-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <div class="text-center sm:text-left mb-2 sm:mb-0">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" target="_blank" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                    View Website
                </a>
                @if(config('app.env') === 'local')
                    <span class="text-sm px-2 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                        Development Mode
                    </span>
                @endif
                <span class="text-xs text-gray-400 dark:text-gray-500">
                    v{{ config('app.version', '1.0.0') }}
                </span>
            </div>
        </div>
    </div>
</footer>