{{-- resources/views/admin/projects/edit.blade.php --}}
<x-layouts.admin title="Edit Project">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Project</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Update project information and settings
            </p>
        </div>
        
        <div class="flex items-center space-x-3 mt-4 md:mt-0">
            <!-- Status Badge -->
            <x-admin.badge 
                type="{{ $project->status === 'completed' ? 'success' : ($project->status === 'in_progress' ? 'warning' : 'info') }}"
                size="lg"
            >
                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
            </x-admin.badge>
            
            <!-- Quick Actions -->
            <x-admin.button 
                href="{{ route('admin.projects.show', $project) }}" 
                color="light"
                size="sm"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View Project
            </x-admin.button>
            
            @if($project->slug)
                <x-admin.button 
                    href="{{ route('portfolio.projects.show', $project->slug) }}" 
                    color="info"
                    size="sm"
                    target="_blank"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    View Live
                </x-admin.button>
            @endif
        </div>
    </div>

    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Projects' => route('admin.projects.index'),
        $project->title => route('admin.projects.show', $project),
        'Edit' => route('admin.projects.edit', $project)
    ]" class="mb-6" />

    <!-- Project Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-admin.stat-card
            title="Progress"
            :value="($project->progress_percentage ?? 0) . '%'"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'
            iconColor="text-blue-500"
            iconBg="bg-blue-100 dark:bg-blue-800/30"
        />
        
        <x-admin.stat-card
            title="Images"
            :value="$project->images->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>'
            iconColor="text-green-500"
            iconBg="bg-green-100 dark:bg-green-800/30"
        />
        
        <x-admin.stat-card
            title="Files"
            :value="$project->files->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
            iconColor="text-purple-500"
            iconBg="bg-purple-100 dark:bg-purple-800/30"
        />
        
        <x-admin.stat-card
            title="Milestones"
            :value="$project->milestones->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h6a2 2 0 002-2V7a2 2 0 00-2-2h-2m0 0V3a1 1 0 00-1-1H8a1 1 0 00-1 1v2z"/>'
            iconColor="text-amber-500"
            iconBg="bg-amber-100 dark:bg-amber-800/30"
        />
    </div>

    <!-- Project Information -->
    @if($project->quotation)
        <x-admin.alert type="info" class="mb-6">
            <x-slot name="title">Project Created from Quotation</x-slot>
            This project was created from 
            <a href="{{ route('admin.quotations.show', $project->quotation) }}" class="font-medium text-blue-600 hover:text-blue-800 underline">
                Quotation #{{ $project->quotation->id }}
            </a>
            submitted by {{ $project->quotation->name }}.
        </x-admin.alert>
    @endif

    <!-- Project Status Alerts -->
    @if($project->isOverdue())
        <x-admin.alert type="danger" class="mb-6">
            <x-slot name="title">Project Overdue</x-slot>
            This project is {{ now()->diffInDays($project->end_date) }} days overdue. 
            Consider updating the timeline or status.
        </x-admin.alert>
    @elseif($project->end_date && $project->end_date->diffInDays(now()) <= 7 && $project->status === 'in_progress')
        <x-admin.alert type="warning" class="mb-6">
            <x-slot name="title">Deadline Approaching</x-slot>
            This project is due in {{ now()->diffInDays($project->end_date) }} days.
        </x-admin.alert>
    @endif

    <!-- Enhanced Project Form -->
    <x-admin.project-form
        :project="$project"
        :clients="$clients"
        :categories="$categories" 
        :services="$services"
        :action="route('admin.projects.update', $project)"
        method="PUT"
    />

    <!-- Quick Actions FAB -->
    <div class="fixed bottom-4 right-4 z-50">
        <x-admin.floating-action-button :actions="[
            [
                'title' => 'Add Milestone', 
                'href' => route('admin.projects.milestones.create', $project),
                'icon' => '<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 6v6m0 0v6m0-6h6m-6 0H6\"/>',
                'color_classes' => 'bg-green-600 hover:bg-green-700'
            ],
            [
                'title' => 'Upload Files', 
                'href' => route('admin.projects.files.create', $project),
                'icon' => '<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12\"/>',
                'color_classes' => 'bg-purple-600 hover:bg-purple-700'
            ],
            [
                'title' => 'View Details', 
                'href' => route('admin.projects.show', $project),
                'icon' => '<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15 12a3 3 0 11-6 0 3 3 0 016 0z\"/>',
                'color_classes' => 'bg-blue-600 hover:bg-blue-700'
            ],
            [
                'title' => 'All Projects', 
                'href' => route('admin.projects.index'),
                'icon' => '<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10\"/>',
                'color_classes' => 'bg-gray-600 hover:bg-gray-700'
            ]
        ]" />
    </div>

    <!-- Delete Confirmation Modal -->
    <x-admin.modal id="delete-project-modal" title="Delete Project" size="md">
        <div class="text-sm text-gray-600 dark:text-gray-400">
            <p class="mb-4">Are you sure you want to delete this project? This action cannot be undone.</p>
            <p class="font-medium text-red-600 dark:text-red-400">
                This will permanently delete:
            </p>
            <ul class="list-disc list-inside mt-2 space-y-1">
                <li>{{ $project->images->count() }} project images</li>
                <li>{{ $project->files->count() }} project files</li>
                <li>{{ $project->milestones->count() }} project milestones</li>
                <li>All project history and data</li>
            </ul>
        </div>
        
        <x-slot name="footer">
            <x-admin.button color="light" onclick="document.getElementById('delete-project-modal').classList.add('hidden')">
                Cancel
            </x-admin.button>
            <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <x-admin.button type="submit" color="danger">
                    Delete Project
                </x-admin.button>
            </form>
        </x-slot>
    </x-admin.modal>
</x-layouts.admin>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-save functionality for form updates
    let autoSaveTimeout;
    const form = document.querySelector('form[method="POST"]');
    
    if (form) {
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(autoSaveTimeout);
                
                // Show auto-save indicator
                showAutoSaveIndicator('saving');
                
                autoSaveTimeout = setTimeout(() => {
                    // Perform auto-save (if implemented)
                    performAutoSave();
                }, 3000);
            });
        });
    }
    
    function showAutoSaveIndicator(status) {
        // Remove existing indicators
        const existing = document.querySelector('.auto-save-indicator');
        if (existing) existing.remove();
        
        const indicator = document.createElement('div');
        indicator.className = 'auto-save-indicator fixed top-4 right-4 px-3 py-2 rounded-md text-sm z-50';
        
        if (status === 'saving') {
            indicator.className += ' bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
            indicator.innerHTML = '<div class="flex items-center"><svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Auto-saving...</div>';
        } else if (status === 'saved') {
            indicator.className += ' bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
            indicator.innerHTML = '<div class="flex items-center"><svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>Auto-saved</div>';
        }
        
        document.body.appendChild(indicator);
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            if (indicator.parentNode) {
                indicator.remove();
            }
        }, 3000);
    }
    
    function performAutoSave() {
        // Implement auto-save logic here
        // For now, just show the saved indicator
        showAutoSaveIndicator('saved');
    }
    
    // Form validation before submit
    const submitButton = form?.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.addEventListener('click', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let hasErrors = false;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    hasErrors = true;
                    field.classList.add('border-red-500');
                    field.focus();
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                alert('Please fill in all required fields before saving.');
                return false;
            }
        });
    }
});

// Helper function to show delete confirmation
function confirmDelete() {
    document.getElementById('delete-project-modal').classList.remove('hidden');
}
</script>
@endpush