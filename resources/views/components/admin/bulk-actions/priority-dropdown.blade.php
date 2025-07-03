{{-- resources/views/components/admin/bulk-actions/priority-dropdown.blade.php --}}

@props([
    'priorities' => [
        'low' => ['label' => 'Low Priority', 'color' => 'gray'],
        'normal' => ['label' => 'Normal Priority', 'color' => 'blue'],
        'high' => ['label' => 'High Priority', 'color' => 'yellow'],
        'urgent' => ['label' => 'Urgent Priority', 'color' => 'red']
    ]
])

<div class="relative">
    <button type="button" id="priority-dropdown-btn" onclick="togglePriorityDropdown()"
            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
        <x-admin.icons.tag class="w-4 h-4 mr-1" />
        Set Priority
        <x-admin.icons.chevron-down class="w-4 h-4 ml-1" />
    </button>
    
    <div id="priority-dropdown" class="hidden absolute right-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-50 border border-gray-200 dark:border-gray-700">
        <div class="py-1">
            @foreach($priorities as $key => $priority)
                <button type="button" onclick="bulkSetPriority('{{ $key }}')" 
                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span class="inline-block w-3 h-3 bg-{{ $priority['color'] }}-400 rounded-full mr-2"></span>
                    {{ $priority['label'] }}
                </button>
            @endforeach
        </div>
    </div>
</div>

<script>
function togglePriorityDropdown() {
    const dropdown = document.getElementById('priority-dropdown');
    dropdown.classList.toggle('hidden');
    
    // Close other dropdown
    document.getElementById('more-actions-dropdown')?.classList.add('hidden');
}
</script>