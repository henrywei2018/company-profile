<!-- resources/views/components/admin/breadcrumb-mobile.blade.php -->
@props(['parent' => 'Admin Panel', 'current' => null])

<div class="sticky top-0 inset-x-0 z-20 bg-white border-y border-gray-200 px-4 sm:px-6 md:px-8 lg:hidden dark:bg-gray-800 dark:border-gray-700">
    <div class="flex items-center py-2">
        <!-- Navigation Toggle -->
        <button type="button" class="inline-flex flex-shrink-0 justify-center items-center size-8 rounded-lg text-gray-800 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:text-gray-400 dark:hover:text-gray-300" 
            data-hs-overlay="#hs-application-sidebar" 
            aria-controls="hs-application-sidebar" 
            aria-label="Toggle navigation">
            <span class="sr-only">Toggle Navigation</span>
            <svg class="size-4" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"></path>
            </svg>
        </button>
        <!-- End Navigation Toggle -->

        <!-- Breadcrumb -->
        <ol class="ms-3 flex items-center whitespace-nowrap">
            <li class="flex items-center text-sm text-gray-800 dark:text-gray-400">
                {{ $parent }}
                @if($current)
                <svg class="shrink-0 mx-3 overflow-visible size-2.5 text-gray-400 dark:text-gray-600" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                @endif
            </li>
            @if($current)
            <li class="text-sm font-semibold text-gray-800 truncate dark:text-gray-400" aria-current="page">
                {{ $current }}
            </li>
            @endif
        </ol>
        <!-- End Breadcrumb -->
    </div>
</div>