{{-- resources/views/admin/projects/milestones/edit.blade.php --}}
<x-layouts.admin title="Edit Milestone">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Milestone</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Update milestone for {{ $project->title }}
            </p>
        </div>
        
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            <x-admin.badge 
                type="{{ $milestone->status === 'completed' ? 'success' : ($milestone->isOverdue() ? 'danger' : 'warning') }}"
                size="lg"
            >
                {{ ucfirst($milestone->status) }}
            </x-admin.badge>
            
            @if($milestone->isOverdue() && $milestone->status !== 'completed')
                <x-admin.badge type="danger" size="lg">
                    {{ abs(now()->diffInDays($milestone->due_date)) }} days overdue
                </x-admin.badge>
            @endif
        </div>
    </div>

    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Projects' => route('admin.projects.index'),
        $project->title => route('admin.projects.show', $project),
        'Milestones' => route('admin.projects.show', $project) . '#milestones',
        'Edit: ' . $milestone->title => '#'
    ]" class="mb-6" />

    <!-- Status Alerts -->
    @if($milestone->isOverdue() && $milestone->status !== 'completed')
        <x-admin.alert type="danger" class="mb-6">
            <x-slot name="title">Milestone Overdue</x-slot>
            This milestone was due {{ $milestone->due_date->diffForHumans() }}. 
            Consider updating the due date or marking it as completed if it's done.
        </x-admin.alert>
    @elseif($milestone->due_date && $milestone->due_date->diffInDays(now()) <= 3 && $milestone->status !== 'completed')
        <x-admin.alert type="warning" class="mb-6">
            <x-slot name="title">Milestone Due Soon</x-slot>
            This milestone is due {{ $milestone->due_date->diffForHumans() }}.
        </x-admin.alert>
    @endif

    <!-- Milestone Form -->
    <form action="{{ route('admin.projects.milestones.update', [$project, $milestone]) }}" method="POST" x-data="milestoneEditForm()">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <x-admin.form-section 
                    title="Milestone Information" 
                    description="Update the milestone details and timeline"
                >
                    <div class="space-y-6">
                        <x-admin.input
                            name="title"
                            label="Milestone Title"
                            placeholder="e.g., Design Phase Complete, Testing Phase"
                            :value="old('title', $milestone->title)"
                            required
                            x-model="formData.title"
                        />
                        
                        <x-admin.textarea
                            name="description"
                            label="Description"
                            placeholder="Detailed description of what this milestone entails..."
                            :value="old('description', $milestone->description)"
                            rows="4"
                            x-model="formData.description"
                        />
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-admin.input
                                name="due_date"
                                label="Due Date"
                                type="date"
                                :value="old('due_date', $milestone->due_date?->format('Y-m-d'))"
                                x-model="formData.due_date"
                                required
                            />
                            
                            <x-admin.select
                                name="status"
                                label="Status"
                                :options="[
                                    'pending' => 'Pending',
                                    'in_progress' => 'In Progress',
                                    'completed' => 'Completed',
                                    'delayed' => 'Delayed'
                                ]"
                                :value="old('status', $milestone->status)"
                                x-model="formData.status"
                                required
                            />
                        </div>
                        
                        <div x-show="formData.status === 'completed'" x-transition>
                            <x-admin.input
                                name="completion_date"
                                label="Completion Date"
                                type="date"
                                :value="old('completion_date', $milestone->completion_date?->format('Y-m-d'))"
                                x-model="formData.completion_date"
                                helper="Date when this milestone was actually completed"
                            />
                        </div>
                    </div>
                </x-admin.form-section>
                
                <!-- Progress Tracking -->
                <x-admin.form-section 
                    title="Progress Tracking" 
                    description="Track milestone progress and completion"
                >
                    <div class="space-y-6">
                        <div>
                            <x-admin.input
                                name="progress_percent"
                                label="Progress Percentage"
                                type="number"
                                min="0"
                                max="100"
                                :value="old('progress_percent', $milestone->progress_percent ?? 0)"
                                x-model="formData.progress"
                                helper="Current completion percentage for this milestone"
                            />
                            
                            <!-- Progress Indicator -->
                            <div class="mt-2">
                                <x-admin.progress 
                                    :value="0" 
                                    x-bind:value="formData.progress"
                                    height="sm"
                                    showLabel="true"
                                    color="blue"
                                />
                            </div>
                        </div>
                        
                        <x-admin.textarea
                            name="notes"
                            label="Progress Notes"
                            placeholder="Notes about progress, blockers, or updates..."
                            :value="old('notes', $milestone->notes)"
                            rows="3"
                        />
                    </div>
                </x-admin.form-section>
                
                <!-- Dependencies & Advanced -->
                <x-admin.form-section 
                    title="Dependencies & Settings" 
                    description="Configure milestone dependencies and advanced options"
                >
                    <div class="space-y-6">
                        @if($project->milestones->where('id', '!=', $milestone->id)->count() > 0)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    Dependencies
                                </label>
                                <div class="space-y-2 max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-3">
                                    @foreach($project->milestones->where('id', '!=', $milestone->id) as $otherMilestone)
                                        <label class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                name="dependencies[]" 
                                                value="{{ $otherMilestone->id }}"
                                                @if($milestone->dependencies && in_array($otherMilestone->id, $milestone->dependencies))
                                                    checked
                                                @endif
                                                class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500"
                                            >
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $otherMilestone->title }}
                                                <x-admin.badge 
                                                    type="{{ $otherMilestone->status === 'completed' ? 'success' : 'warning' }}"
                                                    size="sm"
                                                    class="ml-1"
                                                >
                                                    {{ ucfirst($otherMilestone->status) }}
                                                </x-admin.badge>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Select milestones that must be completed before this one
                                </p>
                            </div>
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-admin.input
                                name="estimated_hours"
                                label="Estimated Hours"
                                type="number"
                                min="0"
                                step="0.5"
                                :value="old('estimated_hours', $milestone->estimated_hours)"
                                placeholder="0"
                                helper="Estimated time to complete"
                            />
                            
                            <x-admin.input
                                name="actual_hours"
                                label="Actual Hours"
                                type="number"
                                min="0"
                                step="0.5"
                                :value="old('actual_hours', $milestone->actual_hours)"
                                placeholder="0"
                                helper="Time actually spent"
                            />
                        </div>
                        
                        <x-admin.select
                            name="priority"
                            label="Priority Level"
                            :options="[
                                'low' => 'Low',
                                'normal' => 'Normal',
                                'high' => 'High',
                                'critical' => 'Critical'
                            ]"
                            :value="old('priority', $milestone->priority ?? 'normal')"
                        />
                    </div>
                </x-admin.form-section>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Milestone Status -->
                <x-admin.card title="Milestone Status">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Current Status:</span>
                            <x-admin.badge 
                                type="{{ $milestone->status === 'completed' ? 'success' : ($milestone->isOverdue() ? 'danger' : 'warning') }}"
                            >
                                {{ ucfirst($milestone->status) }}
                            </x-admin.badge>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Progress:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $milestone->progress_percent ?? 0 }}%
                            </span>
                        </div>
                        
                        @if($milestone->due_date)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Due Date:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $milestone->due_date->format('M j, Y') }}
                                </span>
                            </div>
                        @endif
                        
                        @if($milestone->completion_date)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Completed:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $milestone->completion_date->format('M j, Y') }}
                                </span>
                            </div>
                        @endif
                        
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <x-admin.progress 
                                :value="$milestone->progress_percent ?? 0" 
                                height="md"
                                showLabel="true"
                                color="{{ $milestone->status === 'completed' ? 'green' : ($milestone->isOverdue() ? 'red' : 'blue') }}"
                            />
                        </div>
                    </div>
                </x-admin.card>
                
                <!-- Quick Actions -->
                <x-admin.card title="Quick Actions">
                    <div class="space-y-2">
                        @if($milestone->status !== 'completed')
                            <form method="POST" action="{{ route('admin.projects.milestones.complete', [$project, $milestone]) }}">
                                @csrf
                                @method('PATCH')
                                <x-admin.button 
                                    type="submit" 
                                    color="success" 
                                    size="sm"
                                    class="w-full"
                                    onclick="return confirm('Mark this milestone as completed?')"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Mark Completed
                                </x-admin.button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.projects.milestones.reopen', [$project, $milestone]) }}">
                                @csrf
                                @method('PATCH')
                                <x-admin.button 
                                    type="submit" 
                                    color="warning" 
                                    size="sm"
                                    class="w-full"
                                    onclick="return confirm('Reopen this milestone?')"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Reopen
                                </x-admin.button>
                            </form>
                        @endif
                        
                        <x-admin.button 
                            href="{{ route('admin.projects.milestones.create', $project) }}" 
                            color="light" 
                            size="sm"
                            class="w-full"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add New Milestone
                        </x-admin.button>
                    </div>
                </x-admin.card>
                
                <!-- Project Overview -->
                <x-admin.card title="Project Progress">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Overall Progress:</span>
                            <span class="text-sm font-medium">{{ $project->progress_percentage ?? 0 }}%</span>
                        </div>
                        
                        <x-admin.progress 
                            :value="$project->progress_percentage ?? 0" 
                            height="sm"
                            color="blue"
                        />
                        
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $project->milestones->where('status', 'completed')->count() }}
                                </div>
                                <div class="text-gray-500 dark:text-gray-400">Completed</div>
                            </div>
                            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $project->milestones->count() }}
                                </div>
                                <div class="text-gray-500 dark:text-gray-400">Total</div>
                            </div>
                        </div>
                        
                        <div class="pt-3">
                            <x-admin.button 
                                href="{{ route('admin.projects.show', $project) }}" 
                                color="light" 
                                size="sm"
                                class="w-full"
                            >
                                View Project Details
                            </x-admin.button>
                        </div>
                    </div>
                </x-admin.card>
                
                <!-- Activity History -->
                <x-admin.card title="Recent Changes">
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start space-x-2">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <p class="text-gray-900 dark:text-white">Milestone created</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $milestone->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        
                        @if($milestone->updated_at != $milestone->created_at)
                            <div class="flex items-start space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                                <div>
                                    <p class="text-gray-900 dark:text-white">Last updated</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $milestone->updated_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endif
                        
                        @if($milestone->completion_date)
                            <div class="flex items-start space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                                <div>
                                    <p class="text-gray-900 dark:text-white">Completed</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $milestone->completion_date->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </x-admin.card>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between pt-6 mt-8 border-t border-gray-200 dark:border-gray-700">
            <div class="flex space-x-3">
                <x-admin.button 
                    href="{{ route('admin.projects.show', $project) }}#milestones" 
                    color="light"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Project
                </x-admin.button>
                
                <!-- Delete Button -->
                <x-admin.button 
                    type="button" 
                    color="danger"
                    onclick="confirmDelete()"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Milestone
                </x-admin.button>
            </div>
            
            <div class="flex space-x-3">
                <x-admin.button 
                    type="submit" 
                    name="action" 
                    value="save_and_continue" 
                    color="light"
                >
                    Save & Continue Editing
                </x-admin.button>
                
                <x-admin.button type="submit" color="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Milestone
                </x-admin.button>
            </div>
        </div>
    </form>

    <!-- Delete Confirmation Modal -->
    <x-admin.modal id="delete-milestone-modal" title="Delete Milestone" size="md">
        <div class="text-sm text-gray-600 dark:text-gray-400">
            <p class="mb-4">Are you sure you want to delete this milestone?</p>
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-3">
                <p class="font-medium text-red-800 dark:text-red-400 mb-2">
                    "{{ $milestone->title }}"
                </p>
                <p class="text-red-700 dark:text-red-300">
                    This action cannot be undone and will permanently delete all milestone data.
                </p>
            </div>
        </div>
        
        <x-slot name="footer">
            <x-admin.button 
                color="light" 
                onclick="document.getElementById('delete-milestone-modal').classList.add('hidden')"
            >
                Cancel
            </x-admin.button>
            <form action="{{ route('admin.projects.milestones.destroy', [$project, $milestone]) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <x-admin.button type="submit" color="danger">
                    Delete Milestone
                </x-admin.button>
            </form>
        </x-slot>
    </x-admin.modal>
</x-layouts.admin>

@push('scripts')
<script>
function milestoneEditForm() {
    return {
        formData: {
            title: @js(old('title', $milestone->title)),
            description: @js(old('description', $milestone->description)),
            due_date: @js(old('due_date', $milestone->due_date?->format('Y-m-d'))),
            status: @js(old('status', $milestone->status)),
            completion_date: @js(old('completion_date', $milestone->completion_date?->format('Y-m-d'))),
            progress: @js(old('progress_percent', $milestone->progress_percent ?? 0))
        },
        
        init() {
            this.updateForm();
            this.$watch('formData.status', (value) => {
                this.handleStatusChange(value);
            });
            this.$watch('formData.progress', (value) => {
                this.handleProgressChange(value);
            });
        },
        
        updateForm() {
            // Initialize form with current data
        },
        
        handleStatusChange(status) {
            if (status === 'completed') {
                if (!this.formData.completion_date) {
                    this.formData.completion_date = new Date().toISOString().split('T')[0];
                }
                this.formData.progress = 100;
            } else {
                if (status !== 'completed') {
                    // Don't auto-clear completion date for other statuses in edit mode
                }
            }
        },
        
        handleProgressChange(progress) {
            const progressNum = parseInt(progress);
            if (progressNum >= 100 && this.formData.status !== 'completed') {
                if (confirm('Progress is 100%. Would you like to mark this milestone as completed?')) {
                    this.formData.status = 'completed';
                    if (!this.formData.completion_date) {
                        this.formData.completion_date = new Date().toISOString().split('T')[0];
                    }
                }
            }
        }
    }
}

function confirmDelete() {
    document.getElementById('delete-milestone-modal').classList.remove('hidden');
}

// Form validation and auto-save
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    // Auto-save functionality (optional)
    let autoSaveTimeout;
    const inputs = form.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimeout);
            showAutoSaveIndicator('saving');
            
            autoSaveTimeout = setTimeout(() => {
                // Could implement auto-save here
                showAutoSaveIndicator('saved');
            }, 3000);
        });
    });
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        const title = form.querySelector('[name="title"]').value.trim();
        const dueDate = form.querySelector('[name="due_date"]').value;
        const status = form.querySelector('[name="status"]').value;
        const completionDate = form.querySelector('[name="completion_date"]').value;
        
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
        
        // Validate completion date when status is completed
        if (status === 'completed' && !completionDate) {
            if (!confirm('Milestone is marked as completed but no completion date is set. Continue anyway?')) {
                e.preventDefault();
                return false;
            }
        }
        
        // Check if completion date is after due date
        if (completionDate && dueDate && completionDate < dueDate) {
            if (!confirm('Completion date is before the due date. This might indicate the milestone was completed early. Continue?')) {
                e.preventDefault();
                return false;
            }
        }
    });
});

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
</script>
@endpush