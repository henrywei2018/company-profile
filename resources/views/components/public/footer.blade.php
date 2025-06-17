{{-- resources/views/components/public/footer.blade.php - CLEAN VERSION --}}
@props([
    'variant' => 'default', // default, minimal, gradient
    'companyProfile' => null,
    'showNewsletter' => false,
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
        {{-- Pattern Overlay --}}
        <div class="absolute inset-0 bg-black/10"></div>
        
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Stay Updated with Our Latest News
                </h2>
                <p class="text-lg text-orange-100 mb-8">
                    Get exclusive updates, industry insights, and special offers delivered directly to your inbox.
                </p>
                
                
                
                <p class="text-sm text-orange-200 mt-4">
                    We respect your privacy. Unsubscribe at any time.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Main Footer Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            
            {{-- Company Info --}}
            <div class="lg:col-span-2">
                {{-- Logo --}}
                <div class="mb-6">
                    @if($companyProfile->logo_url)
                        <img src="{{ $companyProfile->logo_url }}" 
                             alt="{{ $companyProfile->company_name ?? config('app.name') }}"
                             class="h-12 w-auto">
                    @else
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <span class="text-2xl font-bold text-gray-900">
                                {{ $companyProfile->company_name ?? config('app.name') }}
                            </span>
                        </div>
                    @endif
                </div>
                
                {{-- Company Description --}}
                <p class="text-gray-600 mb-6 leading-relaxed">
                    {{ $companyProfile->description ?? 'We are committed to providing excellent services and building lasting relationships with our clients through innovation and quality.' }}
                </p>
                
                {{-- Contact Info --}}
                <div class="space-y-4">
                    @if($companyProfile->address)
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-orange-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-gray-600">{{ $companyProfile->address }}</span>
                    </div>
                    @endif
                    
                    @if($companyProfile->phone)
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <a href="tel:{{ $companyProfile->phone }}" 
                           class="text-gray-600 hover:text-orange-600 transition-colors duration-300">
                            {{ $companyProfile->phone }}
                        </a>
                    </div>
                    @endif
                    
                    @if($companyProfile->email)
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <a href="mailto:{{ $companyProfile->email }}" 
                           class="text-gray-600 hover:text-orange-600 transition-colors duration-300">
                            {{ $companyProfile->email }}
                        </a>
                    </div>
                    @endif
                    
                {{-- Quick Contact CTA --}}
                <div class="text-center md:text-left">
                    <p class="text-sm text-gray-600 mb-3">Need immediate assistance?</p>
                    <a href="{{ route('contact.index') }}" 
                       class="inline-flex items-center gap-x-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-xl hover:from-orange-600 hover:to-amber-600 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Contact Us Now
                    </a>
                </div>
                </div>
            </div>
            
            {{-- Quick Links --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Quick Links</h3>
                <ul class="space-y-4">
                    <li>
                        <a href="{{ route('home') }}" 
                           class="text-gray-600 hover:text-orange-600 transition-colors duration-300 flex items-center group">
                            <svg class="w-4 h-4 mr-2 text-orange-500 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" 
                           class="text-gray-600 hover:text-orange-600 transition-colors duration-300 flex items-center group">
                            <svg class="w-4 h-4 mr-2 text-orange-500 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            About Us
                        </a>
                    </li>
                    @if(Route::has('services'))
                    <li>
                        <a href="{{ route('services') }}" 
                           class="text-gray-600 hover:text-orange-600 transition-colors duration-300 flex items-center group">
                            <svg class="w-4 h-4 mr-2 text-orange-500 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Services
                        </a>
                    </li>
                    @endif
                    @if(Route::has('portfolio'))
                    <li>
                        <a href="{{ route('portfolio') }}" 
                           class="text-gray-600 hover:text-orange-600 transition-colors duration-300 flex items-center group">
                            <svg class="w-4 h-4 mr-2 text-orange-500 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Portfolio
                        </a>
                    </li>
                    @endif
                    @if(Route::has('contact.index'))
                    <li>
                        <a href="{{ route('contact.index') }}" 
                           class="text-gray-600 hover:text-orange-600 transition-colors duration-300 flex items-center group">
                            <svg class="w-4 h-4 mr-2 text-orange-500 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Contact
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
            
            {{-- Services or Additional Links --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Our Services</h3>
                <ul class="space-y-4">
                    <li>
                        <span class="text-gray-600 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Consulting Services
                        </span>
                    </li>
                    <li>
                        <span class="text-gray-600 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Project Management
                        </span>
                    </li>
                    <li>
                        <span class="text-gray-600 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Technical Support
                        </span>
                    </li>
                    <li>
                        <span class="text-gray-600 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Custom Solutions
                        </span>
                    </li>
                </ul>
                
                {{-- Operating Hours --}}
                <div class="mt-8">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Business Hours</h4>
                    <div class="text-sm text-gray-600 space-y-1">
                        <div class="flex justify-between">
                            <span>Monday - Friday:</span>
                            <span>9:00 AM - 6:00 PM</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Saturday:</span>
                            <span>9:00 AM - 1:00 PM</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Sunday:</span>
                            <span>Closed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Social Media Section --}}
        @if($showSocialMedia)
        <div class="mt-12 pt-8 border-t border-orange-100">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        {{-- Facebook --}}
                        @if($companyProfile->facebook_url)
                        <a href="{{ $companyProfile->facebook_url }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl flex items-center justify-center hover:from-blue-600 hover:to-blue-700 transform hover:scale-110 transition-all duration-300 shadow-lg hover:shadow-xl social-icon"
                           aria-label="Follow us on Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        @endif
                        
                        {{-- Twitter --}}
                        @if($companyProfile->twitter_url)
                        <a href="{{ $companyProfile->twitter_url }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="w-10 h-10 bg-gradient-to-br from-sky-500 to-sky-600 text-white rounded-xl flex items-center justify-center hover:from-sky-600 hover:to-sky-700 transform hover:scale-110 transition-all duration-300 shadow-lg hover:shadow-xl social-icon"
                           aria-label="Follow us on Twitter">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        @endif
                        
                        {{-- LinkedIn --}}
                        @if($companyProfile->linkedin_url)
                        <a href="{{ $companyProfile->linkedin_url }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="w-10 h-10 bg-gradient-to-br from-blue-700 to-blue-800 text-white rounded-xl flex items-center justify-center hover:from-blue-800 hover:to-blue-900 transform hover:scale-110 transition-all duration-300 shadow-lg hover:shadow-xl social-icon"
                           aria-label="Follow us on LinkedIn">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                        @endif
                        
                        {{-- Instagram --}}
                        @if($companyProfile->instagram_url)
                        <a href="{{ $companyProfile->instagram_url }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="w-10 h-10 bg-gradient-to-br from-pink-500 via-red-500 to-yellow-500 text-white rounded-xl flex items-center justify-center hover:from-pink-600 hover:via-red-600 hover:to-yellow-600 transform hover:scale-110 transition-all duration-300 shadow-lg hover:shadow-xl social-icon"
                           aria-label="Follow us on Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C8.396 0 7.989.013 7.041.048 6.094.082 5.45.204 4.896.388a7.418 7.418 0 00-2.682 1.748 7.418 7.418 0 00-1.748 2.682C.204 5.45.082 6.094.048 7.041.013 7.989 0 8.396 0 12.017s.013 4.028.048 4.976c.034.947.156 1.591.34 2.145a7.418 7.418 0 001.748 2.682 7.418 7.418 0 002.682 1.748c.554.184 1.198.306 2.145.34.947.035 1.354.048 4.976.048s4.028-.013 4.976-.048c.947-.034 1.591-.156 2.145-.34a7.418 7.418 0 002.682-1.748 7.418 7.418 0 001.748-2.682c.184-.554.306-1.198.34-2.145.035-.948.048-1.354.048-4.976s-.013-4.028-.048-4.976c-.034-.947-.156-1.591-.34-2.145a7.418 7.418 0 00-1.748-2.682A7.418 7.418 0 0019.146.388c-.554-.184-1.198-.306-2.145-.34C16.054.013 15.647 0 12.017 0zm0 5.838a6.18 6.18 0 100 12.36 6.18 6.18 0 000-12.36zm0 10.162a3.982 3.982 0 110-7.964 3.982 3.982 0 010 7.964zm7.846-10.405a1.441 1.441 0 11-2.883 0 1.441 1.441 0 012.883 0z"/>
                            </svg>
                        </a>
                        @endif
                        
                        {{-- YouTube --}}
                        @if($companyProfile->youtube_url)
                        <a href="{{ $companyProfile->youtube_url }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 text-white rounded-xl flex items-center justify-center hover:from-red-600 hover:to-red-700 transform hover:scale-110 transition-all duration-300 shadow-lg hover:shadow-xl social-icon"
                           aria-label="Follow us on YouTube">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>
                        @endif
                        
                        {{-- WhatsApp --}}
                        @if($companyProfile->whatsapp_number)
                        <a href="https://wa.me/{{ $companyProfile->whatsapp_number }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl flex items-center justify-center hover:from-green-600 hover:to-green-700 transform hover:scale-110 transition-all duration-300 shadow-lg hover:shadow-xl social-icon"
                           aria-label="Contact us on WhatsApp">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.893 3.488"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
                
            </div>
        </div>
        @endif
    </div>
    
    {{-- Bottom Footer --}}
    <div class="border-t border-orange-100 bg-gray-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-center md:text-left">
                    <p class="text-sm text-gray-600">
                        &copy; {{ date('Y') }} 
                        <span class="font-semibold text-orange-600">
                            {{ $companyProfile->company_name ?? config('app.name') }}
                        </span>. 
                        All rights reserved.
                    </p>
                </div>
                
                <div class="flex flex-wrap items-center gap-6 text-sm">
                    <a href="#" 
                       class="text-gray-600 hover:text-orange-600 transition-colors duration-300">
                        Privacy Policy
                    </a>
                    <a href="#" 
                       class="text-gray-600 hover:text-orange-600 transition-colors duration-300">
                        Terms of Service
                    </a>
                    <a href="#" 
                       class="text-gray-600 hover:text-orange-600 transition-colors duration-300">
                        Sitemap
                    </a>
                    
                    {{-- Back to Top Button --}}
                    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
                            class="flex items-center gap-x-1 text-orange-600 hover:text-orange-700 transition-colors duration-300"
                            aria-label="Back to top">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        <span class="text-xs font-medium">Top</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</footer>