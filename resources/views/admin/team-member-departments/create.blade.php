<!-- resources/views/admin/team-member-departments/create.blade.php -->
<x-layouts.admin title="Create Department">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Team Management' => route('admin.team.index'),
            'Departments' => route('admin.team-member-departments.index'),
            'Create Department' => '#'
        ]" />
    </div>
    
    <form action="{{ route('admin.team-member-departments.store') }}" method="POST">
        @csrf
        
        <!-- Department Information -->
        <x-admin.form-section title="Department Information" description="Create a new department for team members.">
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <x-admin.input
                        name="name"
                        label="Department Name"
                        placeholder="Enter department name"
                        required
                    />
                </div>
                
                <div>
                    <x-admin.textarea
                        name="description"
                        label="Description"
                        placeholder="Enter department description (optional)"
                        rows="4"
                    />
                </div>
                
                <div>
                    <x-admin.toggle
                        name="is_active"
                        label="Active Status"
                        checked="true"
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
                Create Department
            </x-admin.button>
        </div>
    </form>
</x-layouts.admin>  