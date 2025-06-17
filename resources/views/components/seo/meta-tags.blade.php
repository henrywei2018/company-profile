{{-- resources/views/components/seo/meta-tags.blade.php --}}
@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'image' => null,
    'type' => 'website',
    'model' => null,
    'noindex' => false
])

@php
    $siteTitle = $title ?? config('app.name');
    $siteDescription = $description ?? 'Professional construction and engineering services';
    $siteKeywords = $keywords ?? 'construction, engineering, professional services';
    $siteImage = $image ?? asset('images/og-image.jpg');
    $siteUrl = request()->url();
@endphp

{{-- Basic Meta Tags --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Title --}}
<title>{{ $siteTitle }}</title>

{{-- SEO Meta Tags --}}
<meta name="description" content="{{ $siteDescription }}">
<meta name="keywords" content="{{ $siteKeywords }}">
<meta name="author" content="{{ config('app.name') }}">

{{-- Robots --}}
@if($noindex)
<meta name="robots" content="noindex, nofollow">
@else
<meta name="robots" content="index, follow">
@endif

{{-- Open Graph Meta Tags --}}
<meta property="og:title" content="{{ $siteTitle }}">
<meta property="og:description" content="{{ $siteDescription }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $siteUrl }}">
<meta property="og:image" content="{{ $siteImage }}">
<meta property="og:site_name" content="{{ config('app.name') }}">

{{-- Twitter Card Meta Tags --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $siteTitle }}">
<meta name="twitter:description" content="{{ $siteDescription }}">
<meta name="twitter:image" content="{{ $siteImage }}">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $siteUrl }}">

{{-- Favicon --}}
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

{{-- JSON-LD Structured Data --}}
@if($model && method_exists($model, 'getStructuredData'))
<script type="application/ld+json">
{!! json_encode($model->getStructuredData()) !!}
</script>
@endif