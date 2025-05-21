<!-- resources/views/admin/team-member-departments/edit.blade.php -->
<x-layouts.admin title="Edit Department">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Team Management' => route('admin.team.index'),
            'Departments' => route('admin.team-member-departments.index'),
            'Edit Department' => '#'
        ]" />
        
        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            <form action="{{ route('admin.team-member-departments.destroy', $teamMemberDepartment) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this department?')">
                @csrf
                @method('DELETE')
                <x-admin.button
                    type="submit"
                    color="danger"
                >
                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Department
                </x-admin.button>
            </form>
        </div>
    </div>
    
    <form action="{{ route('admin.team-member-departments.update', $teamMemberDepartment) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Department Information -->
        <x-admin.form-section title="Department Information" description="Update the department details.">
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <x-admin.input
                        name="name"
                        label="Department Name"
                        placeholder="Enter department name"
                        :value="$teamMemberDepartment->name"
                        required
                    />
                </div>
                
                <div>
                    <x-admin.textarea
                        name="description"
                        label="Description"
                        placeholder="Enter department description (optional)"
                        rows="4"
                        :value="$teamMemberDepartment->description"
                    />
                </div>
                
                <div>
                    <x-admin.toggle
                        name="is_active"
                        label="Active Status"
                        :checked="$teamMemberDepartment->is_active"
                        helper="Set department as active/inactive"
                    />
                </div>
            </div>
        </x-admin.form-section>
        
        <!-- Form Buttons -->
        <div class="flex justify-end mt-8 gap-3">
            <x-admin.button
                href="{{ route('admin.team-member-departments.index') }}"
                color="light"
                type="button"
            >
                Cancel
            </x-admin.button>
            
            <x-admin.button
                type="submit"
                color="primary"
            >
                Update Department
            </x-admin.button>
        </div>
    </form>
</x-layouts.admin>