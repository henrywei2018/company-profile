<!-- resources/views/admin/messages/show.blade.php -->
<x-layouts.admin title="Message Details" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header with better spacing -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-2">
        <div class="mb-2 lg:mb-0">
            <x-admin.breadcrumb :items="[
                'Messages' => route('admin.messages.index'),
                'Message Details' => '#'
            ]" />
        </div>
        
        <div class="flex flex-wrap gap-3">
            <form action="{{ route('admin.messages.toggle-read', $message) }}" method="POST" class="inline">
                @csrf
                <x-admin.button
                    type="submit"
                    color="{{ $message->is_read ? 'warning' : 'success' }}"
                    size="sm"
                    class="shadow-sm"
                >
                <div class="flex items-center justify-center">
                    @if($message->is_read)
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Mark as Unread
                    @else
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Mark as Read
                    @endif
                </div>
                </x-admin.button>
            </form>
            
            <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this message?')">
                @csrf
                @method('DELETE')
                <x-admin.button
                    type="submit"
                    color="danger"
                    size="sm"
                    class="shadow-sm"
                >
                <div class="flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Message
                </div>
                </x-admin.button>
            </form>
        </div>
    </div>
    
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-3">
        <!-- Main Content - Enhanced with better spacing -->
        <div class="xl:col-span-3 space-y-3">
            <!-- Message Content Card with elegant design -->
            <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-200 dark:border-neutral-700 overflow-hidden">
                <!-- Message Header with gradient background -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-neutral-800 dark:to-neutral-700 px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <!-- Subject with better typography -->
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 leading-tight">
                                {{ $message->subject ?: 'Request for construction consultation' }}
                            </h1>
                            
                            <!-- Sender info with icons and better spacing -->
                            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600 dark:text-neutral-400">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <span class="font-medium">{{ $message->name ?: 'Unknown Sender' }}</span>
                                </div>
                                
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <span class="font-medium">{{ $message->email ?: 'No email' }}</span>
                                </div>
                                
                                @if($message->phone)
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <span class="font-medium">{{ $message->phone }}</span>
                                </div>
                                @endif
                                
                                @if($message->company)
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <span class="font-medium">{{ $message->company }}</span>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Timestamp with better styling -->
                            <div class="flex items-center text-sm text-gray-500 dark:text-neutral-500 mr-3 ml-2 mt-2">
                                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium">{{ $message->created_at->format('l, F j, Y \a\t g:i A') }}</span>
                            </div>
                        </div>
                        
                        <!-- Status badge with better positioning -->
                        <div class="ml-6 flex-shrink-0">
                            @if($message->is_read)
                                <x-admin.badge type="success" dot="true" class="text-sm px-4 py-3">Read</x-admin.badge>
                            @else
                                <x-admin.badge type="warning" dot="true" class="text-sm px-4 py-3">Unread</x-admin.badge>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Message Content with elegant typography -->
                <div class="px-4 py-3">
                    <div class="prose prose-lg max-w-none dark:prose-invert prose-gray dark:prose-neutral">
                        <div class="text-gray-700 dark:text-neutral-300 font-small">
                            {{ $message->message ?: 'Hello, I am interested in getting a consultation for an office building construction project in Jakarta. We are planning to start the project in the next 6 months and would like to discuss the possibilities with your team. Please let me know when we can schedule a meeting.' }}
                        </div>
                    </div>
                </div>
                
                <!-- Attachments Section with enhanced design -->
                @if($message->attachments && $message->attachments->count() > 0)
                    <div class="px-4 py-3 bg-gray-50 dark:bg-neutral-800/50 border-t border-gray-200 dark:border-neutral-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                            </div>
                            Attachments ({{ $message->attachments->count() }})
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($message->attachments as $attachment)
                                @php
                                    $extension = pathinfo($attachment->file_name, PATHINFO_EXTENSION);
                                    $iconClass = match(strtolower($extension)) {
                                        'pdf' => 'text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30',
                                        'doc', 'docx' => 'text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30',
                                        'xls', 'xlsx' => 'text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/30',
                                        'jpg', 'jpeg', 'png', 'gif', 'webp' => 'text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/30',
                                        'zip', 'rar', '7z' => 'text-orange-600 dark:text-orange-400 bg-orange-100 dark:bg-orange-900/30',
                                        default => 'text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700'
                                    };
                                @endphp
                                
                                <div class="flex items-center p-5 border border-gray-200 dark:border-neutral-700 rounded-xl bg-white dark:bg-neutral-800 hover:shadow-md dark:hover:shadow-neutral-900/20 transition-all duration-200 group">
                                    <div class="flex-shrink-0 mr-4">
                                        <div class="w-12 h-12 rounded-xl {{ $iconClass }} flex items-center justify-center">
                                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            {{ $attachment->file_name }}
                                        </p>
                                        <div class="flex items-center space-x-3 text-xs text-gray-500 dark:text-neutral-400 mt-1">
                                            <span class="font-medium">{{ $attachment->file_size_formatted }}</span>
                                            <span class="uppercase font-bold">{{ $extension }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 ml-4">
                                        <a href="{{ route('admin.messages.attachments.download', ['message' => $message->id, 'attachmentId' => $attachment->id]) }}" 
                                           class="inline-flex items-center p-3 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-all duration-200"
                                           title="Download {{ $attachment->file_name }}">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Reply Form Section with enhanced styling and functional file upload -->
            @if(in_array($message->type, ['client_to_admin', 'contact_form']))
                <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-200 dark:border-neutral-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                            </div>
                            Send Reply
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-neutral-400 mt-2">Respond to this message</p>
                    </div>
                    
                    <form action="{{ route('admin.messages.reply', $message) }}" method="POST" enctype="multipart/form-data" class="p-4" id="reply-form">
                        @csrf
                        
                        <div class="space-y-3">
                            <x-admin.input
                                name="subject"
                                label="Subject"
                                value="RE: {{ $message->subject ?: 'Request for construction consultation' }}"
                                required
                                placeholder="Enter reply subject"
                                class="text-base"
                            />
                            
                            <div class="space-y-3">
                                <label for="message" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300">
                                    Message <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    name="message" 
                                    id="message" 
                                    rows="8" 
                                    required
                                    placeholder="Enter your reply message..."
                                    class="block w-full px-4 py-3 text-base border border-gray-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:text-white placeholder-gray-400 dark:placeholder-neutral-500 transition-all duration-200"
                                ></textarea>
                            </div>
                            
                            <!-- Enhanced file upload area with working JavaScript -->
                            <div class="space-y-3" x-data="fileUploader()">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-neutral-300">
                                    Attachments (Optional)
                                </label>
                                <div 
                                    class="border-2 border-dashed border-gray-300 dark:border-neutral-700 rounded-xl p-8 text-center hover:border-blue-400 dark:hover:border-blue-600 transition-colors bg-gray-50 dark:bg-neutral-800/50"
                                    @click="$refs.fileInput.click()"
                                    @dragover.prevent="isDragging = true"
                                    @dragleave.prevent="isDragging = false"
                                    @drop.prevent="handleDrop($event)"
                                    :class="{ 'border-blue-400 bg-blue-50 dark:border-blue-600 dark:bg-blue-900/30': isDragging }"
                                >
                                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                    </div>
                                    <p class="text-base font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        <span class="text-blue-600 dark:text-blue-400 cursor-pointer hover:underline">Click to upload</span> or drag and drop
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-neutral-500">
                                        PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP, RAR up to 2MB each (Max 5 files)
                                    </p>
                                    <input 
                                        type="file" 
                                        name="attachments[]" 
                                        multiple 
                                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar" 
                                        class="hidden"
                                        x-ref="fileInput"
                                        @change="handleFileSelect($event)"
                                    >
                                </div>
                                
                                <!-- File preview area -->
                                <div x-show="files.length > 0" class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-neutral-300 mb-3">Selected Files:</h4>
                                    <div class="space-y-2">
                                        <template x-for="(file, index) in files" :key="index">
                                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-neutral-800/50 rounded-lg border border-gray-200 dark:border-neutral-700">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="file.name"></p>
                                                        <p class="text-xs text-gray-500 dark:text-neutral-500" x-text="formatFileSize(file.size)"></p>
                                                    </div>
                                                </div>
                                                <button 
                                                    type="button" 
                                                    @click="removeFile(index)"
                                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                                >
                                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                
                                <!-- Error messages -->
                                <div x-show="errors.length > 0" class="mt-2">
                                    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg p-3">
                                        <ul class="text-sm text-red-600 dark:text-red-400 space-y-1">
                                            <template x-for="error in errors" :key="error">
                                                <li x-text="error"></li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end px-1 py-2 border-t border-gray-200 dark:border-neutral-700">
                            <x-admin.button
                                type="submit"
                                color="primary"
                                size="sm"
                                class="text-base font-semibold shadow-lg"
                            >
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Send Reply
                            </div>
                            </x-admin.button>
                        </div>
                    </form>
                </div>
            @endif
            
            <!-- Conversation History with timeline design -->
            @if(isset($relatedMessages) && $relatedMessages->count() > 0)
                <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-200 dark:border-neutral-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-neutral-800 dark:to-neutral-700 px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            Conversation History
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-neutral-400 mt-2">Previous messages in this thread</p>
                    </div>
                    
                    <div class="p-4">
                        <div class="space-y-3">
                            @foreach($relatedMessages as $relatedMessage)
                                <div class="flex space-x-3">
                                    <!-- Timeline indicator -->
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $relatedMessage->type === 'admin_to_client' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-600 dark:bg-neutral-700 dark:text-neutral-400' }}">
                                            @if($relatedMessage->type === 'admin_to_client')
                                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                            @else
                                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            @endif
                                        </div>
                                        @if(!$loop->last)
                                            <div class="w-0.5 h-16 bg-gray-200 dark:bg-neutral-700 mt-4"></div>
                                        @endif
                                    </div>
                                    
                                    <!-- Message Content -->
                                    <div class="flex-1 min-w-0 pb-8">
                                        <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6 border border-gray-200 dark:border-neutral-700">
                                            <div class="flex items-center justify-between mb-4">
                                                <h4 class="text-base font-semibold {{ $relatedMessage->type === 'admin_to_client' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-white' }}">
                                                    {{ $relatedMessage->type === 'admin_to_client' ? 'Admin' : $relatedMessage->name }}
                                                </h4>
                                                <span class="text-sm text-gray-500 dark:text-neutral-500 font-medium">
                                                    {{ $relatedMessage->created_at->format('M d, Y H:i') }}
                                                </span>
                                            </div>
                                            
                                            @if($relatedMessage->subject !== $message->subject)
                                                <p class="text-sm font-semibold text-gray-800 dark:text-neutral-200 mb-3">
                                                    {{ $relatedMessage->subject }}
                                                </p>
                                            @endif
                                            
                                            <div class="text-sm text-gray-700 dark:text-neutral-300">
                                                {{ $relatedMessage->message }}
                                            </div>
                                            
                                            @if($relatedMessage->attachments && $relatedMessage->attachments->count() > 0)
                                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-neutral-600">
                                                    <p class="text-xs text-gray-500 dark:text-neutral-500 mb-3 font-medium">
                                                        Attachments ({{ $relatedMessage->attachments->count() }}):
                                                    </p>
                                                    <div class="flex flex-wrap gap-3">
                                                        @foreach($relatedMessage->attachments as $attachment)
                                                            <a href="{{ route('admin.messages.attachments.download', ['message' => $relatedMessage->id, 'attachmentId' => $attachment->id]) }}" 
                                                               class="inline-flex items-center px-4 py-3 bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-lg text-xs font-medium text-gray-700 dark:text-neutral-300 hover:bg-gray-50 dark:hover:bg-neutral-600 transition-colors">
                                                                <svg class="w-3 h-3 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                                </svg>
                                                                {{ \Illuminate\Support\Str::limit($attachment->file_name, 20) }}
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
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Enhanced Sidebar with better spacing and design -->
        <div class="xl:col-span-1 space-y-3">
            <!-- Message Information Card -->
            <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-200 dark:border-neutral-700 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Message Information</h3>
                </div>
                
                <div class="p-4 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600 dark:text-neutral-400">Status</span>
                        @if($message->is_read)
                            <x-admin.badge type="success" dot="true" class="text-sm">Read</x-admin.badge>
                        @else
                            <x-admin.badge type="warning" dot="true" class="text-sm">Unread</x-admin.badge>
                        @endif
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600 dark:text-neutral-400">Type</span>
                        @if($message->type === 'contact_form')
                            <x-admin.badge type="info" class="text-sm">Contact Form</x-admin.badge>
                        @elseif($message->type === 'client_to_admin')
                            <x-admin.badge type="primary" class="text-sm">Client Message</x-admin.badge>
                        @elseif($message->type === 'admin_to_client')
                            <x-admin.badge type="dark" class="text-sm">Admin Reply</x-admin.badge>
                        @else
                            <x-admin.badge class="text-sm">{{ ucfirst(str_replace('_', ' ', $message->type)) }}</x-admin.badge>
                        @endif
                    </div>
                    
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-medium text-gray-600 dark:text-neutral-400">Received</span>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $message->created_at->format('M d, Y') }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-neutral-500">
                                {{ $message->created_at->format('g:i A') }}
                            </div>
                        </div>
                    </div>
                    
                    @if($message->is_read && $message->read_at)
                        <div class="flex justify-between items-start">
                            <span class="text-sm font-medium text-gray-600 dark:text-neutral-400">Read At</span>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $message->read_at->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-neutral-500">
                                    {{ $message->read_at->format('g:i A') }}
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($message->attachments && $message->attachments->count() > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-600 dark:text-neutral-400">Attachments</span>
                            <x-admin.badge type="info" class="text-sm">{{ $message->attachments->count() }}</x-admin.badge>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Sender Information Card -->
            <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-200 dark:border-neutral-700 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Sender Information</h3>
                </div>
                
                <div class="p-4 space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-neutral-400 block mb-1">Name</label>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $message->name ?: 'Ahmad Fauzi' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-neutral-400 block mb-1">Email</label>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white break-all">
                            {{ $message->email ?: 'ahmad.fauzi@example.com' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-neutral-400 block mb-1">Phone</label>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $message->phone ?: '+62 812 3456 7890' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-neutral-400 block mb-1">Company</label>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $message->company ?: 'PT Sejahtera Mandiri' }}
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Client Profile Card (if registered user) -->
            @if($message->user)
                <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-200 dark:border-neutral-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Client Profile</h3>
                    </div>
                    
                    <div class="p-4">
                        <div class="flex items-center mb-2">
                            <div class="flex-shrink-0">
                                @if($message->user->profile_photo_path)
                                    <img class="h-8 w-8 rounded-full object-cover ring-2 ring-gray-200 dark:ring-neutral-700" 
                                         src="{{ asset('storage/' . $message->user->profile_photo_path) }}" 
                                         alt="{{ $message->user->name }}">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg ring-2 ring-gray-200 dark:ring-neutral-700">
                                        {{ strtoupper(substr($message->user->name ?: 'P', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <h4 class="text-base font-bold text-gray-900 dark:text-white">
                                    {{ $message->user->name ?: 'PT Maju Bersama' }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-neutral-400 font-medium">
                                    Registered Client
                                </p>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-neutral-400">Email</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $message->user->email ?: 'client1@example.com' }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-neutral-400">Phone</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $message->user->phone ?: '+62 21 3456 7890' }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-neutral-400">Member Since</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $message->user->created_at ? $message->user->created_at->format('M Y') : 'May 2025' }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-neutral-400">Status</span>
                                @if($message->user->email_verified_at ?? true)
                                    <x-admin.badge type="success" size="sm">Verified</x-admin.badge>
                                @else
                                    <x-admin.badge type="warning" size="sm">Unverified</x-admin.badge>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-neutral-700">
                            <a href="{{ route('admin.users.show', $message->user) }}" 
                               class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                View Full Profile
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Quick Actions Card -->
            <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-200 dark:border-neutral-700 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Quick Actions</h3>
                </div>
                
                <div class="p-4 space-y-3">
                    @if($message->type === 'contact_form' && $message->email)
                        <a href="mailto:{{ $message->email }}?subject=RE: {{ urlencode($message->subject ?: 'Your Message') }}" 
                           class="inline-flex items-center w-full px-4 py-3 text-sm font-semibold text-gray-700 dark:text-neutral-300 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-neutral-700 dark:to-neutral-600 border border-gray-200 dark:border-neutral-600 rounded-xl hover:from-blue-50 hover:to-blue-100 dark:hover:from-blue-900/20 dark:hover:to-blue-800/20 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-200">
                            <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Reply via Email
                        </a>
                    @endif
                    
                    <a href="{{ route('admin.messages.index', ['search' => $message->email]) }}" 
                       class="inline-flex items-center w-full px-4 py-3 text-sm font-semibold text-gray-700 dark:text-neutral-300 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-neutral-700 dark:to-neutral-600 border border-gray-200 dark:border-neutral-600 rounded-xl hover:from-green-50 hover:to-green-100 dark:hover:from-green-900/20 dark:hover:to-green-800/20 hover:border-green-300 dark:hover:border-green-600 transition-all duration-200">
                        <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search All Messages
                    </a>
                    
                    <form action="{{ route('admin.messages.toggle-read', $message) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center w-full px-4 py-3 text-sm font-semibold text-gray-700 dark:text-neutral-300 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-neutral-700 dark:to-neutral-600 border border-gray-200 dark:border-neutral-600 rounded-xl hover:from-yellow-50 hover:to-yellow-100 dark:hover:from-yellow-900/20 dark:hover:to-yellow-800/20 hover:border-yellow-300 dark:hover:border-yellow-600 transition-all duration-200">
                            @if($message->is_read)
                                <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Mark as Unread
                            @else
                                <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Mark as Read
                            @endif
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="w-full" 
                          onsubmit="return confirm('Are you sure you want to delete this message? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center w-full px-4 py-3 text-sm font-semibold text-red-600 dark:text-red-400 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border border-red-200 dark:border-red-700 rounded-xl hover:from-red-100 hover:to-red-200 dark:hover:from-red-900/30 dark:hover:to-red-800/30 hover:border-red-300 dark:hover:border-red-600 transition-all duration-200">
                            <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>

<script>
    // File uploader component
    function fileUploader() {
        return {
            files: [],
            isDragging: false,
            errors: [],
            maxFiles: 5,
            maxFileSize: 2 * 1024 * 1024, // 2MB
            allowedTypes: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'image/jpeg', 'image/png', 'image/jpg', 'application/zip', 'application/x-rar-compressed'],
            
            handleFileSelect(event) {
                this.addFiles(event.target.files);
            },
            
            handleDrop(event) {
                this.isDragging = false;
                this.addFiles(event.dataTransfer.files);
            },
            
            addFiles(fileList) {
                this.errors = [];
                const newFiles = Array.from(fileList);
                
                // Check total file count
                if (this.files.length + newFiles.length > this.maxFiles) {
                    this.errors.push(`Maximum ${this.maxFiles} files allowed.`);
                    return;
                }
                
                // Validate each file
                newFiles.forEach(file => {
                    // Check file size
                    if (file.size > this.maxFileSize) {
                        this.errors.push(`${file.name} exceeds the maximum file size of 2MB.`);
                        return;
                    }
                    
                    // Check file type
                    if (!this.allowedTypes.includes(file.type)) {
                        // Also check by extension for some types
                        const extension = file.name.split('.').pop().toLowerCase();
                        const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'zip', 'rar'];
                        if (!allowedExtensions.includes(extension)) {
                            this.errors.push(`${file.name} has an unsupported file type.`);
                            return;
                        }
                    }
                    
                    // Add file if validation passes
                    this.files.push(file);
                });
                
                // Update the actual file input
                this.updateFileInput();
            },
            
            removeFile(index) {
                this.files.splice(index, 1);
                this.updateFileInput();
            },
            
            updateFileInput() {
                // Create a new DataTransfer object and add our files
                const dt = new DataTransfer();
                this.files.forEach(file => dt.items.add(file));
                
                // Update the file input
                const fileInput = this.$refs.fileInput;
                fileInput.files = dt.files;
            },
            
            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        }
    }
</script>

@if (function_exists('human_filesize'))
@else
@php
    if (!function_exists('human_filesize')) {
        function human_filesize($bytes, $decimals = 2) {
            $size = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
        }
    }
@endphp
@endif