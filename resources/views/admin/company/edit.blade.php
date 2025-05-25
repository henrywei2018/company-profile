<!-- resources/views/admin/company/edit.blade.php -->
<x-layouts.admin>
    <x-slot name="title">Company Profile</x-slot>
    
    <form action="{{ route('admin.company-profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Company Information Section -->
            <x-admin.form-section title="Company Information" description="Update your company's basic information.">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <x-form.input 
                            name="legal_name" 
                            label="Company Name" 
                            :value="$companyProfile->legal_name ?? config('app.name')" 
                            required 
                        />

                    </div>
                    
                    <div>
                        <x-form.input 
                            name="tagline" 
                            label="Tagline" 
                            :value="$companyProfile->tagline ?? null" 
                            helper="A short slogan that appears below your company name"
                        />
                    </div>
                    
                    <div class="lg:col-span-2">
                        <x-form.file-input 
                            name="logo" 
                            label="Company Logo" 
                            accept="image/*" 
                            :preview="$companyProfile && $companyProfile->logo ? $companyProfile->logoUrl : null" 
                            helper="Recommended size: 200x80 pixels, transparent background (PNG format)"
                        >
                            Upload your company logo
                        </x-form.file-input>
                    </div>
                    
                    <div class="lg:col-span-2">
                        <x-form.file-input 
                            name="logo_white" 
                            label="White Logo (for dark backgrounds)" 
                            accept="image/*" 
                            :preview="$companyProfile && $companyProfile->logo_white ? $companyProfile->logoWhiteUrl : null" 
                            helper="White version of your logo for dark backgrounds"
                        >
                            Upload a white version of your logo
                        </x-form.file-input>
                    </div>
                    
                    <div class="lg:col-span-2">
                        <x-form.textarea 
                            name="about" 
                            label="About Us (Short)" 
                            :value="$companyProfile->about ?? null" 
                            rows="3"
                            helper="A brief summary about your company (max 200 characters)"
                        />
                    </div>
                    
                    <div class="lg:col-span-2">
                        <x-form.rich-editor 
                            name="description" 
                            label="Company Description" 
                            :value="$companyProfile->description ?? null" 
                            helper="Detailed description of your company"
                        />
                    </div>
                </div>
            </x-admin.form-section>
            
            <!-- Vision & Mission -->
            <x-admin.form-section title="Vision & Mission" description="Define your company's vision and mission statements.">
                <div class="space-y-6">
                    <x-form.textarea 
                        name="vision" 
                        label="Vision" 
                        :value="$companyProfile->vision ?? null" 
                        rows="3"
                        helper="Your company's vision for the future"
                    />
                    
                    <x-form.textarea 
                        name="mission" 
                        label="Mission" 
                        :value="$companyProfile->mission ?? null" 
                        rows="3"
                        helper="Your company's mission statement"
                    />
                </div>
            </x-admin.form-section>
            
            <!-- Contact Information -->
            <x-admin.form-section title="Contact Information" description="Update your company's contact details.">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <x-form.input 
                            name="email" 
                            label="Email Address" 
                            type="email"
                            :value="$companyProfile->email ?? null" 
                            required 
                            helper="Primary contact email address"
                        />
                    </div>
                    
                    <div>
                        <x-form.input 
                            name="phone" 
                            label="Phone Number" 
                            type="tel"
                            :value="$companyProfile->phone ?? null" 
                            required 
                            helper="Primary contact phone number"
                        />
                    </div>
                    
                    <div>
                        <x-form.input 
                            name="alternative_email" 
                            label="Alternative Email" 
                            type="email"
                            :value="$companyProfile->alternative_email ?? null" 
                            helper="Secondary contact email (optional)"
                        />
                    </div>
                    
                    <div>
                        <x-form.input 
                            name="alternative_phone" 
                            label="Alternative Phone" 
                            type="tel"
                            :value="$companyProfile->alternative_phone ?? null" 
                            helper="Secondary contact phone (optional)"
                        />
                    </div>
                    
                    <div class="lg:col-span-2">
                        <x-form.textarea 
                            name="address" 
                            label="Address" 
                            :value="$companyProfile->address ?? null" 
                            rows="3"
                            required
                        />
                    </div>
                </div>
            </x-admin.form-section>
            
            <!-- Social Media Links -->
            <x-admin.form-section title="Social Media Links" description="Add links to your company's social media profiles.">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <x-form.input 
                            name="facebook" 
                            label="Facebook" 
                            type="url"
                            :value="$companyProfile->facebook ?? null" 
                            helper="Full URL to your Facebook page"
                        />
                    </div>
                    
                    <div>
                        <x-form.input 
                            name="twitter" 
                            label="Twitter / X" 
                            type="url"
                            :value="$companyProfile->twitter ?? null" 
                            helper="Full URL to your Twitter profile"
                        />
                    </div>
                    
                    <div>
                        <x-form.input 
                            name="instagram" 
                            label="Instagram" 
                            type="url"
                            :value="$companyProfile->instagram ?? null" 
                            helper="Full URL to your Instagram profile"
                        />
                    </div>
                    
                    <div>
                        <x-form.input 
                            name="linkedin" 
                            label="LinkedIn" 
                            type="url"
                            :value="$companyProfile->linkedin ?? null" 
                            helper="Full URL to your LinkedIn company page"
                        />
                    </div>
                    
                    <div>
                        <x-form.input 
                            name="youtube" 
                            label="YouTube" 
                            type="url"
                            :value="$companyProfile->youtube ?? null" 
                            helper="Full URL to your YouTube channel"
                        />
                    </div>
                    
                    <div>
                        <x-form.input 
                            name="whatsapp" 
                            label="WhatsApp" 
                            :value="$companyProfile->whatsapp ?? null" 
                            helper="WhatsApp number with country code (e.g., +621234567890)"
                        />
                    </div>
                </div>
            </x-admin.form-section>
            
            <!-- Legal Information -->
            <x-admin.form-section title="Legal Information" description="Add your company's legal details.">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <x-form.input 
                            name="legal_name" 
                            label="Legal Company Name" 
                            :value="$companyProfile->legal_name ?? null" 
                            helper="Full legal name of your company"
                        />
                    </div>
                    
                    <div>
                        <x-form.input 
                            name="tax_id" 
                            label="Tax ID / NPWP" 
                            :value="$companyProfile->tax_id ?? null" 
                            helper="Your company's tax identification number"
                        />
                    </div>
                    
                    <div>
                        <x-form.input 
                            name="registration_number" 
                            label="Registration Number" 
                            :value="$companyProfile->registration_number ?? null" 
                            helper="Business registration number"
                        />
                    </div>
                    
                    <div>
                        <x-form.input 
                            name="established" 
                            label="Year Established" 
                            type="number"
                            min="1900"
                            max="{{ date('Y') }}"
                            :value="$companyProfile->established ?? null" 
                            helper="Year your company was founded"
                        />
                    </div>
                </div>
            </x-admin.form-section>
            
            <!-- Map Information -->
            <x-admin.form-section title="Map Information" description="Add your location on the map.">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <x-form.input 
                            name="map_embed" 
                            label="Google Maps Embed Code" 
                            :value="$companyProfile->map_embed ?? null" 
                            helper="Paste your Google Maps embed iframe code here"
                        />
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <x-form.input 
                                name="latitude" 
                                label="Latitude" 
                                type="text"
                                :value="$companyProfile->latitude ?? null" 
                                helper="GPS latitude coordinate"
                            />
                        </div>
                        
                        <div>
                            <x-form.input 
                                name="longitude" 
                                label="Longitude" 
                                type="text"
                                :value="$companyProfile->longitude ?? null" 
                                helper="GPS longitude coordinate"
                            />
                        </div>
                    </div>
                    
                    @if($companyProfile && $companyProfile->map_embed)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Current Map Preview</label>
                            <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                                {!! $companyProfile->map_embed !!}
                            </div>
                        </div>
                    @endif
                </div>
            </x-admin.form-section>
            
            <!-- Business Hours -->
            <x-admin.form-section title="Business Hours" description="Set your company's operating hours.">
                <div class="space-y-4">
                    @php
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        $hours = $companyProfile->business_hours ?? [];
                        if (is_string($hours)) {
                            $hours = json_decode($hours, true) ?? [];
                        }
                    @endphp
                    
                    @foreach($days as $day)
                        <div class="grid grid-cols-5 gap-4 items-center">
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ $day }}</label>
                            </div>
                            
                            <div class="col-span-4 flex items-center gap-4">
                                <div class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        id="{{ strtolower($day) }}_closed" 
                                        name="business_hours[{{ strtolower($day) }}][closed]" 
                                        value="1" 
                                        class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                                        {{ isset($hours[strtolower($day)]['closed']) && $hours[strtolower($day)]['closed'] ? 'checked' : '' }}
                                        onchange="document.getElementById('{{ strtolower($day) }}_hours').style.display = this.checked ? 'none' : 'flex';"
                                    >
                                    <label for="{{ strtolower($day) }}_closed" class="ml-2 text-sm text-gray-700 dark:text-gray-200">
                                        Closed
                                    </label>
                                </div>
                                
                                <div id="{{ strtolower($day) }}_hours" class="flex-1 flex items-center gap-2" style="{{ isset($hours[strtolower($day)]['closed']) && $hours[strtolower($day)]['closed'] ? 'display: none;' : '' }}">
                                    <div class="w-1/3">
                                        <input 
                                            type="time" 
                                            name="business_hours[{{ strtolower($day) }}][open]" 
                                            value="{{ $hours[strtolower($day)]['open'] ?? '09:00' }}" 
                                            class="py-2 px-3 block w-full border-gray-200 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400"
                                        >
                                    </div>
                                    <span class="text-gray-500">to</span>
                                    <div class="w-1/3">
                                        <input 
                                            type="time" 
                                            name="business_hours[{{ strtolower($day) }}][close]" 
                                            value="{{ $hours[strtolower($day)]['close'] ?? '17:00' }}" 
                                            class="py-2 px-3 block w-full border-gray-200 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-admin.form-section>
            
            <!-- Form Buttons -->
            <div class="flex justify-end">
                <x-form.button submitText="Update Company Profile" />
            </div>
        </div>
    </form>
</x-layouts.admin>