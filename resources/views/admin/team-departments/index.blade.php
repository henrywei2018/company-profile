<!-- resources/views/admin/team-departments/index.blade.php -->
<x-layouts.admin title="Team Departments Management">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Team Departments Management' => route('admin.team-member-departments.index')
        ]" />
        
        <div class="mt-4 md:mt-0">
            <x-admin.button href="{{ route('admin.team-member-departments.create') }}" icon='<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>'>
                Add New Department
            </x-admin.button>
        </div>
    </div>
    
    <!-- Filters -->
    <x-admin.filter action="{{ route('admin.team-member-departments.index') }}" method="GET" :resetRoute="route('admin.team-member-departments.index')">
        <!-- Filter content remains the same -->
    </x-admin.filter>
    
    <!-- Departments List -->
    <x-admin.card>
        <!-- Card content remains the same but with updated routes -->
        
        @if($departments->count() > 0)
            <x-admin.data-table>
                <!-- Table content with updated routes -->
                @foreach($departments as $department)
                    <x-admin.table-row>
                        <!-- Update all the route references -->
                        <x-admin.table-cell highlight="true">
                            <a href="#" 
                               class="text-blue-600 dark:text-blue-400 hover:underline"
                               onclick="event.preventDefault(); showDepartmentDetails({{ $department->id }})">
                                {{ $department->name }}
                            </a>
                        </x-admin.table-cell>
                        
                        <!-- Other cells remain the same -->
                        
                        <x-admin.table-cell>
                            <div class="flex items-center space-x-2">
                                <x-admin.icon-button 
                                    href="{{ route('admin.team-member-departments.edit', $department) }}"
                                    tooltip="Edit department"
                                    color="primary"
                                    size="sm"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </x-admin.icon-button>
                                
                                <form action="{{ route('admin.team-member-departments.destroy', $department) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.icon-button 
                                        type="submit"
                                        tooltip="Delete department"
                                        color="danger"
                                        size="sm"
                                        onclick="return confirm('Are you sure you want to delete this department?')"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </x-admin.icon-button>
                                </form>
                                
                                <form action="{{ route('admin.team-member-departments.toggle-active', $department) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <x-admin.icon-button 
                                        type="submit"
                                        tooltip="{{ $department->is_active ? 'Deactivate' : 'Activate' }}"
                                        color="{{ $department->is_active ? 'warning' : 'success' }}"
                                        size="sm"
                                    >
                                        <!-- Icon content remains the same -->
                                    </x-admin.icon-button>
                                </form>
                            </div>
                        </x-admin.table-cell>
                    </x-admin.table-row>
                @endforeach
            </x-admin.data-table>
            
            <div class="px-6 py-4">
                {{ $departments->withQueryString()->links() }}
            </div>
        @else
            <x-admin.empty-state 
                title="No departments found" 
                description="There are no departments matching your criteria."
                icon='<svg class="w-10 h-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>'
                actionText="Add New Department"
                :actionUrl="route('admin.team-member-departments.create')"
            />
        @endif
    </x-admin.card>
    
    <!-- Department Details Modal -->
    <x-admin.modal id="department-details-modal" title="Department Details" size="lg">
        <div id="department-details-content" class="min-h-[200px]">
            <div class="flex items-center justify-center h-full">
                <svg class="animate-spin h-8 w-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        
        <x-slot name="footer">
            <x-admin.button
                id="edit-department-btn"
                color="primary"
                href="#"
            >
                <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Department
            </x-admin.button>
            <x-admin.button
                color="light"
                data-hs-overlay="#department-details-modal"
            >
                Close
            </x-admin.button>
        </x-slot>
    </x-admin.modal>
    
    @push('scripts')
    <script>
        function showDepartmentDetails(departmentId) {
            // Update the edit button URL
            document.getElementById('edit-department-btn').href = `{{ route('admin.team-member-departments.edit', '') }}/${departmentId}`;
            
            // Show the modal
            HSOverlay.open(document.getElementById('department-details-modal'));
            
            // Load department details via fetch
            fetch(`{{ route('admin.team-member-departments.show', '') }}/${departmentId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update modal content
                document.getElementById('department-details-content').innerHTML = data.html;
            })
            .catch(error => {
                console.error('Error loading department details:', error);
                document.getElementById('department-details-content').innerHTML = `
                    <div class="p-4 text-center">
                        <p class="text-red-500">Error loading department details. Please try again.</p>
                    </div>
                `;
            });
        }
    </script>
    @endpush
</x-layouts.admin>