<!-- resources/views/components/tabs.blade.php -->
<div>
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
            {{ $tabs }}
        </nav>
    </div>
    <div class="mt-3">
        {{ $content }}
    </div>
</div>