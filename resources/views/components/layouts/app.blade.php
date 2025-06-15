{{-- resources/views/layouts/app.blade.php --}}
@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'image' => null,
    'type' => 'website',
    'model' => null,
    'noindex' => false,
    'breadcrumbs' => null
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- SEO Meta Tags Component --}}
    <x-seo.meta-tags 
        :title="$title ?? $autoSeo['title'] ?? null"
        :description="$description ?? $autoSeo['description'] ?? null"
        :keywords="$keywords"
        :image="$image"
        :type="$type ?? $autoSeo['type'] ?? 'website'"
        :model="$model"
        :noindex="$noindex"
    />
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/orange-theme.css') }}">
    @stack('styles')
    
    {{-- Analytics & Tracking - Head --}}
    <x-seo.analytics position="head" />
</head>
<body class="bg-white text-gray-900">
    {{-- Analytics & Tracking - Body --}}
    <x-seo.analytics position="body" />
    <x-navigation-header />
    {{-- Main Content --}}
    {{ $slot }}
    
    {{-- Schema.org JSON-LD --}}
    <x-seo.schema 
        type="company"
        :breadcrumbs="$breadcrumbs ?? null"
    />
    
    {{-- Page-specific schema --}}
    @if(isset($model))
        @if(isset($model->title) || isset($model->name))
            <x-seo.schema 
                type="article"
                :data="$model"
            />
        @endif
    @endif
    
    @stack('scripts')
</body>
</html>