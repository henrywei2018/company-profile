<!-- resources/views/admin/messages/index.blade.php -->
<x-admin-layout :title="'Messages'">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Manage Messages</h2>
        <div class="mt-4 md:mt-0 flex flex-col md:flex-row gap-3">
            <form action="{{ route('admin.messages.index') }}" method="GET" class="flex items-center space-x-2">
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        placeholder="Search messages..."
                        value="{{ request()->get('search') }}"
                        class="w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <button type="submit" class="text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <select
                    name="type"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    onchange="this.form.submit()"
                >
                    <option value="">All Types</option>
                    <option value="contact_form" {{ request()->get('type') === 'contact_form' ? 'selected' : '' }}>Contact Form</option>
                    <option value="project_inquiry" {{ request()->get('type') === 'project_inquiry' ? 'selected' : '' }}>Project Inquiry</option>
                    <option value="support" {{ request()->get('type') === 'support' ? 'selected' : '' }}>Support</option>
                </select>
                <select
                    name="is_read"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    onchange="this.form.submit()"
                >
                    <option value="">All Status</option>
                    <option value="0" {{ request()->get('is_read') === '0' ? 'selected' : '' }}>Unread</option>
                    <option value="1" {{ request()->get('is_read') === '1' ? 'selected' : '' }}>Read</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($messages->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sender
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Subject
                        </th>
                        <th scope="col" class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <th scope="col" class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($messages as $message)
                        <tr class="{{ $message->is_read ? '' : 'bg-blue-50' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $message->name }}
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $message->email }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 truncate max-w-xs">
                                    {{ $message->subject }}
                                </div>
                                <div class="text-sm text-gray-500 truncate max-w-xs">
                                    {{ Str::limit(strip_tags($message->message), 50) }}
                                </div>
                            </td>
                            <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $message->type === 'contact_form' ? 'bg-blue-100 text-blue-800' : 
                                       ($message->type === 'project_inquiry' ? 'bg-green-100 text-green-800' : 
                                       'bg-purple-100 text-purple-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $message->type)) }}
                                </span>
                            </td>
                            <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $message->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $message->is_read ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $message->is_read ? 'Read' : 'Unread' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.messages.show', $message->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        View
                                    </a>
                                    @if(!$message->is_read)
                                        <form action="{{ route('admin.messages.mark-as-read', $message->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-900">
                                                Mark as Read
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.messages.destroy', $message->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4 border-t border-gray-200">
                {{ $messages->links('components.pagination') }}
            </div>
        @else
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No messages found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    {{ request()->has('search') || request()->has('type') || request()->has('is_read') 
                        ? 'Try adjusting your search or filter criteria.' 
                        : 'Messages from your website contact form and client communications will appear here.' }}
                </p>
                @if(request()->has('search') || request()->has('type') || request()->has('is_read'))
                    <div class="mt-6">
                        <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Clear Filters
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-admin-layout>