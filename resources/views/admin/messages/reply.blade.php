{{-- resources/views/admin/messages/reply.blade.php --}}
<x-layouts.admin 
    title="Reply to Message" 
    :unreadMessages="0"
    :pendingApprovals="0"
>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('admin.messages.index') }}" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400">
                                Messages
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('admin.messages.show', $originalMessage) }}" class="ml-1 text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400">
                                    Message Details
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-gray-500 dark:text-gray-400">Reply</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Reply to Message</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Replying to: {{ $originalMessage->subject }}
                </p>
            </div>

            <div class="mt-4 sm:mt-0 flex gap-3">
                <a href="{{ route('admin.messages.show', $originalMessage) }}" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Message
                </a>
            </div>
        </div>

        <!-- Original Message Context -->
        <x-admin.card>
            <x-slot name="title">Original Message</x-slot>
            
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                {{ substr($originalMessage->name, 0, 2) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $originalMessage->name }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $originalMessage->email }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $originalMessage->created_at->format('M j, Y \a\t g:i A') }}
                                </p>
                                <div class="flex items-center mt-1 space-x-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        {{ $originalMessage->priority === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                           ($originalMessage->priority === 'high' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : 
                                           'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') }}">
                                        {{ ucfirst($originalMessage->priority ?? 'normal') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">
                            {{ $originalMessage->subject }}
                        </h4>
                        
                        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                            {{ Str::limit($originalMessage->message, 300) }}
                            @if(strlen($originalMessage->message) > 300)
                                <button onclick="toggleFullMessage()" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 ml-2">
                                    Read more...
                                </button>
                            @endif
                        </div>
                        
                        @if($originalMessage->attachments && $originalMessage->attachments->count() > 0)
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    Original attachments ({{ $originalMessage->attachments->count() }}):
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($originalMessage->attachments as $attachment)
                                        <span class="inline-flex items-center px-2 py-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-xs text-gray-700 dark:text-gray-300">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            {{ Str::limit($attachment->file_name, 15) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </x-admin.card>

        <!-- Reply Form -->
        <form action="{{ route('admin.messages.reply', $originalMessage) }}" method="POST" enctype="multipart/form-data" x-data="replyForm()">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Reply Form -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Reply Content -->
                    <x-admin.card>
                        <x-slot name="title">Your Reply</x-slot>
                        
                        <div class="space-y-4">
                            <!-- Subject (auto-filled, editable) -->
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="subject" id="subject" required
                                       value="{{ old('subject', 'Re: ' . $originalMessage->subject) }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                       placeholder="Reply subject">
                                @error('subject')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Reply Message -->
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Your Reply <span class="text-red-500">*</span>
                                </label>
                                <textarea name="message" id="message" rows="12" required
                                          x-model="replyMessage"
                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                          placeholder="Type your reply here...">{{ old('message') }}</textarea>
                                <div class="mt-2 flex items-center justify-between">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Use a professional and helpful tone in your response.
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-show="replyMessage" x-text="`${replyMessage.length} characters`"></p>
                                </div>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Quick Reply Templates -->
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    Quick Reply Templates
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <button type="button" onclick="useReplyTemplate('acknowledgment')" 
                                        class="text-left px-3 py-2 text-xs border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <div class="font-medium text-gray-900 dark:text-white">Acknowledgment</div>
                                        <div class="text-gray-500 dark:text-gray-400">Thank you for contacting us...</div>
                                    </button>
                                    
                                    <button type="button" onclick="useReplyTemplate('request_info')" 
                                        class="text-left px-3 py-2 text-xs border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <div class="font-medium text-gray-900 dark:text-white">Request Information</div>
                                        <div class="text-gray-500 dark:text-gray-400">Could you please provide...</div>
                                    </button>
                                    
                                    <button type="button" onclick="useReplyTemplate('resolution')" 
                                        class="text-left px-3 py-2 text-xs border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <div class="font-medium text-gray-900 dark:text-white">Issue Resolution</div>
                                        <div class="text-gray-500 dark:text-gray-400">We have resolved your issue...</div>
                                    </button>
                                    
                                    <button type="button" onclick="useReplyTemplate('follow_up')" 
                                        class="text-left px-3 py-2 text-xs border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <div class="font-medium text-gray-900 dark:text-white">Follow Up</div>
                                        <div class="text-gray-500 dark:text-gray-400">Following up on your request...</div>
                                    </button>
                                </div>
                            </div>

                            <!-- Attachments -->
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <label for="attachments" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Attachments (optional)
                                </label>
                                <input type="file" name="attachments[]" id="attachments" multiple
                                       class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/20 dark:file:text-blue-300">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Max size: 10MB per file. Allowed types: PDF, DOC, DOCX, JPG, PNG, ZIP.
                                </p>
                                @error('attachments')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                @error('attachments.*')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </x-admin.card>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button type="button" onclick="saveDraft()" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Save Draft
                            </button>
                            
                            <button type="button" onclick="previewReply()" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Preview
                            </button>
                        </div>
                        
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.messages.show', $originalMessage) }}" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                Cancel
                            </a>
                            
                            <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Send Reply
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Reply Settings -->
                    <x-admin.card>
                        <x-slot name="title">Reply Settings</x-slot>
                        
                        <div class="space-y-4">
                            <!-- Priority for Reply -->
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Reply Priority
                                </label>
                                <select name="priority" id="priority" 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Mark Original as Read -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="mark_original_read" value="1" 
                                           {{ !$originalMessage->is_read ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Mark original message as read</span>
                                </label>
                            </div>

                            <!-- Email Notification -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="send_email_notification" value="1" checked
                                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Send email notification to client</span>
                                </label>
                            </div>

                            <!-- Internal Note -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="add_internal_note" value="1"
                                           x-model="addInternalNote"
                                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Add internal note</span>
                                </label>
                            </div>

                            <!-- Internal Note Text -->
                            <div x-show="addInternalNote" x-transition>
                                <textarea name="internal_note" rows="3" 
                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                          placeholder="Internal note (not visible to client)...">{{ old('internal_note') }}</textarea>
                            </div>

                            <!-- Auto-Close Related Messages -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="auto_close_thread" value="1"
                                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Mark conversation as resolved</span>
                                </label>
                            </div>
                        </div>
                    </x-admin.card>

                    <!-- Client Information -->
                    <x-admin.card>
                        <x-slot name="title">Client Information</x-slot>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Name:</span>
                                <span class="font-medium">{{ $originalMessage->name }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Email:</span>
                                <span class="font-medium">{{ $originalMessage->email }}</span>
                            </div>
                            
                            @if($originalMessage->user)
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Client Since:</span>
                                    <span class="font-medium">
                                        {{ $originalMessage->user->created_at->format('M Y') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Total Messages:</span>
                                    <span class="font-medium">{{ $originalMessage->user->messages()->count() }}</span>
                                </div>

                                @if($originalMessage->user->projects()->count() > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Active Projects:</span>
                                        <span class="font-medium">{{ $originalMessage->user->projects()->where('status', '!=', 'completed')->count() }}</span>
                                    </div>
                                @endif

                                <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                    <a href="{{ route('admin.users.show', $originalMessage->user) }}" 
                                       class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                        View Client Profile →
                                    </a>
                                </div>
                            @endif
                        </div>
                    </x-admin.card>

                    <!-- Message Thread -->
                    @if($conversationHistory && $conversationHistory->count() > 0)
                        <x-admin.card>
                            <x-slot name="title">Message Thread</x-slot>
                            
                            <div class="space-y-3 text-sm">
                                <p class="text-gray-600 dark:text-gray-400">
                                    This conversation has {{ $conversationHistory->count() + 1 }} messages.
                                </p>
                                
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    @foreach($conversationHistory->take(5) as $msg)
                                        <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="font-medium text-xs {{ $msg->type === 'admin_to_client' ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400' }}">
                                                    {{ $msg->type === 'admin_to_client' ? 'Admin' : 'Client' }}
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $msg->created_at->format('M j') }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-700 dark:text-gray-300">
                                                {{ Str::limit($msg->message, 100) }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @if($conversationHistory->count() > 5)
                                    <div class="text-center pt-2">
                                        <a href="{{ route('admin.messages.show', $originalMessage) }}" 
                                           class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                            View full conversation ({{ $conversationHistory->count() }} more)
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </x-admin.card>
                    @endif

                    <!-- Related Information -->
                    @if($originalMessage->project)
                        <x-admin.card>
                            <x-slot name="title">Related Project</x-slot>
                            
                            <div class="space-y-2 text-sm">
                                <div>
                                    <a href="{{ route('admin.projects.show', $originalMessage->project) }}" 
                                       class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                        {{ $originalMessage->project->title }}
                                    </a>
                                </div>
                                <div class="text-gray-500 dark:text-gray-400">
                                    Status: {{ ucfirst($originalMessage->project->status) }}
                                </div>
                                @if($originalMessage->project->end_date)
                                    <div class="text-gray-500 dark:text-gray-400">
                                        Due: {{ $originalMessage->project->end_date->format('M j, Y') }}
                                    </div>
                                @endif
                            </div>
                        </x-admin.card>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Preview Modal -->
    <div id="preview-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 50;">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Reply Preview</h3>
                    <button onclick="hidePreviewModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <div class="border-b border-gray-200 dark:border-gray-600 pb-4 mb-4">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white" id="preview-subject"></h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            From: Admin Team &lt;admin@example.com&gt;<br>
                            To: {{ $originalMessage->email }}<br>
                            Priority: <span id="preview-priority"></span>
                        </p>
                    </div>
                    
                    <div class="prose dark:prose-invert max-w-none">
                        <div id="preview-message" class="whitespace-pre-wrap text-gray-900 dark:text-white"></div>
                    </div>
                    
                    <div id="preview-attachments" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 hidden">
                        <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Attachments:</h5>
                        <div id="preview-attachments-list"></div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="hidePreviewModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        Close Preview
                    </button>
                    <button onclick="sendFromPreview()" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                        Send Reply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function replyForm() {
            return {
                replyMessage: @json(old('message', '')),
                addInternalNote: @json(old('add_internal_note', false))
            }
        }

        // Reply Templates
        const replyTemplates = {
            acknowledgment: {
                subject: 'Re: {{ $originalMessage->subject }}',
                message: 'Dear {{ $originalMessage->name }},\n\nThank you for contacting us. We have received your message and will review it carefully.\n\nWe will get back to you within 24-48 hours with a detailed response.\n\nIf you have any urgent questions in the meantime, please don\'t hesitate to reach out.\n\nBest regards,\nThe Support Team'
            },
            request_info: {
                subject: 'Re: {{ $originalMessage->subject }} - Additional Information Needed',
                message: 'Dear {{ $originalMessage->name }},\n\nThank you for your message. To better assist you, could you please provide the following additional information:\n\n• [Information needed 1]\n• [Information needed 2]\n• [Information needed 3]\n\nOnce we have this information, we\'ll be able to provide you with a comprehensive response.\n\nBest regards,\nThe Support Team'
            },
            resolution: {
                subject: 'Re: {{ $originalMessage->subject }} - Issue Resolved',
                message: 'Dear {{ $originalMessage->name }},\n\nWe are pleased to inform you that we have resolved the issue you reported.\n\nSolution implemented:\n[Describe the solution here]\n\nPlease let us know if you experience any further issues or if you have any questions about the resolution.\n\nBest regards,\nThe Support Team'
            },
            follow_up: {
                subject: 'Re: {{ $originalMessage->subject }} - Follow Up',
                message: 'Dear {{ $originalMessage->name }},\n\nI wanted to follow up on your recent message to ensure that your issue has been fully resolved and that you\'re satisfied with our response.\n\nIf you have any additional questions or concerns, please don\'t hesitate to reach out.\n\nWe value your business and want to ensure you have the best possible experience with our services.\n\nBest regards,\nThe Support Team'
            }
        };

        function useReplyTemplate(templateName) {
            const template = replyTemplates[templateName];
            if (template) {
                if (confirm('This will replace your current message. Continue?')) {
                    document.getElementById('subject').value = template.subject;
                    document.getElementById('message').value = template.message;
                    
                    // Update Alpine.js model
                    const form = document.querySelector('[x-data]').__x.$data;
                    form.replyMessage = template.message;
                }
            }
        }

        function toggleFullMessage() {
            // Toggle between truncated and full message view
            const messageDiv = event.target.previousElementSibling;
            const fullMessage = @json($originalMessage->message);
            
            if (messageDiv.textContent.includes('...')) {
                messageDiv.textContent = fullMessage;
                event.target.textContent = 'Show less';
            } else {
                messageDiv.textContent = fullMessage.substring(0, 300) + '...';
                event.target.textContent = 'Read more...';
            }
        }

        function saveDraft() {
            const formData = {
                originalMessageId: {{ $originalMessage->id }},
                subject: document.getElementById('subject').value,
                message: document.getElementById('message').value,
                priority: document.getElementById('priority').value,
                timestamp: new Date().toISOString()
            };

            localStorage.setItem('admin_reply_draft_{{ $originalMessage->id }}', JSON.stringify(formData));
            showNotification('Draft saved successfully!', 'success');
        }

        function loadDraft() {
            const draft = localStorage.getItem('admin_reply_draft_{{ $originalMessage->id }}');
            if (draft) {
                const data = JSON.parse(draft);
                
                // Check if draft is recent (within 24 hours)
                const draftTime = new Date(data.timestamp);
                const now = new Date();
                const hoursDiff = (now - draftTime) / (1000 * 60 * 60);
                
                if (hoursDiff < 24 && confirm('Load saved draft from ' + draftTime.toLocaleString() + '?')) {
                    document.getElementById('subject').value = data.subject || '';
                    document.getElementById('message').value = data.message || '';
                    document.getElementById('priority').value = data.priority || 'normal';
                    
                    // Update Alpine.js model
                    const form = document.querySelector('[x-data]').__x.$data;
                    form.replyMessage = data.message || '';
                    
                    showNotification('Draft loaded successfully!', 'success');
                }
            }
        }

        function previewReply() {
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;
            const priority = document.getElementById('priority').value;
            
            if (!subject || !message) {
                alert('Please fill in subject and message before previewing.');
                return;
            }

            // Update preview content
            document.getElementById('preview-subject').textContent = subject;
            document.getElementById('preview-message').textContent = message;
            document.getElementById('preview-priority').textContent = priority.charAt(0).toUpperCase() + priority.slice(1);
            
            // Handle attachments preview
            const attachmentsField = document.getElementById('attachments');
            if (attachmentsField.files.length > 0) {
                document.getElementById('preview-attachments').classList.remove('hidden');
                const attachmentsList = document.getElementById('preview-attachments-list');
                attachmentsList.innerHTML = '';
                
                Array.from(attachmentsField.files).forEach(file => {
                    const fileDiv = document.createElement('div');
                    fileDiv.className = 'text-sm text-gray-600 dark:text-gray-400';
                    fileDiv.textContent = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                    attachmentsList.appendChild(fileDiv);
                });
            } else {
                document.getElementById('preview-attachments').classList.add('hidden');
            }
            
            document.getElementById('preview-modal').classList.remove('hidden');
        }

        function hidePreviewModal() {
            document.getElementById('preview-modal').classList.add('hidden');
        }

        function sendFromPreview() {
            hidePreviewModal();
            document.querySelector('form').submit();
        }

        // Notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transition-all duration-300 transform ${
                type === 'success' ? 'bg-green-100 border-green-400 text-green-700' :
                type === 'error' ? 'bg-red-100 border-red-400 text-red-700' :
                'bg-blue-100 border-blue-400 text-blue-700'
            } border`;
            
            notification.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        ${type === 'success' ? 
                            '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>' :
                            '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
                        }
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="inline-flex rounded-md p-1.5 hover:bg-gray-100">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }

        // Auto-save draft every 60 seconds
        setInterval(saveDraft, 60000);

        // Clear draft after successful send
        function clearDraft() {
            localStorage.removeItem('admin_reply_draft_{{ $originalMessage->id }}');
        }

        // Character counter for message
        function updateCharacterCount() {
            const messageField = document.getElementById('message');
            const charCount = messageField.value.length;
            
            // You can add character count display here if needed
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadDraft();
            
            // Add character counter to message field
            const messageField = document.getElementById('message');
            if (messageField) {
                messageField.addEventListener('input', updateCharacterCount);
            }
            
            // Handle success/error messages from Laravel
            @if(session('success'))
                showNotification('{{ session('success') }}', 'success');
                clearDraft(); // Clear draft after successful send
            @endif

            @if(session('error'))
                showNotification('{{ session('error') }}', 'error');
            @endif

            // Focus on message field for better UX
            setTimeout(() => {
                document.getElementById('message').focus();
            }, 100);
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + Enter to send
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                document.querySelector('form').submit();
            }
            
            // Ctrl/Cmd + S to save draft
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveDraft();
            }
            
            // Ctrl/Cmd + P to preview
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                previewReply();
            }
        });

        // Warn about unsaved changes
        let formChanged = false;
        document.querySelectorAll('input, textarea, select').forEach(field => {
            field.addEventListener('change', () => {
                formChanged = true;
            });
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
                return 'You have unsaved changes. Are you sure you want to leave?';
            }
        });

        // Clear form changed flag on submit
        document.querySelector('form').addEventListener('submit', function() {
            formChanged = false;
        });
    </script>

</x-layouts.admin>