{{-- resources/views/admin/projects/create.blade.php --}}
<x-layouts.admin title="Create New Project">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Project</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Add a new project to your portfolio</p>
        </div>
        
        @if(isset($quotation))
            <x-admin.badge type="info" size="lg">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                From Quotation #{{ $quotation->id }}
            </x-admin.badge>
        @endif
    </div>

    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Projects' => route('admin.projects.index'),
        'Create Project' => route('admin.projects.create')
    ]" class="mb-6" />

    <!-- Quotation Info (if creating from quotation) -->
    @if(isset($quotation))
        <x-admin.alert type="info" class="mb-6">
            <x-slot name="title">Creating Project from Approved Quotation</x-slot>
            <div class="mt-2">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium">Client:</span> {{ $quotation->name }}
                    </div>
                    <div>
                        <span class="font-medium">Project Type:</span> {{ $quotation->project_type }}
                    </div>
                    <div>
                        <span class="font-medium">Budget:</span> {{ $quotation->budget_range ?? 'Not specified' }}
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('admin.quotations.show', $quotation) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View Original Quotation →
                    </a>
                </div>
            </div>
        </x-admin.alert>
    @endif

    <!-- Help Text -->
    <x-admin.help-text type="info" class="mb-6" dismissible>
        <x-slot name="title">Project Creation Tips</x-slot>
        Fill in all relevant information to create a comprehensive project record. 
        You can always edit details later, add milestones, and upload additional files after creation.
        @if(!isset($quotation))
            <div class="mt-2">
                <strong>Pro tip:</strong> Consider creating projects from approved quotations for automatic data population.
            </div>
        @endif
    </x-admin.help-text>

    <!-- Enhanced Project Form -->
    <x-admin.project-form
        :clients="$clients"
        :categories="$categories" 
        :services="$services"
        :action="route('admin.projects.store')"
        method="POST"
        :quotation="$quotation ?? null"
    />

    <!-- Quick Actions Sidebar (for future milestones/files) -->
    @if(isset($quotation))
        <div class="fixed bottom-4 right-4 z-50">
            <x-admin.floating-action-button :actions="[
                [
                    'title' => 'View Quotation', 
                    'href' => route('admin.quotations.show', $quotation),
                    'icon' => '<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\"/>',
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
    @endif
</x-layouts.admin>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Basic form validation
            const requiredFields = form.querySelectorAll('[required]');
            let hasErrors = false;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    hasErrors = true;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
        });
    }
});

let autoSaveTimeout;
document.querySelectorAll('input, textarea, select').forEach(input => {
    input.addEventListener('input', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            console.log('Auto-saving draft...');
        }, 5000);
    });
});
</script>
@endpush