{{-- resources/views/client/quotations/edit.blade.php --}}
<x-layouts.client :title="'Edit Quotation: ' . $quotation->project_type">
    <!-- Compact Header with improved breadcrumb -->
    <div class="mb-6">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ route('client.quotations.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">My Quotations</a></li>
                <li class="text-gray-400">/</li>
                <li><a href="{{ route('client.quotations.show', $quotation) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">{{ Str::limit($quotation->project_type, 25) }}</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-600 dark:text-gray-300">Edit</li>
            </ol>
        </nav>
        
        <!-- Header with status and actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Quotation</h1>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-sm text-gray-500 dark:text-gray-400">#{{ $quotation->quotation_number }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $quotation->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                            {{ $quotation->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                            {{ $quotation->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                            {{ ucfirst($quotation->status) }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('client.quotations.show', $quotation) }}" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </div>

    <!-- Main Form with improved styling -->
    <form action="{{ route('client.quotations.update', $quotation) }}" method="POST" 
          class="space-y-6" x-data="quotationEditForm()" @submit="loading = true">
        @csrf
        @method('PUT')
        
        <!-- Two Column Layout for better space utilization -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content (Left 2 columns) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Contact Information - Compact Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Contact Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" required
                                       value="{{ old('name', $quotation->name) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                              shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors">
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" id="email" required
                                       value="{{ old('email', $quotation->email) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                              shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors">
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Phone Number
                                </label>
                                <input type="text" name="phone" id="phone"
                                       value="{{ old('phone', $quotation->phone) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                              shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors">
                                @error('phone')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Company
                                </label>
                                <input type="text" name="company" id="company"
                                       value="{{ old('company', $quotation->company) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                              shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors">
                                @error('company')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project Details - Compact Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Project Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="service_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Service Category
                                </label>
                                <select name="service_id" id="service_id"
                                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                               shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors">
                                    <option value="">Select a service...</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" 
                                                {{ old('service_id', $quotation->service_id) == $service->id ? 'selected' : '' }}>
                                            {{ $service->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="project_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Project Type <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="project_type" id="project_type" required
                                       value="{{ old('project_type', $quotation->project_type) }}"
                                       placeholder="e.g., Website Development, Mobile App"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                              shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors">
                                @error('project_type')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Project Location
                                </label>
                                <input type="text" name="location" id="location"
                                       value="{{ old('location', $quotation->location) }}"
                                       placeholder="e.g., New York, Remote"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                              shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors">
                                @error('location')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
    <label for="budget" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Project Budget
        <span class="text-red-500">*</span>
    </label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <span class="text-gray-500 text-sm">Rp.</span>
        </div>
        <input type="text" 
               name="budget" 
               id="budget"
               value="{{ old('budget', $quotation->budget ?? '') }}"
               required
               placeholder="Enter your project budget"
               class="block w-full pl-12 pr-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                      shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors budget-input"
               onInput="formatBudgetInput(this)"
               onBlur="validateBudget(this)">
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
        </div>
    </div>
    
    {{-- Budget Range Indicators --}}
    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
        <div class="flex flex-wrap gap-2">
            <span class="budget-range px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600" 
                  onclick="setBudgetValue(1000000)">1</span>
            <span class="budget-range px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600" 
                  onclick="setBudgetValue(5000000)">5</span>
            <span class="budget-range px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600" 
                  onclick="setBudgetValue(10000000)">10</span>
            <span class="budget-range px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600" 
                  onclick="setBudgetValue(25000000)">25</span>
            <span class="budget-range px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600" 
                  onclick="setBudgetValue(50000000)">50</span>
        </div>
        <p class="mt-1">Click on amounts above for quick selection, or enter your custom budget</p>
    </div>
    
    {{-- Budget Validation Messages --}}
    <div id="budget-feedback" class="mt-1 text-xs hidden">
        <div class="budget-warning text-yellow-600 dark:text-yellow-400 hidden">
            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            Budget seems quite low for typical construction projects
        </div>
        <div class="budget-success text-green-600 dark:text-green-400 hidden">
            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Budget looks good for your project scope
        </div>
    </div>
    
    @error('budget')
        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
                            
                            <div class="md:col-span-2">
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Preferred Start Date
                                </label>
                                <input type="date" name="start_date" id="start_date"
                                       value="{{ old('start_date', $quotation->start_date ? $quotation->start_date->format('Y-m-d') : '') }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                              shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors">
                                @error('start_date')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requirements - Full width -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Project Requirements
                        </h3>
                        
                        <div>
                            <label for="requirements" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Detailed Requirements <span class="text-red-500">*</span>
                            </label>
                            <textarea name="requirements" id="requirements" rows="6" required
                                      placeholder="Please describe your project requirements in detail..."
                                      class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                             shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors resize-none">{{ old('requirements', $quotation->requirements) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Be as detailed as possible for the most accurate quotation.
                            </p>
                            @error('requirements')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar (Right column) -->
            <div class="space-y-6">
                
                <!-- Current Attachments -->
                @if($quotation->attachments && $quotation->attachments->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6" x-data="attachmentManager()">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            Current Attachments ({{ $quotation->attachments->count() }})
                        </h3>
                        
                        <div class="space-y-3">
                            @foreach($quotation->attachments as $attachment)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center space-x-3 min-w-0 flex-1">
                                        <div class="flex-shrink-0 w-8 h-8 {{ $attachment->file_icon ?? 'bg-gray-100 dark:bg-gray-600' }} rounded flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $attachment->file_name }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $attachment->formatted_file_size }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-1 ml-3">
                                        <!-- Download -->
                                        <a href="{{ route('client.quotations.download-attachment', [$quotation, $attachment]) }}" 
                                           class="p-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>
                                        <!-- Delete -->
                                        <button type="button" 
                                                @click="deleteAttachment({{ $attachment->id }})"
                                                :disabled="deleting"
                                                class="p-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 disabled:opacity-50 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Help & Tips -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="p-6">
                        <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Tips for Better Quotes
                        </h3>
                        <ul class="text-xs text-blue-800 dark:text-blue-300 space-y-2">
                            <li class="flex items-start">
                                <span class="inline-block w-1 h-1 bg-blue-400 rounded-full mt-2 mr-2 flex-shrink-0"></span>
                                Provide detailed requirements for more accurate pricing
                            </li>
                            <li class="flex items-start">
                                <span class="inline-block w-1 h-1 bg-blue-400 rounded-full mt-2 mr-2 flex-shrink-0"></span>
                                Include any existing materials or references
                            </li>
                            <li class="flex items-start">
                                <span class="inline-block w-1 h-1 bg-blue-400 rounded-full mt-2 mr-2 flex-shrink-0"></span>
                                Specify your preferred timeline and budget range
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Action Buttons - Sticky on mobile -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 sticky top-6">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit" 
                                    :disabled="loading"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-lg 
                                           text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 
                                           focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed 
                                           transition-colors">
                                <svg x-show="!loading" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <svg x-show="loading" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="loading ? 'Updating...' : 'Update Quotation'"></span>
                            </button>
                            
                            <a href="{{ route('client.quotations.show', $quotation) }}" 
                               class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-300 dark:border-gray-600 
                                      rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 
                                      hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Cancel
                            </a>
                        </div>
                        
                        <p class="mt-4 text-xs text-gray-500 dark:text-gray-400 text-center">
                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            Updating will reset status to "Pending" for review
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        function quotationEditForm() {
            return {
                loading: false,
                init() {
                    // Auto-save functionality could go here
                }
            }
        }

        function attachmentManager() {
            return {
                deleting: false,
                
                async deleteAttachment(attachmentId) {
                    if (!confirm('Are you sure you want to delete this attachment?')) {
                        return;
                    }

                    this.deleting = true;

                    try {
                        // Fix: Properly construct the route with both parameters
                        const deleteUrl = "{{ route('client.quotations.delete-attachment', [$quotation->id, ':attachmentId']) }}".replace(':attachmentId', attachmentId);
                        
                        const response = await fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Remove the attachment element from the DOM with smooth animation
                            const attachmentElement = event.target.closest('.flex.items-center.justify-between');
                            attachmentElement.style.transition = 'all 0.3s ease';
                            attachmentElement.style.opacity = '0';
                            attachmentElement.style.transform = 'translateX(100%)';
                            
                            setTimeout(() => {
                                attachmentElement.remove();
                                this.showNotification('Attachment deleted successfully', 'success');
                            }, 300);
                        } else {
                            this.showNotification(data.message || 'Failed to delete attachment', 'error');
                        }
                    } catch (error) {
                        console.error('Error deleting attachment:', error);
                        this.showNotification('Failed to delete attachment', 'error');
                    } finally {
                        this.deleting = false;
                    }
                },

                showNotification(message, type) {
                    // Modern toast notification
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm w-full transform transition-all duration-300 ease-in-out ${
                        type === 'success' 
                            ? 'bg-green-500 text-white' 
                            : 'bg-red-500 text-white'
                    }`;
                    
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                ${type === 'success' 
                                    ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
                                    : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
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
                    
                    // Trigger entrance animation
                    setTimeout(() => {
                        notification.style.transform = 'translateX(0)';
                    }, 10);
                    
                    // Auto remove after 5 seconds
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.style.transform = 'translateX(100%)';
                            setTimeout(() => {
                                if (notification.parentElement) {
                                    notification.remove();
                                }
                            }, 300);
                        }
                    }, 5000);
                }
            }
        }

        // Form validation and UX improvements
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-resize textareas
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
            });

            // Enhanced form validation feedback
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input, select, textarea');
            
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
                const errorElement = field.parentElement.querySelector('.text-red-600, .text-red-400');
                
                if (isValid) {
                    field.classList.remove('border-red-500', 'dark:border-red-500');
                    field.classList.add('border-green-500', 'dark:border-green-500');
                    if (errorElement && !errorElement.textContent.includes('{{')) {
                        errorElement.style.display = 'none';
                    }
                } else {
                    field.classList.remove('border-green-500', 'dark:border-green-500');
                    field.classList.add('border-red-500', 'dark:border-red-500');
                }
                
                setTimeout(() => {
                    field.classList.remove('border-green-500', 'dark:border-green-500');
                }, 2000);
            }

            // Smooth scrolling for better UX
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Format budget input with thousand separators
function formatBudgetInput(element) {
    let value = element.value || element.target.value;
    
    // Remove all non-numeric characters except decimal point
    value = value.replace(/[^0-9]/g, '');
    
    // Don't format if empty
    if (!value) {
        if (element.target) {
            element.target.value = '';
        } else {
            element.value = '';
        }
        return;
    }
    
    // Convert to number and back to string to remove leading zeros
    value = parseInt(value).toString();
    
    // Add thousand separators
    const formatted = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    
    // Update the input
    if (element.target) {
        element.target.value = formatted;
    } else {
        element.value = formatted;
    }
    
    // Update Alpine.js model if available
    if (typeof Alpine !== 'undefined' && element.target && element.target._x_model) {
        Alpine.store('quotationForm').formData.budget = value;
    }
}

// Set budget value from quick selection buttons
function setBudgetValue(amount) {
    const budgetInput = document.getElementById('budget');
    if (budgetInput) {
        budgetInput.value = amount.toLocaleString('id-ID');
        budgetInput.focus();
        
        // Trigger input event for Alpine.js
        const event = new Event('input', { bubbles: true });
        budgetInput.dispatchEvent(event);
        
        // Validate the budget
        validateBudget(budgetInput);
    }
}

// Validate budget and show feedback
function validateBudget(element) {
    const input = element.target || element;
    const value = parseInt(input.value.replace(/[^0-9]/g, ''));
    const feedbackContainer = document.getElementById('budget-feedback');
    const warningDiv = feedbackContainer?.querySelector('.budget-warning');
    const successDiv = feedbackContainer?.querySelector('.budget-success');
    
    if (!feedbackContainer || !value) {
        feedbackContainer?.classList.add('hidden');
        return;
    }
    
    // Hide all feedback first
    warningDiv?.classList.add('hidden');
    successDiv?.classList.add('hidden');
    
    // Show feedback based on budget amount
    if (value < 5000000) { // Less than 5M IDR
        warningDiv?.classList.remove('hidden');
        feedbackContainer.classList.remove('hidden');
    } else if (value >= 5000000) { // 5M IDR or more
        successDiv?.classList.remove('hidden');
        feedbackContainer.classList.remove('hidden');
    } else {
        feedbackContainer.classList.add('hidden');
    }
}

// Initialize budget formatting on page load
document.addEventListener('DOMContentLoaded', function() {
    const budgetInput = document.getElementById('budget');
    if (budgetInput && budgetInput.value) {
        formatBudgetInput(budgetInput);
        validateBudget(budgetInput);
    }
});
    </script>
    @endpush

    <style>
        /* Custom scrollbar for sidebar */
        .sticky {
            scroll-margin-top: 1.5rem;
        }
        
        /* Smooth transitions for form elements */
        input, select, textarea {
            transition: all 0.2s ease-in-out;
        }
        
        input:focus, select:focus, textarea:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }
        
        /* Loading state for buttons */
        button:disabled {
            cursor: not-allowed;
        }
        
        /* Attachment hover effects */
        .attachment-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Better focus indicators for accessibility */
        button:focus, a:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .sticky {
                position: relative !important;
                top: auto !important;
            }
        }
        
        /* Dark mode enhancements */
        @media (prefers-color-scheme: dark) {
            .attachment-item:hover {
                box-shadow: 0 2px 8px rgba(255, 255, 255, 0.1);
            }
        }
    </style>
</x-layouts.client>