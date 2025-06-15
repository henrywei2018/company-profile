{{-- resources/views/client/quotations/edit.blade.php --}}
<x-layouts.client :title="'Edit Quotation: ' . $quotation->project_type">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div class="mb-4 lg:mb-0">
            <x-admin.breadcrumb :items="[
                'My Quotations' => route('client.quotations.index'),
                $quotation->project_type => route('client.quotations.show', $quotation),
                'Edit' => '#'
            ]" />
        </div>
        
        <div class="flex items-center gap-3">
            <x-admin.button href="{{ route('client.quotations.show', $quotation) }}" color="light" size="sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Details
            </x-admin.button>
        </div>
    </div>

    <form action="{{ route('client.quotations.update', $quotation) }}" method="POST" class="space-y-8" x-data="quotationEditForm()">
        @csrf
        @method('PUT')
        
        <!-- Header Card -->
        <x-admin.card>
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Edit Quotation Request
                        </h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                            Update your quotation details
                            @if($quotation->quotation_number)
                                • #{{ $quotation->quotation_number }}
                            @endif
                        </p>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <x-admin.badge 
                            :type="match($quotation->status) {
                                'pending' => 'warning',
                                'reviewed' => 'info',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'default'
                            }"
                        >
                            {{ ucfirst($quotation->status) }}
                        </x-admin.badge>
                    </div>
                </div>
            </div>
            
            <!-- Status Notice -->
            @if($quotation->status === 'reviewed')
                <div class="px-6 py-4 bg-blue-50 dark:bg-blue-900/20 border-b border-gray-200 dark:border-neutral-700">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700 dark:text-blue-200">
                                <strong>Note:</strong> This quotation is currently under review. Updating it will reset the status to "Pending" for re-evaluation.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </x-admin.card>

        <!-- Project Requirements -->
        <x-admin.card>
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Requirements</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                    Update your project requirements and details
                </p>
            </div>
            
            <div class="p-6">
                <!-- Requirements -->
                <div class="mb-6">
                    <label for="requirements" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Requirements *</label>
                    <textarea name="requirements" id="requirements" rows="6" required
                              placeholder="Please describe your project requirements in detail..."
                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('requirements', $quotation->requirements) }}</textarea>
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
                              placeholder="Any additional information, special requirements, or constraints..."
                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('additional_info', $quotation->additional_info) }}</textarea>
                    @error('additional_info')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-admin.card>

        <!-- Project Details -->
        <x-admin.card>
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Details</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                    Update basic project information
                </p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Location</label>
                        <input type="text" name="location" id="location" value="{{ old('location', $quotation->location) }}" placeholder="e.g., New York, Remote, etc."
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
                            <option value="Under $5,000" {{ old('budget_range', $quotation->budget_range) === 'Under $5,000' ? 'selected' : '' }}>Under $5,000</option>
                            <option value="$5,000 - $10,000" {{ old('budget_range', $quotation->budget_range) === '$5,000 - $10,000' ? 'selected' : '' }}>$5,000 - $10,000</option>
                            <option value="$10,000 - $25,000" {{ old('budget_range', $quotation->budget_range) === '$10,000 - $25,000' ? 'selected' : '' }}>$10,000 - $25,000</option>
                            <option value="$25,000 - $50,000" {{ old('budget_range', $quotation->budget_range) === '$25,000 - $50,000' ? 'selected' : '' }}>$25,000 - $50,000</option>
                            <option value="$50,000 - $100,000" {{ old('budget_range', $quotation->budget_range) === '$50,000 - $100,000' ? 'selected' : '' }}>$50,000 - $100,000</option>
                            <option value="Over $100,000" {{ old('budget_range', $quotation->budget_range) === 'Over $100,000' ? 'selected' : '' }}>Over $100,000</option>
                            <option value="Flexible" {{ old('budget_range', $quotation->budget_range) === 'Flexible' ? 'selected' : '' }}>Flexible</option>
                        </select>
                        @error('budget_range')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preferred Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $quotation->start_date?->format('Y-m-d')) }}" min="{{ now()->addDay()->format('Y-m-d') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('start_date')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Preferences -->
                    <div>
                        <label for="preferred_contact_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preferred Contact Method</label>
                        <select name="preferred_contact_method" id="preferred_contact_method" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="email" {{ old('preferred_contact_method', $quotation->preferred_contact_method ?? 'email') === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="phone" {{ old('preferred_contact_method', $quotation->preferred_contact_method) === 'phone' ? 'selected' : '' }}>Phone Call</option>
                            <option value="whatsapp" {{ old('preferred_contact_method', $quotation->preferred_contact_method) === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        </select>
                        @error('preferred_contact_method')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preferred Contact Time -->
                    <div class="md:col-span-2">
                        <label for="preferred_contact_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preferred Contact Time</label>
                        <select name="preferred_contact_time" id="preferred_contact_time" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">No preference</option>
                            <option value="morning" {{ old('preferred_contact_time', $quotation->preferred_contact_time) === 'morning' ? 'selected' : '' }}>Morning (9 AM - 12 PM)</option>
                            <option value="afternoon" {{ old('preferred_contact_time', $quotation->preferred_contact_time) === 'afternoon' ? 'selected' : '' }}>Afternoon (12 PM - 5 PM)</option>
                            <option value="evening" {{ old('preferred_contact_time', $quotation->preferred_contact_time) === 'evening' ? 'selected' : '' }}>Evening (5 PM - 8 PM)</option>
                        </select>
                        @error('preferred_contact_time')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </x-admin.card>

        <!-- Current Attachments -->
        @if($quotation->attachments->count() > 0)
            <x-admin.card>
                <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Current Attachments</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                        Manage your uploaded files
                    </p>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" x-data="attachmentManager()">
                        @foreach($quotation->attachments as $attachment)
                            <div class="relative border border-gray-300 dark:border-neutral-600 rounded-lg p-4" 
                                 x-data="{ deleting: false }" 
                                 :class="{ 'opacity-50': deleting }">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $attachment->file_icon }}">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $attachment->file_name }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-neutral-400">
                                                {{ $attachment->formatted_file_size }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 ml-3">
                                        <!-- Download -->
                                        <a href="{{ route('client.quotations.attachments.download', [$quotation, $attachment]) }}" 
                                           class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>
                                        <!-- Delete -->
                                        <button type="button" 
                                                @click="deleteAttachment({{ $attachment->id }})"
                                                :disabled="deleting"
                                                class="text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300 disabled:opacity-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-admin.card>
        @endif

        <!-- Action Buttons -->
        <x-admin.card>
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500 dark:text-neutral-400">
                        <span class="font-medium">Note:</span> Updating this quotation will reset its status to "Pending" for review.
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <x-admin.button href="{{ route('client.quotations.show', $quotation) }}" color="light">
                            Cancel
                        </x-admin.button>
                        
                        <x-admin.button type="submit" color="primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Update Quotation
                        </x-admin.button>
                    </div>
                </div>
            </div>
        </x-admin.card>
    </form>

    @push('scripts')
    <script>
        function quotationEditForm() {
            return {
                init() {
                    // Initialize any edit form-specific logic
                }
            }
        }

        function attachmentManager() {
            return {
                async deleteAttachment(attachmentId) {
                    if (!confirm('Are you sure you want to delete this attachment?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`{{ route('client.quotations.attachments.delete', [$quotation, '']) }}/${attachmentId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Remove the attachment element from the DOM
                            event.target.closest('.relative.border').remove();
                            
                            // Show success message
                            this.showNotification('Attachment deleted successfully', 'success');
                        } else {
                            this.showNotification(data.message || 'Failed to delete attachment', 'error');
                        }
                    } catch (error) {
                        console.error('Error deleting attachment:', error);
                        this.showNotification('Failed to delete attachment', 'error');
                    }
                },

                showNotification(message, type) {
                    // Simple notification system
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg ${
                        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                    }`;
                    notification.textContent = message;
                    
                    document.body.appendChild(notification);
                    
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                }
            }
        }
    </script>
    @endpush
</x-layouts.client>