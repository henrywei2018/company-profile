<!-- resources/views/admin/company/edit.blade.php -->
<x-admin-layout :title="'Edit Company Profile'">
    <div class="mb-6">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6">
            <div class="bg-green-50 border-l-4 border-green-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.company.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Company Information -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Company Information</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Basic information about your company.
                </p>
            </div>

            <div class="p-6 bg-white space-y-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Company Name -->
                    <div class="sm:col-span-4">
                        <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="company_name" 
                                   id="company_name" 
                                   value="{{ old('company_name', $companyProfile->company_name ?? '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                   required>
                        </div>
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tagline -->
                    <div class="sm:col-span-4">
                        <label for="tagline" class="block text-sm font-medium text-gray-700">Tagline</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="tagline" 
                                   id="tagline" 
                                   value="{{ old('tagline', $companyProfile->tagline ?? '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">A short slogan or motto for your company.</p>
                        @error('tagline')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo -->
                    <div class="sm:col-span-6">
                        <label for="logo" class="block text-sm font-medium text-gray-700">Company Logo</label>
                        @if(isset($companyProfile) && $companyProfile->logo)
                            <div class="mt-2 mb-3">
                                <img src="{{ asset('storage/' . $companyProfile->logo) }}" alt="Company Logo" class="h-16 w-auto">
                            </div>
                        @endif
                        <div class="mt-1">
                            <input type="file" 
                                   name="logo" 
                                   id="logo" 
                                   accept="image/*" 
                                   class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Recommended size: 200×60 pixels. PNG format with transparent background preferred.</p>
                        @error('logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Contact Information</h2>
                <p class="mt-1 text-sm text-gray-500">
                    How clients can reach your company.
                </p>
            </div>

            <div class="p-6 bg-white space-y-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Phone -->
                    <div class="sm:col-span-3">
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="phone" 
                                   id="phone" 
                                   value="{{ old('phone', $companyProfile->phone ?? '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="sm:col-span-3">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="mt-1">
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email', $companyProfile->email ?? '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="sm:col-span-6">
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <div class="mt-1">
                            <textarea id="address" 
                                      name="address" 
                                      rows="3" 
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('address', $companyProfile->address ?? '') }}</textarea>
                        </div>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City -->
                    <div class="sm:col-span-2">
                        <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="city" 
                                   id="city" 
                                   value="{{ old('city', $companyProfile->city ?? '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Postal Code -->
                    <div class="sm:col-span-2">
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="postal_code" 
                                   id="postal_code" 
                                   value="{{ old('postal_code', $companyProfile->postal_code ?? '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div class="sm:col-span-2">
                        <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="country" 
                                   id="country" 
                                   value="{{ old('country', $companyProfile->country ?? 'Indonesia') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Map Location -->
                    <div class="sm:col-span-3">
                        <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="latitude" 
                                   id="latitude" 
                                   value="{{ old('latitude', $companyProfile->latitude ?? '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('latitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="longitude" 
                                   id="longitude" 
                                   value="{{ old('longitude', $companyProfile->longitude ?? '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('longitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Social Media</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Your company's social media profiles.
                </p>
            </div>

            <div class="p-6 bg-white space-y-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Facebook -->
                    <div class="sm:col-span-3">
                        <label for="facebook" class="block text-sm font-medium text-gray-700">Facebook</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                https://
                            </span>
                            <input type="text" 
                                   name="facebook" 
                                   id="facebook" 
                                   value="{{ old('facebook', str_replace('https://', '', $companyProfile->facebook ?? '')) }}" 
                                   placeholder="facebook.com/yourcompany" 
                                   class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                        </div>
                        @error('facebook')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Twitter -->
                    <div class="sm:col-span-3">
                        <label for="twitter" class="block text-sm font-medium text-gray-700">Twitter</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                https://
                            </span>
                            <input type="text" 
                                   name="twitter" 
                                   id="twitter" 
                                   value="{{ old('twitter', str_replace('https://', '', $companyProfile->twitter ?? '')) }}" 
                                   placeholder="twitter.com/yourcompany" 
                                   class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                        </div>
                        @error('twitter')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Instagram -->
                    <div class="sm:col-span-3">
                        <label for="instagram" class="block text-sm font-medium text-gray-700">Instagram</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                https://
                            </span>
                            <input type="text" 
                                   name="instagram" 
                                   id="instagram" 
                                   value="{{ old('instagram', str_replace('https://', '', $companyProfile->instagram ?? '')) }}" 
                                   placeholder="instagram.com/yourcompany" 
                                   class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                        </div>
                        @error('instagram')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- LinkedIn -->
                    <div class="sm:col-span-3">
                        <label for="linkedin" class="block text-sm font-medium text-gray-700">LinkedIn</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                https://
                            </span>
                            <input type="text" 
                                   name="linkedin" 
                                   id="linkedin" 
                                   value="{{ old('linkedin', str_replace('https://', '', $companyProfile->linkedin ?? '')) }}" 
                                   placeholder="linkedin.com/company/yourcompany" 
                                   class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                        </div>
                        @error('linkedin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- YouTube -->
                    <div class="sm:col-span-3">
                        <label for="youtube" class="block text-sm font-medium text-gray-700">YouTube</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                https://
                            </span>
                            <input type="text" 
                                   name="youtube" 
                                   id="youtube" 
                                   value="{{ old('youtube', str_replace('https://', '', $companyProfile->youtube ?? '')) }}" 
                                   placeholder="youtube.com/channel/yourchannelid" 
                                   class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                        </div>
                        @error('youtube')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- WhatsApp -->
                    <div class="sm:col-span-3">
                        <label for="whatsapp" class="block text-sm font-medium text-gray-700">WhatsApp</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="whatsapp" 
                                   id="whatsapp" 
                                   value="{{ old('whatsapp', $companyProfile->whatsapp ?? '') }}" 
                                   placeholder="+62812xxxxxxxx" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Enter full phone number with country code (e.g., +62812xxxxxxxx)</p>
                        @error('whatsapp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- About Information -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">About Information</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Information about your company's background, mission, and values.
                </p>
            </div>

            <div class="p-6 bg-white space-y-6">
                <!-- About -->
                <div>
                    <label for="about" class="block text-sm font-medium text-gray-700">About Us</label>
                    <div class="mt-1">
                        <textarea id="about" 
                                  name="about" 
                                  rows="6" 
                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('about', $companyProfile->about ?? '') }}</textarea>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">A brief description of your company, its history, and what it does.</p>
                    @error('about')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Vision -->
                <div>
                    <label for="vision" class="block text-sm font-medium text-gray-700">Vision</label>
                    <div class="mt-1">
                        <textarea id="vision" 
                                  name="vision" 
                                  rows="3" 
                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('vision', $companyProfile->vision ?? '') }}</textarea>
                    </div>
                    @error('vision')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mission -->
                <div>
                    <label for="mission" class="block text-sm font-medium text-gray-700">Mission</label>
                    <div class="mt-1">
                        <textarea id="mission" 
                                  name="mission" 
                                  rows="3" 
                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('mission', $companyProfile->mission ?? '') }}</textarea>
                    </div>
                    @error('mission')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- History -->
                <div>
                    <label for="history" class="block text-sm font-medium text-gray-700">Company History</label>
                    <div class="mt-1">
                        <textarea id="history" 
                                  name="history" 
                                  rows="6" 
                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('history', $companyProfile->history ?? '') }}</textarea>
                    </div>
                    @error('history')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Values -->
                <div>
                    <label for="values" class="block text-sm font-medium text-gray-700">Company Values</label>
                    <div class="mt-1">
                        <textarea id="values" 
                                  name="values" 
                                  rows="6" 
                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('values', is_array($companyProfile->values ?? null) ? implode("\n", $companyProfile->values) : ($companyProfile->values ?? '')) }}</textarea>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Enter one value per line. Each value will be displayed as a separate item.</p>
                    @error('values')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Buttons -->
        <div class="flex justify-end">
            <button type="reset" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Reset
            </button>
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Save Changes
            </button>
        </div>
    </form>
</x-admin-layout>