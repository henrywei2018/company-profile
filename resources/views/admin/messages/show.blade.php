<!-- resources/views/admin/messages/show.blade.php -->
<x-admin-layout :title="'Message Details'">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900 mb-2">
                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Messages
            </a>
            <h2 class="text-xl font-semibold text-gray-900">{{ $message->subject }}</h2>
            <div class="flex items-center mt-1 text-sm text-gray-500">
                Received {{ $message->created_at->format('M d, Y \a\t H:i') }}
                @if($message->is_read)
                    <span class="mx-2">•</span>
                    <span class="text-gray-600">Read {{ $message->read_at ? $message->read_at->format('M d, Y \a\t H:i') : '' }}</span>
                @endif
            </div>
        </div>
        <div class="flex items-center mt-4 md:mt-0 space-x-3">
            @if(!$message->is_read)
                <form action="{{ route('admin.messages.mark-as-read', $message->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="mr-2 -ml-1 h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Mark as Read
                    </button>
                </form>
            @endif
            <form action="{{ route('admin.messages.destroy', $message->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="mr-2 -ml-1 h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <!-- Sender Information -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-700 font-medium text-lg">{{ substr($message->name, 0, 1) }}</span>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ $message->name }}</h3>
                    <div class="text-sm text-gray-500">
                        <a href="mailto:{{ $message->email }}" class="text-indigo-600 hover:text-indigo-900">
                            {{ $message->email }}
                        </a>
                        @if($message->phone)
                            <span class="mx-2">•</span>
                            <a href="tel:{{ $message->phone }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $message->phone }}
                            </a>
                        @endif
                    </div>
                    @if($message->company)
                        <div class="text-sm text-gray-500 mt-1">
                            Company: {{ $message->company }}
                        </div>
                    @endif
                </div>
                <div class="ml-auto">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $message->type === 'contact_form' ? 'bg-blue-100 text-blue-800' : 
                           ($message->type === 'project_inquiry' ? 'bg-green-100 text-green-800' : 
                           'bg-purple-100 text-purple-800') }}">
                        {{ ucfirst(str_replace('_', ' ', $message->type)) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Message Content -->
        <div class="px-6 py-4">
            <div class="prose max-w-none">
                {!! nl2br(e($message->message)) !!}
            </div>
        </div>

        <!-- Attachments if any -->
        @if(isset($message->attachments) && $message->attachments->count() > 0)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Attachments</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($message->attachments as $attachment)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 flex items-center">
                            <div class="flex-shrink-0 text-gray-400">
                                @php
                                    $extension = pathinfo($attachment->file_name, PATHINFO_EXTENSION);
                                    $iconClass = match(strtolower($extension)) {
                                        'pdf' => 'text-red-500',
                                        'doc', 'docx' => 'text-blue-500',
                                        'xls', 'xlsx' => 'text-green-500',
                                        'jpg', 'jpeg', 'png', 'gif' => 'text-purple-500',
                                        default => 'text-gray-500'
                                    };
                                @endphp
                                <svg class="h-8 w-8 {{ $iconClass }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $attachment->file_name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ strtoupper($extension) }} • {{ number_format($attachment->file_size / 1024, 0) }} KB
                                </p>
                            </div>
                            <div class="ml-2">
                                <a href="{{ route('admin.messages.download-attachment', $attachment->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Related Project (if any) -->
        @if(isset($message->project) && $message->project)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Related Project</h4>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h5 class="text-base font-medium text-gray-900">{{ $message->project->title }}</h5>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $message->project->category }} • 
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $message->project->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($message->project->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                       'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($message->project->status) }}
                                </span>
                            </p>
                        </div>
                        <a href="{{ route('admin.projects.edit', $message->project->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            View Project
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Related Client (if any) -->
        @if(isset($message->user) && $message->user)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Related Client</h4>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h5 class="text-base font-medium text-gray-900">{{ $message->user->name }}</h5>
                            <p class="mt-1 text-sm text-gray-500">
                                <a href="mailto:{{ $message->user->email }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $message->user->email }}
                                </a>
                                @if($message->user->phone)
                                    <span class="mx-2">•</span>
                                    {{ $message->user->phone }}
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('admin.users.edit', $message->user->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            View Client
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Reply Form -->
        <div class="px-6 py-4 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Reply to this message</h4>
            <form action="{{ route('admin.messages.reply', $message->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="reply_subject" class="block text-sm font-medium text-gray-700">Subject</label>
                    <input type="text" name="reply_subject" id="reply_subject" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="Re: {{ $message->subject }}">
                </div>
                <div class="mb-4">
                    <label for="reply_message" class="block text-sm font-medium text-gray-700">Message</label>
                    <textarea name="reply_message" id="reply_message" rows="5" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>