<!-- resources/views/admin/team/index.blade.php -->
<x-admin-layout :title="'Team Members'">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Team Management' => route('admin.team.index'),
        ]" />
        
        <div class="mt-4 md:mt-0">
            <x-admin.button 
                href="{{ route('admin.team.create') }}" 
                icon='<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>'
            >
                Add Team Member
            </x-admin.button>
        </div>
    </div>

    <x-admin.filter action="{{ route('admin.team.index') }}" method="GET" :resetRoute="route('admin.team.index')">
        <x-admin.input
            name="search"
            label="Search"
            placeholder="Search by name, position, or department"
            value="{{ request('search') }}"
        />
        
        <x-admin.input
            name="department"
            label="Department"
            placeholder="Filter by department"
            value="{{ request('department') }}"
        />
        
        <x-admin.select
            name="status"
            label="Status"
            :options="['1' => 'Active', '0' => 'Inactive']"
            placeholder="All Statuses"
            value="{{ request('status') }}"
        />
        
        <x-admin.select
            name="featured"
            label="Featured"
            :options="['1' => 'Featured', '0' => 'Not Featured']"
            placeholder="All"
            value="{{ request('featured') }}"
        />
    </x-admin.filter>

    <!-- Team Members List -->
    <x-admin.card>
        <x-slot name="headerActions">
            <span class="text-sm text-gray-500 dark:text-gray-400 px-4 py-4">{{ $teamMembers->total() }} team members found</span>
        </x-slot>
        
        @if($teamMembers->count() > 0)
            <x-admin.data-table>
                <x-slot name="columns">
                    <x-admin.table-column>Name</x-admin.table-column>
                    <x-admin.table-column>Position</x-admin.table-column>
                    <x-admin.table-column>Department</x-admin.table-column>
                    <x-admin.table-column>Featured</x-admin.table-column>
                    <x-admin.table-column>Status</x-admin.table-column>
                    <x-admin.table-column>Actions</x-admin.table-column>
                </x-slot>
                
                @foreach($teamMembers as $member)
                    <x-admin.table-row>
                        <x-admin.table-cell class="max-w-xs truncate" highlight="true">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ $member->photoUrl }}" alt="{{ $member->name }}">
                                </div>
                                <div class="ml-4">
                                    <a href="{{ route('admin.team.edit', $member) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $member->name }}
                                    </a>
                                    @if($member->email)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $member->email }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            {{ $member->position }}
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            {{ $member->department ?: 'N/A' }}
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            @if($member->featured)
                                <x-admin.badge type="primary" dot="true">Featured</x-admin.badge>
                            @else
                                <x-admin.badge type="default">No</x-admin.badge>
                            @endif
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            @if($member->is_active)
                                <x-admin.badge type="success" dot="true">Active</x-admin.badge>
                            @else
                                <x-admin.badge type="danger" dot="true">Inactive</x-admin.badge>
                            @endif
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            <div class="flex items-center space-x-2">
                                <x-admin.icon-button 
                                    href="{{ route('admin.team.edit', $member) }}"
                                    tooltip="Edit team member"
                                    color="primary"
                                    size="sm"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </x-admin.icon-button>
                                
                                <form action="{{ route('admin.team.destroy', $member) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.icon-button 
                                        type="submit"
                                        tooltip="Delete team member"
                                        color="danger"
                                        size="sm"
                                        onclick="return confirm('Are you sure you want to delete this team member?')"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </x-admin.icon-button>
                                </form>
                                
                                <form action="{{ route('admin.team.toggle-active', $member) }}" method="POST" class="inline">
                                    @csrf
                                    @method('POST')
                                    <x-admin.icon-button 
                                        type="submit"
                                        tooltip="{{ $member->is_active ? 'Deactivate' : 'Activate' }}"
                                        color="{{ $member->is_active ? 'warning' : 'success' }}"
                                        size="sm"
                                    >
                                        @if($member->is_active)
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
                                
                                <form action="{{ route('admin.team.toggle-featured', $member) }}" method="POST" class="inline">
                                    @csrf
                                    @method('POST')
                                    <x-admin.icon-button 
                                        type="submit"
                                        tooltip="{{ $member->featured ? 'Remove from featured' : 'Add to featured' }}"
                                        color="{{ $member->featured ? 'light' : 'primary' }}"
                                        size="sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $member->featured ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    </x-admin.icon-button>
                                </form>
                            </div>
                        </x-admin.table-cell>
                    </x-admin.table-row>
                @endforeach
            </x-admin.data-table>
            
            <div class="px-6 py-4">
                {{ $teamMembers->withQueryString()->links() }}
            </div>
        @else
            <x-admin.empty-state 
                title="No team members found" 
                description="There are no team members matching your criteria."
                icon='<svg class="w-10 h-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>'
                actionText="Add Team Member"
                :actionUrl="route('admin.team.create')"
            />
        @endif
    </x-admin.card>
</x-admin-layout>