{{-- resources/views/admin/projects/milestones/create.blade.php --}}
<x-layouts.admin title="Add Milestone">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add Milestone</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Create a new milestone for {{ $project->title }}
            </p>
        </div>
        
        <div class="mt-4 md:mt-0">
            <x-admin.badge 
                type="{{ $project->status === 'completed' ? 'success' : ($project->status === 'in_progress' ? 'warning' : 'info') }}"
                size="lg"
            >
                Project: {{ ucfirst(str_replace('_', ' ', $project->status)) }}
            </x-admin.badge>
        </div>
    </div>

    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Projects' => route('admin.projects.index'),
        $project->title => route('admin.projects.show', $project),
        'Add Milestone' => '#'
    ]" class="mb-6" />

    <!-- Project Overview -->
    <x-admin.card class="mb-6">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                @if($project->featured_image_url)
                    <img src="{{ $project->featured_image_url }}" alt="{{ $project->title }}" 
                         class="h-16 w-16 object-cover rounded-lg">
                @else
                    <div class="h-16 w-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white truncate">{{ $project->title }}</h3>
                <div class="flex items-center space-x-4 mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if($project->client)
                        <span>Client: {{ $project->client->name }}</span>
                    @endif
                    @if($project->end_date)
                        <span>Due: {{ $project->end_date->format('M j, Y') }}</span>
                    @endif
                    <span>Progress: {{ $project->progress_percentage ?? 0 }}%</span>
                </div>
            </div>
            <div class="flex-shrink-0">
                <x-admin.progress 
                    :value="$project->progress_percentage ?? 0" 
                    height="sm"
                    showLabel="true"
                    color="blue"
                />
            </div>
        </div>
    </x-admin.card>

    <!-- Milestone Form -->
    <form action="{{ route('admin.projects.milestones.store', $project) }}" method="POST" x-data="milestoneForm()">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <x-admin.form-section 
                    title="Milestone Information" 
                    description="Define the milestone details and timeline"
                >
                    <div class="space-y-6">
                        <x-admin.input
                            name="title"
                            label="Milestone Title"
                            placeholder="e.g., Design Phase Complete, Testing Phase"
                            :value="old('title')"
                            required
                            x-model="formData.title"
                        />
                        
                        <x-admin.textarea
                            name="description"
                            label="Description"
                            placeholder="Detailed description of what this milestone entails..."
                            :value="old('description')"
                            rows="4"
                            x-model="formData.description"
                        />
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-admin.input
                                name="due_date"
                                label="Due Date"
                                type="date"
                                :value="old('due_date')"
                                x-model="formData.due_date"
                                required
                            />
                            
                            <x-admin.select
                                name="status"
                                label="Initial Status"
                                :options="[
                                    'pending' => 'Pending',
                                    'in_progress' => 'In Progress',
                                    'completed' => 'Completed',
                                    'delayed' => 'Delayed'
                                ]"
                                :value="old('status', 'pending')"
                                x-model="formData.status"
                                required
                            />
                        </div>
                        
                        <div x-show="formData.status === 'completed'" x-transition>
                            <x-admin.input
                                name="completion_date"
                                label="Completion Date"
                                type="date"
                                :value="old('completion_date')"
                                x-model="formData.completion_date"
                                helper="Set completion date if milestone is already completed"
                            />
                        </div>
                    </div>
                </x-admin.form-section>
                
                <!-- Milestone Dependencies (Optional Advanced Feature) -->
                <x-admin.form-section 
                    title="Dependencies & Notes" 
                    description="Additional milestone configuration"
                >
                    <div class="space-y-6">
                        @if($project->milestones->count() > 0)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    Dependencies (Optional)
                                </label>
                                <div class="space-y-2 max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-3">
                                    @foreach($project->milestones as $existingMilestone)
                                        <label class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                name="dependencies[]" 
                                                value="{{ $existingMilestone->id }}"
                                                class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500"
                                            >
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $existingMilestone->title }}
                                                <span class="text-xs text-gray-500">
                                                    ({{ ucfirst($existingMilestone->status) }})
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Select milestones that must be completed before this one
                                </p>
                            </div>
                        @endif
                        
                        <x-admin.textarea
                            name="notes"
                            label="Internal Notes"
                            placeholder="Internal notes for the team (not visible to clients)..."
                            :value="old('notes')"
                            rows="3"
                        />
                    </div>
                </x-admin.form-section>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Milestone Preview -->
                <x-admin.card title="Milestone Preview">
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white" 
                                x-text="formData.title || 'Milestone Title'"></h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" 
                               x-text="formData.description || 'Description will appear here...'"></p>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Status:</span>
                            <x-admin.badge 
                                x-bind:type="formData.status === 'completed' ? 'success' : (formData.status === 'delayed' ? 'danger' : 'warning')"
                                size="sm"
                            >
                                <span x-text="formData.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                            </x-admin.badge>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Due Date:</span>
                            <span class="text-xs text-gray-900 dark:text-white" 
                                  x-text="formData.due_date ? new Date(formData.due_date).toLocaleDateString() : 'Not set'"></span>
                        </div>
                    </div>
                </x-admin.card>
                
                <!-- Project Milestones Overview -->
                @if($project->milestones->count() > 0)
                    <x-admin.card title="Existing Milestones">
                        <div class="space-y-3">
                            @foreach($project->milestones->take(5) as $milestone)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-900 dark:text-white truncate">{{ $milestone->title }}</span>
                                    <x-admin.badge 
                                        type="{{ $milestone->status === 'completed' ? 'success' : ($milestone->isOverdue() ? 'danger' : 'warning') }}"
                                        size="sm"
                                    >
                                        {{ ucfirst($milestone->status) }}
                                    </x-admin.badge>
                                </div>
                            @endforeach
                            
                            @if($project->milestones->count() > 5)
                                <div class="text-xs text-gray-500 dark:text-gray-400 text-center pt-2 border-t border-gray-200 dark:border-gray-700">
                                    +{{ $project->milestones->count() - 5 }} more milestones
                                </div>
                            @endif
                        </div>
                    </x-admin.card>
                @endif
                
                <!-- Quick Tips -->
                <x-admin.help-text type="info" dismissible>
                    <x-slot name="title">Milestone Tips</x-slot>
                    <ul class="text-xs space-y-1 mt-2">
                        <li>• Set realistic due dates</li>
                        <li>• Break large tasks into smaller milestones</li>
                        <li>• Use dependencies to maintain order</li>
                        <li>• Add detailed descriptions for clarity</li>
                    </ul>
                </x-admin.help-text>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between pt-6 mt-8 border-t border-gray-200 dark:border-gray-700">
            <x-admin.button 
                href="{{ route('admin.projects.show', $project) }}#milestones" 
                color="light"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Project
            </x-admin.button>
            
            <div class="flex space-x-3">
                <x-admin.button 
                    type="submit" 
                    name="action" 
                    value="save_and_add_another" 
                    color="light"
                >
                    Save & Add Another
                </x-admin.button>
                
                <x-admin.button type="submit" color="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Create Milestone
                </x-admin.button>
            </div>
        </div>
    </form>
</x-layouts.admin>

@push('scripts')
<script>
function milestoneForm() {
    return {
        formData: {
            title: @js(old('title', '')),
            description: @js(old('description', '')),
            due_date: @js(old('due_date', '')),
            status: @js(old('status', 'pending')),
            completion_date: @js(old('completion_date', ''))
        },
        
        init() {
            this.updatePreview();
        },
        
        updatePreview() {
            // Update the preview card when form data changes
            this.$watch('formData', () => {
                this.validateDates();
            });
        },
        
        validateDates() {
            // Auto-set completion date if status is completed and no date is set
            if (this.formData.status === 'completed' && !this.formData.completion_date) {
                this.formData.completion_date = new Date().toISOString().split('T')[0];
            }
            
            // Clear completion date if status is not completed
            if (this.formData.status !== 'completed') {
                this.formData.completion_date = '';
            }
        }
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        const title = form.querySelector('[name="title"]').value.trim();
        const dueDate = form.querySelector('[name="due_date"]').value;
        const status = form.querySelector('[name="status"]').value;
        
        if (!title) {
            e.preventDefault();
            alert('Please enter a milestone title.');
            form.querySelector('[name="title"]').focus();
            return false;
        }
        
        if (!dueDate) {
            e.preventDefault();
            alert('Please set a due date for the milestone.');
            form.querySelector('[name="due_date"]').focus();
            return false;
        }
        
        // Check if due date is in the past for new milestones
        const today = new Date().toISOString().split('T')[0];
        if (dueDate < today && status !== 'completed') {
            if (!confirm('The due date is in the past. Do you want to continue?')) {
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>
@endpush