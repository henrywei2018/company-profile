<!-- resources/views/admin/team-member-departments/index.blade.php -->
<x-layouts.admin title="Team Departments Management">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Team Management' => route('admin.team.index'),
            'Departments' => route('admin.team-member-departments.index')
        ]" />
        
        <div class="mt-4 md:mt-0">
            <x-admin.button href="{{ route('admin.team-member-departments.create') }}" icon='<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>'>
                Add New Department
            </x-admin.button>
        </div>
    </div>
    
    <!-- Departments List -->
    <x-admin.card>
        <x-slot name="headerActions">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $departments->total() }} departments found</span>
        </x-slot>
        
        @if($departments->count() > 0)
            <x-admin.data-table>
                <x-slot name="columns">
                    <x-admin.table-column>Name</x-admin.table-column>
                    <x-admin.table-column>Team Members</x-admin.table-column>
                    <x-admin.table-column>Status</x-admin.table-column>
                    <x-admin.table-column>Actions</x-admin.table-column>
                </x-slot>
                
                @foreach($departments as $department)
                    <x-admin.table-row>
                        <x-admin.table-cell highlight="true">
                            {{ $department->name }}
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            {{ $department->team_members_count }}
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            @if($department->is_active)
                                <x-admin.badge type="success" dot="true">Active</x-admin.badge>
                            @else
                                <x-admin.badge type="danger" dot="true">Inactive</x-admin.badge>
                            @endif
                        </x-admin.table-cell>
                        
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
                                        @if($department->is_active)
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </x-admin.icon-button>
                                </form>
                            </div>
                        </x-admin.table-cell>
                    </x-admin.table-row>
                @endforeach
            </x-admin.data-table>
            
            <div class="px-6 py-4">
                {{ $departments->links() }}
            </div>
        @else
            <x-admin.empty-state 
                title="No departments found" 
                description="There are no departments yet."
                icon='<svg class="w-10 h-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>'
                actionText="Add New Department"
                :actionUrl="route('admin.team-member-departments.create')"
            />
        @endif
    </x-admin.card>
</x-layouts.admin>