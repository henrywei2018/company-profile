<!-- resources/views/admin/dashboard.blade.php -->
<x-layouts.admin>
    <x-slot name="title">Dashboard</x-slot>

    <!-- Statistics Section -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Overview</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Projects Stat -->
            <x-stat-card 
                title="Total Projects" 
                value="{{ $totalProjects }}" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z' />"
                iconColor="text-purple-500" 
                iconBg="bg-purple-100" 
                change="{{ $projectsChange }}"
                href="{{ route('admin.projects.index') }}"
            />
            
            <!-- Other stat cards... -->
        </div>
    </div>
    
    <!-- Rest of your dashboard content... -->
</x-layouts.admin>