<x-layouts.client title="Detail Pesan" :unreadMessages="0" :pendingQuotations="0">
    <!-- Compact Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('client.messages.index') }}" 
               class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali to Messages
            </a>
            <div class="h-4 w-px bg-gray-300 dark:bg-gray-600"></div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $rootMessage->subject }}</h1>
        </div>

        <div class="flex items-center gap-2">
            @if($rootMessage->isUrgent())
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                    Urgent
                </span>
            @else
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                    {{ $rootMessage->priority === 'high' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400' : 
                       ($rootMessage->priority === 'low' ? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' : 
                       'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400') }}">
                    {{ ucfirst($rootMessage->priority) }}
                </span>
            @endif

            @if($canEscalate && !$rootMessage->isUrgent())
                <form action="{{ route('client.messages.mark-urgent', $message) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full text-orange-700 bg-orange-100 hover:bg-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:hover:bg-orange-900/50"
                            onclick="return confirm('Mark this message as urgent?')">
                        Mark Urgent
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Message Info Card -->
            <x-admin.card>
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">{{ $rootMessage->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $rootMessage->email }}</p>
                        </div>
                    </div>
                    
                    <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                        <div>{{ $rootMessage->created_at->format('M j, Y') }}</div>
                        <div>{{ $rootMessage->created_at->format('g:i A') }}</div>
                    </div>
                </div>

                @if($rootMessage->project)
                    <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="text-sm font-medium text-green-800 dark:text-green-300">Related to Project: 
                                <a href="{{ route('client.projects.show', $rootMessage->project) }}" class="underline hover:no-underline">
                                    {{ $rootMessage->project->title }}
                                </a>
                            </span>
                        </div>
                    </div>
                @endif
            </x-admin.card>

            <!-- Conversation Thread -->
            <x-admin.card>
                <x-slot name="title">
                    Conversation ({{ $thread->count() }})
                    @if($thread->where('is_read', false)->where('type', 'admin_to_client')->count() > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                            {{ $thread->where('is_read', false)->where('type', 'admin_to_client')->count() }} new
                        </span>
                    @endif
                </x-slot>

                <div class="space-y-6" id="conversation-thread">
                    @foreach($thread as $index => $threadMessage)
                        <div class="flex {{ $threadMessage->type === 'admin_to_client' ? 'flex-row-reverse' : '' }} gap-3" 
                             data-message-id="{{ $threadMessage->id }}">
                            
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center
                                    {{ $threadMessage->type === 'admin_to_client' 
                                        ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' 
                                        : 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                    @if($threadMessage->type === 'admin_to_client')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <!-- Message Content -->
                            <div class="flex-1 min-w-0">
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4 {{ $threadMessage->type === 'admin_to_client' ? 'border-l-4 border-green-400' : 'border-l-4 border-blue-400' }} break-words overflow-hidden">
                                    
                                    <!-- Message Header -->
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2 min-w-0 flex-1">
                                            <span class="font-medium text-sm {{ $threadMessage->type === 'admin_to_client' ? 'text-green-700 dark:text-green-400' : 'text-gray-900 dark:text-white' }}">
                                                {{ $threadMessage->type === 'admin_to_client' ? 'Support Team' : 'You' }}
                                            </span>
                                            
                                            @if(!$threadMessage->is_read && $threadMessage->type === 'admin_to_client')
                                                <span class="px-1.5 py-0.5 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded text-xs font-medium flex-shrink-0">
                                                    New
                                                </span>
                                            @endif

                                            @if($index === 0)
                                                <span class="px-1.5 py-0.5 bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 rounded text-xs flex-shrink-0">
                                                    Original
                                                </span>
                                            @endif
                                        </div>

                                        <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 ml-2">
                                            {{ $threadMessage->created_at->format('M j, g:i A') }}
                                        </span>
                                    </div>

                                    <!-- Message Body -->
                                    <div class="text-sm text-gray-900 dark:text-white break-words overflow-hidden">
                                        <div class=" break-all overflow-wrap-anywhere max-w-full">
                                            {!! nl2br(e($threadMessage->message)) !!}
                                        </div>
                                    </div>

                                    <!-- Attachments -->
                                    @if($threadMessage->attachments->count() > 0)
                                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($threadMessage->attachments as $attachment)
                                                    <a href="{{ route('client.messages.attachment.download', ['message' => $threadMessage->id, 'attachmentId' => $attachment->id]) }}" 
                                                       class="inline-flex items-center px-2 py-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-xs hover:bg-gray-50 dark:hover:bg-gray-600 max-w-full">
                                                        <svg class="w-3 h-3 mr-1 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                        </svg>
                                                        <span class="truncate min-w-0">{{ Str::limit($attachment->file_name, 20) }}</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-admin.card>

            <!-- Reply Form -->
            @if($canReply)
                <x-admin.card id="reply-section">
                    <x-slot name="title">Send Reply</x-slot>
                    
                    <form action="{{ route('client.messages.reply', $message) }}" method="POST" id="reply-form">
                        @csrf
                        
                        <div class="space-y-4">
                            <!-- Message Input -->
                            <div>
                                <label for="reply-message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Your Reply <span class="text-red-500">*</span>
                                </label>
                                <textarea id="reply-message" 
                                          name="message" 
                                          rows="4" 
                                          required 
                                          minlength="10"
                                          maxlength="5000"
                                          placeholder="Type your reply here..."
                                          class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('message') }}</textarea>
                                <div class="mt-1 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <span><span id="char-count">0</span>/5000 characters</span>
                                    <span>You will receive a notification when support responds</span>
                                </div>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Universal File Uploader -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Attachments (Optional)
                                </label>
                                
                                <x-universal-file-uploader 
                                    name="files"
                                    :multiple="true"
                                    :maxFiles="5"
                                    maxFileSize="10MB"
                                    :acceptedFileTypes="[
                                        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                                        'application/pdf',
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/vnd.ms-excel',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'text/plain', 'text/csv',
                                        'application/zip',
                                        'application/x-rar-compressed'
                                    ]"
                                    dropDescription="Drop reply attachments here or click to browse"
                                    uploadEndpoint="{{ route('client.messages.temp-upload') }}"
                                    deleteEndpoint="{{ route('client.messages.temp-delete') }}"
                                    :enableCategories="false"
                                    :enableDescription="false"
                                    :enablePublicToggle="false"
                                    :autoUpload="true"
                                    :uploadOnDrop="true"
                                    :compact="true"
                                    theme="minimal"
                                    id="reply-attachments"
                                    component="reply-attachments"
                                    uploaderId="reply-attachments"
                                />
                                
                                @error('temp_files')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Hidden field for temp files -->
                            <input type="hidden" name="temp_files" id="reply_temp_files" value="">

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-end gap-3 pt-4">
                                <button type="button" 
                                        onclick="clearReplyForm()"
                                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Clear
                                </button>
                                
                                <button type="submit" 
                                        id="send-reply-btn"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Send Reply
                                </button>
                            </div>
                        </div>
                    </form>
                </x-admin.card>
            @else
                <x-admin.card>
                    <div class="text-center py-8">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Cannot Reply</h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            This conversation has been closed or you don't have permission to reply.
                        </p>
                    </div>
                </x-admin.card>
            @endif
        </div>

        <!-- Compact Sidebar -->
        <div class="lg:col-span-1 space-y-4">
            <!-- Quick Actions -->
            <x-admin.card>
                <x-slot name="title">Quick Actions</x-slot>
                
                <div class="space-y-2">
                    @if($canReply)
                        <button onclick="scrollToReply()" 
                                class="w-full inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 border border-transparent rounded-md text-sm font-medium text-white">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                            </svg>
                            Reply
                        </button>
                    @endif

                    <a href="{{ route('client.messages.create', ['project_id' => $rootMessage->project_id]) }}" 
                       class="w-full inline-flex items-center justify-center px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Pesan Baru
                    </a>
                </div>
            </x-admin.card>

            <!-- Detail Pesan -->
            <x-admin.card>
                <x-slot name="title">Details</x-slot>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Type:</span>
                        <span class="ml-2 font-medium">{{ ucfirst(str_replace('_', ' ', $rootMessage->type)) }}</span>
                    </div>
                    
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Status:</span>
                        <span class="ml-2">
                            @if($rootMessage->is_replied)
                                <span class="text-green-600 dark:text-green-400 font-medium">Replied</span>
                            @else
                                <span class="text-yellow-600 dark:text-yellow-400 font-medium">Pending</span>
                            @endif
                        </span>
                    </div>
                    
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Messages:</span>
                        <span class="ml-2 font-medium">{{ $thread->count() }}</span>
                    </div>
                    
                    @if($thread->count() > 1)
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Last Activity:</span>
                            <span class="ml-2 font-medium">{{ $thread->last()->created_at->format('M j, g:i A') }}</span>
                        </div>
                    @endif
                </div>
            </x-admin.card>

            <!-- Related Messages -->
            @if($relatedMessages->count() > 0)
                <x-admin.card>
                    <x-slot name="title">Related Messages</x-slot>
                    
                    <div class="space-y-2">
                        @foreach($relatedMessages->take(3) as $relatedMessage)
                            <a href="{{ route('client.messages.show', $relatedMessage) }}" 
                               class="block p-2 bg-gray-50 dark:bg-gray-800 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="font-medium text-sm truncate">{{ $relatedMessage->subject }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $relatedMessage->created_at->format('M j') }}
                                    @if(!$relatedMessage->is_read)
                                        <span class="ml-1 w-1.5 h-1.5 bg-blue-500 rounded-full inline-block"></span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                        
                        @if($relatedMessages->count() > 3)
                            <div class="text-center pt-1">
                                <a href="{{ route('client.messages.project', $rootMessage->project) }}" 
                                   class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                    View all {{ $relatedMessages->count() }} messages
                                </a>
                            </div>
                        @endif
                    </div>
                </x-admin.card>
            @endif
        </div>
    </div>

    <!-- JavaScript -->
    <style>
        /* Fix for long text overflow in message bubbles */
        .break-all {
            word-break: break-all !important;
            overflow-wrap: break-word !important;
            word-wrap: break-word !important;
            hyphens: auto;
        }
        
        .overflow-wrap-anywhere {
            overflow-wrap: anywhere !important;
        }
        
        /* Ensure message containers don't overflow */
        #conversation-thread .flex-1 {
            min-width: 0;
            max-width: 100%;
        }
        
        /* Handle very long URLs or text strings */
        .message-content {
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
            -webkit-hyphens: auto;
            -moz-hyphens: auto;
            hyphens: auto;
        }
        
        /* Force break for very long words */
        .whitespace-pre-wrap {
            white-space: pre-wrap !important;
            word-break: break-all !important;
            overflow-wrap: anywhere !important;
        }
        
        /* Conversation thread responsive containers */
        @media (max-width: 768px) {
            #conversation-thread > div {
                max-width: calc(100vw - 2rem);
            }
        }
    </style>

    <script>
        // Session ID for constructing file paths
        const sessionId = '{{ session()->getId() }}';
        
        // Character counter
        function updateCharCount() {
            const textarea = document.getElementById('reply-message');
            const counter = document.getElementById('char-count');
            if (!textarea || !counter) return;
            
            const count = textarea.value.length;
            counter.textContent = count;
            
            if (count > 4500) {
                counter.className = 'text-red-600 font-medium';
            } else if (count > 4000) {
                counter.className = 'text-yellow-600 font-medium';
            } else {
                counter.className = '';
            }
        }

        // Scroll to reply form
        function scrollToReply() {
            const replySection = document.getElementById('reply-section');
            if (replySection) {
                replySection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                setTimeout(() => {
                    document.getElementById('reply-message')?.focus();
                }, 500);
            }
        }

        // Clear reply form - UPDATED
        function clearReplyForm() {
            const textarea = document.getElementById('reply-message');
            const tempFiles = document.getElementById('reply_temp_files');
            
            if (textarea) textarea.value = '';
            if (tempFiles) tempFiles.value = '';
            
            // Clear the JavaScript array
            replyUploadedFiles = [];
            
            updateCharCount();
            
            // Clear universal uploader files
            if (window.universalUploaderInstances && window.universalUploaderInstances['reply-attachments']) {
                window.universalUploaderInstances['reply-attachments'].clearAll();
            }
            
            // Alternative clearing method
            const uploaderElement = document.getElementById('reply-attachments');
            if (uploaderElement && uploaderElement.__vue__) {
                uploaderElement.__vue__.clearAll();
            }
        }

        // Universal Uploader Integration - FIXED FOR files-uploaded EVENT
        let replyUploadedFiles = [];

        document.addEventListener('DOMContentLoaded', function() {
            updateCharCount();

            // Auto-scroll to latest message
            const threadMessages = document.querySelectorAll('#conversation-thread > div');
            if (threadMessages.length > 1) {
                setTimeout(() => {
                    const lastMessage = threadMessages[threadMessages.length - 1];
                    lastMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }

            // Auto-focus reply if there are unread messages
            const unreadMessages = document.querySelectorAll('[data-message-id] .bg-red-100');
            if (unreadMessages.length > 0 && document.getElementById('reply-message')) {
                setTimeout(scrollToReply, 1500);
            }

            // PRIMARY EVENT LISTENER - files-uploaded (this is what your uploader uses!)
            window.addEventListener('files-uploaded', function(e) {
                console.log('🎯 files-uploaded event captured:', e.detail);
                
                // Check if this event is for our reply uploader
                const isReplyUploader = e.detail.component === 'reply-attachments' || 
                                       e.detail.uploaderId === 'reply-attachments' ||
                                       e.detail.id === 'reply-attachments';
                
                if (isReplyUploader && e.detail.files && Array.isArray(e.detail.files)) {
                    console.log('✅ Event is for reply-attachments uploader');
                    
                    // Extract file paths from the uploaded files
                    const newFilePaths = e.detail.files.map(file => {
                        console.log('Processing file:', file);
                        
                        // The API response shows files have these properties: path, file_path, etc.
                        let filePath = file.path || file.file_path || file.filePath;
                        
                        // If no direct path, construct it from available data
                        if (!filePath && file.temp_id && file.original_name) {
                            filePath = `temp/message-attachments/${sessionId}/${file.temp_id}_${file.original_name}`;
                        } else if (!filePath && file.id && file.original_name) {
                            filePath = `temp/message-attachments/${sessionId}/${file.id}_${file.original_name}`;
                        } else if (!filePath && file.filename) {
                            filePath = `temp/message-attachments/${sessionId}/${file.filename}`;
                        }
                        
                        console.log('Resolved file path:', filePath);
                        return filePath;
                    }).filter(path => path); // Remove any undefined/null paths
                    
                    // Add new files to our array (avoid duplicates)
                    newFilePaths.forEach(filePath => {
                        if (!replyUploadedFiles.includes(filePath)) {
                            replyUploadedFiles.push(filePath);
                        }
                    });
                    
                    updateTempFilesInput();
                    console.log('✅ Files added via files-uploaded:', newFilePaths);
                    console.log('📋 Current file list:', replyUploadedFiles);
                } else {
                    console.log('❌ Event not for reply-attachments uploader', {
                        component: e.detail.component,
                        uploaderId: e.detail.uploaderId,
                        id: e.detail.id
                    });
                }
            });

            // SECONDARY EVENT LISTENERS (backup methods)
            window.addEventListener('file-uploaded', function(e) {
                console.log('📁 file-uploaded event:', e.detail);
                handleSingleFileUpload(e.detail);
            });

            window.addEventListener('fileUploaded', function(e) {
                console.log('📁 fileUploaded event:', e.detail);
                handleSingleFileUpload(e.detail);
            });

            // FILE DELETION EVENTS
            window.addEventListener('file-deleted', function(e) {
                console.log('🗑️ file-deleted event:', e.detail);
                handleFileHapus(e.detail);
            });

            window.addEventListener('fileHapusd', function(e) {
                console.log('🗑️ fileHapusd event:', e.detail);
                handleFileHapus(e.detail);
            });

            // Helper functions
            function handleSingleFileUpload(detail) {
                if (detail && detail.uploaderId === 'reply-attachments') {
                    const file = detail.file || detail;
                    const filePath = file.path || file.file_path || file.filePath;
                    if (filePath && !replyUploadedFiles.includes(filePath)) {
                        replyUploadedFiles.push(filePath);
                        updateTempFilesInput();
                        console.log('✅ Single file added:', filePath);
                    }
                }
            }

            function handleFileHapus(detail) {
                if (detail && detail.uploaderId === 'reply-attachments') {
                    const file = detail.file || detail;
                    const filePath = file.path || file.file_path || file.filePath;
                    if (filePath) {
                        replyUploadedFiles = replyUploadedFiles.filter(path => path !== filePath);
                        updateTempFilesInput();
                        console.log('🗑️ File removed:', filePath);
                    }
                }
            }

            function updateTempFilesInput() {
                const tempFilesInput = document.getElementById('reply_temp_files');
                if (tempFilesInput) {
                    tempFilesInput.value = JSON.stringify(replyUploadedFiles);
                    console.log('📝 Updated temp_files input:', tempFilesInput.value);
                }
            }

            // MANUAL FILE EXTRACTION (fallback method)
            function extractFilesFromUploader() {
                let extractedFiles = [];
                
                // Check universal uploader instances
                if (window.universalUploaderInstances && window.universalUploaderInstances['reply-attachments']) {
                    const instance = window.universalUploaderInstances['reply-attachments'];
                    if (instance.uploadedFiles && Array.isArray(instance.uploadedFiles)) {
                        extractedFiles = instance.uploadedFiles.map(file => 
                            file.path || file.file_path || file.filePath
                        ).filter(path => path);
                    }
                }
                
                // Check Vue instances
                const uploaderElement = document.getElementById('reply-attachments');
                if (uploaderElement && uploaderElement.__vue__) {
                    const vueInstance = uploaderElement.__vue__;
                    if (vueInstance.uploadedFiles && Array.isArray(vueInstance.uploadedFiles)) {
                        const vueFiles = vueInstance.uploadedFiles.map(file => 
                            file.path || file.file_path || file.filePath
                        ).filter(path => path);
                        extractedFiles = [...extractedFiles, ...vueFiles];
                    }
                }
                
                // Remove duplicates
                extractedFiles = [...new Set(extractedFiles)].filter(path => path && path.trim() !== '');
                
                console.log('🔍 Manual extraction found:', extractedFiles);
                return extractedFiles;
            }

            // Form submission handling
            document.getElementById('reply-form')?.addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('send-reply-btn');
                const messageTextarea = document.getElementById('reply-message');
                const message = messageTextarea?.value.trim() || '';
                
                console.log('🚀 Form submitting...');
                console.log('📋 Current replyUploadedFiles:', replyUploadedFiles);
                
                if (message.length < 10) {
                    e.preventDefault();
                    alert('Please write at least 10 characters in your reply.');
                    messageTextarea?.focus();
                    return;
                }

                // MANUAL EXTRACTION as fallback
                if (replyUploadedFiles.length === 0) {
                    console.log('⚠️ No files in array, attempting manual extraction...');
                    const extractedFiles = extractFilesFromUploader();
                    if (extractedFiles.length > 0) {
                        replyUploadedFiles = extractedFiles;
                        console.log('✅ Manual extraction successful:', replyUploadedFiles);
                    }
                }

                // Final update of temp_files input
                updateTempFilesInput();
                
                const finalValue = document.getElementById('reply_temp_files').value;
                console.log('📤 Final temp_files value being submitted:', finalValue);
                
                // Show user feedback if files are attached
                if (replyUploadedFiles.length > 0) {
                    console.log(`🎉 Kirimting with ${replyUploadedFiles.length} file(s)`);
                } else {
                    console.log('⚠️ No files detected - submitting without attachments');
                }

                // Disable submit button
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending...
                    `;
                }
            });
        });

        // Character count listener
        document.getElementById('reply-message')?.addEventListener('input', updateCharCount);

        // Handle flash messages
        @if(session('success'))
            setTimeout(() => {
                alert('{{ session('success') }}');
            }, 100);
        @endif

        @if(session('error'))
            setTimeout(() => {
                alert('{{ session('error') }}');
            }, 100);
        @endif
    </script>
</x-layouts.client>