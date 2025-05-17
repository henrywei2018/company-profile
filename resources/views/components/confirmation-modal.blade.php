<!-- resources/views/components/confirmation-modal.blade.php -->
@props(['id', 'title', 'content', 'action', 'actionText' => 'Delete', 'cancelText' => 'Cancel'])

<div id="{{ $id }}" class="hs-overlay hidden w-full h-full fixed top-0 left-0 z-[60] overflow-x-hidden overflow-y-auto">
    <div class="hs-overlay-open:opacity-100 hs-overlay-open:duration-500 opacity-0 transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-gray-800 dark:border-gray-700 dark:shadow-slate-700/[.7]">
            <div class="flex justify-between items-center py-3 px-4 border-b dark:border-gray-700">
                <h3 class="font-bold text-gray-800 dark:text-white">
                    {{ $title }}
                </h3>
                <button type="button" class="hs-dropdown-toggle inline-flex flex-shrink-0 justify-center items-center h-8 w-8 rounded-md text-gray-500 hover:text-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 focus:ring-offset-white transition-all text-sm dark:focus:ring-gray-700 dark:focus:ring-offset-gray-800" data-hs-overlay="#{{ $id }}">
                    <span class="sr-only">Close</span>
                    <svg class="w-3.5 h-3.5" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.258206 1.00652C0.351976 0.912791 0.479126 0.860131 0.611706 0.860131C0.744296 0.860131 0.871447 0.912791 0.965207 1.00652L3.61171 3.65302L6.25822 1.00652C6.30432 0.960515 6.36042 0.924419 6.42207 0.900787C6.48372 0.877156 6.55028 0.865919 6.61736 0.867812C6.68443 0.869704 6.75048 0.884774 6.81057 0.912136C6.87066 0.939498 6.92362 0.978433 6.96533 1.02703C7.00704 1.07563 7.03551 1.13282 7.04915 1.19393C7.06279 1.25504 7.06117 1.31799 7.04444 1.37836C7.02771 1.43873 6.99621 1.49492 6.95246 1.54211C6.90871 1.5893 6.85453 1.62572 6.79406 1.64822L4.14756 4.29471L6.79406 6.94121C6.88325 7.03039 6.93359 7.15181 6.93359 7.27907C6.93359 7.40633 6.88325 7.52775 6.79406 7.61694C6.70487 7.70613 6.58346 7.75647 6.45619 7.75647C6.32893 7.75647 6.20751 7.70613 6.11833 7.61694L3.47183 4.97044L0.825326 7.61694C0.736141 7.70613 0.614725 7.75647 0.487461 7.75647C0.360197 7.75647 0.238781 7.70613 0.149597 7.61694C0.060412 7.52775 0.0100708 7.40633 0.0100708 7.27907C0.0100708 7.15181 0.060412 7.03039 0.149597 6.94121L2.7961 4.29471L0.149597 1.64822C0.0551988 1.5541 0.00132174 1.42938 3.12712e-05 1.29932C-0.00125919 1.16927 0.0500878 1.04343 0.141472 0.947979C0.232856 0.852532 0.358874 0.798131 0.492869 0.797541C0.626864 0.796951 0.75317 0.85018 0.845326 0.944452L0.258206 1.00652Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>
            <div class="p-4 overflow-y-auto">
                <p class="mt-1 text-gray-800 dark:text-gray-400">
                    {{ $content }}
                </p>
            </div>
            <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t dark:border-gray-700">
                <button type="button" class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-md border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-blue-600 transition-all text-sm dark:bg-gray-800 dark:hover:bg-slate-800 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white dark:focus:ring-offset-gray-800" data-hs-overlay="#{{ $id }}">
                    {{ $cancelText }}
                </button>
                <form action="{{ $action }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-md border border-transparent font-semibold bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all text-sm dark:focus:ring-offset-gray-800">
                        {{ $actionText }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>