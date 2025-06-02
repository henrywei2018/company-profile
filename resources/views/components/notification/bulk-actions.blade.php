@props([
    'variant' => 'default'
])

<div class="px-6 py-3 bg-gray-50 dark:bg-neutral-900 border-b border-gray-200 dark:border-neutral-700">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <input 
                type="checkbox" 
                id="select-all-notifications"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                onchange="toggleSelectAll()"
            >
            <label for="select-all-notifications" class="text-sm font-medium text-gray-700 dark:text-neutral-300">
                Select all
            </label>
            <span id="selected-count" class="text-sm text-gray-500 dark:text-neutral-400">0 selected</span>
        </div>

        <div class="flex items-center space-x-2">
            <button 
                type="button"
                onclick="bulkMarkAsRead()"
                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800 disabled:opacity-50 disabled:cursor-not-allowed"
                disabled
                id="bulk-mark-read-btn"
            >
                Mark as read
            </button>
            <button 
                type="button"
                onclick="bulkDelete()"
                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-900 dark:text-red-200 dark:hover:bg-red-800 disabled:opacity-50 disabled:cursor-not-allowed"
                disabled
                id="bulk-delete-btn"
            >
                Delete
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedNotifications = new Set();

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('select-all-notifications');
    const checkboxes = document.querySelectorAll('.notification-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
        if (selectAllCheckbox.checked) {
            selectedNotifications.add(checkbox.value);
        } else {
            selectedNotifications.delete(checkbox.value);
        }
    });
    
    updateBulkActionButtons();
}
function handleNotificationClick(notificationId, url) {
    // Mark as read first
    markNotificationAsRead(notificationId);
    
    // Navigate to URL if provided
    if (url && url !== '#') {
        window.location.href = url;
    }
}

function markNotificationAsRead(notificationId) {
    const route = '{{ $variant === "admin" ? "/admin/notifications/" : "/client/notifications/" }}' + notificationId + '/read';
    
    fetch(route, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI without full reload
            const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationElement) {
                notificationElement.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
                notificationElement.classList.add('bg-white', 'dark:bg-neutral-800');
            }
        }
    })
    .catch(error => console.error('Error:', error));
}
function toggleNotificationSelection(notificationId) {
    if (selectedNotifications.has(notificationId)) {
        selectedNotifications.delete(notificationId);
    } else {
        selectedNotifications.add(notificationId);
    }
    
    updateBulkActionButtons();
    updateSelectAllCheckbox();
}

function updateBulkActionButtons() {
    const count = selectedNotifications.size;
    const countSpan = document.getElementById('selected-count');
    const markReadBtn = document.getElementById('bulk-mark-read-btn');
    const deleteBtn = document.getElementById('bulk-delete-btn');
    
    countSpan.textContent = `${count} selected`;
    markReadBtn.disabled = count === 0;
    deleteBtn.disabled = count === 0;
}

function updateSelectAllCheckbox() {
    const selectAllCheckbox = document.getElementById('select-all-notifications');
    const checkboxes = document.querySelectorAll('.notification-checkbox');
    const checkedBoxes = document.querySelectorAll('.notification-checkbox:checked');
    
    selectAllCheckbox.checked = checkboxes.length > 0 && checkedBoxes.length === checkboxes.length;
    selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < checkboxes.length;
}

function bulkMarkAsRead() {
    const route = '{{ $variant === "admin" ? route("admin.notifications.bulk-mark-as-read") : route("client.notifications.mark-all-read") }}';
    
    fetch(route, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            notification_ids: Array.from(selectedNotifications)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function bulkDelete() {
    if (!confirm('Are you sure you want to delete the selected notifications?')) {
        return;
    }
    
    const route = '{{ $variant === "admin" ? route("admin.notifications.bulk-delete") : route("client.notifications.clear-read") }}';
    
    fetch(route, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            notification_ids: Array.from(selectedNotifications)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endpush