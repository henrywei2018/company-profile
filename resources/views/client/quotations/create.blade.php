{{-- resources/views/client/quotations/create.blade.php --}}
<x-layouts.client title="New Quotation Request">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div class="mb-4 lg:mb-0">
            <x-admin.breadcrumb :items="[
                'My Quotations' => route('client.quotations.index'),
                'New Request' => '#'
            ]" />
        </div>
        
        <div class="flex items-center gap-3">
            <x-admin.button href="{{ route('client.quotations.index') }}" color="light" size="sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to List
            </x-admin.button>
        </div>
    </div>

    <form action="{{ route('client.quotations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" x-data="quotationForm()">
        @csrf
        
        <!-- Header Card -->
        <x-admin.card>
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            New Quotation Request
                        </h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                            Tell us about your project requirements and we'll provide you with a detailed quotation
                        </p>
                    </div>
                </div>
            </div>
        </x-admin.card>

        <!-- Project Information -->
        <x-admin.card>
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Information</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                    Provide basic details about your project
                </p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Service -->
                    <div>
                        <label for="service_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Service <span class="text-gray-400">(Optional)</span></label>
                        <select name="service_id" id="service_id" x-model="selectedService" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select a service</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                    {{ $service->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Project Type -->
                    <div>
                        <label for="project_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Type *</label>
                        <input type="text" name="project_type" id="project_type" value="{{ old('project_type') }}" placeholder="e.g., Website Development, Mobile App, etc." required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('project_type')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Location</label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}" placeholder="e.g., New York, Remote, etc."
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('location')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Budget Range -->
                    <div>
                        <label for="budget_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Budget Range</label>
                        <select name="budget_range" id="budget_range" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select budget range</option>
                            <option value="Under $5,000" {{ old('budget_range') === 'Under $5,000' ? 'selected' : '' }}>Under $5,000</option>
                            <option value="$5,000 - $10,000" {{ old('budget_range') === '$5,000 - $10,000' ? 'selected' : '' }}>$5,000 - $10,000</option>
                            <option value="$10,000 - $25,000" {{ old('budget_range') === '$10,000 - $25,000' ? 'selected' : '' }}>$10,000 - $25,000</option>
                            <option value="$25,000 - $50,000" {{ old('budget_range') === '$25,000 - $50,000' ? 'selected' : '' }}>$25,000 - $50,000</option>
                            <option value="$50,000 - $100,000" {{ old('budget_range') === '$50,000 - $100,000' ? 'selected' : '' }}>$50,000 - $100,000</option>
                            <option value="Over $100,000" {{ old('budget_range') === 'Over $100,000' ? 'selected' : '' }}>Over $100,000</option>
                            <option value="Flexible" {{ old('budget_range') === 'Flexible' ? 'selected' : '' }}>Flexible</option>
                        </select>
                        @error('budget_range')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preferred Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" min="{{ now()->addDay()->format('Y-m-d') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('start_date')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority Level</label>
                        <select name="priority" id="priority" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('priority')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </x-admin.card>

        <!-- Project Requirements -->
        <x-admin.card>
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Requirements</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                    Describe your project in detail to help us provide an accurate quotation
                </p>
            </div>
            
            <div class="p-6">
                <!-- Requirements -->
                <div class="mb-6">
                    <label for="requirements" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Requirements *</label>
                    <textarea name="requirements" id="requirements" rows="6" required
                              placeholder="Please describe your project requirements in detail. Include features, functionality, design preferences, technical specifications, and any other relevant information that will help us understand your needs..."
                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('requirements') }}</textarea>
                    <p class="mt-2 text-sm text-gray-500 dark:text-neutral-400">
                        Minimum 50 characters required. Be as detailed as possible for the most accurate quotation.
                    </p>
                    @error('requirements')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Information -->
                <div>
                    <label for="additional_info" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional Information</label>
                    <textarea name="additional_info" id="additional_info" rows="4"
                              placeholder="Any additional information, special requirements, or constraints we should know about..."
                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('additional_info') }}</textarea>
                    @error('additional_info')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-admin.card>

        <!-- Contact Preferences -->
        <x-admin.card>
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Contact Preferences</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                    How would you prefer us to contact you about this quotation?
                </p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Preferred Contact Method -->
                    <div>
                        <label for="preferred_contact_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preferred Contact Method</label>
                        <select name="preferred_contact_method" id="preferred_contact_method" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="email" {{ old('preferred_contact_method', 'email') === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="phone" {{ old('preferred_contact_method') === 'phone' ? 'selected' : '' }}>Phone Call</option>
                            <option value="whatsapp" {{ old('preferred_contact_method') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        </select>
                        @error('preferred_contact_method')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preferred Contact Time -->
                    <div>
                        <label for="preferred_contact_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preferred Contact Time</label>
                        <select name="preferred_contact_time" id="preferred_contact_time" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">No preference</option>
                            <option value="morning" {{ old('preferred_contact_time') === 'morning' ? 'selected' : '' }}>Morning (9 AM - 12 PM)</option>
                            <option value="afternoon" {{ old('preferred_contact_time') === 'afternoon' ? 'selected' : '' }}>Afternoon (12 PM - 5 PM)</option>
                            <option value="evening" {{ old('preferred_contact_time') === 'evening' ? 'selected' : '' }}>Evening (5 PM - 8 PM)</option>
                        </select>
                        @error('preferred_contact_time')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </x-admin.card>

        <!-- File Attachments -->
        <x-admin.card>
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">File Attachments</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                    Upload any relevant files that will help us understand your requirements
                </p>
            </div>
            
            <div class="p-6">
                <div x-data="fileUpload()" class="space-y-4">
                    <!-- File Upload Area -->
                    <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md dark:border-neutral-600">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 dark:text-neutral-400">
                                <label for="attachments" class="relative cursor-pointer bg-white dark:bg-neutral-800 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload files</span>
                                    <input id="attachments" name="attachments[]" type="file" class="sr-only" multiple 
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip,.rar"
                                           @change="handleFiles($event)">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-neutral-400">
                                PDF, DOC, DOCX, JPG, PNG, GIF, ZIP, RAR up to 10MB each
                            </p>
                        </div>
                    </div>

                    <!-- Selected Files -->
                    <div x-show="files.length > 0" class="mt-4">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Selected Files:</h4>
                        <div class="space-y-2">
                            <template x-for="(file, index) in files" :key="index">
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-neutral-700 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-900 dark:text-white" x-text="file.name"></span>
                                        <span class="text-xs text-gray-500 dark:text-neutral-400 ml-2" x-text="formatFileSize(file.size)"></span>
                                    </div>
                                    <button type="button" @click="removeFile(index)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    @error('attachments.*')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-admin.card>

        <!-- Action Buttons -->
        <x-admin.card>
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <x-admin.button href="{{ route('client.quotations.index') }}" color="light">
                            Cancel
                        </x-admin.button>
                        
                        <x-admin.button type="submit" color="primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Submit Quotation Request
                        </x-admin.button>
                    </div>
                </div>
            </div>
        </x-admin.card>
    </form>

    @push('scripts')
    <script>
        function quotationForm() {
            return {
                selectedService: '',
                
                init() {
                    // Initialize any form-specific logic
                }
            }
        }

        function fileUpload() {
            return {
                files: [],
                
                handleFiles(event) {
                    const newFiles = Array.from(event.target.files);
                    this.files = [...this.files, ...newFiles];
                },
                
                removeFile(index) {
                    this.files.splice(index, 1);
                    // Update the file input
                    const input = document.getElementById('attachments');
                    const dt = new DataTransfer();
                    this.files.forEach(file => dt.items.add(file));
                    input.files = dt.files;
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
    @endpush
</x-layouts.client>