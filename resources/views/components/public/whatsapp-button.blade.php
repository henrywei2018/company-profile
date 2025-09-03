{{-- resources/views/components/public/whatsapp-button.blade.php --}}
@props([
    'number' => null,
    'message' => 'Halo! Saya ingin menanyakan tentang layanan dari ' . ($companyProfile->company_name ?? config('app.name')) . '.',
    'position' => 'bottom-right',
    'showTooltip' => true,
    'showPulse' => true,
    'size' => 'default' // default, small, large
])

@php
    $sizeClasses = match($size) {
        'small' => 'w-12 h-12',
        'large' => 'w-16 h-16',
        default => 'w-14 h-14'
    };
    
    $iconSizeClasses = match($size) {
        'small' => 'w-6 h-6',
        'large' => 'w-8 h-8',
        default => 'w-7 h-7'
    };
@endphp

@if($number)
<div class="fixed {{ $position === 'bottom-left' ? 'bottom-6 left-6' : 'bottom-6 right-6' }} z-50 no-print">
    {{-- Pulse Animation Ring --}}
    @if($showPulse)
    <div class="absolute inset-0 {{ $sizeClasses }} rounded-full bg-green-400 animate-ping opacity-75"></div>
    <div class="absolute inset-0 {{ $sizeClasses }} rounded-full bg-green-400 animate-pulse opacity-50"></div>
    @endif
    
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $number) }}?text={{ urlencode($message) }}" 
       target="_blank"
       rel="noopener noreferrer"
       class="relative group flex items-center justify-center {{ $sizeClasses }} bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-full shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-110 focus:outline-none focus:ring-4 focus:ring-green-300 focus:ring-opacity-50"
       aria-label="Chat dengan kami di WhatsApp"
       title="Chat dengan kami di WhatsApp">
        
        {{-- WhatsApp Icon --}}
        <svg class="{{ $iconSizeClasses }} drop-shadow-sm" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
        </svg>
        
        {{-- Online Indicator --}}
        <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 border-2 border-white rounded-full animate-pulse"></div>
        
        {{-- Tooltip --}}
        @if($showTooltip)
        <div class="absolute {{ $position === 'bottom-left' ? 'right-16' : 'left-16' }} bottom-0 mb-2 opacity-0 group-hover:opacity-100 group-focus:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 pointer-events-none">
            <div class="bg-gray-900 text-white text-sm px-3 py-2 rounded-lg shadow-lg whitespace-nowrap font-medium">
                💬 Chat dengan kami di WhatsApp
                <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
            </div>
        </div>
        @endif
    </a>
    
    {{-- Call-to-action message (appears on first visit) --}}
    <div id="whatsapp-cta-message" class="absolute {{ $position === 'bottom-left' ? 'right-16' : 'left-16' }} bottom-0 mb-2 max-w-xs opacity-0 transform translate-y-4 pointer-events-none transition-all duration-500">
        <div class="bg-white border border-gray-200 rounded-lg shadow-xl p-4 relative">
            <button onclick="hideWhatsAppCTA()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-gray-900 mb-1">💬 Ada yang bisa kami bantu?</h4>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Tim customer service kami siap membantu Anda 24/7. Klik untuk chat sekarang!
                    </p>
                    <div class="mt-2 flex items-center text-xs text-green-600">
                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                        Online sekarang
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 {{ $position === 'bottom-left' ? 'left-6' : 'right-6' }} transform translate-y-full">
                <div class="w-0 h-0 border-l-4 border-r-4 border-t-8 border-transparent border-t-white"></div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for CTA functionality --}}
@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show WhatsApp CTA message after 3 seconds on first visit
    const ctaMessage = document.getElementById('whatsapp-cta-message');
    const ctaKey = 'whatsapp_cta_shown_' + new Date().toDateString();
    
    if (ctaMessage && !localStorage.getItem(ctaKey)) {
        setTimeout(() => {
            ctaMessage.classList.remove('opacity-0', 'translate-y-4');
            ctaMessage.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto');
            
            // Auto hide after 10 seconds
            setTimeout(() => {
                hideWhatsAppCTA();
            }, 10000);
        }, 3000);
    }
    
    // Global function to hide CTA
    window.hideWhatsAppCTA = function() {
        if (ctaMessage) {
            ctaMessage.classList.add('opacity-0', 'translate-y-4');
            ctaMessage.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto');
            localStorage.setItem(ctaKey, 'true');
        }
    };
    
    // Show CTA again when hovering over WhatsApp button (if previously dismissed)
    const whatsappButton = document.querySelector('a[href*="wa.me"]');
    if (whatsappButton && localStorage.getItem(ctaKey)) {
        whatsappButton.addEventListener('mouseenter', function() {
            if (ctaMessage && ctaMessage.classList.contains('opacity-0')) {
                ctaMessage.classList.remove('opacity-0', 'translate-y-4');
                ctaMessage.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto');
                
                // Auto hide after 5 seconds on hover
                setTimeout(() => {
                    hideWhatsAppCTA();
                }, 5000);
            }
        });
    }
    
    // Track WhatsApp button clicks for analytics
    if (whatsappButton) {
        whatsappButton.addEventListener('click', function() {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    event_category: 'Contact',
                    event_label: 'WhatsApp Button',
                    value: 1
                });
            }
        });
    }
});
</script>
@endpush
@endonce
@endif