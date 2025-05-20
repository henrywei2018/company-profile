<!-- resources/views/admin/team-departments/create.blade.php -->
<x-layouts.admin title="Add New Department">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Team Departments Management' => route('admin.team-member-departments.index'),
            'Add New Department' => route('admin.team-member-departments.create'),
        ]" />
    </div>

    <form action="{{ route('admin.team-member-departments.store') }}" method="POST">
        @csrf

        <!-- Basic Information -->
        <x-admin.form-section title="Department Information" description="Enter the details of the department.">
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
                    <x-admin.input
                        name="slug"
                        label="Slug"
                        placeholder="department-slug"
                        helper="Leave empty to auto-generate from name."
                    />
                </div>

                <div>
                    <x-admin.textarea
                        name="description"
                        label="Description"
                        placeholder="Enter department description"
                        rows="3"
                    />
                </div>
                
                <div>
                    <x-admin.input
                        name="sort_order"
                        label="Sort Order"
                        type="number"
                        placeholder="0"
                        helper="Lower numbers appear first"
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