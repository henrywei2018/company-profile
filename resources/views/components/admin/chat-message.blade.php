{{-- resources/views/components/chat-messages.blade.php --}}
@props(['messages'])

@forelse($messages as $message)
    <div class="flex {{ $message->isFromVisitor() ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
        <div class="max-w-xs lg:max-w-md">
            <!-- Message bubble -->
            <div class="px-4 py-2 rounded-lg {{ $message->isFromVisitor() ? 'bg-blue-600 text-white rounded-br-none' : ($message->isFromBot() ? 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-bl-none' : ($message->sender_type === 'system' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded-lg italic text-center text-sm' : 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-bl-none')) }}">
                
                <!-- Sender info -->
                <div class="flex items-center space-x-2 mb-1">
                    <span class="text-xs">
                        @if($message->isFromVisitor())
                            👤
                        @elseif($message->isFromBot())
                            🤖
                        @elseif($message->sender_type === 'system')
                            ℹ️
                        @else
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </span>
                    <span class="text-xs font-medium {{ $message->isFromVisitor() ? 'text-blue-100' : 'opacity-75' }}">
                        {{ $message->getSenderName() }}
                    </span>
                    <span class="text-xs {{ $message->isFromVisitor() ? 'text-blue-200' : 'opacity-50' }}">
                        {{ $message->created_at->format('H:i') }}
                    </span>
                </div>
                
                <!-- Message content -->
                <div class="text-sm">
                    @if($message->message_type === 'system')
                        <em>{{ $message->message }}</em>
                    @else
                        {{ $message->message }}
                    @endif
                </div>
                
                <!-- Message status -->
                @if($message->isFromVisitor() && $message->is_read)
                    <div class="text-right mt-1">
                        <span class="text-xs text-blue-200">✓ Read</span>
                    </div>
                @endif
            </div>
            
            <!-- Timestamp -->
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 {{ $message->isFromVisitor() ? 'text-right' : 'text-left' }}">
                {{ $message->created_at->format('M j, H:i:s') }}
            </div>
        </div>
    </div>
@empty
    <div class="text-center text-gray-500 dark:text-gray-400 py-8">
        <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <p>No messages yet</p>
        <p class="text-xs mt-1">Messages will appear here when the conversation starts</p>
    </div>
@endforelse

