{{-- resources/views/admin/messages/create.blade.php --}}
<x-layouts.admin 
    title="Send Message" 
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
                                <span class="ml-1 text-gray-500 dark:text-gray-400">Send Message</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Send New Message</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Send a message to one or multiple clients
                </p>
            </div>

            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.messages.index') }}" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Messages
                </a>
            </div>
        </div>

        <!-- Message Form -->
        <form action="{{ route('admin.messages.store') }}" method="POST" enctype="multipart/form-data" x-data="messageForm()">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Recipient Selection -->
                    <x-admin.card>
                        <x-slot name="title">Recipients</x-slot>
                        
                        <div class="space-y-4">
                            <!-- Send Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    Send To:
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <label class="relative flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <input type="radio" name="send_type" value="specific_clients" x-model="sendType" 
                                               class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">Specific Clients</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Choose individual clients</div>
                                        </div>
                                    </label>
                                    
                                    <label class="relative flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <input type="radio" name="send_type" value="all_clients" x-model="sendType" 
                                               class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">All Clients</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Send to all registered clients</div>
                                        </div>
                                    </label>
                                    
                                    <label class="relative flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <input type="radio" name="send_type" value="custom_email" x-model="sendType" 
                                               class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">Custom Email</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Enter email manually</div>
                                        </div>
                                    </label>
                                </div>
                                @error('send_type')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Specific Clients Selection -->
                            <div x-show="sendType === 'specific_clients'" x-transition>
                                <label for="client_ids" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Select Clients <span class="text-red-500">*</span>
                                </label>
                                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-3 bg-gray-50 dark:bg-gray-800">
                                    @foreach($clients as $client)
                                        <label class="flex items-center p-2 hover:bg-white dark:hover:bg-gray-700 rounded">
                                            <input type="checkbox" name="client_ids[]" value="{{ $client->id }}" 
                                                   class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                   {{ in_array($client->id, old('client_ids', [])) ? 'checked' : '' }}>
                                            <div class="ml-3 flex-1">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $client->email }}</div>
                                            </div>
                                            @if($client->projects_count > 0)
                                                <div class="text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 px-2 py-1 rounded">
                                                    {{ $client->projects_count }} projects
                                                </div>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                                @error('client_ids')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- All Clients Confirmation -->
                            <div x-show="sendType === 'all_clients'" x-transition>
                                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                                Send to All Clients
                                            </h3>
                                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                                <p>This will send the message to all {{ $totalClients }} registered clients. 
                                                   They will receive the message via email.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Email Input -->
                            <div x-show="sendType === 'custom_email'" x-transition>
                                <label for="custom_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="custom_email" id="custom_email" 
                                       value="{{ old('custom_email') }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                       placeholder="Enter email address">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Enter the email address to send the message to.
                                </p>
                                @error('custom_email')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </x-admin.card>

                    <!-- Message Content -->
                    <x-admin.card>
                        <x-slot name="title">Message Content</x-slot>
                        
                        <div class="space-y-4">
                            <!-- Subject -->
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="subject" id="subject" required
                                       value="{{ old('subject') }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                       placeholder="Enter message subject">
                                @error('subject')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Message Body -->
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Message <span class="text-red-500">*</span>
                                </label>
                                <textarea name="message" id="message" rows="8" required
                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                          placeholder="Enter your message content...">{{ old('message') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    You can use basic formatting and line breaks.
                                </p>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Attachments -->
                            <div>
                                <label for="attachments" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Attachments (optional)
                                </label>
                                <input type="file" name="attachments[]" id="attachments" multiple
                                       class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/20 dark:file:text-blue-300">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    You can upload multiple files. Max size: 10MB per file. Allowed types: PDF, DOC, DOCX, JPG, PNG, ZIP.
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
                            
                            <button type="button" onclick="previewMessage()" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Preview
                            </button>
                        </div>
                        
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.messages.index') }}" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                Cancel
                            </a>
                            
                            <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Send Message
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Message Settings -->
                    <x-admin.card>
                        <x-slot name="title">Message Settings</x-slot>
                        
                        <div class="space-y-4">
                            <!-- Message Type -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Message Type
                                </label>
                                <select name="type" id="type" 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                    <option value="general" {{ old('type') === 'general' ? 'selected' : '' }}>General</option>
                                    <option value="announcement" {{ old('type') === 'announcement' ? 'selected' : '' }}>Announcement</option>
                                    <option value="notification" {{ old('type') === 'notification' ? 'selected' : '' }}>Notification</option>
                                    <option value="update" {{ old('type') === 'update' ? 'selected' : '' }}>Update</option>
                                    <option value="reminder" {{ old('type') === 'reminder' ? 'selected' : '' }}>Reminder</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Priority -->
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Priority Level
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

                            <!-- Project Association -->
                            <div>
                                <label for="project_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Related Project (optional)
                                </label>
                                <select name="project_id" id="project_id" 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                    <option value="">Select project...</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ Str::limit($project->title, 50) }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Associate this message with a specific project.
                                </p>
                                @error('project_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Delivery Options -->
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Delivery Options</h4>
                                
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="send_email" value="1" checked
                                               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Send email notification</span>
                                    </label>
                                    
                                    <label class="flex items-center">
                                        <input type="checkbox" name="save_to_messages" value="1" checked
                                               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Save to message system</span>
                                    </label>
                                    
                                    <label class="flex items-center">
                                        <input type="checkbox" name="require_read_receipt" value="1"
                                               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Request read receipt</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Schedule Delivery -->
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Schedule Delivery</h4>
                                
                                <label class="flex items-center mb-3">
                                    <input type="checkbox" id="schedule_delivery" name="schedule_delivery" value="1"
                                           x-model="scheduleDelivery"
                                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Schedule for later</span>
                                </label>
                                
                                <div x-show="scheduleDelivery" x-transition class="space-y-3">
                                    <div>
                                        <label for="scheduled_at_date" class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                                            Date
                                        </label>
                                        <input type="date" name="scheduled_at_date" id="scheduled_at_date" 
                                               value="{{ old('scheduled_at_date') }}"
                                               min="{{ date('Y-m-d') }}"
                                               class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                    </div>
                                    
                                    <div>
                                        <label for="scheduled_at_time" class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                                            Time
                                        </label>
                                        <input type="time" name="scheduled_at_time" id="scheduled_at_time" 
                                               value="{{ old('scheduled_at_time', '09:00') }}"
                                               class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-admin.card>

                    <!-- Quick Templates -->
                    <x-admin.card>
                        <x-slot name="title">Quick Templates</x-slot>
                        
                        <div class="space-y-2">
                            <button type="button" onclick="useTemplate('welcome')" 
                                class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="font-medium text-gray-900 dark:text-white">Welcome Message</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Standard welcome template</div>
                            </button>
                            
                            <button type="button" onclick="useTemplate('update')" 
                                class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="font-medium text-gray-900 dark:text-white">Project Update</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Project status update template</div>
                            </button>
                            
                            <button type="button" onclick="useTemplate('reminder')" 
                                class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="font-medium text-gray-900 dark:text-white">Payment Reminder</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Payment reminder template</div>
                            </button>
                            
                            <button type="button" onclick="useTemplate('announcement')" 
                                class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="font-medium text-gray-900 dark:text-white">Announcement</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">General announcement template</div>
                            </button>
                        </div>
                    </x-admin.card>

                    <!-- Recipient Summary -->
                    <x-admin.card x-show="sendType" x-transition>
                        <x-slot name="title">Recipient Summary</x-slot>
                        
                        <div class="text-sm space-y-2">
                            <div x-show="sendType === 'specific_clients'">
                                <span class="text-gray-500 dark:text-gray-400">Selected clients:</span>
                                <span class="font-medium" x-text="getSelectedClientsCount()"></span>
                            </div>
                            
                            <div x-show="sendType === 'all_clients'">
                                <span class="text-gray-500 dark:text-gray-400">Total recipients:</span>
                                <span class="font-medium">{{ $totalClients }} clients</span>
                            </div>
                            
                            <div x-show="sendType === 'custom_email'">
                                <span class="text-gray-500 dark:text-gray-400">Recipient:</span>
                                <span class="font-medium">Custom email address</span>
                            </div>
                            
                            <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Estimated delivery time:</span>
                                    <span class="font-medium text-xs">~2 minutes</span>
                                </div>
                            </div>
                        </div>
                    </x-admin.card>
                </div>
            </div>
        </form>
    </div>

    <!-- Message Preview Modal -->
    <div id="preview-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 50;">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Message Preview</h3>
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
                            To: <span id="preview-recipients"></span><br>
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
                        Send Message
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Data -->
    <script>
        function messageForm() {
            return {
                sendType: @json(old('send_type', 'specific_clients')),
                scheduleDelivery: @json(old('schedule_delivery', false)),
                
                getSelectedClientsCount() {
                    const checkboxes = document.querySelectorAll('input[name="client_ids[]"]:checked');
                    return checkboxes.length;
                }
            }
        }

        // Message Templates
        const templates = {
            welcome: {
                subject: 'Welcome to Our Service!',
                message: 'Dear {{client_name}},\n\nWelcome to our service! We\'re excited to have you on board.\n\nIf you have any questions, please don\'t hesitate to reach out.\n\nBest regards,\nThe Team'
            },
            update: {
                subject: 'Project Update - {{project_name}}',
                message: 'Dear {{client_name}},\n\nWe wanted to provide you with an update on your project.\n\n[Project update details here]\n\nNext steps:\n- [Next step 1]\n- [Next step 2]\n\nBest regards,\nThe Team'
            },
            reminder: {
                subject: 'Payment Reminder - Invoice {{invoice_number}}',
                message: 'Dear {{client_name}},\n\nThis is a friendly reminder that invoice {{invoice_number}} is due for payment.\n\nDue date: {{due_date}}\nAmount: {{amount}}\n\nPlease let us know if you have any questions.\n\nBest regards,\nThe Team'
            },
            announcement: {
                subject: 'Important Announcement',
                message: 'Dear {{client_name}},\n\nWe have an important announcement to share with you.\n\n[Announcement details here]\n\nThank you for your attention.\n\nBest regards,\nThe Team'
            }
        };

        function useTemplate(templateName) {
            const template = templates[templateName];
            if (template) {
                document.getElementById('subject').value = template.subject;
                document.getElementById('message').value = template.message;
                
                // Set appropriate type and priority based on template
                const typeField = document.getElementById('type');
                switch(templateName) {
                    case 'welcome':
                        typeField.value = 'notification';
                        break;
                    case 'update':
                        typeField.value = 'update';
                        break;
                    case 'reminder':
                        typeField.value = 'reminder';
                        document.getElementById('priority').value = 'high';
                        break;
                    case 'announcement':
                        typeField.value = 'announcement';
                        document.getElementById('priority').value = 'high';
                        break;
                }
            }
        }

        function saveDraft() {
            const formData = {
                subject: document.getElementById('subject').value,
                message: document.getElementById('message').value,
                type: document.getElementById('type').value,
                priority: document.getElementById('priority').value,
                sendType: document.querySelector('input[name="send_type"]:checked')?.value
            };

            localStorage.setItem('admin_message_draft', JSON.stringify(formData));
            showNotification('Draft saved successfully!', 'success');
        }

        function loadDraft() {
            const draft = localStorage.getItem('admin_message_draft');
            if (draft) {
                const data = JSON.parse(draft);
                
                if (confirm('Load saved draft?')) {
                    document.getElementById('subject').value = data.subject || '';
                    document.getElementById('message').value = data.message || '';
                    document.getElementById('type').value = data.type || 'general';
                    document.getElementById('priority').value = data.priority || 'normal';
                    
                    if (data.sendType) {
                        const sendTypeRadio = document.querySelector(`input[name="send_type"][value="${data.sendType}"]`);
                        if (sendTypeRadio) sendTypeRadio.checked = true;
                    }
                    
                    showNotification('Draft loaded successfully!', 'success');
                }
            }
        }

        function previewMessage() {
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;
            const priority = document.getElementById('priority').value;
            const sendType = document.querySelector('input[name="send_type"]:checked')?.value;
            
            if (!subject || !message) {
                alert('Please fill in subject and message before previewing.');
                return;
            }

            // Update preview content
            document.getElementById('preview-subject').textContent = subject;
            document.getElementById('preview-message').textContent = message;
            document.getElementById('preview-priority').textContent = priority.charAt(0).toUpperCase() + priority.slice(1);
            
            // Update recipients text
            let recipientsText = '';
            if (sendType === 'all_clients') {
                recipientsText = '{{ $totalClients }} clients';
            } else if (sendType === 'specific_clients') {
                const selectedCount = document.querySelectorAll('input[name="client_ids[]"]:checked').length;
                recipientsText = `${selectedCount} selected clients`;
            } else if (sendType === 'custom_email') {
                const customEmail = document.getElementById('custom_email').value;
                recipientsText = customEmail || 'Custom email address';
            }
            document.getElementById('preview-recipients').textContent = recipientsText;
            
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

        // Load draft on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadDraft();
            
            // Handle success/error messages from Laravel
            @if(session('success'))
                showNotification('{{ session('success') }}', 'success');
                localStorage.removeItem('admin_message_draft'); // Clear draft after successful send
            @endif

            @if(session('error'))
                showNotification('{{ session('error') }}', 'error');
            @endif
        });
    </script>

</x-layouts.admin>