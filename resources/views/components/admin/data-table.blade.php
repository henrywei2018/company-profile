<!-- resources/views/components/admin/data-table.blade.php -->
@props([
    'header' => null, 
    'footer' => null, 
    'striped' => true,
    'hover' => true,
    'bordered' => true,
    'responsive' => true,
    'checkbox' => false
])

<div class="w-full">
    @if($header)
    <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-neutral-700">
        {{ $header }}
    </div>
    @endif
    
    <!-- Fixed: Removed nested overflow containers that caused double scrollbars -->
    <div class="{{ $bordered ? 'border border-gray-200 dark:border-neutral-700 rounded-xl overflow-hidden' : '' }}">
        @if($responsive)
        <div class="overflow-x-auto">
        @endif
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                @if(isset($columns))
                <thead class="bg-gray-50 dark:bg-neutral-800">
                    <tr>
                        @if($checkbox)
                        <th scope="col" class="ps-6 py-3 text-start">
                            <div class="flex">
                                <input id="hs-at-with-checkboxes-main" type="checkbox" class="shrink-0 border-gray-300 rounded-sm text-blue-600 focus:ring-blue-500 checked:border-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                                <label for="hs-at-with-checkboxes-main" class="sr-only">Checkbox</label>
                            </div>
                        </th>
                        @endif
                        
                        {{ $columns }}
                    </tr>
                </thead>
                @endif
                
                <tbody class="{{ $striped ? 'divide-y divide-gray-200 dark:divide-neutral-700' : '' }}">
                    {{ $slot }}
                </tbody>
            </table>
        @if($responsive)
        </div>
        @endif
    </div>
    
    @if($footer)
    <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-neutral-700">
        {{ $footer }}
    </div>
    @endif
</div>