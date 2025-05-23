{{-- resources/views/admin/quotations/create.blade.php --}}
<x-layouts.admin title="Create New Quotation" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Quotations' => route('admin.quotations.index'),
            'Create New Quotation' => '#'
        ]" />
    </div>

    <!-- Form Card -->
    <x-admin.card>
        <x-slot name="title">Create New Quotation</x-slot>
        <x-slot name="description">Manually create a quotation request for a client</x-slot>

        <form action="{{ route('admin.quotations.store') }}" method="POST" enctype="multipart/form-data" 
              x-data="quotationCreateForm()" class="space-y-6">
            @csrf
            
            <!-- Client Information Section -->
            <div class="space-y-6">
                <div class="border-b border-gray-200 dark:border-neutral-700 pb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Client Information</h3>
                    <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">Enter client contact details</p>
                </div>

                <!-- Client Search -->
                <div x-data="clientSearch()">
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                        Link Existing Client (Optional)
                    </label>
                    
                    <div class="relative">
                        <input 
                            type="text" 
                            x-model="searchQuery"
                            x-ref="searchInput"
                            @focus="showDropdown = true"
                            @input="filterClients()"
                            @keydown.escape="showDropdown = false"
                            placeholder="Search existing clients..."
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-neutral-700 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:text-white"
                        >
                        
                        <!-- Dropdown -->
                        <div 
                            x-show="showDropdown && filteredClients.length > 0"
                            x-transition
                            @click.away="showDropdown = false"
                            class="absolute z-50 mt-1 w-full bg-white border border-gray-300 dark:bg-neutral-800 dark:border-neutral-700 rounded-md shadow-lg max-h-48 overflow-y-auto"
                        >
                            <template x-for="client in filteredClients" :key="client.id">
                                <div 
                                    @click="selectClient(client)"
                                    class="px-4 py-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-neutral-700"
                                >
                                    <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="client.name"></p>
                                    <p class="text-xs text-gray-500 dark:text-neutral-400" x-text="client.email"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Selected Client Display -->
                    <div x-show="selectedClient" class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-900 dark:text-blue-300" x-text="selectedClient ? selectedClient.name : ''"></p>
                                <p class="text-xs text-blue-700 dark:text-blue-400" x-text="selectedClient ? selectedClient.email : ''"></p>
                            </div>
                            <button type="button" @click="clearSelection()" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <input type="hidden" name="client_id" x-model="selectedClientId">
                </div>

                <!-- Client Form Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-admin.input 
                        label="Full Name" 
                        name="name" 
                        :value="old('name')"
                        required
                        x-model="clientName"
                    />
                    
                    <x-admin.input 
                        label="Email Address" 
                        name="email" 
                        type="email"
                        :value="old('email')"
                        required
                        x-model="clientEmail"
                    />
                    
                    <x-admin.input 
                        label="Phone Number" 
                        name="phone" 
                        :value="old('phone')"
                        x-model="clientPhone"
                    />
                    
                    <x-admin.input 
                        label="Company" 
                        name="company" 
                        :value="old('company')"
                        x-model="clientCompany"
                    />
                </div>
            </div>

            <!-- Project Details Section -->
            <div class="space-y-6">
                <div class="border-b border-gray-200 dark:border-neutral-700 pb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Project Details</h3>
                    <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">Information about the project requirements</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-admin.input 
                        label="Project Type" 
                        name="project_type" 
                        :value="old('project_type')"
                        placeholder="e.g., Office Building, Residential House"
                        required
                    />
                    
                    <x-admin.select 
                        label="Related Service" 
                        name="service_id" 
                        :value="old('service_id')"
                        :options="['' => 'Select a service (optional)'] + $services->pluck('title', 'id')->toArray()"
                    />
                    
                    <x-admin.input 
                        label="Project Location" 
                        name="location" 
                        :value="old('location')"
                    />
                    
                    <x-admin.select 
                        label="Budget Range" 
                        name="budget_range" 
                        :value="old('budget_range')"
                        :options="[
                            '' => 'Select budget range (optional)',
                            '< Rp 500 juta' => '< Rp 500 juta',
                            'Rp 500 juta - 1 miliar' => 'Rp 500 juta - 1 miliar',
                            'Rp 1 - 5 miliar' => 'Rp 1 - 5 miliar',
                            'Rp 5 - 10 miliar' => 'Rp 5 - 10 miliar',
                            '> Rp 10 miliar' => '> Rp 10 miliar'
                        ]"
                    />
                    
                    <x-admin.input 
                        label="Desired Start Date" 
                        name="start_date" 
                        type="date"
                        :value="old('start_date')"
                    />
                    
                    <x-admin.select 
                        label="Priority Level" 
                        name="priority" 
                        :value="old('priority', 'normal')"
                        :options="[
                            'low' => 'Low Priority',
                            'normal' => 'Normal Priority',
                            'high' => 'High Priority',
                            'urgent' => 'Urgent'
                        ]"
                    />
                </div>
                
                <x-admin.textarea 
                    label="Project Requirements" 
                    name="requirements" 
                    :value="old('requirements')"
                    rows="4"
                    placeholder="Detailed description of project requirements..."
                    required
                />
            </div>

            <!-- Admin Settings Section -->
            <div class="space-y-6">
                <div class="border-b border-gray-200 dark:border-neutral-700 pb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Admin Settings</h3>
                    <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">Initial status and estimates</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-admin.select 
                        label="Initial Status" 
                        name="status" 
                        :value="old('status', 'pending')"
                        :options="[
                            'pending' => 'Pending Review',
                            'reviewed' => 'Under Review',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected'
                        ]"
                        required
                    />
                    
                    <x-admin.input 
                        label="Estimated Cost" 
                        name="estimated_cost" 
                        :value="old('estimated_cost')"
                        placeholder="e.g., Rp 2.5M - 3.5M"
                    />
                    
                    <x-admin.input 
                        label="Estimated Timeline" 
                        name="estimated_timeline" 
                        :value="old('estimated_timeline')"
                        placeholder="e.g., 8-12 weeks"
                    />
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-admin.textarea 
                        label="Internal Notes" 
                        name="internal_notes" 
                        :value="old('internal_notes')"
                        rows="3"
                        helper="Internal use only"
                    />
                    
                    <x-admin.textarea 
                        label="Admin Notes" 
                        name="admin_notes" 
                        :value="old('admin_notes')"
                        rows="3"
                        helper="May be shared with client"
                    />
                </div>
            </div>

            <!-- File Attachments Section -->
            <div class="space-y-6" x-data="fileUploader()">
                <div class="border-b border-gray-200 dark:border-neutral-700 pb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">File Attachments</h3>
                    <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">Upload supporting documents (optional)</p>
                </div>

                <div 
                    class="border-2 border-dashed border-gray-300 dark:border-neutral-700 rounded-lg p-6 text-center hover:border-blue-400 dark:hover:border-blue-600 transition-colors"
                    @click="$refs.fileInput.click()"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="handleDrop($event)"
                    :class="{ 'border-blue-400 bg-blue-50 dark:border-blue-600 dark:bg-blue-900/30': isDragging }"
                >
                    <svg class="w-8 h-8 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-neutral-400">
                        <span class="text-blue-600 dark:text-blue-400 cursor-pointer">Click to upload</span> or drag and drop
                    </p>
                    <p class="text-xs text-gray-500 dark:text-neutral-500 mt-1">
                        PDF, DOC, DOCX, XLS, XLSX, JPG, PNG up to 10MB each
                    </p>
                    
                    <input 
                        type="file" 
                        name="attachments[]" 
                        multiple 
                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx"
                        class="hidden"
                        x-ref="fileInput"
                        @change="handleFileSelect($event)"
                    >
                </div>
                
                <!-- File Preview -->
                <div x-show="files.length > 0" class="space-y-2">
                    <template x-for="(file, index) in files" :key="index">
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-neutral-800/50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="file.name"></p>
                                    <p class="text-xs text-gray-500 dark:text-neutral-500" x-text="formatFileSize(file.size)"></p>
                                </div>
                            </div>
                            <button type="button" @click="removeFile(index)" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-neutral-700">
                <x-admin.button href="{{ route('admin.quotations.index') }}" color="light">
                    Cancel
                </x-admin.button>
                
                <div class="flex space-x-3">
                    <x-admin.button type="submit" name="action" value="save_and_continue" color="light">
                        Save & Continue Editing
                    </x-admin.button>
                    
                    <x-admin.button type="submit">
                        Create Quotation
                    </x-admin.button>
                </div>
            </div>
        </form>
    </x-admin.card>
</x-layouts.admin>

<script>
    function quotationCreateForm() {
        return {
            clientName: '',
            clientEmail: '', 
            clientPhone: '',
            clientCompany: ''
        }
    }

    function clientSearch() {
        return {
            clients: @json($clients->toArray()),
            filteredClients: [],
            searchQuery: '',
            selectedClient: null,
            selectedClientId: '',
            showDropdown: false,
            
            init() {
                this.filteredClients = this.clients;
            },
            
            filterClients() {
                if (!this.searchQuery.trim()) {
                    this.filteredClients = this.clients;
                    return;
                }
                
                const query = this.searchQuery.toLowerCase();
                this.filteredClients = this.clients.filter(client => 
                    client.name.toLowerCase().includes(query) ||
                    client.email.toLowerCase().includes(query)
                );
                this.showDropdown = true;
            },
            
            selectClient(client) {
                this.selectedClient = client;
                this.selectedClientId = client.id;
                this.searchQuery = client.name;
                this.showDropdown = false;
                
                // Populate form fields
                const form = this.$el.closest('form');
                if (form) {
                    form.querySelector('[name="name"]').value = client.name;
                    form.querySelector('[name="email"]').value = client.email;
                    form.querySelector('[name="phone"]').value = client.phone || '';
                    form.querySelector('[name="company"]').value = client.company || '';
                }
            },
            
            clearSelection() {
                this.selectedClient = null;
                this.selectedClientId = '';
                this.searchQuery = '';
                this.showDropdown = false;
                
                // Clear form fields
                const form = this.$el.closest('form');
                if (form) {
                    form.querySelector('[name="name"]').value = '';
                    form.querySelector('[name="email"]').value = '';
                    form.querySelector('[name="phone"]').value = '';
                    form.querySelector('[name="company"]').value = '';
                }
            }
        }
    }

    function fileUploader() {
        return {
            files: [],
            isDragging: false,
            
            handleFileSelect(event) {
                this.addFiles(event.target.files);
            },
            
            handleDrop(event) {
                this.isDragging = false;
                this.addFiles(event.dataTransfer.files);
            },
            
            addFiles(fileList) {
                const newFiles = Array.from(fileList);
                newFiles.forEach(file => {
                    if (file.size <= 10 * 1024 * 1024) { // 10MB limit
                        this.files.push(file);
                    }
                });
                this.updateFileInput();
            },
            
            removeFile(index) {
                this.files.splice(index, 1);
                this.updateFileInput();
            },
            
            updateFileInput() {
                const dt = new DataTransfer();
                this.files.forEach(file => dt.items.add(file));
                this.$refs.fileInput.files = dt.files;
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
</x-layouts.admin>