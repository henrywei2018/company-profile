{{-- resources/views/components/seo/meta-tags.blade.php --}}
@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'image' => null,
    'url' => null,
    'type' => 'website',
    'model' => null,
    'noindex' => false,
    'nofollow' => false
])

@php
use App\Helpers\SeoHelper;

// Generate SEO data
$seoTitle = SeoHelper::generateTitle($title);
$seoDescription = SeoHelper::generateDescription($description);
$seoKeywords = SeoHelper::generateKeywords($keywords);
$canonicalUrl = SeoHelper::generateCanonicalUrl($url);
$ogImage = SeoHelper::generateOgImage($model) ?: $image;
$robots = SeoHelper::generateRobots(!$noindex, !$nofollow);

// Get model SEO data if available
if ($model && method_exists($model, 'getSeoData')) {
    $modelSeo = $model->getSeoData();
    if ($modelSeo) {
        $seoTitle = $modelSeo->title ?: $seoTitle;
        $seoDescription = $modelSeo->description ?: $seoDescription;
        $seoKeywords = $modelSeo->keywords ?: $seoKeywords;
        if ($modelSeo->og_image) {
            $ogImage = asset('storage/' . $modelSeo->og_image);
        }
    }
}
@endphp

<!-- Basic Meta Tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- SEO Meta Tags -->
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
@if($seoKeywords)
<meta name="keywords" content="{{ $seoKeywords }}">
@endif
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

<!-- Open Graph Meta Tags -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:site_name" content="{{ settings('site_name', config('app.name')) }}">
@if($ogImage)
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
@endif

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
@if($ogImage)
<meta name="twitter:image" content="{{ $ogImage }}">
@endif
@if(settings('twitter_site'))
<meta name="twitter:site" content="{{ settings('twitter_site') }}">
@endif

<!-- Additional Meta Tags -->
<meta name="author" content="{{ settings('site_name', config('app.name')) }}">
<meta name="generator" content="Laravel {{ app()->version() }}">

<!-- Verification Meta Tags -->
@if(settings('seo_google_verification'))
<meta name="google-site-verification" content="{{ settings('seo_google_verification') }}">
@endif
@if(settings('seo_bing_verification'))
<meta name="msvalidate.01" content="{{ settings('seo_bing_verification') }}">
@endif

<!-- Favicon -->
@if(settings('site_favicon'))
<link rel="icon" type="image/x-icon" href="{{ asset('storage/' . settings('site_favicon')) }}">
@else
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
@endif