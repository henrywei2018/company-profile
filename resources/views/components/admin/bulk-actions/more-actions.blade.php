{{-- resources/views/components/admin/bulk-actions/more-actions.blade.php --}}

@props([
    'canForceDelete' => false,
    'showAssignActions' => false,
])

<div class="relative">
    <button type="button" id="more-actions-btn" onclick="toggleMoreActionsDropdown()"
            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
        More Actions
        <x-admin.icons.chevron-down class="w-4 h-4 ml-1" />
    </button>
    
    <div id="more-actions-dropdown" class="hidden absolute right-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-50 border border-gray-200 dark:border-gray-700">
        <div class="py-1">
            <button type="button" onclick="bulkDelete()" 
                    class="w-full text-left px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                <x-admin.icons.trash class="w-4 h-4 inline mr-2" />
                Delete Messages
            </button>
            
            <button type="button" onclick="bulkDeleteThreads()" 
                    class="w-full text-left px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                <x-admin.icons.x-circle class="w-4 h-4 inline mr-2" />
                Delete Conversations
            </button>
            
            @if($canForceDelete)
                <button type="button" onclick="bulkForceDelete()" 
                        class="w-full text-left px-4 py-2 text-sm text-red-800 dark:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 font-medium">
                    <x-admin.icons.exclamation class="w-4 h-4 inline mr-2" />
                    Force Delete
                </button>
            @endif
            
            @if($showAssignActions)
                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                <button type="button" onclick="showAssignDialog()" 
                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <x-admin.icons.user-group class="w-4 h-4 inline mr-2" />
                    Assign to Admin
                </button>
            @endif
            
            <div class="border-t border-gray-200 dark:border-gray-600"></div>
            <button type="button" onclick="previewBulkAction()" 
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <x-admin.icons.eye class="w-4 h-4 inline mr-2" />
                Preview Impact
            </button>
        </div>
    </div>
</div>

<script>
function toggleMoreActionsDropdown() {
    const dropdown = document.getElementById('more-actions-dropdown');
    dropdown.classList.toggle('hidden');
    
    // Close other dropdown
    document.getElementById('priority-dropdown')?.classList.add('hidden');
}

async function bulkForceDelete() {
    showConfirmationModal(
        'Force Delete Messages',
        `⚠️ WARNING: Force delete will permanently remove ${selectedMessages.size} message(s) and cannot be undone. This bypasses all safety checks.`,
        'delete',
        { force: true }
    );
}

function showAssignDialog() {
    // Implementation for showing assignment dialog
    // This would show a modal with admin selection
    alert('Assignment dialog would open here');
}

async function previewBulkAction() {
    if (selectedMessages.size === 0) {
        showNotification('Please select messages first.', 'warning');
        return;
    }
    
    try {
        const response = await fetch(bulkActionConfig.previewRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete',
                message_ids: Array.from(selectedMessages)
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showImpactPreview(data.impact_info, data.message);
        } else {
            showNotification(data.message || 'Failed to preview action impact.', 'error');
        }
    } catch (error) {
        console.error('Preview error:', error);
        showNotification('An error occurred while previewing action impact.', 'error');
    }
}

function showImpactPreview(impactInfo, message) {
    const modal = document.getElementById('bulk-action-modal');
    const title = document.getElementById('modal-title');
    const messageEl = document.getElementById('modal-message');
    const impactPreview = document.getElementById('impact-preview');
    const impactDetails = document.getElementById('impact-details');
    const confirmBtn = document.getElementById('confirm-action-btn');
    
    title.textContent = 'Action Impact Preview';
    messageEl.textContent = message;
    
    // Show impact details
    impactDetails.innerHTML = `
        <div class="space-y-1">
            <div>Total messages: <strong>${impactInfo.total_messages}</strong></div>
            ${impactInfo.urgent_messages > 0 ? `<div class="text-red-600">Urgent messages: <strong>${impactInfo.urgent_messages}</strong></div>` : ''}
            ${impactInfo.project_linked > 0 ? `<div>Project-linked: <strong>${impactInfo.project_linked}</strong></div>` : ''}
            ${impactInfo.unique_clients > 0 ? `<div>Unique clients: <strong>${impactInfo.unique_clients}</strong></div>` : ''}
            ${impactInfo.with_attachments > 0 ? `<div>With attachments: <strong>${impactInfo.with_attachments}</strong></div>` : ''}
        </div>
    `;
    
    impactPreview.classList.remove('hidden');
    confirmBtn.textContent = 'Close';
    confirmBtn.className = 'px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700';
    confirmBtn.onclick = closeModal;
    
    modal.classList.remove('hidden');
}
</script>