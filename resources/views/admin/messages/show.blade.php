<!-- resources/views/admin/messages/show.blade.php -->
<x-layouts.admin title="Message Details" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Messages' => route('admin.messages.index'),
            'Message Details' => '#'
        ]" />
        
        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            <form action="{{ route('admin.messages.toggle-read', $message) }}" method="POST" class="inline">
                @csrf
                <x-admin.button
                    type="submit"
                    color="{{ $message->is_read ? 'warning' : 'success' }}"
                >
                    @if($message->is_read)
                        <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Mark as Unread
                    @else
                        <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                        </svg>
                        Mark as Read
                    @endif
                </x-admin.button>
            </form>
            
            <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this message?')">
                @csrf
                @method('DELETE')
                <x-admin.button
                    type="submit"
                    color="danger"
                >
                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Message
                </x-admin.button>
            </form>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Message Content -->
        <div class="md:col-span-2">
            <x-admin.card>
                <x-slot name="title">Message</x-slot>
                
                <div class="mb-6">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $message->subject }}</h1>
                    <div class="flex items-center text-sm text-gray-500 dark:text-neutral-400">
                        <span>From: {{ $message->name }} &lt;{{ $message->email }}&gt;</span>
                        <span class="mx-2">•</span>
                        <span>{{ $message->created_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
                
                <div class="prose max-w-none dark:prose-invert mb-6">
                    {!! nl2br(e($message->message)) !!}
                </div>
                
                @if($message->attachments && count($message->attachments) > 0)
                    <div class="border-t border-gray-200 dark:border-neutral-700 pt-5 mt-6">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Attachments</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($message->attachments as $attachment)
                                <div class="flex items-center p-3 border border-gray-200 dark:border-neutral-700 rounded-lg bg-gray-50 dark:bg-neutral-800">
                                    <div class="flex-shrink-0 mr-3">
                                        @php
                                            $extension = pathinfo($attachment->file_name, PATHINFO_EXTENSION);
                                            $iconClass = match(strtolower($extension)) {
                                                'pdf' => 'text-red-600 dark:text-red-400',
                                                'doc', 'docx' => 'text-blue-600 dark:text-blue-400',
                                                'xls', 'xlsx' => 'text-green-600 dark:text-green-400',
                                                'jpg', 'jpeg', 'png', 'gif' => 'text-purple-600 dark:text-purple-400',
                                                default => 'text-gray-600 dark:text-gray-400'
                                            };
                                        @endphp
                                        <svg class="h-8 w-8 {{ $iconClass }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $attachment->file_name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-neutral-400">
                                            {{ human_filesize($attachment->file_size) }}
                                        </p>
                                    </div>
                                    <a href="{{ route('admin.messages.attachments.download', ['message' => $message->id, 'attachmentId' => $attachment->id]) }}" class="ml-4 flex-shrink-0 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if($message->type === 'client_to_admin' || $message->type === 'contact_form')
                    <div class="border-t border-gray-200 dark:border-neutral-700 pt-5 mt-6">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Reply</h3>
                        <form action="{{ route('admin.messages.reply', $message) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-4">
                                <x-admin.input
                                    name="subject"
                                    label="Subject"
                                    value="RE: {{ $message->subject }}"
                                    required
                                />
                            </div>
                            
                            <div class="mb-4">
                                <x-admin.rich-editor
                                    name="message"
                                    label="Message"
                                    placeholder="Enter your reply..."
                                    value=""
                                    minHeight="200px"
                                    required
                                />
                            </div>
                            
                            <div class="mb-4">
                                <x-admin.file-upload
                                    name="attachments[]"
                                    label="Attachments"
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar"
                                    multiple="true"
                                    helper="Max 5 files. Maximum size per file: 2MB."
                                    maxFiles="5"
                                    maxFileSize="2"
                                />
                                </div>
                            
                            <div class="flex justify-end">
                                <x-admin.button
                                    type="submit"
                                    color="primary"
                                >
                                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Send Reply
                                </x-admin.button>
                            </div>
                        </form>
                    </div>
                @endif
            </x-admin.card>
            
            <!-- Previous Message Thread (if any) -->
            @if($relatedMessages && $relatedMessages->count() > 0)
                <x-admin.card class="mt-6">
                    <x-slot name="title">Conversation History</x-slot>
                    
                    <div class="space-y-6">
                        @foreach($relatedMessages as $relatedMessage)
                            <div class="border-b border-gray-200 dark:border-neutral-700 pb-5 {{ !$loop->last ? 'mb-5' : '' }}">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="size-10 rounded-full flex items-center justify-center {{ $relatedMessage->type === 'admin_to_client' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-600 dark:bg-neutral-800 dark:text-neutral-400' }}">
                                            @if($relatedMessage->type === 'admin_to_client')
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            @else
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-medium {{ $relatedMessage->type === 'admin_to_client' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-white' }}">
                                                {{ $relatedMessage->type === 'admin_to_client' ? 'Admin' : $relatedMessage->name }}
                                            </h3>
                                            <p class="text-xs text-gray-500 dark:text-neutral-500">
                                                {{ $relatedMessage->created_at->format('M d, Y H:i') }}
                                            </p>
                                        </div>
                                        <div class="mt-1 text-sm text-gray-700 dark:text-neutral-300">
                                            <p class="font-medium">{{ $relatedMessage->subject }}</p>
                                            <div class="mt-2 prose-sm max-w-none dark:prose-invert">
                                                {!! nl2br(e($relatedMessage->message)) !!}
                                            </div>
                                        </div>
                                        
                                        @if($relatedMessage->attachments && count($relatedMessage->attachments) > 0)
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-500 dark:text-neutral-500 mb-1">Attachments:</p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($relatedMessage->attachments as $attachment)
                                                        <a href="{{ route('admin.messages.attachments.download', ['message' => $relatedMessage->id, 'attachmentId' => $attachment->id]) }}" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 dark:border-neutral-700 rounded-md text-xs font-medium bg-white dark:bg-neutral-800 text-gray-700 dark:text-neutral-300 hover:bg-gray-50 dark:hover:bg-neutral-700">
                                                            <svg class="h-4 w-4 mr-1 text-gray-500 dark:text-neutral-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                            </svg>
                                                            {{ Str::limit($attachment->file_name, 20) }}
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
            @endif
        </div>
        
        <!-- Sidebar with Message Details -->
        <div class="space-y-6">
            <x-admin.card>
                <x-slot name="title">Message Details</x-slot>
                
                <ul class="divide-y divide-gray-200 dark:divide-neutral-700">
                    <li class="py-3 flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-neutral-400">Status</span>
                        <span>
                            @if($message->is_read)
                                <x-admin.badge type="success" dot="true">Read</x-admin.badge>
                            @else
                                <x-admin.badge type="warning" dot="true">Unread</x-admin.badge>
                            @endif
                        </span>
                    </li>
                    <li class="py-3 flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-neutral-400">Type</span>
                        <span>
                            @if($message->type === 'contact_form')
                                <x-admin.badge type="info">Contact Form</x-admin.badge>
                            @elseif($message->type === 'client_to_admin')
                                <x-admin.badge type="primary">Client Message</x-admin.badge>
                            @elseif($message->type === 'admin_to_client')
                                <x-admin.badge type="dark">Admin Message</x-admin.badge>
                            @else
                                <x-admin.badge>{{ $message->type }}</x-admin.badge>
                            @endif
                        </span>
                    </li>
                    <li class="py-3 flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-neutral-400">Received</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $message->created_at->format('M d, Y H:i') }}</span>
                    </li>
                    @if($message->is_read && $message->read_at)
                        <li class="py-3 flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-neutral-400">Read At</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $message->read_at->format('M d, Y H:i') }}</span>
                        </li>
                    @endif
                    <li class="py-3 flex flex-col">
                        <span class="text-sm text-gray-600 dark:text-neutral-400 mb-1">From</span>
                        <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $message->name }}</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $message->email }}</span>
                        @if($message->phone)
                            <span class="text-sm text-gray-900 dark:text-white">{{ $message->phone }}</span>
                        @endif
                    </li>
                    @if($message->ip_address)
                        <li class="py-3 flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-neutral-400">IP Address</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $message->ip_address }}</span>
                        </li>
                    @endif
                    @if($message->user_agent)
                        <li class="py-3 flex flex-col">
                            <span class="text-sm text-gray-600 dark:text-neutral-400 mb-1">User Agent</span>
                            <span class="text-xs text-gray-500 dark:text-neutral-500 break-words">{{ $message->user_agent }}</span>
                        </li>
                    @endif
                </ul>
            </x-admin.card>
            
            @if($message->type === 'client_to_admin' && $message->client)
                <x-admin.card>
                    <x-slot name="title">Client Information</x-slot>
                    
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            @if($message->client->profile_photo_path)
                                <img class="h-12 w-12 rounded-full" src="{{ asset('storage/' . $message->client->profile_photo_path) }}" alt="{{ $message->client->name }}">
                            @else
                                <div class="h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-medium text-lg">
                                    {{ strtoupper(substr($message->client->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <h3 class="text-base font-medium text-gray-900 dark:text-white">{{ $message->client->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-neutral-500">
                                @if($message->client->company_name)
                                    {{ $message->client->company_name }}
                                @else
                                    Client
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <ul class="divide-y divide-gray-200 dark:divide-neutral-700">
                        <li class="py-2 flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-neutral-400">Email</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $message->client->email }}</span>
                        </li>
                        <li class="py-2 flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-neutral-400">Phone</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $message->client->phone ?? 'N/A' }}</span>
                        </li>
                        <li class="py-2 flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-neutral-400">Member Since</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $message->client->created_at->format('M Y') }}</span>
                        </li>
                    </ul>
                    
                    <div class="mt-4">
                        <a href="{{ route('admin.users.show', $message->client) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium flex items-center">
                            <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                            View Client Profile
                        </a>
                    </div>
                </x-admin.card>
            @endif
            
            @if(isset($clientMessages) && $clientMessages->count() > 0)
                <x-admin.card>
                    <x-slot name="title">Other Messages</x-slot>
                    <x-slot name="subtitle">From this sender</x-slot>
                    
                    <div class="space-y-3">
                        @foreach($clientMessages as $clientMessage)
                            <a href="{{ route('admin.messages.show', $clientMessage) }}" class="block p-3 border border-gray-200 dark:border-neutral-700 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-800">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="text-sm font-medium {{ $clientMessage->is_read ? 'text-gray-700 dark:text-neutral-300' : 'text-gray-900 dark:text-white' }} truncate max-w-xs">
                                        {{ $clientMessage->subject }}
                                    </h4>
                                    <span class="text-xs text-gray-500 dark:text-neutral-500">
                                        {{ $clientMessage->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-neutral-500 truncate">
                                    {{ Str::limit(strip_tags($clientMessage->message), 100) }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                    
                    @if($totalClientMessages > count($clientMessages))
                        <div class="mt-3 text-center">
                            <a href="{{ route('admin.messages.index', ['search' => $message->email]) }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                View all {{ $totalClientMessages }} messages
                            </a>
                        </div>
                    @endif
                </x-admin.card>
            @endif
        </div>
    </div>
</x-layouts.admin>