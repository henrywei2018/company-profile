{{-- 
    Chat Message Component
    Reusable component untuk menampilkan pesan chat
    Usage: <x-chat.message :message="$message" :user-type="'visitor'" />
--}}

@props([
    'message',
    'userType' => 'visitor', // 'visitor', 'operator', 'admin'
    'showAvatar' => true,
    'showTime' => true,
    'theme' => 'default'
])

@php
    $isOwn = $message->sender_type === $userType;
    
    // Theme classes
    $themes = [
        'default' => [
            'own' => 'bg-blue-500 text-white',
            'other' => 'bg-white border border-gray-200 text-gray-900 dark:bg-gray-800 dark:border-gray-600 dark:text-white',
            'container' => $isOwn ? 'justify-end' : 'justify-start'
        ],
        'admin' => [
            'own' => 'bg-blue-600 text-white',
            'other' => 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white',
            'container' => $isOwn ? 'justify-end' : 'justify-start'
        ]
    ];

    $currentTheme = $themes[$theme] ?? $themes['default'];
    $messageClass = $isOwn ? $currentTheme['own'] : $currentTheme['other'];
    $borderRadius = $isOwn ? 'rounded-lg rounded-br-none' : 'rounded-lg rounded-bl-none';
@endphp

<div class="flex {{ $currentTheme['container'] }} mb-4" x-data="chatMessage()" x-init="init()">
    <!-- Avatar (for other users) -->
    @if(!$isOwn && $showAvatar)
        <div class="flex-shrink-0 mr-3">
            <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center">
                @if($message->sender_avatar)
                    <img src="{{ $message->sender_avatar }}" 
                         alt="{{ $message->sender_name }}" 
                         class="w-8 h-8 rounded-full object-cover">
                @else
                    <span class="text-white text-sm font-medium">
                        {{ substr($message->sender_name ?? 'U', 0, 1) }}
                    </span>
                @endif
            </div>
        </div>
    @endif

    <!-- Message Bubble -->
    <div class="max-w-xs lg:max-w-md relative group">
        <!-- Sender Name (for other users in group chats) -->
        @if(!$isOwn && isset($message->sender_name) && $userType === 'admin')
            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 px-1">
                {{ $message->sender_name }}
                @if($message->sender_type === 'operator')
                    <span class="text-blue-500">(Operator)</span>
                @endif
            </div>
        @endif

        <!-- Message Content -->
        <div class="{{ $messageClass }} {{ $borderRadius }} px-4 py-2 relative">
            <!-- Message Type: Text -->
            @if($message->type === 'text' || !isset($message->type))
                <div class="break-words">
                    {!! nl2br(e($message->content)) !!}
                </div>
            @endif

            <!-- Message Type: File -->
            @if($message->type === 'file')
                <div class="space-y-2">
                    @if($message->attachments && count($message->attachments) > 0)
                        @foreach($message->attachments as $attachment)
                            <div class="flex items-center space-x-2 p-2 bg-black bg-opacity-10 rounded">
                                <!-- File Icon -->
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                
                                <!-- File Info -->
                                <div class="flex-1 min-w-0">
                                    <a href="{{ $attachment->url ?? '#' }}" 
                                       target="_blank" 
                                       class="text-sm underline hover:no-underline block truncate"
                                       download="{{ $attachment->original_name ?? 'file' }}">
                                        {{ $attachment->original_name ?? 'Unknown File' }}
                                    </a>
                                    @if(isset($attachment->size))
                                        <div class="text-xs opacity-75">
                                            {{ formatFileSize($attachment->size) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                    
                    @if($message->content)
                        <div class="break-words mt-2">
                            {!! nl2br(e($message->content)) !!}
                        </div>
                    @endif
                </div>
            @endif

            <!-- Message Type: Image -->
            @if($message->type === 'image')
                <div class="space-y-2">
                    @if($message->attachments && count($message->attachments) > 0)
                        <div class="grid gap-2 {{ count($message->attachments) > 1 ? 'grid-cols-2' : 'grid-cols-1' }}">
                            @foreach($message->attachments as $attachment)
                                <div class="relative group cursor-pointer" 
                                     @click="openImagePreview('{{ $attachment->url }}', '{{ $attachment->original_name }}')">
                                    <img src="{{ $attachment->url }}" 
                                         alt="{{ $attachment->original_name ?? 'Image' }}"
                                         class="max-w-full rounded object-cover hover:opacity-90 transition-opacity"
                                         style="max-height: 200px;">
                                    
                                    <!-- Zoom Icon Overlay -->
                                    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded">
                                        <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                        </svg>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    @if($message->content)
                        <div class="break-words">
                            {!! nl2br(e($message->content)) !!}
                        </div>
                    @endif
                </div>
            @endif

            <!-- Message Type: System -->
            @if($message->type === 'system')
                <div class="italic text-center text-sm opacity-75">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $message->content }}
                </div>
            @endif
        </div>

        <!-- Message Meta Info -->
        <div class="flex items-center justify-between mt-1 px-1 opacity-0 group-hover:opacity-100 transition-opacity">
            @if($showTime)
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $message->created_at ? $message->created_at->format('H:i') : '' }}
                </span>
            @endif

            <!-- Message Status (for own messages) -->
            @if($isOwn)
                <div class="flex items-center space-x-1">
                    @if(isset($message->status))
                        @switch($message->status)
                            @case('sending')
                                <svg class="w-3 h-3 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                @break
                            @case('sent')
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                @break
                            @case('delivered')
                                <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                @break
                            @case('read')
                                <div class="flex">
                                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <svg class="w-3 h-3 text-blue-500 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                @break
                            @case('failed')
                                <svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                @break
                        @endswitch
                    @endif
                    
                    <!-- Retry Button for Failed Messages -->
                    @if(isset($message->status) && $message->status === 'failed')
                        <button @click="retryMessage({{ $message->id }})" 
                                class="text-xs text-red-500 hover:text-red-700 underline ml-1"
                                title="Retry sending">
                            Retry
                        </button>
                    @endif
                </div>
            @endif
        </div>

        <!-- Message Actions Menu (shown on hover) -->
        <div class="absolute top-0 {{ $isOwn ? 'left-0 -ml-8' : 'right-0 -mr-8' }} opacity-0 group-hover:opacity-100 transition-opacity">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="p-1 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                </button>

                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute {{ $isOwn ? 'right-0' : 'left-0' }} mt-2 w-32 bg-white dark:bg-gray-700 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-10">
                    <div class="py-1">
                        <button @click="copyMessage('{{ addslashes($message->content) }}'); open = false" 
                                class="block w-full text-left px-3 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                            Copy Text
                        </button>
                        
                        @if($userType === 'admin' || $userType === 'operator')
                            <button @click="flagMessage({{ $message->id }}); open = false" 
                                    class="block w-full text-left px-3 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                Flag Message
                            </button>
                        @endif
                        
                        @if($isOwn && isset($message->status) && $message->status !== 'failed')
                            <button @click="deleteMessage({{ $message->id }}); open = false" 
                                    class="block w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600">
                                Delete
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Avatar (for own messages) -->
    @if($isOwn && $showAvatar)
        <div class="flex-shrink-0 ml-3">
            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                @if(auth()->user() && auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" 
                         alt="{{ auth()->user()->name }}" 
                         class="w-8 h-8 rounded-full object-cover">
                @else
                    <span class="text-white text-sm font-medium">
                        {{ auth()->user() ? substr(auth()->user()->name, 0, 1) : 'U' }}
                    </span>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
function chatMessage() {
    return {
        init() {
            // Initialize message component
        },

        openImagePreview(imageUrl, imageName) {
            // Create and show image preview modal
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4';
            modal.innerHTML = `
                <div class="relative max-w-full max-h-full">
                    <img src="${imageUrl}" alt="${imageName}" class="max-w-full max-h-full object-contain">
                    <button onclick="this.closest('.fixed').remove()" 
                            class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-75 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <div class="absolute bottom-4 left-4 text-white bg-black bg-opacity-50 px-3 py-2 rounded-lg">
                        <p class="text-sm font-medium">${imageName}</p>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Close on background click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        },

        copyMessage(content) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(content).then(() => {
                    this.showToast('Message copied to clipboard');
                }).catch(() => {
                    this.fallbackCopy(content);
                });
            } else {
                this.fallbackCopy(content);
            }
        },

        fallbackCopy(content) {
            const textArea = document.createElement('textarea');
            textArea.value = content;
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
                this.showToast('Message copied to clipboard');
            } catch (err) {
                this.showToast('Failed to copy message', 'error');
            }
            document.body.removeChild(textArea);
        },

        async retryMessage(messageId) {
            try {
                // Emit retry event to parent component
                this.$dispatch('message:retry', { messageId });
            } catch (error) {
                console.error('Failed to retry message:', error);
                this.showToast('Failed to retry message', 'error');
            }
        },

        async flagMessage(messageId) {
            try {
                // Emit flag event to parent component
                this.$dispatch('message:flag', { messageId });
                this.showToast('Message flagged successfully');
            } catch (error) {
                console.error('Failed to flag message:', error);
                this.showToast('Failed to flag message', 'error');
            }
        },

        async deleteMessage(messageId) {
            if (!confirm('Are you sure you want to delete this message?')) {
                return;
            }

            try {
                // Emit delete event to parent component
                this.$dispatch('message:delete', { messageId });
            } catch (error) {
                console.error('Failed to delete message:', error);
                this.showToast('Failed to delete message', 'error');
            }
        },

        showToast(message, type = 'success') {
            const toast = document.createElement('div');
            const bgColor = type === 'error' ? 'bg-red-500' : 
                           type === 'warning' ? 'bg-yellow-500' : 'bg-green-500';
            
            toast.className = `fixed top-4 right-4 ${bgColor} text-white px-4 py-2 rounded-lg shadow-lg z-50 max-w-sm`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    }
}

// Helper function for file size formatting
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>