{{-- resources/views/components/admin/closed-session.blade.php --}}
@props(['session'])

@php
    // Only render if this is an authenticated user session
    if (!$session->user_id) {
        return;
    }a
@endphp

<div class="p-4 border border-gray-200 dark:border-neutral-700 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors" 
     data-session-id="{{ $session->session_id }}">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center space-x-2 mb-2">
                <!-- User Avatar -->
                <div class="flex-shrink-0">
                    @if($session->user && $session->user->avatar)
                        <img class="h-8 w-8 rounded-full object-cover" 
                             src="{{ $session->user->avatar_url }}" 
                             alt="{{ $session->getVisitorName() }}">
                    @else
                        <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-800 flex items-center justify-center">
                            <span class="text-xs font-medium text-blue-800 dark:text-blue-200">
                                {{ substr($session->getVisitorName(), 0, 2) }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- User Info -->
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                        {{ $session->getVisitorName() }}
                    </h4>
                    
                    <!-- Authenticated User Badge -->
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Registered User
                        </span>
                        
                        @if($session->priority !== 'normal')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $session->priority === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                {{ ucfirst($session->priority) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Session Details -->
            <div class="space-y-1 text-xs text-gray-500 dark:text-gray-400">
                <div class="flex items-center justify-between">
                    <span>Email:</span>
                    <span class="text-gray-900 dark:text-white">{{ $session->getVisitorEmail() ?: 'Not provided' }}</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span>Duration:</span>
                    <span class="text-gray-900 dark:text-white">
                        {{ $session->getDuration() ? $session->getDuration() . ' min' : 'Unknown' }}
                    </span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span>Messages:</span>
                    <span class="text-gray-900 dark:text-white">{{ $session->messages_count ?? $session->messages->count() }}</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span>Operator:</span>
                    <span class="text-gray-900 dark:text-white">{{ $session->operator ? $session->operator->name : 'Bot/System' }}</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span>Closed:</span>
                    <span class="text-gray-900 dark:text-white">{{ $session->ended_at ? $session->ended_at->diffForHumans() : $session->updated_at->diffForHumans() }}</span>
                </div>

                @if($session->close_reason)
                    <div class="flex items-start justify-between">
                        <span>Reason:</span>
                        <span class="text-gray-900 dark:text-white text-right max-w-32 break-words">{{ $session->close_reason }}</span>
                    </div>
                @endif
            </div>

            <!-- Last Message Preview -->
            @if($session->messages->isNotEmpty())
                @php
                    $lastMessage = $session->messages->last();
                @endphp
                <div class="mt-3 p-2 bg-gray-100 dark:bg-gray-600 rounded text-xs">
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="font-medium text-gray-700 dark:text-gray-300">
                            {{ $lastMessage->sender_type === 'visitor' ? 'Customer' : ($lastMessage->sender_type === 'operator' ? 'Agent' : 'System') }}:
                        </span>
                        <span class="text-gray-500 dark:text-gray-400">
                            {{ $lastMessage->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 line-clamp-2">
                        {{ Str::limit($lastMessage->message, 80) }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="flex flex-col space-y-2 ml-4">
            <a href="{{ route('admin.chat.show', $session) }}" 
               class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800 dark:hover:bg-blue-900/50 transition-colors">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View
            </a>
            
            @if($session->user && $session->user->email)
                <button onclick="sendFollowUpEmail('{{ $session->session_id }}')" 
                        class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium text-green-600 bg-green-50 border border-green-200 rounded hover:bg-green-100 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800 dark:hover:bg-green-900/50 transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 3.26a2 2 0 001.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Follow Up
                </button>
            @endif

            <button onclick="archiveSession('{{ $session->session_id }}')" 
                    class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l4 4 4-4"/>
                </svg>
                Archive
            </button>
        </div>
    </div>

    <!-- Customer Satisfaction Rating (if available) -->
    @if(isset($session->feedback_rating) && $session->feedback_rating)
        <div class="mt-3 flex items-center justify-between p-2 bg-blue-50 dark:bg-blue-900/30 rounded border border-blue-200 dark:border-blue-800">
            <span class="text-xs font-medium text-blue-800 dark:text-blue-300">Customer Rating:</span>
            <div class="flex items-center space-x-1">
                @for($i = 1; $i <= 5; $i++)
                    <svg class="w-3 h-3 {{ $i <= $session->feedback_rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                         fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endfor
                <span class="text-xs text-blue-600 dark:text-blue-400 ml-1">({{ $session->feedback_rating }}/5)</span>
            </div>
        </div>
    @endif

    <!-- Customer Feedback (if available) -->
    @if(isset($session->feedback_comment) && $session->feedback_comment)
        <div class="mt-2 p-2 bg-gray-50 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600">
            <div class="flex items-center space-x-2 mb-1">
                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1.586l-4 4z"/>
                </svg>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Customer Feedback:</span>
            </div>
            <p class="text-xs text-gray-600 dark:text-gray-400 italic">
                "{{ $session->feedback_comment }}"
            </p>
        </div>
    @endif
</div>

<script>
    function sendFollowUpEmail(sessionId) {
        if (!confirm('Send a follow-up email to this customer?')) return;
        
        fetch(`/admin/chat/sessions/${sessionId}/follow-up-email`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Follow-up email sent successfully!', 'success');
            } else {
                showNotification(data.message || 'Failed to send follow-up email', 'error');
            }
        })
        .catch(error => {
            console.error('Follow-up email error:', error);
            showNotification('Failed to send follow-up email', 'error');
        });
    }

    function archiveSession(sessionId) {
        if (!confirm('Archive this session? It will be moved to archived sessions.')) return;
        
        fetch(`/admin/chat/sessions/${sessionId}/archive`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Session archived successfully!', 'success');
                // Remove the session card from the view
                const sessionCard = document.querySelector(`[data-session-id="${sessionId}"]`);
                if (sessionCard) {
                    sessionCard.style.transition = 'all 0.3s ease';
                    sessionCard.style.opacity = '0';
                    sessionCard.style.transform = 'translateX(-100%)';
                    setTimeout(() => sessionCard.remove(), 300);
                }
            } else {
                showNotification(data.message || 'Failed to archive session', 'error');
            }
        })
        .catch(error => {
            console.error('Archive session error:', error);
            showNotification('Failed to archive session', 'error');
        });
    }
</script>