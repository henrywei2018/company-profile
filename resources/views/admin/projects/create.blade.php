<!-- resources/views/admin/projects/create.blade.php -->
<x-layouts.admin>
    <x-slot name="title">Create Project</x-slot>
    
    <x-slot name="breadcrumbs">
        <li class="inline-flex items-center">
            <svg class="w-5 h-5 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
            <a href="{{ route('admin.projects.index') }}" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-500">Projects</a>
        </li>
        <li class="inline-flex items-center">
            <svg class="w-5 h-5 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
            <span class="text-gray-700 dark:text-gray-300">Create Project</span>
        </li>
    </x-slot>
    
    <div class="max-w-4xl mx-auto">
        <x-admin.card>
            <x-slot name="header">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    Create New Project
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Add a new project to your portfolio.
                </p>
            </x-slot>
            
            @include('admin.projects.form')
        </x-admin.card>
    </div>
</x-layouts.admin>