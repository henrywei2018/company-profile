{{-- resources/views/admin/settings/index.blade.php --}}
<x-layouts.admin title="General Settings">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">General Settings</h2>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Configure basic website settings and preferences</p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center gap-3">
            <button type="button" id="clearCacheBtn" 
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Clear Cache
            </button>
        </div>
    </div>

    <!-- Settings Tabs -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <a href="{{ route('admin.settings.index') }}" 
                   class="border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600 dark:text-indigo-400">
                    General
                </a>
                <a href="{{ route('admin.settings.seo') }}" 
                   class="border-transparent border-b-2 py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                    SEO
                </a>
                <a href="{{ route('admin.settings.email') }}" 
                   class="border-transparent border-b-2 py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                    Email
                </a>
            </nav>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-8">
            @csrf
            @method('PUT')

            <!-- Site Information -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Site Information</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Basic information about your website.</p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Site Name</label>
                        <input type="text" 
                               name="site_name" 
                               id="site_name" 
                               value="{{ old('site_name', settings('site_name', config('app.name'))) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                               required>
                        @error('site_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Contact Email</label>
                        <input type="email" 
                               name="contact_email" 
                               id="contact_email" 
                               value="{{ old('contact_email', settings('contact_email')) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                               required>
                        @error('contact_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="site_description" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Site Description</label>
                    <textarea id="site_description" 
                              name="site_description" 
                              rows="3" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('site_description', settings('site_description')) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Brief description of your website for SEO purposes.</p>
                    @error('site_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Contact Phone</label>
                        <input type="text" 
                               name="contact_phone" 
                               id="contact_phone" 
                               value="{{ old('contact_phone', settings('contact_phone')) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('contact_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="footer_text" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Footer Text</label>
                        <input type="text" 
                               name="footer_text" 
                               id="footer_text" 
                               value="{{ old('footer_text', settings('footer_text')) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('footer_text')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- File Uploads -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Branding</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Upload your brand assets.</p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Site Logo -->
                    <div>
                        <label for="site_logo" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Site Logo</label>
                        @if(settings('site_logo'))
                            <div class="mt-2 mb-3">
                                <img src="{{ asset('storage/' . settings('site_logo')) }}" alt="Site Logo" class="h-16 w-auto rounded">
                            </div>
                        @endif
                        <input type="file" 
                               name="site_logo" 
                               id="site_logo" 
                               accept="image/*" 
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-300">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG, JPG up to 2MB. Recommended size: 200×60 pixels.</p>
                        @error('site_logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Site Favicon -->
                    <div>
                        <label for="site_favicon" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Site Favicon</label>
                        @if(settings('site_favicon'))
                            <div class="mt-2 mb-3">
                                <img src="{{ asset('storage/' . settings('site_favicon')) }}" alt="Site Favicon" class="h-8 w-auto rounded">
                            </div>
                        @endif
                        <input type="file" 
                               name="site_favicon" 
                               id="site_favicon" 
                               accept="image/x-icon,image/png" 
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-300">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">ICO or PNG format. Recommended size: 32×32 pixels.</p>
                        @error('site_favicon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Email Notifications -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Email Notifications</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Configure which email notifications to send.</p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="notify_new_message" 
                                   name="notify_new_message" 
                                   type="checkbox" 
                                   value="1"
                                   {{ old('notify_new_message', settings('notify_new_message', true)) ? 'checked' : '' }}
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="notify_new_message" class="font-medium text-gray-700 dark:text-gray-200">New Contact Messages</label>
                            <p class="text-gray-500 dark:text-gray-400">Receive email notifications when new contact form messages are submitted.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="notify_new_quotation" 
                                   name="notify_new_quotation" 
                                   type="checkbox" 
                                   value="1"
                                   {{ old('notify_new_quotation', settings('notify_new_quotation', true)) ? 'checked' : '' }}
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="notify_new_quotation" class="font-medium text-gray-700 dark:text-gray-200">New Quotation Requests</label>
                            <p class="text-gray-500 dark:text-gray-400">Receive email notifications when new quotation requests are submitted.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="notify_client_registration" 
                                   name="notify_client_registration" 
                                   type="checkbox" 
                                   value="1"
                                   {{ old('notify_client_registration', settings('notify_client_registration', true)) ? 'checked' : '' }}
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="notify_client_registration" class="font-medium text-gray-700 dark:text-gray-200">New Client Registrations</label>
                            <p class="text-gray-500 dark:text-gray-400">Receive email notifications when new clients register.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle cache clearing
            const clearCacheBtn = document.getElementById('clearCacheBtn');
            if (clearCacheBtn) {
                clearCacheBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to clear all application cache?')) {
                        fetch('{{ route('admin.settings.clear-cache') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Cache cleared successfully!');
                            } else {
                                alert('Failed to clear cache: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while clearing cache.');
                        });
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.admin>