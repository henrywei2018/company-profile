{{-- resources/views/pages/contact/thank-you.blade.php --}}
<x-layouts.public
    title="Thank You - {{ $siteConfig['site_title'] }}"
    description="Thank you for contacting us. We have received your message and will get back to you soon."
    keywords="thank you, contact confirmation, message received"
    type="website"
>

{{-- Thank You Hero Section --}}
<section class="relative min-h-screen flex items-center bg-gradient-to-br from-orange-50 via-white to-amber-50 overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 bg-[url('/images/grid.svg')] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]"></div>
    
    {{-- Floating Success Elements --}}
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-green-200/30 rounded-full animate-float"></div>
        <div class="absolute bottom-1/3 right-1/4 w-48 h-48 bg-orange-200/20 rounded-full animate-float animation-delay-1000"></div>
        <div class="absolute top-1/2 right-1/3 w-24 h-24 bg-amber-300/40 rounded-full animate-float animation-delay-2000"></div>
    </div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 z-10 text-center">
        {{-- Success Icon --}}
        <div class="w-32 h-32 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-8 animate-bounce-once shadow-2xl">
            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        
        <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6 animate-fade-in-up">
            Thank You!
        </h1>
        
        <h2 class="text-2xl md:text-3xl font-semibold text-orange-600 mb-8 animate-fade-in-up animation-delay-200">
            Your Message Has Been Sent
        </h2>
        
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 animate-fade-in-up animation-delay-400 max-w-2xl mx-auto">
            <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                We have successfully received your message and appreciate you taking the time to contact us. 
                Our team will review your inquiry and get back to you within <strong class="text-orange-600">24 hours</strong>.
            </p>
            
            {{-- What Happens Next --}}
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">What happens next?</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <span class="text-orange-600 font-bold">1</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-1">Review</h4>
                            <p class="text-gray-600">We'll carefully review your message and project requirements.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <span class="text-orange-600 font-bold">2</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-1">Contact</h4>
                            <p class="text-gray-600">Our expert will reach out to discuss your project in detail.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <span class="text-orange-600 font-bold">3</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-1">Proposal</h4>
                            <p class="text-gray-600">We'll provide you with a detailed quote and project plan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Quick Actions --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8 animate-fade-in-up animation-delay-600">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-600 to-amber-600 text-white font-semibold rounded-xl hover:from-orange-700 hover:to-amber-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Back to Home
            </a>
            
            <a href="{{ route('services.index') }}" 
               class="inline-flex items-center px-8 py-4 border-2 border-orange-600 text-orange-600 font-semibold rounded-xl hover:bg-orange-600 hover:text-white transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                View Our Services
            </a>
        </div>
        
        {{-- Contact Information --}}
        <div class="text-center animate-fade-in-up animation-delay-800">
            <p class="text-gray-600 mb-4">Need immediate assistance? Feel free to call us directly:</p>
            @if($contactInfo['phone'])
            <a href="tel:{{ $contactInfo['phone'] }}" 
               class="inline-flex items-center text-orange-600 font-semibold text-lg hover:text-orange-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                {{ $contactInfo['phone'] }}
            </a>
            @endif
        </div>
    </div>
</section>

{{-- Additional Information Section --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Response Time --}}
            <div class="text-center p-6 bg-gray-50 rounded-2xl">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Quick Response</h3>
                <p class="text-gray-600">We typically respond to all inquiries within 24 hours during business days.</p>
            </div>
            
            {{-- Free Consultation --}}
            <div class="text-center p-6 bg-gray-50 rounded-2xl">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.405L3 21l2.595-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Free Consultation</h3>
                <p class="text-gray-600">All initial consultations and project assessments are completely free of charge.</p>
            </div>
            
            {{-- Professional Service --}}
            <div class="text-center p-6 bg-gray-50 rounded-2xl">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Professional Team</h3>
                <p class="text-gray-600">You'll work directly with our experienced professionals from start to finish.</p>
            </div>
        </div>
    </div>
</section>

{{-- Recent Projects Showcase --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">While You Wait, Check Out Our Recent Work</h2>
            <p class="text-gray-600">See examples of projects we've completed for other satisfied clients.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- You can add featured projects here or remove this section if not needed --}}
            @for($i = 1; $i <= 3; $i++)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="h-48 bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Recent Project {{ $i }}</h3>
                    <p class="text-gray-600 text-sm mb-4">Professional construction service delivered with quality and expertise.</p>
                    <a href="{{ route('portfolio.index') }}" 
                       class="inline-flex items-center text-orange-600 font-medium text-sm hover:text-orange-700 transition-colors">
                        View Portfolio
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

{{-- JavaScript for Animations --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Trigger animations on page load
    const animatedElements = document.querySelectorAll('.animate-fade-in-up, .animate-bounce-once');
    
    animatedElements.forEach((element, index) => {
        setTimeout(() => {
            element.classList.add('animate');
        }, index * 200);
    });
    
    // Auto redirect after 10 seconds (optional)
    // setTimeout(() => {
    //     window.location.href = "{{ route('home') }}";
    // }, 10000);
});
</script>
@endpush

{{-- Additional CSS for Animations --}}
@push('styles')
<style>
/* Animation keyframes */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes bounce-once {
    0% { transform: scale(0) rotate(0deg); }
    50% { transform: scale(1.1) rotate(5deg); }
    100% { transform: scale(1) rotate(0deg); }
}

/* Animation classes */
.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-fade-in-up {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease-out;
}

.animate-fade-in-up.animate {
    opacity: 1;
    transform: translateY(0);
}

.animate-bounce-once {
    animation: bounce-once 0.8s ease-out;
}

/* Animation delays */
.animation-delay-200 {
    animation-delay: 200ms;
}

.animation-delay-400 {
    animation-delay: 400ms;
}

.animation-delay-600 {
    animation-delay: 600ms;
}

.animation-delay-800 {
    animation-delay: 800ms;
}

.animation-delay-1000 {
    animation-delay: 1000ms;
}

.animation-delay-2000 {
    animation-delay: 2000ms;
}

/* Enhanced transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Success styling */
.success-icon {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.25);
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .text-4xl {
        font-size: 2.25rem;
        line-height: 2.5rem;
    }
    
    .text-6xl {
        font-size: 3rem;
        line-height: 1;
    }
}
</style>
@endpush

</x-layouts.public>