{{-- resources/views/components/admin/message-project.blade.php --}}

@props(['project'])

@if($project)
    <a href="{{ route('admin.projects.show', $project) }}" 
       class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
        {{ Str::limit($project->title, 30) }}
    </a>
@else
    <span class="text-sm text-gray-400">-</span>
@endif