<!-- resources/views/components/admin/project-gallery.blade.php -->
@props([
    'project', // The project model with images relationship
    'columns' => 3,
    'lightbox' => true,
])

@php
    // Format images for the gallery component
    $formattedImages = [];
    if ($project && $project->images) {
        foreach ($project->images as $image) {
            $formattedImages[] = [
                'path' => $image->image_path,
                'alt' => $image->alt_text,
                'caption' => $image->alt_text,
                'id' => $image->id,
                'is_featured' => $image->is_featured
            ];
        }
    }
@endphp

<x-admin.image-gallery 
    :images="$formattedImages"
    :columns="$columns"
    :lightbox="$lightbox"
    aspectRatio="4:3"
    showActions="true"
>
    @if($project->images->count() > 0)
        <x-slot name="actions">
            <!-- Custom action buttons for each image -->
            <div class="flex gap-2">
                <a href="#" class="p-2 bg-white/90 rounded-full text-gray-800 hover:bg-white transition-colors duration-200">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </a>
            </div>
        </x-slot>
    @endif
</x-admin.image-gallery>