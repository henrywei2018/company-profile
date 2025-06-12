{{-- resources/views/components/admin/bulk-actions.blade.php --}}
@props([
    'formId' => 'bulk-form',
    'actionRoute',
    'actions' => [],
    'selectedCountText' => 'items selected'
])

<!-- Bulk Actions Form -->
<form id="{{ $formId }}" method="POST" action="{{ $actionRoute }}" class="hidden">
    @csrf
    <input type="hidden" name="action" id="bulk-action">
</form>

<!-- Bulk Actions Bar -->
<div id="bulk-actions" class="hidden mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
    <div class="flex items-center justify-between">
        <span id="selected-count" class="text-sm font-medium text-blue-900 dark:text-blue-100">
            0 {{ $selectedCountText }}
        </span>
        <div class="flex gap-2">
            @foreach($actions as $action)
                <button type="button" 
                        onclick="bulkAction('{{ $action['value'] }}')" 
                        class="px-3 py-1 text-xs font-medium rounded-md hover:{{ $action['hoverColor'] ?? 'bg-gray-200' }} {{ $action['bgColor'] ?? 'bg-gray-100' }} {{ $action['textColor'] ?? 'text-gray-700' }}">
                    {{ $action['label'] }}
                </button>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bulk selection functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    
    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateBulkActions();
        });
    }
    
    // Individual checkbox change
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = checkedBoxes.length === itemCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < itemCheckboxes.length;
            }
            updateBulkActions();
        });
    });
    
    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (count > 0) {
            bulkActions.classList.remove('hidden');
            selectedCount.textContent = `${count} {{ $selectedCountText }}`;
        } else {
            bulkActions.classList.add('hidden');
        }
    }
});

// Bulk actions function
function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select at least one item.');
        return;
    }

    const itemIds = Array.from(checkedBoxes).map(cb => cb.value);

    let confirmMessage = '';
    switch(action) {
        case 'delete':
            confirmMessage = `Are you sure you want to delete ${itemIds.length} item(s)?`;
            break;
        case 'activate':
            confirmMessage = `Are you sure you want to activate ${itemIds.length} item(s)?`;
            break;
        case 'deactivate':
            confirmMessage = `Are you sure you want to deactivate ${itemIds.length} item(s)?`;
            break;
        default:
            confirmMessage = `Are you sure you want to ${action} ${itemIds.length} item(s)?`;
    }

    if (!confirm(confirmMessage)) {
        return;
    }

    // Set action input
    document.getElementById('bulk-action').value = action;

    // Clear existing item_ids inputs
    const bulkForm = document.getElementById('{{ $formId }}');
    const existingInputs = bulkForm.querySelectorAll('input[name*="_ids[]"]');
    existingInputs.forEach(el => el.remove());

    // Add fresh item_ids[] inputs
    const inputName = getInputName(action);
    itemIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = inputName;
        input.value = id;
        bulkForm.appendChild(input);
    });

    bulkForm.submit();
}

function getInputName(action) {
    // This can be customized based on your naming convention
    return 'item_ids[]'; // Default, can be overridden
}
</script>
@endpush