{{-- resources/views/components/admin/statistics-modal.blade.php --}}
@props([
    'modalId' => 'statistics-modal',
    'title' => 'Statistics',
    'statsEndpoint'
])

<!-- Statistics Modal -->
<div id="{{ $modalId }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
            <button onclick="closeStatistics('{{ $modalId }}')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="{{ $modalId }}-content">
            <!-- Statistics content will be loaded here -->
            <div class="flex items-center justify-center py-8">
                <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Statistics functionality for modal: {{ $modalId }}
function showStatistics(modalId = '{{ $modalId }}') {
    document.getElementById(modalId).classList.remove('hidden');
    loadStatistics(modalId, '{{ $statsEndpoint }}');
}

function closeStatistics(modalId = '{{ $modalId }}') {
    document.getElementById(modalId).classList.add('hidden');
}

function loadStatistics(modalId, endpoint) {
    fetch(endpoint)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderStatistics(data.data, modalId);
            } else {
                document.getElementById(modalId + '-content').innerHTML = 
                    '<div class="text-center py-8 text-red-600">Failed to load statistics</div>';
            }
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
            document.getElementById(modalId + '-content').innerHTML = 
                '<div class="text-center py-8 text-red-600">Failed to load statistics</div>';
        });
}

function renderStatistics(stats, modalId) {
    const content = `
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            ${Object.entries(stats.overview || {}).map(([key, value]) => `
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">${value.count || value}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">${value.label || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</div>
                </div>
            `).join('')}
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Recent Items</h4>
                <div class="space-y-2">
                    ${(stats.recent_items || []).map(item => `
                        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                            <div>
                                <div class="font-medium text-sm">${item.title || item.name}</div>
                                <div class="text-xs text-gray-500">${item.category || ''} • ${item.created_at || item.date}</div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded ${item.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">${item.status || 'unknown'}</span>
                        </div>
                    `).join('') || '<div class="text-sm text-gray-500">No recent items</div>'}
                </div>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Categories</h4>
                <div class="space-y-2">
                    ${(stats.popular_categories || []).map(category => `
                        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                            <div class="font-medium text-sm">${category.name}</div>
                            <span class="text-xs text-gray-500">${category.count || category.items_count} items</span>
                        </div>
                    `).join('') || '<div class="text-sm text-gray-500">No categories</div>'}
                </div>
            </div>
        </div>
        
        ${stats.additional_metrics ? `
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                ${Object.entries(stats.additional_metrics).map(([key, value]) => `
                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                        <span class="font-medium">${key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}:</span> ${value}
                    </div>
                `).join('')}
            </div>
        ` : ''}
    `;
    
    document.getElementById(modalId + '-content').innerHTML = content;
}

// Close modal when clicking outside
document.getElementById('{{ $modalId }}').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStatistics('{{ $modalId }}');
    }
});
</script>
@endpush