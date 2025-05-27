<x-layouts.admin title="Chat Templates">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Live Chat' => route('admin.chat.index'), 
        'Quick Responses' => ''
    ]" />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Quick Response Templates</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage predefined responses for faster chat support</p>
        </div>
        <div class="flex gap-3">
            <x-admin.button color="primary" onclick="openCreateModal()">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Template
            </x-admin.button>
        </div>
    </div>

    <!-- Templates List -->
    <x-admin.card>
        @if(isset($templates) && $templates->count() > 0)
            <div class="space-y-4">
                @foreach($templates as $template)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $template->name }}</h3>
                                    <x-admin.badge :type="$template->type === 'greeting' ? 'info' : ($template->type === 'auto_response' ? 'success' : 'light')">
                                        {{ ucfirst(str_replace('_', ' ', $template->type)) }}
                                    </x-admin.badge>
                                </div>
                                
                                @if($template->trigger)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                        <strong>Trigger:</strong> {{ $template->trigger }}
                                    </p>
                                @endif
                                
                                <div class="bg-gray-50 dark:bg-gray-800 rounded p-3 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $template->message }}
                                </div>
                                
                                <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span>Used {{ $template->usage_count ?? 0 }} times</span>
                                    <span>Created {{ $template->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2 ml-4">
                                <button onclick="editTemplate({{ $template->id }})" 
                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button onclick="deleteTemplate({{ $template->id }})" 
                                        class="text-red-400 hover:text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No templates yet</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Create your first quick response template to speed up chat support.
                </p>
                <div class="mt-6">
                    <x-admin.button color="primary" onclick="openCreateModal()">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Template
                    </x-admin.button>
                </div>
            </div>
        @endif
    </x-admin.card>

    <!-- Create/Edit Modal -->
    <div id="template-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <form id="template-form" method="POST" action="{{ route('admin.chat.templates.store') }}">
                @csrf
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Add Quick Response Template</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Template Name</label>
                            <input type="text" name="name" required 
                                   class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                            <select name="type" required 
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white">
                                <option value="greeting">Greeting</option>
                                <option value="auto_response">Auto Response</option>
                                <option value="quick_reply">Quick Reply</option>
                                <option value="offline">Offline Message</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Trigger (Optional)</label>
                            <input type="text" name="trigger" 
                                   placeholder="e.g., hello, help, quote"
                                   class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white">
                            <p class="text-xs text-gray-500 mt-1">Keywords that will trigger this response</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Message</label>
                            <textarea name="message" rows="4" required 
                                      class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white"
                                      placeholder="Enter your response message..."></textarea>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeModal()" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                            Save Template
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openCreateModal() {
            document.getElementById('template-modal').classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('template-modal').classList.add('hidden');
        }
        
        function editTemplate(id) {
            // You can implement edit functionality here
            alert('Edit functionality coming soon!');
        }
        
        function deleteTemplate(id) {
            if (confirm('Are you sure you want to delete this template?')) {
                // You can implement delete functionality here
                alert('Delete functionality coming soon!');
            }
        }
        
        // Close modal when clicking outside
        document.getElementById('template-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
    @endpush
</x-layouts.admin>