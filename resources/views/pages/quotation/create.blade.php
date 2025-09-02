<x-layouts.public :title="$seoData['title']" :description="$seoData['description']" :keywords="$seoData['keywords']">
    
    {{-- Hero Section --}}
    <section class="relative bg-gradient-to-br from-orange-50 via-white to-amber-50 py-20">
        <div class="absolute inset-0 bg-[url('/images/grid.svg')] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="inline-flex items-center px-4 py-2 bg-orange-100 text-orange-600 rounded-full text-sm font-semibold mb-6">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"/>
                    </svg>
                    Penawaran Gratis & Cepat
                </div>
                
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Dapatkan Penawaran 
                    <span class="bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
                        Proyek Anda
                    </span>
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                    Dapatkan penawaran detail dan profesional untuk proyek Anda. Tim berpengalaman kami akan menganalisis kebutuhan Anda dan memberikan harga kompetitif dengan rincian yang transparan.
                </p>
                
                {{-- Quick Stats --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-12">
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-center w-12 h-12 bg-orange-100 rounded-full mx-auto mb-3">
                            <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-orange-600 mb-2">24-48 Jam</div>
                        <div class="text-sm text-gray-600">Respon Awal</div>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mx-auto mb-3">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-green-600 mb-2">GRATIS</div>
                        <div class="text-sm text-gray-600">Konsultasi</div>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mx-auto mb-3">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-blue-600 mb-2">500+</div>
                        <div class="text-sm text-gray-600">Proyek Selesai</div>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-full mx-auto mb-3">
                            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-purple-600 mb-2">15+</div>
                        <div class="text-sm text-gray-600">Tahun Pengalaman</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Form Section --}}
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Progress Steps --}}
            <div class="mb-12">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 bg-orange-600 text-white rounded-full text-sm font-semibold">1</div>
                        <div class="ml-3 text-sm font-medium text-gray-900">Project Details</div>
                    </div>
                    <div class="flex-1 mx-4 h-1 bg-gray-200 rounded"></div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-600 rounded-full text-sm font-semibold">2</div>
                        <div class="ml-3 text-sm font-medium text-gray-500">Tinjau Review & Submit Kirim</div>
                    </div>
                    <div class="flex-1 mx-4 h-1 bg-gray-200 rounded"></div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-600 rounded-full text-sm font-semibold">3</div>
                        <div class="ml-3 text-sm font-medium text-gray-500">Confirmation</div>
                    </div>
                </div>
            </div>

            <form action="{{ route('quotation.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="quotationForm">
                @csrf
                
                {{-- Klien Information Section --}}
                <div class="bg-gray-50 rounded-xl p-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Your Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200"
                                   placeholder="Enter your full name">
                            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Alamat Email *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200"
                                   placeholder="your.email@example.com">
                            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200"
                                   placeholder="+62 812 3456 7890">
                            @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                            <input type="text" id="company" name="company" value="{{ old('company') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200"
                                   placeholder="Your company name (optional)">
                            @error('company')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Project Information Section --}}
                <div class="bg-gray-50 rounded-xl p-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Project Details
                    </h2>
                    
                    <div class="space-y-6">
                        {{-- Service Selection --}}
                        <div>
                            <label for="service_id" class="block text-sm font-medium text-gray-700 mb-2">Service Required *</label>
                            <select id="service_id" name="service_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200">
                                <option value="">Select a service...</option>
                                @foreach($serviceKategori as $category)
                                    <optgroup label="{{ $category->name }}">
                                        @foreach($category->activeServices as $service)
                                            <option value="{{ $service->id }}" 
                                                    {{ ($selectedService && $selectedService->id == $service->id) || old('service_id') == $service->id ? 'selected' : '' }}>
                                                {{ $service->title }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('service_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        
                                <option value="emergency" {{ old('project_type') == 'emergency' ? 'selected' : '' }}>Emergency Repair</option>
                                <option value="consultation" {{ old('project_type') == 'consultation' ? 'selected' : '' }}>Consultation Only</option>
                            </select>
                            @error('project_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        
                        {{-- Project Lokasi --}}
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Project Lokasi</label>
                            <textarea id="location" name="location" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200"
                                      placeholder="Please provide the full address or location details...">{{ old('location') }}</textarea>
                            @error('location')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        
                        {{-- Project Requirements --}}
                        <div>
                            <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">Project Requirements & Description</label>
                            <textarea id="requirements" name="requirements" rows="5"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200"
                                      placeholder="Please describe your project requirements, scope of work, and any specific needs...">{{ old('requirements') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Please provide as much detail as possible to help us prepare an accurate quotation</p>
                            @error('requirements')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Anggaran and Timeline Section --}}
                <div class="bg-gray-50 rounded-xl p-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                        Anggaran & Timeline
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Anggaran Range --}}
                        <div>
                            <label for="budget_range" class="block text-sm font-medium text-gray-700 mb-2">Anggaran Range (IDR)</label>
                            <select id="budget_range" name="budget_range"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200">
                                <option value="">Select budget range...</option>
                                <option value="under_50m" {{ old('budget_range') == 'under_50m' ? 'selected' : '' }}>Under 50 Million</option>
                                <option value="50m_100m" {{ old('budget_range') == '50m_100m' ? 'selected' : '' }}>50 - 100 Million</option>
                                <option value="100m_500m" {{ old('budget_range') == '100m_500m' ? 'selected' : '' }}>100 - 500 Million</option>
                                <option value="500m_1b" {{ old('budget_range') == '500m_1b' ? 'selected' : '' }}>500 Million - 1 Billion</option>
                                <option value="over_1b" {{ old('budget_range') == 'over_1b' ? 'selected' : '' }}>Over 1 Billion</option>
                                <option value="flexible" {{ old('budget_range') == 'flexible' ? 'selected' : '' }}>Flexible / To be discussed</option>
                            </select>
                            <p class="mt-2 text-sm text-gray-500">Select the range that best fits your project budget</p>
                            @error('budget_range')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        
                        {{-- Preferred Tanggal Mulai --}}
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Preferred Tanggal Mulai</label>
                            <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200">
                            <p class="mt-2 text-sm text-gray-500">When would you like the project to begin?</p>
                            @error('start_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- File Attachments Section --}}
                <div class="bg-gray-50 rounded-xl p-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        Attachments & Additional Information
                    </h2>
                    
                    <div class="space-y-6">
                        {{-- Upload File --}}
                        <div>
                            <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">Project Files (Optional)</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-orange-400 transition-colors duration-200">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div class="mt-4">
                                    <label for="attachments" class="cursor-pointer">
                                        <span class="mt-2 block text-sm font-medium text-gray-900">Upload file proyek</span>
                                        <span class="mt-2 block text-sm text-gray-500">or drag and drop</span>
                                    </label>
                                    <input id="attachments" name="attachments[]" type="file" multiple class="sr-only"
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.dwg,.zip">
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    PDF, DOC, DOCX, JPG, PNG, DWG, ZIP up to 10MB each
                                </p>
                            </div>
                            @error('attachments.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        
                        {{-- Additional Information --}}
                        <div>
                            <label for="additional_info" class="block text-sm font-medium text-gray-700 mb-2">Additional Information</label>
                            <textarea id="additional_info" name="additional_info" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200"
                                      placeholder="Any additional information, special requests, or questions you'd like to share...">{{ old('additional_info') }}</textarea>
                            @error('additional_info')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        {{-- Source Tracking (Hidden) --}}
                        <input type="hidden" name="source" value="website">
                    </div>
                </div>

                {{-- Bagian Syarat dan Kirim --}}
                <div class="bg-gray-50 rounded-xl p-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Terms & Submission
                    </h2>
                    
                    <div class="space-y-6">
                        {{-- Terms and Conditions --}}
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="terms_accepted" name="terms_accepted" type="checkbox" value="1" required
                                           class="focus:ring-orange-500 h-4 w-4 text-orange-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="terms_accepted" class="font-medium text-gray-700">
                                        I agree to the <a href="#" class="text-orange-600 hover:text-orange-500">Terms and Conditions</a> *
                                    </label>
                                    <p class="text-gray-500">
                                        By submitting this form, you agree to our terms of service and quotation process.
                                    </p>
                                </div>
                            </div>
                            @error('terms_accepted')<p class="ml-7 text-sm text-red-600">{{ $message }}</p>@enderror
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="privacy_accepted" name="privacy_accepted" type="checkbox" value="1" required
                                           class="focus:ring-orange-500 h-4 w-4 text-orange-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="privacy_accepted" class="font-medium text-gray-700">
                                        I agree to the <a href="#" class="text-orange-600 hover:text-orange-500">Privacy Policy</a> *
                                    </label>
                                    <p class="text-gray-500">
                                        We will protect your personal information and use it only for quotation purposes.
                                    </p>
                                </div>
                            </div>
                            @error('privacy_accepted')<p class="ml-7 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        {{-- Tombol Kirim --}}
                        <div class="pt-6 border-t border-gray-200">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <button type="submit" 
                                        class="flex-1 bg-gradient-to-r from-orange-600 to-amber-600 text-white font-semibold py-4 px-8 rounded-xl hover:from-orange-700 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                                    <span class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                        Kirim Permintaan Penawaran
                                    </span>
                                </button>
                                
                                <a href="{{ route('home') }}" 
                                   class="flex-none sm:flex-1 bg-gray-200 text-gray-700 font-semibold py-4 px-8 rounded-xl hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-300 text-center">
                                    Cancel
                                </a>
                            </div>
                            
                            <p class="mt-4 text-sm text-gray-500 text-center">
                                By submitting this form, you will receive a confirmation email and our team will contact you within 24-48 hours.
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    {{-- Kontak Information Section --}}
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Need Help with Your Request?</h2>
                <p class="text-lg text-gray-600">Our team is here to assist you with any questions about the quotation process.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @if($companyProfile && $companyProfile->phone)
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Call Us</h3>
                    <p class="text-gray-600">{{ $companyProfile->phone }}</p>
                    <p class="text-sm text-gray-500 mt-1">Mon-Fri, 8 AM - 6 PM</p>
                </div>
                @endif
                
                @if($companyProfile && $companyProfile->email)
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Email Us</h3>
                    <p class="text-gray-600">{{ $companyProfile->email }}</p>
                    <p class="text-sm text-gray-500 mt-1">24 hour response time</p>
                </div>
                @endif
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Visit Us</h3>
                    <p class="text-gray-600">{{ $companyProfile->address ?? 'Visit our office' }}</p>
                    <p class="text-sm text-gray-500 mt-1">Free consultation available</p>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        // Form enhancement with JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // File upload handling
            const fileInput = document.getElementById('attachments');
            const fileContainer = fileInput.closest('.border-dashed');
            
            fileInput.addEventListener('change', function() {
                const files = Array.from(this.files);
                if (files.length > 0) {
                    fileContainer.classList.add('border-orange-500', 'bg-orange-50');
                    const fileNames = files.map(file => file.name).join(', ');
                    const fileText = fileContainer.querySelector('.block.text-sm.font-medium');
                    fileText.textContent = `${files.length} file(s) selected: ${fileNames}`;
                }
            });

            // Form validation enhancement
            const form = document.getElementById('quotationForm');
            form.addEventListener('submit', function(e) {
                const requiredFields = ['name', 'email', 'terms_accepted', 'privacy_accepted'];
                let hasErrors = false;

                requiredFields.forEach(fieldName => {
                    const field = form.querySelector(`[name="${fieldName}"]`);
                    if (field && !field.value && fieldName !== 'terms_accepted' && fieldName !== 'privacy_accepted') {
                        hasErrors = true;
                        field.classList.add('border-red-500');
                    } else if ((fieldName === 'terms_accepted' || fieldName === 'privacy_accepted') && !field.checked) {
                        hasErrors = true;
                        field.classList.add('border-red-500');
                    }
                });

                if (hasErrors) {
                    e.preventDefault();
                    alert('Please fill in all required fields and accept the terms.');
                }
            });
        });
    </script>
    @endpush
</x-layouts.public>-4 gap-3">
                                @php $urgencyLevels = [
                                    'flexible' => ['label' => 'Flexible', 'desc' => 'No rush, can wait'],
                                    'normal' => ['label' => 'Normal', 'desc' => 'Standard timeline'],
                                    'urgent' => ['label' => 'Urgent', 'desc' => 'Need it soon'],
                                    'critical' => ['label' => 'Critical', 'desc' => 'Emergency project']
                                ] @endphp
                                
                                @foreach($urgencyLevels as $value => $info)
                                <label class="relative">
                                    <input type="radio" name="urgency" value="{{ $value }}" 
                                           class="sr-only peer" {{ old('urgency') == $value ? 'checked' : '' }}>
                                    <div class="p-4 border-2 border-gray-300 rounded-lg cursor-pointer transition-all duration-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:border-orange-300">
                                        <div class="font-medium text-sm">{{ $info['label'] }}</div>
                                        <div class="text-xs text-gray-500 mt-1">{{ $info['desc'] }}</div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('urgency')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- File Attachments Section --}}
                <div class="bg-gray-50 rounded-xl p-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        Attachments & Additional Information
                    </h2>
                    
                    <div class="space-y-6">
                        {{-- Upload File --}}
                        <div>
                            <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">Project Files</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-orange-400 transition-colors duration-200">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div class="mt-4">
                                    <label for="attachments" class="cursor-pointer">
                                        <span class="mt-2 block text-sm font-medium text-gray-900">Upload file proyek</span>
                                        <span class="mt-2 block text-sm text-gray-500">or drag and drop</span>
                                    </label>
                                    <input id="attachments" name="attachments[]" type="file" multiple class="sr-only"
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.dwg,.zip">
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    PDF, DOC, DOCX, JPG, PNG, DWG, ZIP up to 10MB each
                                </p>
                            </div>
                            @error('attachments.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        
                        {{-- Additional Information --}}
                        <div>
                            <label for="additional_info" class="block text-sm font-medium text-gray-700 mb-2">Additional Information</label>
                            <textarea id="additional_info" name="additional_info" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200"
                                      placeholder="Any additional information, special requests, or questions you'd like to share...">{{ old('additional_info') }}</textarea>
                            @error('additional_info')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        {{-- Source Tracking (Hidden) --}}
                        <input type="hidden" name="source" value="website">
                    </div>
                </div>

                {{-- Bagian Syarat dan Kirim --}}
                <div class="bg-gray-50 rounded-xl p-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Terms & Submission
                    </h2>
                    
                    <div class="space-y-6">
                        {{-- Terms and Conditions --}}
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="terms_accepted" name="terms_accepted" type="checkbox" value="1" required
                                           class="focus:ring-orange-500 h-4 w-4 text-orange-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="terms_accepted" class="font-medium text-gray-700">
                                        I agree to the <a href="#" class="text-orange-600 hover:text-orange-500">Terms and Conditions</a> *
                                    </label>
                                    <p class="text-gray-500">
                                        By submitting this form, you agree to our terms of service and quotation process.
                                    </p>
                                </div>
                            </div>
                            @error('terms_accepted')<p class="ml-7 text-sm text-red-600">{{ $message }}</p>@enderror
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="privacy_accepted" name="privacy_accepted" type="checkbox" value="1" required
                                           class="focus:ring-orange-500 h-4 w-4 text-orange-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="privacy_accepted" class="font-medium text-gray-700">
                                        I agree to the <a href="#" class="text-orange-600 hover:text-orange-500">Privacy Policy</a> *
                                    </label>
                                    <p class="text-gray-500">
                                        We will protect your personal information and use it only for quotation purposes.
                                    </p>
                                </div>
                            </div>
                            @error('privacy_accepted')<p class="ml-7 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        {{-- Tombol Kirim --}}
                        <div class="pt-6 border-t border-gray-200">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <button type="submit" 
                                        class="flex-1 bg-gradient-to-r from-orange-600 to-amber-600 text-white font-semibold py-4 px-8 rounded-xl hover:from-orange-700 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                                    <span class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                        Kirim Permintaan Penawaran
                                    </span>
                                </button>
                                
                                <a href="{{ route('home') }}" 
                                   class="flex-none sm:flex-1 bg-gray-200 text-gray-700 font-semibold py-4 px-8 rounded-xl hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-300 text-center">
                                    Cancel
                                </a>
                            </div>
                            
                            <p class="mt-4 text-sm text-gray-500 text-center">
                                By submitting this form, you will receive a confirmation email and our team will contact you within 24-48 hours.
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    {{-- Kontak Information Section --}}
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Need Help with Your Request?</h2>
                <p class="text-lg text-gray-600">Our team is here to assist you with any questions about the quotation process.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @if($companyProfile && $companyProfile->phone)
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Call Us</h3>
                    <p class="text-gray-600">{{ $companyProfile->phone }}</p>
                    <p class="text-sm text-gray-500 mt-1">Mon-Fri, 8 AM - 6 PM</p>
                </div>
                @endif
                
                @if($companyProfile && $companyProfile->email)
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Email Us</h3>
                    <p class="text-gray-600">{{ $companyProfile->email }}</p>
                    <p class="text-sm text-gray-500 mt-1">24 hour response time</p>
                </div>
                @endif
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Visit Us</h3>
                    <p class="text-gray-600">{{ $companyProfile->address ?? 'Visit our office' }}</p>
                    <p class="text-sm text-gray-500 mt-1">Free consultation available</p>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        // Form enhancement with JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // File upload handling
            const fileInput = document.getElementById('attachments');
            const fileContainer = fileInput.closest('.border-dashed');
            
            fileInput.addEventListener('change', function() {
                const files = Array.from(this.files);
                if (files.length > 0) {
                    fileContainer.classList.add('border-orange-500', 'bg-orange-50');
                    const fileNames = files.map(file => file.name).join(', ');
                    const fileText = fileContainer.querySelector('.block.text-sm.font-medium');
                    fileText.textContent = `${files.length} file(s) selected: ${fileNames}`;
                }
            });

            // Form validation enhancement
            const form = document.getElementById('quotationForm');
            form.addEventListener('submit', function(e) {
                const requiredFields = ['name', 'email', 'terms_accepted', 'privacy_accepted'];
                let hasErrors = false;

                requiredFields.forEach(fieldName => {
                    const field = form.querySelector(`[name="${fieldName}"]`);
                    if (field && !field.value && fieldName !== 'terms_accepted' && fieldName !== 'privacy_accepted') {
                        hasErrors = true;
                        field.classList.add('border-red-500');
                    } else if ((fieldName === 'terms_accepted' || fieldName === 'privacy_accepted') && !field.checked) {
                        hasErrors = true;
                        field.classList.add('border-red-500');
                    }
                });

                if (hasErrors) {
                    e.preventDefault();
                    alert('Please fill in all required fields and accept the terms.');
                }
            });
        });
    </script>
    @endpush
</x-layouts.public>