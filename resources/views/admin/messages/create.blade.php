<!-- resources/views/admin/messages/create.blade.php -->
<x-layouts.admin title="Send Direct Message" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Messages' => route('admin.messages.index'),
            'Send Direct Message' => '#'
        ]" />
    </div>

    <form action="{{ route('admin.messages.store') }}" method="POST" enctype="multipart/form-data" x-data="messageForm()" @submit="return validateForm($event)">
        @csrf

        <!-- Recipient Selection -->
        <x-admin.form-section title="Recipient Information" description="Choose who to send the message to.">
            <div class="space-y-6">
                <!-- Recipient Type Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-neutral-300">
                        Send Message To <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-6">
                        <div class="flex items-center">
                            <input 
                                type="radio" 
                                id="existing_client" 
                                name="recipient_type" 
                                value="existing_client"
                                x-model="recipientType"
                                class="shrink-0 mt-0.5 border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700"
                                {{ old('recipient_type', $selectedClient ? 'existing_client' : '') == 'existing_client' ? 'checked' : '' }}
                            >
                            <label for="existing_client" class="ml-3 block text-sm text-gray-700 dark:text-neutral-300">
                                Existing Registered Client
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input 
                                type="radio" 
                                id="custom_email" 
                                name="recipient_type" 
                                value="custom_email"
                                x-model="recipientType"
                                class="shrink-0 mt-0.5 border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700"
                                {{ old('recipient_type', $selectedEmail ? 'custom_email' : '') == 'custom_email' ? 'checked' : '' }}
                            >
                            <label for="custom_email" class="ml-3 block text-sm text-gray-700 dark:text-neutral-300">
                                Custom Email Address
                            </label>
                        </div>
                    </div>
                    @error('recipient_type')
                        <div class="mt-1">
                            <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- Existing Client Selection -->
                <div x-show="recipientType === 'existing_client'" x-transition.duration.300ms x-cloak>
                    <div class="mb-4" x-data="clientSearch()">
                        <label for="user_id" class="block text-sm font-medium mb-2 text-gray-700 dark:text-neutral-300">
                            Select Client <span class="text-red-500" x-show="recipientType === 'existing_client'">*</span>
                        </label>
                        
                        <!-- Search Input Container with Proper Positioning -->
                        <div class="relative">
                            <input 
                                type="text" 
                                x-model="searchQuery"
                                x-ref="searchInput"
                                @focus="showDropdown = true"
                                @input="filterClients()"
                                @keydown.escape="showDropdown = false"
                                @keydown.arrow-down.prevent="navigateDown()"
                                @keydown.arrow-up.prevent="navigateUp()"
                                @keydown.enter.prevent="selectHighlighted()"
                                placeholder="Search clients by name, email, or company..."
                                class="py-3 px-4 pr-10 block w-full rounded-md text-sm border-gray-300 dark:border-neutral-700 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 bg-white dark:bg-neutral-800"
                                autocomplete="off"
                            >
                            
                            <!-- Search Icon -->
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            
                            <!-- Clear Button -->
                            <button 
                                type="button"
                                x-show="selectedClient || searchQuery"
                                @click="clearSelection()"
                                class="absolute inset-y-0 right-8 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                            >
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            
                            <!-- FIXED: Dropdown with proper z-index to escape container clipping -->
                            <div 
                                x-show="showDropdown && filteredClients.length > 0"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                @click.away="showDropdown = false"
                                class="absolute mt-1 w-full bg-white border border-gray-300 dark:bg-neutral-800 dark:border-neutral-700 rounded-md shadow-lg max-h-60 overflow-y-auto"
                                style="z-index: 9999; position: fixed; width: inherit; left: auto; right: auto;"
                                x-anchor="$refs.searchInput"
                            >
                                <template x-for="(client, index) in filteredClients" :key="client.id">
                                    <div 
                                        @click="selectClient(client)"
                                        @mouseenter="highlightedIndex = index"
                                        :class="{
                                            'bg-blue-50 dark:bg-blue-900/30': highlightedIndex === index,
                                            'hover:bg-gray-50 dark:hover:bg-neutral-700': highlightedIndex !== index
                                        }"
                                        class="px-3 py-2 cursor-pointer border-b border-gray-100 dark:border-neutral-700 last:border-b-0"
                                    >
                                        <div class="flex items-center space-x-3">
                                            <!-- Avatar -->
                                            <div class="flex-shrink-0 h-7 w-7 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-medium text-blue-600 dark:text-blue-400" x-text="client.name.charAt(0).toUpperCase()"></span>
                                            </div>

                                            <!-- Client Info -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="client.name"></p>
                                                        <div class="flex items-center space-x-2 mt-0.5">
                                                            <p class="text-xs text-gray-500 dark:text-neutral-400 truncate" x-text="client.email"></p>
                                                            <span x-show="client.company" class="text-xs text-gray-400 dark:text-neutral-500">•</span>
                                                            <p x-show="client.company" class="text-xs text-gray-400 dark:text-neutral-500 truncate" x-text="client.company"></p>
                                                        </div>
                                                    </div>

                                                    <!-- Verification Badge -->
                                                    <div class="flex-shrink-0 ml-2">
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                                            ✓
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                
                                <!-- No Results -->
                                <div x-show="filteredClients.length === 0 && searchQuery.length > 0" class="px-3 py-2 text-sm text-gray-500 dark:text-neutral-400 text-center">
                                    No clients found matching "<span x-text="searchQuery" class="font-medium"></span>"
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden Input for Form Submission -->
                        <input 
                            type="hidden" 
                            name="user_id" 
                            x-model="selectedClientId"
                            :required="recipientType === 'existing_client'"
                        >
                        
                        <!-- REMOVED: The separate dropdown that was causing positioning issues -->
                        
                        <!-- Selected Client Display -->
                        <div x-show="selectedClient" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md dark:bg-blue-900/30 dark:border-blue-800">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-blue-600 dark:text-blue-400" x-text="selectedClient ? selectedClient.name.charAt(0).toUpperCase() : ''"></span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-blue-900 dark:text-blue-300" x-text="selectedClient ? selectedClient.name : ''"></p>
                                        <p class="text-xs text-blue-700 dark:text-blue-400" x-text="selectedClient ? selectedClient.email : ''"></p>
                                        <p x-show="selectedClient && selectedClient.company" class="text-xs text-blue-600 dark:text-blue-500" x-text="selectedClient ? selectedClient.company : ''"></p>
                                    </div>
                                </div>
                                <button 
                                    type="button"
                                    @click="clearSelection()"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                >
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        @error('user_id')
                            <div class="mt-1">
                                <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
                            </div>
                        @enderror
                        
                        @if($clients->isEmpty())
                            <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md dark:bg-yellow-900/30 dark:border-yellow-800">
                                <div class="flex">
                                    <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-800 dark:text-yellow-400">
                                            No registered clients found. You can send to a custom email address instead.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-1">
                                <span class="text-xs text-gray-500 dark:text-neutral-400">
                                    Search through {{ $clients->count() }} registered clients. Use arrow keys to navigate, Enter to select.
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Custom Email Fields -->
                <div x-show="recipientType === 'custom_email'" x-transition.duration.300ms x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-4">
                            <label for="custom_name" class="block text-sm font-medium mb-2 text-gray-700 dark:text-neutral-300">
                                Recipient Name <span class="text-red-500" x-show="recipientType === 'custom_email'">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="custom_name" 
                                id="custom_name"
                                x-model="customName"
                                placeholder="Enter recipient's name"
                                value="{{ old('custom_name') }}"
                                class="py-3 px-4 block w-full rounded-md text-sm border-gray-300 dark:border-neutral-700 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 bg-white dark:bg-neutral-800"
                                :required="recipientType === 'custom_email'"
                            >
                            @error('custom_name')
                                <div class="mt-1">
                                    <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="custom_email" class="block text-sm font-medium mb-2 text-gray-700 dark:text-neutral-300">
                                Email Address <span class="text-red-500" x-show="recipientType === 'custom_email'">*</span>
                            </label>
                            <input 
                                type="email" 
                                name="custom_email" 
                                id="custom_email"
                                x-model="customEmail"
                                placeholder="Enter email address"
                                value="{{ old('custom_email', $selectedEmail) }}"
                                class="py-3 px-4 block w-full rounded-md text-sm border-gray-300 dark:border-neutral-700 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 bg-white dark:bg-neutral-800"
                                :required="recipientType === 'custom_email'"
                            >
                            @error('custom_email')
                                <div class="mt-1">
                                    <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md dark:bg-blue-900/30 dark:border-blue-800">
                        <div class="flex">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-blue-800 dark:text-blue-400">
                                    Enter details for a non-registered recipient. They will receive the message via email.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-admin.form-section>

        <!-- Message Content -->
        <x-admin.form-section title="Message Content" description="Compose your message." class="mt-8">
            <div class="space-y-6">
                <div class="mb-4">
                    <label for="subject" class="block text-sm font-medium mb-2 text-gray-700 dark:text-neutral-300">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="subject" 
                        id="subject"
                        x-model="subject"
                        placeholder="Enter message subject"
                        value="{{ old('subject') }}"
                        required
                        class="py-3 px-4 block w-full rounded-md text-sm border-gray-300 dark:border-neutral-700 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 bg-white dark:bg-neutral-800"
                    >
                    @error('subject')
                        <div class="mt-1">
                            <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="message" class="block text-sm font-medium mb-2 text-gray-700 dark:text-neutral-300">
                        Message <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        name="message" 
                        id="message"
                        x-model="message"
                        rows="8"
                        required
                        placeholder="Enter your message content..."
                        class="py-3 px-4 block w-full rounded-md text-sm border-gray-300 dark:border-neutral-700 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 bg-white dark:bg-neutral-800"
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <div class="mt-1">
                            <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
                        </div>
                    @enderror
                </div>
            </div>
        </x-admin.form-section>

        <!-- Attachments -->
        <x-admin.form-section title="Attachments" description="Attach files to your message (optional)." class="mt-8">
            <div class="mb-4">
                <label for="attachments" class="block text-sm font-medium mb-2 text-gray-700 dark:text-neutral-300">
                    File Attachments
                </label>
                <input 
                    type="file" 
                    name="attachments[]" 
                    id="attachments"
                    multiple
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip,.rar"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-400"
                >
                @error('attachments')
                    <div class="mt-1">
                        <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
                    </div>
                @enderror
                @error('attachments.*')
                    <div class="mt-1">
                        <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
                    </div>
                @enderror
                <div class="mt-1">
                    <span class="text-xs text-gray-500 dark:text-neutral-400">
                        You can attach up to 5 files. Max 2MB per file. Supported formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, ZIP, RAR
                    </span>
                </div>
            </div>
        </x-admin.form-section>

        <!-- Form Actions -->
        <div class="flex justify-end mt-8 gap-3">
            <x-admin.button
                href="{{ route('admin.messages.index') }}"
                color="light"
                type="button"
            >
                Cancel
            </x-admin.button>

            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                :disabled="!isFormValid"
                :class="{ 'opacity-50 cursor-not-allowed': !isFormValid, 'hover:bg-blue-700': isFormValid }"
            >
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                Send Message
            </button>
        </div>

        <!-- Validation Status -->
        <div x-show="!isFormValid && (subject.length > 0 || message.length > 0)" x-transition x-cloak class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md dark:bg-red-900/30 dark:border-red-800">
            <div class="flex">
                <svg class="w-4 h-4 text-red-600 dark:text-red-400 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="ml-3">
                    <p class="text-sm text-red-800 dark:text-red-400">
                        Please complete all required fields before sending the message.
                    </p>
                </div>
            </div>
        </div>
    </form>
</x-layouts.admin>

<script>
    function messageForm() {
        return {
            recipientType: @js(old('recipient_type', $selectedClient ? 'existing_client' : ($selectedEmail ? 'custom_email' : 'existing_client'))),
            selectedUserId: @js(old('user_id', $selectedClient?->id ?? '')),
            customName: @js(old('custom_name', '')),
            customEmail: @js(old('custom_email', $selectedEmail ?? '')),
            subject: @js(old('subject', '')),
            message: @js(old('message', '')),
            
            get isFormValid() {
                if (!this.subject.trim() || !this.message.trim()) {
                    return false;
                }
                
                if (this.recipientType === 'existing_client') {
                    return !!this.selectedUserId;
                } else if (this.recipientType === 'custom_email') {
                    return !!(this.customName.trim() && this.customEmail.trim() && this.isValidEmail(this.customEmail));
                }
                
                return false;
            },
            
            isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            },
            
            validateForm(event) {
                if (!this.isFormValid) {
                    event.preventDefault();
                    this.showValidationErrors();
                    return false;
                }
                return true;
            },
            
            showValidationErrors() {
                let errors = [];
                
                if (!this.subject.trim()) {
                    errors.push('• Subject is required');
                }
                
                if (!this.message.trim()) {
                    errors.push('• Message is required');
                }
                
                if (this.recipientType === 'existing_client' && !this.selectedUserId) {
                    errors.push('• Please select a client');
                } else if (this.recipientType === 'custom_email') {
                    if (!this.customName.trim()) {
                        errors.push('• Recipient name is required');
                    }
                    if (!this.customEmail.trim()) {
                        errors.push('• Email address is required');
                    } else if (!this.isValidEmail(this.customEmail)) {
                        errors.push('• Please enter a valid email address');
                    }
                }
                
                if (errors.length > 0) {
                    alert('Please fix the following errors:\n\n' + errors.join('\n'));
                }
            },
            
            init() {
                this.$watch('recipientType', (value) => {
                    if (value === 'existing_client') {
                        this.customName = '';
                        this.customEmail = '';
                        this.$nextTick(() => {
                            const searchInput = document.querySelector('input[x-model="searchQuery"]');
                            if (searchInput) searchInput.focus();
                        });
                    } else {
                        this.selectedUserId = '';
                        this.$nextTick(() => {
                            const nameField = document.querySelector('[name="custom_name"]');
                            if (nameField) nameField.focus();
                        });
                    }
                });
                
                this.$watch('selectedUserId', (value) => {
                    // This will be updated by the clientSearch component
                });
            }
        }
    }

    function clientSearch() {
        return {
            clients: @js($clients->toArray()),
            filteredClients: [],
            searchQuery: '',
            selectedClient: @js($selectedClient),
            selectedClientId: @js(old('user_id', $selectedClient?->id ?? '')),
            showDropdown: false,
            highlightedIndex: -1,
            
            init() {
                this.filteredClients = this.clients;
                
                // Set initial search query if client is pre-selected
                if (this.selectedClient) {
                    this.searchQuery = this.selectedClient.name;
                }
                
                // Watch for changes to selectedClientId to update parent component
                this.$watch('selectedClientId', (value) => {
                    // Update the parent component's selectedUserId
                    const parentData = this.$el.closest('[x-data*="messageForm"]').__x.$data;
                    if (parentData) {
                        parentData.selectedUserId = value;
                    }
                });
                
                // Position dropdown correctly relative to input
                this.$watch('showDropdown', (value) => {
                    if (value) {
                        this.$nextTick(() => {
                            const dropdown = this.$el.querySelector('[x-show*="showDropdown"]');
                            const input = this.$refs.searchInput;
                            if (dropdown && input) {
                                const rect = input.getBoundingClientRect();
                                dropdown.style.position = 'fixed';
                                dropdown.style.top = (rect.bottom + 4) + 'px';
                                dropdown.style.left = rect.left + 'px';
                                dropdown.style.width = rect.width + 'px';
                                dropdown.style.zIndex = '9999';
                            }
                        });
                    }
                });
            },
            
            filterClients() {
                if (!this.searchQuery.trim()) {
                    this.filteredClients = this.clients;
                    this.highlightedIndex = -1;
                    return;
                }
                
                const query = this.searchQuery.toLowerCase();
                this.filteredClients = this.clients.filter(client => 
                    client.name.toLowerCase().includes(query) ||
                    client.email.toLowerCase().includes(query) ||
                    (client.company && client.company.toLowerCase().includes(query))
                );
                
                this.highlightedIndex = this.filteredClients.length > 0 ? 0 : -1;
                this.showDropdown = true;
            },
            
            selectClient(client) {
                this.selectedClient = client;
                this.selectedClientId = client.id;
                this.searchQuery = client.name;
                this.showDropdown = false;
                this.highlightedIndex = -1;
            },
            
            clearSelection() {
                this.selectedClient = null;
                this.selectedClientId = '';
                this.searchQuery = '';
                this.filteredClients = this.clients;
                this.showDropdown = false;
                this.highlightedIndex = -1;
            },
            
            navigateDown() {
                if (!this.showDropdown) {
                    this.showDropdown = true;
                    return;
                }
                
                if (this.highlightedIndex < this.filteredClients.length - 1) {
                    this.highlightedIndex++;
                }
            },
            
            navigateUp() {
                if (this.highlightedIndex > 0) {
                    this.highlightedIndex--;
                }
            },
            
            selectHighlighted() {
                if (this.highlightedIndex >= 0 && this.filteredClients[this.highlightedIndex]) {
                    this.selectClient(this.filteredClients[this.highlightedIndex]);
                }
            }
        }
    }
</script>

<style>
    [x-cloak] { 
        display: none !important; 
    }
    
    /* Simplified CSS - dropdown now correctly positioned */
    [x-cloak] { 
        display: none !important; 
    }
</style>