{{-- resources/views/admin/settings/seo.blade.php --}}
<x-layouts.admin title="SEO Settings">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">SEO Settings</h2>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Configure search engine optimization settings</p>
        </div>
    </div>

    <!-- Settings Tabs -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <a href="{{ route('admin.settings.index') }}" 
                   class="border-transparent border-b-2 py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                    General
                </a>
                <a href="{{ route('admin.settings.seo') }}" 
                   class="border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600 dark:text-indigo-400">
                    SEO
                </a>
                <a href="{{ route('admin.settings.email') }}" 
                   class="border-transparent border-b-2 py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                    Email
                </a>
            </nav>
        </div>

        <form action="{{ route('admin.settings.seo.update') }}" method="POST" class="p-6 space-y-8">
            @csrf
            @method('PUT')

            <!-- Basic SEO -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Basic SEO Settings</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Configure basic search engine optimization settings.</p>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="seo_title_format" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Title Format</label>
                        <input type="text" 
                               name="seo_title_format" 
                               id="seo_title_format" 
                               value="{{ old('seo_title_format', settings('seo_title_format', '%title% | %site_name%')) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Use %title% for page title and %site_name% for site name</p>
                        @error('seo_title_format')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="seo_description" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Default Meta Description</label>
                        <textarea id="seo_description" 
                                  name="seo_description" 
                                  rows="3" 
                                  maxlength="160"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('seo_description', settings('seo_description')) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum 160 characters. This will be used as default description for pages without specific meta descriptions.</p>
                        @error('seo_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="seo_keywords" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Default Meta Keywords</label>
                        <input type="text" 
                               name="seo_keywords" 
                               id="seo_keywords" 
                               value="{{ old('seo_keywords', settings('seo_keywords')) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Comma-separated keywords relevant to your business</p>
                        @error('seo_keywords')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Search Engine Verification -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Search Engine Verification</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Add verification codes for search engine webmaster tools.</p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="seo_google_verification" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Google Verification Code</label>
                        <input type="text" 
                               name="seo_google_verification" 
                               id="seo_google_verification" 
                               value="{{ old('seo_google_verification', settings('seo_google_verification')) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Google Search Console verification code</p>
                        @error('seo_google_verification')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="seo_bing_verification" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Bing Verification Code</label>
                        <input type="text" 
                               name="seo_bing_verification" 
                               id="seo_bing_verification" 
                               value="{{ old('seo_bing_verification', settings('seo_bing_verification')) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Bing Webmaster Tools verification code</p>
                        @error('seo_bing_verification')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Analytics -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Analytics & Tracking</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Configure website analytics and tracking codes.</p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="google_analytics_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Google Analytics ID</label>
                        <input type="text" 
                               name="google_analytics_id" 
                               id="google_analytics_id" 
                               value="{{ old('google_analytics_id', settings('google_analytics_id')) }}" 
                               placeholder="G-XXXXXXXXXX or UA-XXXXXXXXX-X"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Your Google Analytics tracking ID</p>
                        @error('google_analytics_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="google_tag_manager_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Google Tag Manager ID</label>
                        <input type="text" 
                               name="google_tag_manager_id" 
                               id="google_tag_manager_id" 
                               value="{{ old('google_tag_manager_id', settings('google_tag_manager_id')) }}" 
                               placeholder="GTM-XXXXXXX"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Your Google Tag Manager container ID</p>
                        @error('google_tag_manager_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Robots.txt -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Robots.txt</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Configure how search engines crawl your website.</p>
                </div>

                <div>
                    <label for="seo_robots_txt" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Robots.txt Content</label>
                    <textarea id="seo_robots_txt" 
                              name="seo_robots_txt" 
                              rows="6" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('seo_robots_txt', settings('seo_robots_txt', "User-agent: *\nDisallow: /admin\nDisallow: /api\n\nSitemap: " . url('/sitemap.xml'))) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Content for your robots.txt file. Leave empty to use default settings.</p>
                    @error('seo_robots_txt')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save SEO Settings
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counter for meta description
            const descriptionField = document.getElementById('seo_description');
            if (descriptionField) {
                const maxLength = 160;
                const counter = document.createElement('div');
                counter.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
                counter.textContent = `${descriptionField.value.length}/${maxLength} characters`;
                descriptionField.parentNode.appendChild(counter);

                descriptionField.addEventListener('input', function() {
                    const length = this.value.length;
                    counter.textContent = `${length}/${maxLength} characters`;
                    
                    if (length > maxLength) {
                        counter.className = 'text-xs text-red-600 mt-1';
                    } else if (length > maxLength * 0.9) {
                        counter.className = 'text-xs text-yellow-600 mt-1';
                    } else {
                        counter.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.admin>