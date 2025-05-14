<!-- resources/views/admin/settings/index.blade.php -->
<x-admin-layout :title="'System Settings'">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Manage System Settings</h2>
        <div class="mt-4 md:mt-0">
            <button type="button" id="clearCacheBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Clear Cache
            </button>
        </div>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- General Settings -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">General Settings</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Configure basic website settings.
                </p>
            </div>

            <div class="p-6 bg-white space-y-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Site Title -->
                    <div class="sm:col-span-4">
                        <label for="site_title" class="block text-sm font-medium text-gray-700">Site Title</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="site_title" 
                                   id="site_title" 
                                   value="{{ old('site_title', $settings['site_title'] ?? config('app.name')) }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                   required>
                        </div>
                        @error('site_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Site Description -->
                    <div class="sm:col-span-4">
                        <label for="site_description" class="block text-sm font-medium text-gray-700">Site Description</label>
                        <div class="mt-1">
                            <textarea id="site_description" 
                                      name="site_description" 
                                      rows="2" 
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Brief description of your site for SEO purposes.</p>
                        @error('site_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo Upload -->
                    <div class="sm:col-span-6">
                        <label for="site_logo" class="block text-sm font-medium text-gray-700">Site Logo</label>
                        @if(isset($settings['site_logo']) && $settings['site_logo'])
                            <div class="mt-2 mb-3">
                                <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Site Logo" class="h-16 w-auto">
                            </div>
                        @endif
                        <div class="mt-1">
                            <input type="file" 
                                   name="site_logo" 
                                   id="site_logo" 
                                   accept="image/*" 
                                   class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Recommended size: 200×60 pixels. PNG format with transparent background preferred.</p>
                        @error('site_logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Favicon Upload -->
                    <div class="sm:col-span-6">
                        <label for="site_favicon" class="block text-sm font-medium text-gray-700">Site Favicon</label>
                        @if(isset($settings['site_favicon']) && $settings['site_favicon'])
                            <div class="mt-2 mb-3">
                                <img src="{{ asset('storage/' . $settings['site_favicon']) }}" alt="Site Favicon" class="h-8 w-auto">
                            </div>
                        @endif
                        <div class="mt-1">
                            <input type="file" 
                                   name="site_favicon" 
                                   id="site_favicon" 
                                   accept="image/x-icon,image/png" 
                                   class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Recommended size: 32×32 or 16×16 pixels. ICO or PNG format.</p>
                        @error('site_favicon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact & Social Settings -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Contact & Social Settings</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Configure contact information and social media links.
                </p>
            </div>

            <div class="p-6 bg-white space-y-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Contact Email -->
                    <div class="sm:col-span-3">
                        <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                        <div class="mt-1">
                            <input type="email" 
                                   name="contact_email" 
                                   id="contact_email" 
                                   value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('contact_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Phone -->
                    <div class="sm:col-span-3">
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="contact_phone" 
                                   id="contact_phone" 
                                   value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('contact_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Footer Text -->
                    <div class="sm:col-span-6">
                        <label for="footer_text" class="block text-sm font-medium text-gray-700">Footer Text</label>
                        <div class="mt-1">
                            <textarea id="footer_text" 
                                      name="footer_text" 
                                      rows="2" 
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('footer_text', $settings['footer_text'] ?? '') }}</textarea>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Additional text to display in the footer, such as copyright information.</p>
                        @error('footer_text')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">SEO Settings</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Configure search engine optimization settings.
                </p>
            </div>

            <div class="p-6 bg-white space-y-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Meta Keywords -->
                    <div class="sm:col-span-6">
                        <label for="meta_keywords" class="block text-sm font-medium text-gray-700">Meta Keywords</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="meta_keywords" 
                                   id="meta_keywords" 
                                   value="{{ old('meta_keywords', $settings['meta_keywords'] ?? '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Comma-separated list of keywords relevant to your website.</p>
                        @error('meta_keywords')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Google Analytics ID -->
                    <div class="sm:col-span-3">
                        <label for="google_analytics_id" class="block text-sm font-medium text-gray-700">Google Analytics ID</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="google_analytics_id" 
                                   id="google_analytics_id" 
                                   value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}" 
                                   placeholder="UA-XXXXXXXXX-X or G-XXXXXXXXXX"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Your Google Analytics tracking ID.</p>
                        @error('google_analytics_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Google Site Verification -->
                    <div class="sm:col-span-3">
                        <label for="google_site_verification" class="block text-sm font-medium text-gray-700">Google Site Verification</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="google_site_verification" 
                                   id="google_site_verification" 
                                   value="{{ old('google_site_verification', $settings['google_site_verification'] ?? '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Google Search Console verification code.</p>
                        @error('google_site_verification')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Settings -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Email Settings</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Configure email notification settings.
                </p>
            </div>

            <div class="p-6 bg-white space-y-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Admin Notification Email -->
                    <div class="sm:col-span-3">
                        <label for="admin_notification_email" class="block text-sm font-medium text-gray-700">Admin Notification Email</label>
                        <div class="mt-1">
                            <input type="email" 
                                   name="admin_notification_email" 
                                   id="admin_notification_email" 
                                   value="{{ old('admin_notification_email', $settings['admin_notification_email'] ?? '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Email address for admin notifications.</p>
                        @error('admin_notification_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email From Name -->
                    <div class="sm:col-span-3">
                        <label for="email_from_name" class="block text-sm font-medium text-gray-700">Email From Name</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="email_from_name" 
                                   id="email_from_name" 
                                   value="{{ old('email_from_name', $settings['email_from_name'] ?? config('app.name')) }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Name to display as the sender for system emails.</p>
                        @error('email_from_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email Notification Settings -->
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Email Notifications</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="notify_new_message" 
                                       id="notify_new_message" 
                                       value="1"
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                       {{ old('notify_new_message', $settings['notify_new_message'] ?? true) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="notify_new_message" class="font-medium text-gray-700">New Contact Message</label>
                                <p class="text-gray-500">Receive email notification when a new contact form message is submitted.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="notify_new_quotation" 
                                       id="notify_new_quotation" 
                                       value="1"
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                       {{ old('notify_new_quotation', $settings['notify_new_quotation'] ?? true) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="notify_new_quotation" class="font-medium text-gray-700">New Quotation Request</label>
                                <p class="text-gray-500">Receive email notification when a new quotation request is submitted.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="notify_client_registration" 
                                       id="notify_client_registration" 
                                       value="1"
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                       {{ old('notify_client_registration', $settings['notify_client_registration'] ?? true) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="notify_client_registration" class="font-medium text-gray-700">New Client Registration</label>
                                <p class="text-gray-500">Receive email notification when a new client registers.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Buttons -->
        <div class="flex justify-end">
            <button type="reset" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Reset
            </button>
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Save Settings
            </button>
        </div>
    </form>
</x-admin-layout>

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