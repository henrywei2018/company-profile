{{-- resources/views/components/public/footer.blade.php --}}
@props([
    'variant' => 'default', // default, minimal, gradient
    'companyProfile' => null,
    'showNewsletter' => true,
    'showSocialMedia' => true
])

@php
    // Get company profile if not provided
    if (!$companyProfile) {
        $companyProfile = \App\Models\CompanyProfile::getInstance();
    }
    
    $footerClasses = match($variant) {
        'minimal' => 'bg-gray-50 border-t border-orange-100',
        'gradient' => 'bg-gradient-to-br from-orange-50 via-amber-50 to-orange-100',
        default => 'bg-white border-t border-orange-100/50'
    };
@endphp

<footer class="{{ $footerClasses }}">
    {{-- Newsletter Section --}}
    @if($showNewsletter)
    <div class="bg-gradient-to-r from-orange-500 via-amber-500 to-orange-600 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><circle cx="30" cy="30" r="4"/></g></svg>'); background-size: 60px 60px;"></div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Stay Updated with