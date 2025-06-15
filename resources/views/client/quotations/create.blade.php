{{-- resources/views/client/quotations/create.blade.php --}}
<x-layouts.client>
    <x-slot name="title">Request a Quotation</x-slot>
    <x-slot name="description">Tell us about your project and we'll get back to you with a detailed quote.</x-slot>

    <!-- Progress Header -->
    <div class="mb-8">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Request a Quotation</h1>
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">Get a detailed quote for your project in 3 easy steps</p>
            </div>
            
            <!-- Progress Steps -->
            <div class="flex items-center justify-center space-x-8" x-data="{ currentStep: 1 }">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white font-semibold">1</div>
                    <span class="ml-2 text-sm font-medium text-blue-600 dark:text-blue-400">Contact Info</span>
                </div>
                <div class="w-16 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 font-semibold">2</div>
                    <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Project Details</span>
                </div>
                <div class="w-16 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 font-semibold">3</div>
                    <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Requirements</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form Container -->
    <div class="max-w-4xl mx-auto">
        <form id="quotation-form" 
              action="{{ route('client.quotations.store') }}" 
              method="POST" 
              enctype="multipart/form-data"
              x-data="quotationFormHandler()"
              @submit="handleSubmit">
            @csrf

            <!-- Step 1: Personal Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6"
                 x-show="currentStep === 1" x-transition>
                <div class="p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Contact Information</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Let us know how to reach you</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   required
                                   value="{{ old('name', auth()->user()->name) }}"
                                   x-model="formData.name"
                                   class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                          shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200"
                                   placeholder="Enter your full name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   required
                                   value="{{ old('email', auth()->user()->email) }}"
                                   x-model="formData.email"
                                   class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                          shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200"
                                   placeholder="your@email.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Phone Number
                            </label>
                            <input type="tel" 
                                   name="phone" 
                                   id="phone"
                                   value="{{ old('phone', auth()->user()->phone) }}"
                                   x-model="formData.phone"
                                   class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                          shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200"
                                   placeholder="+1 (555) 123-4567">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Company Name
                            </label>
                            <input type="text" 
                                   name="company" 
                                   id="company"
                                   value="{{ old('company', auth()->user()->company) }}"
                                   x-model="formData.company"
                                   class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                          shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200"
                                   placeholder="Your company name">
                            @error('company')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end mt-8">
                        <button type="button" 
                                @click="nextStep()"
                                :disabled="!isStep1Valid()"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 
                                       disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                            Next: Project Details
                            <svg class="w-4 h-4 ml-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Project Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6"
                 x-show="currentStep === 2" x-transition>
                <div class="p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Project Details</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Tell us about your project</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="service_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Service Category
                            </label>
                            <select name="service_id" 
                                    id="service_id"
                                    x-model="formData.service_id"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200">
                                <option value="">Select a service category...</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="project_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Project Type <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="project_type" 
                                   id="project_type" 
                                   required
                                   value="{{ old('project_type') }}"
                                   x-model="formData.project_type"
                                   class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                          shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200"
                                   placeholder="e.g., Website Development, Mobile App">
                            @error('project_type')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Project Location
                            </label>
                            <input type="text" 
                                   name="location" 
                                   id="location"
                                   value="{{ old('location') }}"
                                   x-model="formData.location"
                                   class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                          shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200"
                                   placeholder="e.g., New York, Remote, Global">
                            @error('location')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="budget_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Budget Range
                            </label>
                            <select name="budget_range" 
                                    id="budget_range"
                                    x-model="formData.budget_range"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200">
                                <option value="">Select your budget range...</option>
                                <option value="Under $1,000" {{ old('budget_range') == 'Under $1,000' ? 'selected' : '' }}>Under $1,000</option>
                                <option value="$1,000 - $5,000" {{ old('budget_range') == '$1,000 - $5,000' ? 'selected' : '' }}>$1,000 - $5,000</option>
                                <option value="$5,000 - $10,000" {{ old('budget_range') == '$5,000 - $10,000' ? 'selected' : '' }}>$5,000 - $10,000</option>
                                <option value="$10,000 - $25,000" {{ old('budget_range') == '$10,000 - $25,000' ? 'selected' : '' }}>$10,000 - $25,000</option>
                                <option value="$25,000 - $50,000" {{ old('budget_range') == '$25,000 - $50,000' ? 'selected' : '' }}>$25,000 - $50,000</option>
                                <option value="$50,000+" {{ old('budget_range') == '$50,000+' ? 'selected' : '' }}>$50,000+</option>
                            </select>
                            @error('budget_range')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Preferred Start Date
                            </label>
                            <input type="date" 
                                   name="start_date" 
                                   id="start_date"
                                   value="{{ old('start_date') }}"
                                   x-model="formData.start_date"
                                   min="{{ date('Y-m-d') }}"
                                   class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                          shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="button" 
                                @click="prevStep()"
                                class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium 
                                       hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </button>

                        <button type="button" 
                                @click="nextStep()"
                                :disabled="!isStep2Valid()"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 
                                       disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                            Next: Requirements
                            <svg class="w-4 h-4 ml-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Requirements & Attachments -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6"
                 x-show="currentStep === 3" x-transition>
                <div class="p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Project Requirements</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Provide detailed information about your needs</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="requirements" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Detailed Requirements <span class="text-red-500">*</span>
                            </label>
                            <textarea name="requirements" 
                                      id="requirements" 
                                      rows="8" 
                                      required
                                      x-model="formData.requirements"
                                      class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                             shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200 resize-none"
                                      placeholder="Please describe your project requirements in detail. Include:&#10;&#10;• What you want to achieve&#10;• Key features and functionality&#10;• Target audience&#10;• Any specific technologies or platforms&#10;• Design preferences&#10;• Timeline expectations&#10;• Any other important details...">{{ old('requirements') }}</textarea>
                            <div class="mt-2 flex items-center justify-between">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Be as detailed as possible for the most accurate quotation.
                                </p>
                                <span class="text-xs text-gray-400" x-text="formData.requirements.length + ' characters'"></span>
                            </div>
                            @error('requirements')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- File Upload Section using Universal Uploader -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Project Files (Optional)</h4>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Max 5 files, 10MB each</span>
                            </div>
                            
                            <x-universal-file-uploader
                                name="files"
                                :multiple="true"
                                :max-files="5"
                                max-file-size="10MB"
                                :accepted-file-types="[
                                    'application/pdf',
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'application/vnd.ms-excel',
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    'image/jpeg',
                                    'image/png',
                                    'image/gif',
                                    'application/zip',
                                    'application/x-rar-compressed',
                                    'text/plain',
                                    'text/csv'
                                ]"
                                upload-endpoint="{{ route('client.quotations.upload-attachment') }}"
                                delete-endpoint="{{ route('client.quotations.delete-temp-file') }}"
                                drop-description="Drop project files here or click to browse"
                                :auto-upload="true"
                                :upload-on-drop="true"
                                :show-progress="true"
                                theme="modern"
                                id="quotation-attachments-uploader"
                                container-class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6"
                                :existing-files="[]"
                            />

                            @error('attachments')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="button" 
                                @click="prevStep()"
                                class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium 
                                       hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </button>

                        <button type="submit" 
                                :disabled="submitting || !isStep3Valid()"
                                class="px-8 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 
                                       focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 
                                       disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 relative">
                            <span x-show="!submitting" class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Submit Quotation Request
                            </span>
                            <span x-show="submitting" class="flex items-center">
                                <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Submitting...
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary Card (Fixed sidebar on larger screens) -->
            <div class="fixed top-24 right-8 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hidden xl:block"
                 x-show="currentStep > 1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quotation Summary</h3>
                
                <div class="space-y-3 text-sm">
                    <div x-show="formData.name">
                        <span class="text-gray-600 dark:text-gray-400">Contact:</span>
                        <span class="text-gray-900 dark:text-white font-medium" x-text="formData.name"></span>
                    </div>
                    
                    <div x-show="formData.email">
                        <span class="text-gray-600 dark:text-gray-400">Email:</span>
                        <span class="text-gray-900 dark:text-white font-medium" x-text="formData.email"></span>
                    </div>
                    
                    <div x-show="formData.project_type">
                        <span class="text-gray-600 dark:text-gray-400">Project:</span>
                        <span class="text-gray-900 dark:text-white font-medium" x-text="formData.project_type"></span>
                    </div>
                    
                    <div x-show="formData.budget_range">
                        <span class="text-gray-600 dark:text-gray-400">Budget:</span>
                        <span class="text-gray-900 dark:text-white font-medium" x-text="formData.budget_range"></span>
                    </div>
                    
                    <div x-show="formData.location">
                        <span class="text-gray-600 dark:text-gray-400">Location:</span>
                        <span class="text-gray-900 dark:text-white font-medium" x-text="formData.location"></span>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                        <p>✓ Fast response within 24 hours</p>
                        <p>✓ Detailed project breakdown</p>
                        <p>✓ Transparent pricing</p>
                        <p>✓ Free consultation included</p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function quotationFormHandler() {
            return {
                currentStep: 1,
                submitting: false,
                uploadedFiles: [],
                tempSession: @json(session()->getId()),
                formData: {
                    name: @json(old('name', auth()->user()->name)),
                    email: @json(old('email', auth()->user()->email)),
                    phone: @json(old('phone', auth()->user()->phone ?? '')),
                    company: @json(old('company', auth()->user()->company ?? '')),
                    service_id: @json(old('service_id', '')),
                    project_type: @json(old('project_type', '')),
                    location: @json(old('location', '')),
                    budget_range: @json(old('budget_range', '')),
                    start_date: @json(old('start_date', '')),
                    requirements: @json(old('requirements', ''))
                },

                init() {
                    console.log('=== QUOTATION FORM DEBUG ===');
                    console.log('Session ID:', this.tempSession);
                    console.log('Upload endpoint:', @json(route('client.quotations.upload-attachment')));
                    console.log('Quotation form handler initialized');
                    
                    // Update progress indicator based on current step
                    this.updateProgressIndicator();
                    
                    // Auto-save to localStorage
                    this.$watch('formData', () => {
                        localStorage.setItem('quotationFormData', JSON.stringify(this.formData));
                    }, { deep: true });

                    // Load saved data from localStorage
                    const savedData = localStorage.getItem('quotationFormData');
                    if (savedData) {
                        try {
                            const parsed = JSON.parse(savedData);
                            Object.assign(this.formData, parsed);
                        } catch (e) {
                            console.log('Failed to load saved form data');
                        }
                    }

                    // Load existing temp files on page load
                    this.loadExistingTempFiles();
                    
                    // Listen for file upload events from universal uploader
                    document.addEventListener('files-uploaded', (event) => {
                        console.log('Files uploaded event received:', event.detail);
                        if (event.detail.component === 'quotation-attachments-uploader') {
                            this.handleFilesUploaded(event.detail);
                        }
                    });
                    
                    // Listen for file removal events
                    document.addEventListener('file-removed', (event) => {
                        console.log('File removed event received:', event.detail);
                        if (event.detail.component === 'quotation-attachments-uploader') {
                            this.handleFileRemoved(event.detail);
                        }
                    });

                    // Listen for upload errors
                    document.addEventListener('upload-error', (event) => {
                        console.log('Upload error event received:', event.detail);
                        this.showNotification('Upload failed: ' + event.detail.error, 'error');
                    });
                },

                async loadExistingTempFiles() {
                    console.log('Loading existing temp files...');
                    try {
                        const response = await fetch(@json(route('client.quotations.get-temp-files')), {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();
                        console.log('Get temp files response:', result);
                        
                        if (result.success && result.files.length > 0) {
                            this.uploadedFiles = result.files;
                            console.log('Loaded temp files:', this.uploadedFiles);
                            
                            // Dispatch event to update uploader component
                            this.$dispatch('load-existing-files', {
                                files: result.files,
                                component: 'quotation-attachments-uploader'
                            });
                        }
                    } catch (error) {
                        console.warn('Could not load existing temp files:', error);
                    }
                },
                
                handleFilesUploaded(detail) {
                    console.log('Handling files uploaded:', detail);
                    
                    if (detail.files && Array.isArray(detail.files)) {
                        this.uploadedFiles = [...this.uploadedFiles, ...detail.files];
                        console.log('Updated uploaded files array:', this.uploadedFiles);
                        
                        this.showNotification(detail.result?.message || 'Files uploaded successfully!', 'success');
                    }
                },

                handleFileRemoved(detail) {
                    console.log('Handling file removed:', detail);
                    
                    // Remove from our local array
                    if (detail.fileId) {
                        this.uploadedFiles = this.uploadedFiles.filter(file => 
                            file.temp_id !== detail.fileId && file.id !== detail.fileId
                        );
                        console.log('Updated uploaded files after removal:', this.uploadedFiles);
                    }
                },

                nextStep() {
                    if (this.currentStep < 3) {
                        this.currentStep++;
                        this.updateProgressIndicator();
                        this.scrollToTop();
                    }
                },

                prevStep() {
                    if (this.currentStep > 1) {
                        this.currentStep--;
                        this.updateProgressIndicator();
                        this.scrollToTop();
                    }
                },

                updateProgressIndicator() {
                    // Update progress steps visual state
                    const steps = document.querySelectorAll('.flex.items-center');
                    steps.forEach((step, index) => {
                        const circle = step.querySelector('div');
                        const text = step.querySelector('span');
                        
                        if (index + 1 <= this.currentStep) {
                            circle.className = 'flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white font-semibold';
                            text.className = 'ml-2 text-sm font-medium text-blue-600 dark:text-blue-400';
                        } else {
                            circle.className = 'flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 font-semibold';
                            text.className = 'ml-2 text-sm font-medium text-gray-500 dark:text-gray-400';
                        }
                    });
                },

                scrollToTop() {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                isStep1Valid() {
                    return this.formData.name && this.formData.email;
                },

                isStep2Valid() {
                    return this.formData.project_type;
                },

                isStep3Valid() {
                    return this.formData.requirements && this.formData.requirements.length >= 50;
                },

                handleSubmit(event) {
                    if (!this.isStep3Valid()) {
                        event.preventDefault();
                        this.showNotification('Please provide detailed requirements (minimum 50 characters)', 'error');
                        return;
                    }
                    
                    this.submitting = true;
                    
                    // Clear saved data on successful submission
                    localStorage.removeItem('quotationFormData');
                },

                showNotification(message, type) {
                    // Create toast notification
                    const notification = document.createElement('div');
                    notification.className = `fixed bottom-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm w-full transform transition-all duration-300 ease-in-out ${
                        type === 'success' ? 'bg-green-500 text-white' : 
                        type === 'error' ? 'bg-red-500 text-white' : 
                        'bg-blue-500 text-white'
                    }`;
                    
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                ${type === 'success' 
                                    ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
                                    : type === 'error'
                                    ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
                                    : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                                }
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium">${message}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    `;
                    
                    // Add entrance animation
                    notification.style.transform = 'translateX(100%)';
                    document.body.appendChild(notification);
                    
                    setTimeout(() => {
                        notification.style.transform = 'translateX(0)';
                    }, 10);
                    
                    // Auto remove after 4 seconds
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.style.transform = 'translateX(100%)';
                            setTimeout(() => {
                                if (notification.parentElement) {
                                    notification.remove();
                                }
                            }, 300);
                        }
                    }, 4000);
                }
            }
        }

        // Enhanced form validation and UX improvements
        document.addEventListener('DOMContentLoaded', function() {
            // Real-time validation feedback
            const inputs = document.querySelectorAll('input, select, textarea');
            
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });
                
                input.addEventListener('input', function() {
                    if (this.classList.contains('border-red-500')) {
                        validateField(this);
                    }
                });
            });

            function validateField(field) {
                const isValid = field.checkValidity();
                
                if (isValid) {
                    field.classList.remove('border-red-500', 'dark:border-red-500');
                    field.classList.add('border-green-500', 'dark:border-green-500');
                    
                    setTimeout(() => {
                        field.classList.remove('border-green-500', 'dark:border-green-500');
                    }, 2000);
                } else {
                    field.classList.remove('border-green-500', 'dark:border-green-500');
                    field.classList.add('border-red-500', 'dark:border-red-500');
                }
            }

            // Auto-resize textarea
            const textarea = document.getElementById('requirements');
            if (textarea) {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
            }

            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.ctrlKey) {
                    const form = document.getElementById('quotation-form');
                    const currentStep = parseInt(form.querySelector('[x-data]').__x.$data.currentStep);
                    
                    if (currentStep < 3) {
                        e.preventDefault();
                        form.querySelector('[x-data]').__x.$data.nextStep();
                    }
                }
            });

            // Prevent accidental form loss
            window.addEventListener('beforeunload', function(e) {
                const form = document.getElementById('quotation-form');
                const formData = new FormData(form);
                let hasData = false;
                
                for (let [key, value] of formData.entries()) {
                    if (value && value.toString().trim() !== '') {
                        hasData = true;
                        break;
                    }
                }
                
                if (hasData && !form.querySelector('[x-data]').__x.$data.submitting) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        });
    </script>
    @endpush

    <style>
        /* Custom scrollbar and animations */
        .step-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Enhanced form styling */
        input:focus, select:focus, textarea:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        /* Progress indicator animations */
        .progress-step {
            transition: all 0.3s ease-in-out;
        }

        /* File upload area styling */
        .file-upload-area:hover {
            border-color: #3b82f6;
            background-color: rgba(59, 130, 246, 0.05);
        }

        /* Button hover effects */
        button:hover:not(:disabled) {
            transform: translateY(-1px);
        }

        button:active:not(:disabled) {
            transform: translateY(0);
        }

        /* Loading spinner */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Responsive improvements */
        @media (max-width: 1280px) {
            .fixed.top-24.right-8 {
                display: none !important;
            }
        }

        /* Dark mode improvements */
        @media (prefers-color-scheme: dark) {
            .file-upload-area:hover {
                background-color: rgba(59, 130, 246, 0.1);
            }
        }

        /* Step indicator improvements */
        .step-indicator {
            position: relative;
        }

        .step-indicator::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, #e5e7eb);
            transform: translateY(-50%);
        }

        .step-indicator:last-child::after {
            display: none;
        }
    </style>
</x-layouts.client>