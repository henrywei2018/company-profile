<!-- resources/views/components/table.blade.php -->
<div class="flex flex-col">
    <div class="-m-1.5 overflow-x-auto">
        <div class="p-1.5 min-w-full inline-block align-middle">
            <div class="border rounded-lg overflow-hidden dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    @if(isset($header))
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        {{ $header }}
                    </thead>
                    @endif
                    
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        {{ $slot }}
                    </tbody>
                    
                    @if(isset($footer))
                    <tfoot class="bg-gray-50 dark:bg-gray-700">
                        {{ $footer }}
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>