{{-- resources/views/components/seo/schema.blade.php --}}
@props([
    'type' => 'company',
    'data' => null,
    'breadcrumbs' => null
])

@php
use App\Helpers\SeoHelper;

$schemas = [];

// Company/Organization Schema
if ($type === 'company' || $type === 'organization') {
    $schemas[] = SeoHelper::generateCompanySchema();
}

// Breadcrumb Schema
if ($breadcrumbs && is_array($breadcrumbs)) {
    $schemas[] = SeoHelper::generateBreadcrumbSchema($breadcrumbs);
}

// Article Schema
if ($type === 'article' && $data) {
    $schemas[] = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $data->title ?? $data->name ?? '',
        'description' => $data->excerpt ?? $data->description ?? '',
        'author' => [
            '@type' => 'Organization',
            'name' => settings('site_name', config('app.name'))
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => settings('site_name', config('app.name')),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => SeoHelper::generateOgImage()
            ]
        ],
        'datePublished' => isset($data->created_at) ? $data->created_at->toISOString() : now()->toISOString(),
        'dateModified' => isset($data->updated_at) ? $data->updated_at->toISOString() : now()->toISOString(),
        'image' => SeoHelper::generateOgImage($data),
        'url' => request()->url()
    ];
}

// Service Schema
if ($type === 'service' && $data) {
    $schemas[] = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $data->title ?? $data->name ?? '',
        'description' => $data->description ?? '',
        'provider' => [
            '@type' => 'Organization',
            'name' => settings('site_name', config('app.name'))
        ],
        'url' => request()->url(),
        'image' => SeoHelper::generateOgImage($data)
    ];
}

// Project/CreativeWork Schema
if ($type === 'project' && $data) {
    $schemas[] = [
        '@context' => 'https://schema.org',
        '@type' => 'CreativeWork',
        'name' => $data->title ?? $data->name ?? '',
        'description' => $data->description ?? '',
        'creator' => [
            '@type' => 'Organization',
            'name' => settings('site_name', config('app.name'))
        ],
        'url' => request()->url(),
        'image' => SeoHelper::generateOgImage($data),
        'dateCreated' => isset($data->created_at) ? $data->created_at->toISOString() : null,
        'dateModified' => isset($data->updated_at) ? $data->updated_at->toISOString() : null
    ];
}

// Custom schema from settings
$customSchema = settings('custom_schema');
if ($customSchema) {
    $decoded = json_decode($customSchema, true);
    if (is_array($decoded)) {
        $schemas[] = $decoded;
    }
}
@endphp

@if(!empty($schemas))
@foreach($schemas as $schema)
<script type="application/ld+json">
{!! json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
</script>
@endforeach
@endif