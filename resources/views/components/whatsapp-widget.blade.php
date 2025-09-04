{{-- resources/views/components/whatsapp-widget.blade.php --}}
@props([
    'phoneNumber' => '6281234567890',
    'message' => 'Halo, saya ingin bertanya tentang layanan Anda.',
    'position' => 'bottom-left',
    'showWhenChatOffline' => true,
    'alwaysShow' => false,
    'offsetFromQuickAction' => true
])

@php
    $positionClasses = [
        'bottom-right' => $offsetFromQuickAction ? 'bottom-6 right-3 sm:bottom-28 sm:right-3.5' : 'bottom-4 right-4 sm:bottom-20 sm:right-6',
        'bottom-left' => $offsetFromQuickAction ? 'bottom-6 left-3 sm:bottom-16 sm:left-3.5' : 'bottom-4 left-4 sm:bottom-6 sm:left-6',
        'top-right' => 'top-4 right-4 sm:top-6 sm:right-6',
        'top-left' => 'top-4 left-4 sm:top-6 sm:left-6',
    ];
    
    $currentPosition = $positionClasses[$position] ?? $positionClasses['bottom-left'];
@endphp

<!-- WhatsApp Widget Container -->
<div id="whatsapp-widget" 
     class="fixed {{ $currentPosition }} z-30 font-sans"
     x-data="whatsappWidget()"
     x-init="init()">
    
    <!-- WhatsApp Button -->
    <div class="relative group">
        <!-- Tooltip -->
        <div class="absolute bottom-full mb-3 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs rounded-lg px-3 py-2 opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none whitespace-nowrap">
            Hubungi via WhatsApp
            <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
        </div>
        
        <!-- Main WhatsApp Button -->
        <button @click="openWhatsApp()" 
                class="w-12 h-12 sm:w-14 sm:h-14 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-lg hover:shadow-xl transform hover:scale-110 transition-all duration-300 flex items-center justify-center group relative overflow-hidden hover:ring-4 hover:ring-green-500/30"
                title="Hubungi via WhatsApp"
                aria-label="Open WhatsApp">
            
            <!-- WhatsApp Icon -->
            <svg class="w-6 h-6 sm:w-7 sm:h-7 transition-transform duration-200 group-hover:scale-110" 
                 fill="currentColor" 
                 viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.787"/>
            </svg>
            
            <!-- Pulse Animation Ring -->
            <div class="absolute inset-0 rounded-full border-2 border-green-400 opacity-75 animate-ping"></div>
            
            <!-- Ripple Effect -->
            <div class="absolute inset-0 rounded-full opacity-0 group-hover:opacity-20 bg-white transition-opacity duration-200"></div>
        </button>
        
        <!-- Online Status Indicator (if connected to chat status) -->
        @if($showWhenChatOffline)
        <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 border-2 border-white rounded-full shadow-sm" 
             x-show="!chatIsOnline"
             title="Chat offline - WhatsApp tersedia">
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function whatsappWidget() {
    return {
        // State
        shouldShow: true, // Always show initially for testing
        chatIsOnline: true,
        phoneNumber: '{{ $phoneNumber }}',
        message: '{{ $message }}',
        showWhenChatOffline: {{ $showWhenChatOffline ? 'true' : 'false' }},
        alwaysShow: {{ $alwaysShow ? 'true' : 'false' }},
        
        // Timers
        statusCheckTimer: null,
        
        // Initialize
        init() {
            if (this.showWhenChatOffline) {
                this.startStatusMonitoring();
            } else if (this.alwaysShow) {
                this.shouldShow = true;
            }
            
            // Show welcome animation after delay
            this.showWelcomeAnimation();
        },
        
        // Monitor chat online status
        startStatusMonitoring() {
            this.checkChatStatus();
            
            // Check status every 30 seconds
            this.statusCheckTimer = setInterval(() => {
                this.checkChatStatus();
            }, 30000);
        },
        
        async checkChatStatus() {
            try {
                const response = await fetch('/api/chat/online-status', {
                    method: 'GET',
                    headers: {
                        'Cache-Control': 'no-cache'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.chatIsOnline = data.is_online || false;
                    
                    // Update visibility based on chat status
                    if (this.showWhenChatOffline) {
                        this.shouldShow = !this.chatIsOnline || this.alwaysShow;
                    }
                } else {
                    // If can't check status, assume chat is offline
                    this.chatIsOnline = false;
                    this.shouldShow = true;
                }
            } catch (error) {
                console.log('WhatsApp widget: Unable to check chat status');
                // If can't check status, assume chat is offline
                this.chatIsOnline = false;
                this.shouldShow = true;
            }
        },
        
        // Open WhatsApp
        openWhatsApp() {
            const encodedMessage = encodeURIComponent(this.message);
            const whatsappUrl = `https://wa.me/${this.phoneNumber}?text=${encodedMessage}`;
            
            // Open WhatsApp in new tab
            window.open(whatsappUrl, '_blank');
            
            // Track the interaction (optional)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'whatsapp_contact', {
                    'event_category': 'whatsapp_widget',
                    'event_label': 'direct_contact',
                    'chat_online': this.chatIsOnline
                });
            }
            
            // Add subtle feedback animation
            this.animateClick();
        },
        
        animateClick() {
            const button = this.$el.querySelector('button');
            if (button) {
                button.classList.add('animate-pulse');
                setTimeout(() => {
                    button.classList.remove('animate-pulse');
                }, 600);
            }
        },
        
        showWelcomeAnimation() {
            // Show welcome animation for new visitors
            const hasShownWelcome = localStorage.getItem('whatsapp_widget_welcome_shown');
            
            if (!hasShownWelcome) {
                setTimeout(() => {
                    const button = this.$el.querySelector('button');
                    if (button && this.shouldShow) {
                        // Add bounce animation
                        button.classList.add('animate-bounce');
                        setTimeout(() => {
                            button.classList.remove('animate-bounce');
                        }, 3000);
                        
                        // Mark as shown
                        localStorage.setItem('whatsapp_widget_welcome_shown', 'true');
                    }
                }, 5000); // Wait 5 seconds after page load
            }
        },
        
        // Cleanup
        destroy() {
            if (this.statusCheckTimer) {
                clearInterval(this.statusCheckTimer);
            }
        }
    }
}

// Cleanup when page unloads
window.addEventListener('beforeunload', () => {
    const widget = document.querySelector('#whatsapp-widget');
    if (widget && widget.__x) {
        widget.__x.destroy();
    }
});
</script>
@endpush