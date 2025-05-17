<!-- resources/views/components/admin/admin-footer.blade.php -->
<footer class="w-full py-3 px-4 mt-auto bg-white dark:bg-neutral-800 border-t border-gray-200 dark:border-gray-700 text-sm">
    <div class="flex flex-col md:flex-row justify-between items-center gap-2">
        <div class="text-center md:text-left">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} 
                {{ config('app.name', 'CV.USAHAPRIMALESTARI') }}.
                All rights reserved.
            </p>
        </div>
        <div class="flex items-center gap-3 text-xs">
            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                View Website
            </a>
            @if(config('app.env') === 'local')
                <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                    Development Mode
                </span>
            @endif
            <span class="text-gray-500 dark:text-gray-400">
                v{{ config('app.version', '1.0.0') }}
            </span>
        </div>
    </div>
</footer>