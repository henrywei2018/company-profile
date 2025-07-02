{{-- resources/views/admin/messages/show.blade.php --}}
<x-layouts.admin title="Message Details" :unreadMessages="0" :pendingApprovals="0">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('admin.messages.index') }}"
                                class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400">
                                Messages
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-gray-500 dark:text-gray-400">Message Details</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $rootMessage->subject }}</h1>
            </div>

            <div class="mt-4 sm:mt-0 flex gap-3">
                <a href="{{ route('admin.messages.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Messages
                </a>

                @if ($canReply)
                    <a href="{{ route('admin.messages.reply', $rootMessage) }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        Reply
                    </a>
                @endif
            </div>
        </div>

        <!-- Message Thread -->
        <div class="grid grid-cols-1 xl:grid-cols-6 gap-6">
            <!-- Main Content - Conversation Thread -->
            <div class="xl:col-span-4 space-y-6">

                <!-- Thread Header -->
                <x-admin.card>
                    <x-slot name="title">
                        <div class="flex items-center justify-between">
                            <span>Conversation ({{ $thread->count() }} messages)</span>
                            <div class="flex items-center space-x-2">
                                @if ($thread->where('is_read', false)->where('type', 'admin_to_client')->count() > 0)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        {{ $thread->where('is_read', false)->where('type', 'admin_to_client')->count() }}
                                        unread
                                    </span>
                                @endif

                                <!-- Thread Priority Badge -->
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $rootMessage->priority === 'urgent'
                                        ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                        : ($rootMessage->priority === 'high'
                                            ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200'
                                            : ($rootMessage->priority === 'normal'
                                                ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                                : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')) }}">
                                    {{ ucfirst($rootMessage->priority ?? 'normal') }}
                                </span>

                                <!-- Thread Type Badge -->
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $rootMessage->type === 'support'
                                        ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                                        : ($rootMessage->type === 'complaint'
                                            ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                            : ($rootMessage->type === 'project_inquiry'
                                                ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                                : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $rootMessage->type)) }}
                                </span>
                            </div>
                        </div>
                    </x-slot>

                    <!-- Threaded Conversation Display -->
                    <div class="space-y-3" id="conversation-thread">
                        @foreach ($thread as $index => $threadMessage)
                            <div class="flex {{ $threadMessage->type === 'admin_to_client' ? 'flex-row-reverse' : '' }} gap-4"
                                data-message-id="{{ $threadMessage->id }}">

                                <!-- Avatar -->
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-10 h-10 rounded-full flex items-center justify-center
                                        {{ $threadMessage->type === 'admin_to_client'
                                            ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400'
                                            : 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                        @if ($threadMessage->type === 'admin_to_client')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                        @endif
                                    </div>
                                </div>

                                <!-- Message Content -->
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3 {{ $threadMessage->type === 'admin_to_client' ? 'border-l-4 border-green-400' : 'border-l-4 border-blue-400' }} break-words overflow-hidden">

                                        <!-- Message Header -->
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                                <span
                                                    class="font-medium text-sm {{ $threadMessage->type === 'admin_to_client' ? 'text-green-700 dark:text-green-400' : 'text-gray-900 dark:text-white' }}">
                                                    {{ $threadMessage->type === 'admin_to_client' ? 'Admin Reply' : $threadMessage->name }}
                                                </span>
                                                @if ($threadMessage->type !== 'admin_to_client')
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-gray-400">{{ $threadMessage->email }}</span>
                                                @endif
                                                @if (!$threadMessage->is_read && $threadMessage->type === 'admin_to_client')
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                        New
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                                    {{ $threadMessage->created_at->format('M j, Y H:i') }}
                                                </span>
                                                <!-- Message Actions Dropdown -->
                                                <div class="relative" x-data="{ open: false }">
                                                    <button @click="open = !open"
                                                        class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path
                                                                d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                    <div x-show="open" @click.away="open = false" x-transition
                                                        class="absolute right-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10 border border-gray-200 dark:border-gray-700">
                                                        <div class="py-1">
                                                            @if ($threadMessage->type !== 'admin_to_client')
                                                                <a href="{{ route('admin.messages.reply', $threadMessage) }}"
                                                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                    Reply to this message
                                                                </a>
                                                            @endif
                                                            <form
                                                                action="{{ route('admin.messages.toggle-read', $threadMessage) }}"
                                                                method="POST" class="block">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                    Mark as
                                                                    {{ $threadMessage->is_read ? 'unread' : 'read' }}
                                                                </button>
                                                            </form>
                                                            <button onclick="copyMessageText({{ $threadMessage->id }})"
                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                Copy message text
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($threadMessage->subject !== $rootMessage->subject)
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                                {{ $threadMessage->subject }}
                                            </p>
                                        @endif

                                        <div class="text-sm text-gray-700 dark:text-gray-300 break-words overflow-wrap-anywhere"
                                            id="message-text-{{ $threadMessage->id }}">
                                            {{ $threadMessage->message }}
                                        </div>

                                        @if ($threadMessage->attachments && $threadMessage->attachments->count() > 0)
                                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 font-medium">
                                                    Attachments ({{ $threadMessage->attachments->count() }}):
                                                </p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach ($threadMessage->attachments as $attachment)
                                                        <a href="{{ route('admin.messages.attachments.download', ['message' => $threadMessage->id, 'attachmentId' => $attachment->id]) }}"
                                                            class="inline-flex items-center px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                                            <svg class="w-3 h-3 mr-2" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                                                </path>
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
                </x-admin.card>

                <!-- Quick Reply Form -->
                @if ($canReply)
                    <x-admin.card>
                        <x-slot name="title">Quick Reply</x-slot>

                        <form action="{{ route('admin.messages.reply', $rootMessage) }}" method="POST"
                            enctype="multipart/form-data" id="admin-reply-form">
                            @csrf
                            <div class="space-y-4">
                                <!-- Subject Field -->
                                <div>
                                    <label for="subject"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Subject <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="subject" id="subject" required
                                        value="{{ old('subject', 'Re: ' . $rootMessage->subject) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                        placeholder="Reply subject">
                                    @error('subject')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Message Content -->
                                <div>
                                    <label for="message"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Your Reply <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="message" id="message" rows="6" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                        placeholder="Type your reply here...">{{ old('message') }}</textarea>
                                    @error('message')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Universal File Uploader -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        Attachments (Optional)
                                    </label>

                                    <x-universal-file-uploader name="files" :multiple="true" :maxFiles="5"
                                        maxFileSize="10MB" :acceptedFileTypes="[
                                            'image/jpeg',
                                            'image/png',
                                            'image/gif',
                                            'image/webp',
                                            'application/pdf',
                                            'application/msword',
                                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                            'application/vnd.ms-excel',
                                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                            'text/plain',
                                            'text/csv',
                                            'application/zip',
                                            'application/x-rar-compressed',
                                        ]"
                                        dropDescription="Drop files here or click to browse"
                                        uploadEndpoint="{{ route('admin.messages.temp-upload') }}"
                                        deleteEndpoint="{{ route('admin.messages.temp-delete') }}" :enableCategories="false"
                                        :enableDescription="false" :enablePublicToggle="false" :autoUpload="true" :uploadOnDrop="true"
                                        :compact="false" theme="default" id="admin-reply-attachments" />

                                    @error('attachments.*')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Hidden input for temp files - CRITICAL -->
                                <input type="hidden" name="temp_files" id="temp_files" value="">

                                <!-- Submit Button -->
                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="clearReplyForm()"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                        Clear
                                    </button>
                                    <button type="submit" id="admin-reply-submit"
                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md disabled:opacity-50">
                                        Send Reply
                                    </button>
                                </div>
                            </div>
                        </form>
                    </x-admin.card>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="xl:col-span-2 space-y-6">
                <!-- Message Actions -->
                <x-admin.card>
                    <x-slot name="title">Actions</x-slot>

                    <div class="space-y-3">
                        @if ($canReply)
                            <a href="{{ route('admin.messages.reply', $message) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 border border-transparent rounded-md text-sm font-medium text-white">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                Reply via Email
                            </a>
                        @endif

                        <a href="{{ route('admin.messages.index', ['search' => $message->email]) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Search All Messages
                        </a>

                        <form action="{{ route('admin.messages.toggle-read', $message) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-600 rounded-md text-sm font-medium text-yellow-700 dark:text-yellow-300 hover:bg-yellow-200 dark:hover:bg-yellow-900/50">
                                @if ($message->is_read)
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Mark as Unread
                                @else
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Mark as Read
                                @endif
                            </button>
                        </form>

                        <!-- Priority Actions -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Update
                                Priority</label>
                            <form action="{{ route('admin.messages.update-priority', $message) }}" method="POST">
                                @csrf
                                <div class="flex space-x-2">
                                    <select name="priority"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white text-sm">
                                        <option value="low" {{ $message->priority === 'low' ? 'selected' : '' }}>
                                            Low</option>
                                        <option value="normal"
                                            {{ $message->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="high" {{ $message->priority === 'high' ? 'selected' : '' }}>
                                            High</option>
                                        <option value="urgent"
                                            {{ $message->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    <button type="submit"
                                        class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                        Update
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Forward Message -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                            <button onclick="showForwardModal()"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-100 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-600 rounded-md text-sm font-medium text-purple-700 dark:text-purple-300 hover:bg-purple-200 dark:hover:bg-purple-900/50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Forward Message
                            </button>
                        </div>

                        <!-- Delete Message -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                            <form action="{{ route('admin.messages.destroy', $message) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this message? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-600 rounded-md text-sm font-medium text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-900/50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                    Delete Message
                                </button>
                            </form>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Message Information -->
                <x-admin.card>
                    <x-slot name="title">Message Information</x-slot>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Message ID:</span>
                            <span class="font-medium">{{ $message->id }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Type:</span>
                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $message->type)) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Priority:</span>
                            <span class="font-medium">{{ ucfirst($message->priority ?? 'normal') }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Status:</span>
                            <span
                                class="font-medium {{ $message->is_read ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $message->is_read ? 'Read' : 'Unread' }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Received:</span>
                            <span class="font-medium">{{ $message->created_at->format('M j, Y H:i') }}</span>
                        </div>

                        @if ($message->updated_at && $message->updated_at != $message->created_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Last Updated:</span>
                                <span class="font-medium">{{ $message->updated_at->format('M j, Y H:i') }}</span>
                            </div>
                        @endif

                        @if ($message->user)
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Client:</span>
                                    <a href="{{ route('admin.users.show', $message->user) }}"
                                        class="font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $message->user->name }}
                                    </a>
                                </div>

                                <div class="flex justify-between mt-2">
                                    <span class="text-gray-500 dark:text-gray-400">Client Projects:</span>
                                    <span class="font-medium">{{ $message->user->projects->count() }}</span>
                                </div>
                            </div>
                        @endif

                        @if ($message->project)
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Related Project:</span>
                                    <a href="{{ route('admin.projects.show', $message->project) }}"
                                        class="font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ Str::limit($message->project->title, 20) }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if ($message->attachments && $message->attachments->count() > 0)
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Attachments:</span>
                                    <span class="font-medium">{{ $message->attachments->count() }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </x-admin.card>

                <!-- Client Information -->
                @if ($clientInfo)
                    <x-admin.card>
                        <x-slot name="title">Client Information</x-slot>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Name:</span>
                                <span class="font-medium">{{ $clientInfo['name'] }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Email:</span>
                                <span class="font-medium">{{ $clientInfo['email'] }}</span>
                            </div>

                            @if (isset($clientInfo['phone']))
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Phone:</span>
                                    <span class="font-medium">{{ $clientInfo['phone'] }}</span>
                                </div>
                            @endif

                            @if (isset($clientInfo['company']))
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Company:</span>
                                    <span class="font-medium">{{ $clientInfo['company'] }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Total Messages:</span>
                                <span class="font-medium">{{ $clientInfo['total_messages'] ?? 0 }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Member Since:</span>
                                <span class="font-medium">
                                    {{ isset($clientInfo['member_since']) ? $clientInfo['member_since']->format('M Y') : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </x-admin.card>
                @endif

                <!-- Recent Activity -->
                @if (isset($recentActivity) && $recentActivity->count() > 0)
                    <x-admin.card>
                        <x-slot name="title">Recent Activity</x-slot>

                        <div class="space-y-3">
                            @foreach ($recentActivity->take(5) as $activity)
                                <div class="text-sm">
                                    <div class="flex items-start space-x-2">
                                        <div class="flex-shrink-0 w-2 h-2 bg-blue-400 rounded-full mt-1.5"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-gray-900 dark:text-white">{{ $activity['description'] }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $activity['created_at']->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-admin.card>
                @endif
            </div>
        </div>
    </div>

    <!-- Forward Message Modal -->
    <div id="forward-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden"
        style="z-index: 50;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Forward Message</h3>

                <form id="forward-form" action="{{ route('admin.messages.forward', $message) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="forward_email"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Forward to Email:
                        </label>
                        <input type="email" id="forward_email" name="email" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Enter email address">
                    </div>

                    <div class="mb-4">
                        <label for="forward_note"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Additional Note (optional):
                        </label>
                        <textarea id="forward_note" name="note" rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Add a note to include with the forwarded message..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideForwardModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                            Forward Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        let adminReplyUploadedFiles = [];

        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Admin messages show view loaded with enhanced uploader support');

            // Initialize all functionality
            initializeExistingFeatures();
            setupUniversalUploaderEvents();
            setupFormHandlers();
            handleSessionMessages();
        });

        // Initialize existing features (character counters, etc.)
        function initializeExistingFeatures() {
            // Character counter for message textarea
            const messageTextarea = document.getElementById('message');
            if (messageTextarea) {
                updateCharCounter(messageTextarea);
                messageTextarea.addEventListener('input', function() {
                    updateCharCounter(this);
                });
            }

            // Auto-focus message area if needed
            if (messageTextarea && !messageTextarea.value.trim()) {
                setTimeout(() => messageTextarea.focus(), 500);
            }
        }

        // Set up Universal Uploader event listeners - ENHANCED VERSION
        function setupUniversalUploaderEvents() {
            console.log('🔧 Setting up enhanced universal uploader events...');

            // PRIMARY EVENT LISTENER - files-uploaded (bulk files)
            window.addEventListener('files-uploaded', function(e) {
                console.log('🎯 Admin files-uploaded event captured:', e.detail);

                // Check if this event is for our admin reply uploader
                if (isEventForAdminReplyUploader(e.detail)) {
                    console.log('✅ Event is for admin reply uploader, processing files...');

                    if (e.detail.files && Array.isArray(e.detail.files)) {
                        // Extract file paths from the uploaded files
                        const newFilePaths = e.detail.files.map(file => {
                            console.log('📎 Processing admin reply file:', file);
                            return extractFilePath(file);
                        }).filter(path => path); // Filter out undefined/null paths

                        if (newFilePaths.length > 0) {
                            // Add to our tracking array
                            adminReplyUploadedFiles = [...adminReplyUploadedFiles, ...newFilePaths];

                            // Update the hidden input
                            updateTempFilesInput();

                            console.log('✅ Admin reply files added:', newFilePaths);
                            console.log('📁 Total admin reply files:', adminReplyUploadedFiles);
                        }
                    }
                }
            });

            // SECONDARY EVENT LISTENER - file-uploaded (single file)
            window.addEventListener('file-uploaded', function(e) {
                console.log('📎 Single file upload event:', e.detail);

                if (isEventForAdminReplyUploader(e.detail)) {
                    const filePath = extractFilePath(e.detail);
                    if (filePath) {
                        adminReplyUploadedFiles.push(filePath);
                        updateTempFilesInput();
                        console.log('✅ Single admin reply file added:', filePath);
                    }
                }
            });

            // FILE DELETION EVENT LISTENER
            window.addEventListener('file-deleted', function(e) {
                console.log('🗑️ File deletion event:', e.detail);

                if (isEventForAdminReplyUploader(e.detail)) {
                    const filePathToRemove = extractFilePath(e.detail);
                    if (filePathToRemove) {
                        adminReplyUploadedFiles = adminReplyUploadedFiles.filter(path => path !== filePathToRemove);
                        updateTempFilesInput();
                        console.log('🗑️ Admin reply file removed:', filePathToRemove);
                        console.log('📁 Remaining admin reply files:', adminReplyUploadedFiles);
                    }
                }
            });
        }

        // Check if the event is for our admin reply uploader
        function isEventForAdminReplyUploader(eventDetail) {
            // Check multiple possible identifiers
            const uploaderIds = ['admin-reply-attachments', 'message-attachments', 'reply-attachments'];

            // Check by uploader ID
            if (eventDetail.uploaderId && uploaderIds.includes(eventDetail.uploaderId)) {
                return true;
            }

            // Check by element ID if present
            if (eventDetail.elementId && uploaderIds.includes(eventDetail.elementId)) {
                return true;
            }

            // Check by target element
            if (eventDetail.target && eventDetail.target.id && uploaderIds.includes(eventDetail.target.id)) {
                return true;
            }

            // Fallback: if we're on the admin messages show page and no specific ID, assume it's ours
            const isAdminMessagesPage = window.location.pathname.includes('/admin/messages/');
            return isAdminMessagesPage;
        }

        // Extract file path from various event formats
        function extractFilePath(eventData) {
            // Handle different data structures
            if (typeof eventData === 'string') {
                return eventData;
            }

            if (eventData && typeof eventData === 'object') {
                // Try different possible properties
                return eventData.path ||
                    eventData.file_path ||
                    eventData.filePath ||
                    eventData.url ||
                    eventData.file?.path ||
                    eventData.file?.file_path ||
                    null;
            }

            return null;
        }

        // Update the hidden temp_files input
        function updateTempFilesInput() {
            const tempFilesInput = document.getElementById('temp_files');
            if (tempFilesInput) {
                const filesJson = JSON.stringify(adminReplyUploadedFiles);
                tempFilesInput.value = filesJson;
                console.log('📝 Updated temp_files input:', filesJson);
            } else {
                console.error('❌ temp_files input not found!');
            }
        }

        // Set up form submission handlers
        function setupFormHandlers() {
            const replyForm = document.getElementById('admin-reply-form');
            if (replyForm) {
                replyForm.addEventListener('submit', function(e) {
                    console.log('📤 Admin reply form submitting...');
                    console.log('📁 Files being submitted:', adminReplyUploadedFiles);
                    console.log('📝 temp_files input value:', document.getElementById('temp_files')?.value);

                    // Optional: Add form validation here
                    const messageText = document.getElementById('message')?.value?.trim();
                    if (!messageText) {
                        e.preventDefault();
                        showNotification('Please enter a reply message', 'error');
                        return false;
                    }

                    // Disable submit button to prevent double submission
                    const submitBtn = document.getElementById('admin-reply-submit');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '⏳ Sending...';

                        // Re-enable after timeout to prevent permanent disable
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }, 15000);
                    }
                });
            } else {
                console.log('❌ Admin reply form not found');
            }
        }

        // Character counter functionality
        function updateCharCounter(textarea) {
            const charCount = textarea.value.length;
            const maxLength = 10000;

            let counterElement = document.getElementById('char-count');
            if (!counterElement) {
                // Create character counter if it doesn't exist
                counterElement = document.createElement('div');
                counterElement.id = 'char-count';
                counterElement.className = 'text-sm text-gray-500 mt-1';
                textarea.parentNode.appendChild(counterElement);
            }

            counterElement.textContent = `${charCount}/${maxLength} characters`;

            if (charCount > maxLength * 0.9) {
                counterElement.className = 'text-sm text-red-500 mt-1';
            } else {
                counterElement.className = 'text-sm text-gray-500 mt-1';
            }
        }

        // Handle Laravel session messages
        function handleSessionMessages() {
            @if (session('success'))
                showNotification('{{ session('success') }}', 'success');
                clearFormAfterSuccess();
            @endif

            @if (session('error'))
                showNotification('{{ session('error') }}', 'error');
            @endif

            @if (session('warning'))
                showNotification('{{ session('warning') }}', 'warning');
            @endif
        }

        // Clear form after successful submission
        function clearFormAfterSuccess() {
            const messageTextarea = document.getElementById('message');
            const subjectInput = document.getElementById('subject');
            const tempFilesInput = document.getElementById('temp_files');

            if (messageTextarea) messageTextarea.value = '';
            if (subjectInput) subjectInput.value = 'Re: {{ $rootMessage->subject ?? '' }}';
            if (tempFilesInput) tempFilesInput.value = '';

            // Clear the JavaScript array
            adminReplyUploadedFiles = [];

            // Clear universal uploader files
            clearUploaderInstances();

            console.log('✅ Admin reply form cleared after successful submission');
        }

        // Clear universal uploader instances
        function clearUploaderInstances() {
            const uploaderIds = ['admin-reply-attachments', 'message-attachments', 'reply-attachments'];

            // Try to clear instances from global registry
            if (window.universalUploaderInstances) {
                uploaderIds.forEach(uploaderId => {
                    if (window.universalUploaderInstances[uploaderId]) {
                        try {
                            window.universalUploaderInstances[uploaderId].clearAll();
                            console.log(`🧹 Cleared uploader instance: ${uploaderId}`);
                        } catch (e) {
                            console.warn(`⚠️ Failed to clear ${uploaderId}:`, e);
                        }
                    }
                });
            }

            // Also try Vue instances
            const uploaderElements = uploaderIds.map(id => document.getElementById(id)).filter(el => el);

            uploaderElements.forEach(element => {
                if (element && element.__vue__ && element.__vue__.clearAll) {
                    try {
                        element.__vue__.clearAll();
                        console.log(`🧹 Cleared Vue instance on ${element.id}`);
                    } catch (e) {
                        console.warn(`⚠️ Failed to clear Vue instance on ${element.id}:`, e);
                    }
                }
            });
        }

        // Clear reply form manually
        function clearReplyForm() {
            if (confirm('Are you sure you want to clear this form?')) {
                const messageTextarea = document.getElementById('message');
                const subjectInput = document.getElementById('subject');
                const tempFilesInput = document.getElementById('temp_files');

                if (messageTextarea) messageTextarea.value = '';
                if (subjectInput) subjectInput.value = 'Re: {{ $rootMessage->subject ?? '' }}';
                if (tempFilesInput) tempFilesInput.value = '';

                adminReplyUploadedFiles = [];
                clearUploaderInstances();

                showNotification('Form cleared', 'success');
            }
        }

        // Show notification function
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg max-w-sm ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
        type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
        type === 'warning' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' :
        'bg-blue-100 text-blue-800 border border-blue-200'
    }`;

            notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-1">${message}</div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-current hover:opacity-70">
                ×
            </button>
        </div>
    `;

            document.body.appendChild(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }

        // Debug function to check current state
        window.debugAdminReplyUploader = function() {
            console.log('🐛 Admin Reply Uploader Debug Info:');
            console.log('📁 Current files array:', adminReplyUploadedFiles);
            console.log('📝 temp_files input value:', document.getElementById('temp_files')?.value);
            console.log('🔧 Universal uploader instances:', window.universalUploaderInstances);
            console.log('📂 DOM elements:', {
                adminReplyAttachments: document.getElementById('admin-reply-attachments'),
                messageAttachments: document.getElementById('message-attachments'),
                replyAttachments: document.getElementById('reply-attachments'),
                tempFilesInput: document.getElementById('temp_files'),
                replyForm: document.getElementById('admin-reply-form')
            });
        };

        // Export functions to global scope
        window.clearReplyForm = clearReplyForm;
        window.showNotification = showNotification;
    </script>
</x-layouts.admin>
