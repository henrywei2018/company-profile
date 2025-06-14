{{-- 
    Chat Typing Indicator Component
    Reusable component untuk menampilkan typing indicator
    Usage: <x-chat.typing-indicator :user-name="'John Doe'" :is-typing="true" />
--}}

@props([
    'userName' => null,
    'isTyping' => false,
    'userType' => 'visitor', // 'visitor', 'operator', 'admin'
    'showAvatar' => true,
    'theme' => 'default'
])

@php
    $themes = [
        'default' => [
            'bubble' => 'bg-white border border-gray-200 text-gray-900 dark:bg-gray-800 dark:border-gray-600 dark:text-white',
            'avatar' => 'bg-gray-400',
            'dots' => 'bg-gray-400'
        ],
        'admin' => [
            'bubble' => 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white',
            'avatar' => 'bg-blue-500',
            'dots' => 'bg-blue-400'
        ]
    ];
    
    $currentTheme = $themes[$theme] ?? $themes['default'];
@endphp

<div x-data="typingIndicator()" 
     x-init="init()"
     x-show="isVisible"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform translate-y-2"
     class="flex justify-start mb-4">

    <!-- Avatar -->
    @if($showAvatar)
        <div class="flex-shrink-0 mr-3">
            <div class="w-8 h-8 {{ $currentTheme['avatar'] }} rounded-full flex items-center justify-center relative">
                @if($userName && auth()->check() && auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" 
                         alt="{{ $userName }}" 
                         class="w-8 h-8 rounded-full object-cover">
                @else
                    <span class="text-white text-sm font-medium">
                        {{ $userName ? substr($userName, 0, 1) : 'U' }}
                    </span>
                @endif
                
                <!-- Online indicator -->
                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
            </div>
        </div>
    @endif

    <!-- Typing Bubble -->
    <div class="{{ $currentTheme['bubble'] }} max-w-xs px-4 py-3 rounded-lg rounded-bl-none shadow-sm">
        <!-- User Name (if provided) -->
        @if($userName && $userType !== 'visitor')
            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                {{ $userName }}
                @switch($userType)
                    @case('operator')
                        <span class="text-blue-500">(Operator)</span>
                        @break
                    @case('admin')
                        <span class="text-purple-500">(Admin)</span>
                        @break
                @endswitch
            </div>
        @endif

        <!-- Typing Animation -->
        <div class="flex items-center space-x-1">
            <!-- Animated dots -->
            <div class="flex space-x-1">
                <div class="w-2 h-2 {{ $currentTheme['dots'] }} rounded-full animate-bounce"></div>
                <div class="w-2 h-2 {{ $currentTheme['dots'] }} rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 {{ $currentTheme['dots'] }} rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
            
            <!-- Typing text -->
            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                {{ $userName ? $userName . ' is typing...' : 'typing...' }}
            </span>
        </div>
    </div>
</div>

<script>
function typingIndicator() {
    return {
        isVisible: {{ $isTyping ? 'true' : 'false' }},
        typingTimer: null,

        init() {
            // Listen for typing events
            this.$watch('$props.isTyping', (newValue) => {
                this.updateVisibility(newValue);
            });

            // Auto-hide after timeout
            if (this.isVisible) {
                this.startTypingTimer();
            }
        },

        updateVisibility(isTyping) {
            this.isVisible = isTyping;
            
            if (isTyping) {
                this.startTypingTimer();
            } else {
                this.clearTypingTimer();
            }
        },

        startTypingTimer() {
            this.clearTypingTimer();
            
            // Auto-hide after 3 seconds of no typing updates
            this.typingTimer = setTimeout(() => {
                this.isVisible = false;
            }, 3000);
        },

        clearTypingTimer() {
            if (this.typingTimer) {
                clearTimeout(this.typingTimer);
                this.typingTimer = null;
            }
        },

        show() {
            this.isVisible = true;
            this.startTypingTimer();
        },

        hide() {
            this.isVisible = false;
            this.clearTypingTimer();
        }
    }
}
</script>

<style>
/* Custom bounce animation with staggered delay */
@keyframes bounce-typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
}

.animate-bounce {
    animation: bounce-typing 1.4s infinite;
}
</style>