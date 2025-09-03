{{-- resources/views/client/quotations/create.blade.php --}}
<x-layouts.client>
    <x-slot name="title">Minta Penawaran</x-slot>
    <x-slot name="description">Ceritakan kepada kami tentang proyek Anda dan kami akan menghubungi Anda dengan penawaran terperinci.</x-slot>

    <!-- Progress Header -->
    <div class="mb-8">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Request a Quotation</h1>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">Dapatkan penawaran terperinci untuk proyek Anda dalam 3 langkah mudah</p>
        </div>
        
        <!-- Progress Steps - Connect to main form's Alpine.js data -->
        <div class="flex items-center justify-center space-x-8" id="progress-steps">
            <div class="flex items-center step-indicator" data-step="1">
                <div class="step-circle flex items-center justify-center w-10 h-10 rounded-full transition-all duration-300">
                    1
                </div>
                <span class="step-text ml-2 text-sm font-medium transition-all duration-300">Informasi Kontak</span>
            </div>
            
            <div class="step-connector w-16 h-0.5 bg-gray-300 dark:bg-gray-600 transition-all duration-300"></div>
            
            <div class="flex items-center step-indicator" data-step="2">
                <div class="step-circle flex items-center justify-center w-10 h-10 rounded-full transition-all duration-300">
                    2
                </div>
                <span class="step-text ml-2 text-sm font-medium transition-all duration-300">Detail Penawaran</span>
            </div>
            
            <div class="step-connector w-16 h-0.5 bg-gray-300 dark:bg-gray-600 transition-all duration-300"></div>
            
            <div class="flex items-center step-indicator" data-step="3">
                <div class="step-circle flex items-center justify-center w-10 h-10 rounded-full transition-all duration-300">
                    3
                </div>
                <span class="step-text ml-2 text-sm font-medium transition-all duration-300">Informasi Tambahan</span>
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
              @submit="handleKirim">
            @csrf

            <!-- Step 1: Personal Informasi -->
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
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Informasi Kontak</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Beri tahu kami cara menghubungi Anda</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
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
                                Alamat Email <span class="text-red-500">*</span>
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
                                Nomor Telepom
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
                                Nama Perusahaan
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
                            Selanjutnya: Detail Penawaran
                            <svg class="w-4 h-4 ml-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Project Informasi -->
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
                                Kategori Layanan
                            </label>
                            <select name="service_id" 
                                    id="service_id"
                                    x-model="formData.service_id"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200">
                                <option value="">Pilih kategori layanan...</option>
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
                                Jenis Penawaran <span class="text-red-500">*</span>
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
                                Lokasi
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
    <label for="budget" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        Estimasi Anggaran
        <span class="text-red-500">*</span>
    </label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <span class="text-gray-500 text-sm">Rp.</span>
        </div>
        <input type="text" 
               name="budget" 
               id="budget"
               value="{{ old('budget') }}"
               x-model="formData.budget"
               required
               placeholder="Enter your project budget"
               class="block w-full pl-12 pr-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                      shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200 budget-input"
               x-on:input="formatBudgetInput($event)"
               x-on:blur="validateBudget($event)">
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
        <p class="mt-1">Klik jumlah di atas untuk pemilihan cepat, atau masukkan anggaran khusus Anda</p>
    </div>
    
    {{-- Budget Validation Messages --}}
    <div id="budget-feedback" class="mt-1 text-xs hidden">
        <div class="budget-warning text-yellow-600 dark:text-yellow-400 hidden">
            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            Anggaran tampaknya cukup rendah untuk proyek konstruksi pada umumnya
        </div>
        <div class="budget-success text-green-600 dark:text-green-400 hidden">
            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Anggaran terlihat bagus untuk cakupan proyek Anda
        </div>
    </div>
    
    @error('budget')
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

                        <div class="md:col-span-2">
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Perkiraan Tanggal Mulai
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
                            Kembali
                        </button>

                        <button type="button" 
                                @click="nextStep()"
                                :disabled="!isStep2Valid()"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 
                                       disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                            Selanjutnya: Informasi Tambahan
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
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Persyaratan Proyek</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Berikan informasi terperinci tentang kebutuhan Anda</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="requirements" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Detai Persyaratan<span class="text-red-500">*</span>
                            </label>
                            <textarea name="requirements" 
                                      id="requirements" 
                                      rows="8" 
                                      required
                                      x-model="formData.requirements"
                                      class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white 
                                             shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200 resize-none"
                                      placeholder="Jelaskan kebutuhan proyek Anda secara detail. Sertakan: • Apa yang ingin Anda capai • Fitur dan fungsi utama • Target audiens • Teknologi atau platform spesifik apa pun • Preferensi desain • Ekspektasi waktu • Detail penting lainnya...">{{ old('requirements') }}</textarea>
                            <div class="mt-2 flex items-center justify-between">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Berikanlah rincian semaksimal mungkin untuk memperoleh kutipan yang paling akurat.
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
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Lampiran (Optional)</h4>
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
                            Kembali
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
                                Kirim Permintaan Penawaran
                            </span>
                            <span x-show="submitting" class="flex items-center">
                                <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Mengirim...
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
                        <span class="text-gray-600 dark:text-gray-400">Kontak:</span>
                        <span class="text-gray-900 dark:text-white font-medium" x-text="formData.name"></span>
                    </div>
                    
                    <div x-show="formData.email">
                        <span class="text-gray-600 dark:text-gray-400">Email:</span>
                        <span class="text-gray-900 dark:text-white font-medium" x-text="formData.email"></span>
                    </div>
                    
                    <div x-show="formData.project_type">
                        <span class="text-gray-600 dark:text-gray-400">Proyek:</span>
                        <span class="text-gray-900 dark:text-white font-medium" x-text="formData.project_type"></span>
                    </div>
                    
                    <div x-show="formData.budget_range">
                        <span class="text-gray-600 dark:text-gray-400">Anggaran:</span>
                        <span class="text-gray-900 dark:text-white font-medium" x-text="formData.budget_range"></span>
                    </div>
                    
                    <div x-show="formData.location">
                        <span class="text-gray-600 dark:text-gray-400">Lokasi:</span>
                        <span class="text-gray-900 dark:text-white font-medium" x-text="formData.location"></span>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                        <p>✓ Respons cepat dalam 24 jam</p>
<p>✓ Rincian proyek terperinci</p>
<p>✓ Harga transparan</p>
<p>✓ Konsultasi gratis termasuk</p>
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
            budget: @json(old('budget', '')), // Updated from budget_range
            start_date: @json(old('start_date', '')),
            requirements: @json(old('requirements', ''))
        },

        init() {
            console.log('Quotation form handler initialized');
            
            // Update progress indicator based on current step
            this.updateProgressIndicator();
            
            // Watch for currentStep changes and update indicator
            this.$watch('currentStep', () => {
                this.updateProgressIndicator();
            });
            
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
        },

        nextStep() {
            if (this.currentStep === 1 && this.isStep1Valid()) {
                this.currentStep = 2;
                this.scrollToTop();
            } else if (this.currentStep === 2 && this.isStep2Valid()) {
                this.currentStep = 3;
                this.scrollToTop();
            }
        },

        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
                this.scrollToTop();
            }
        },

        // Fixed updateProgressIndicator method
        updateProgressIndicator() {
            const stepIndicators = document.querySelectorAll('.step-indicator');
            const stepConnectors = document.querySelectorAll('.step-connector');
            
            stepIndicators.forEach((indicator, index) => {
                const stepNumber = index + 1;
                const circle = indicator.querySelector('.step-circle');
                const text = indicator.querySelector('.step-text');
                
                if (stepNumber <= this.currentStep) {
                    // Active/completed step
                    circle.className = 'step-circle flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white font-semibold transition-all duration-300 shadow-lg';
                    text.className = 'step-text ml-2 text-sm font-medium text-blue-600 dark:text-blue-400 transition-all duration-300';
                } else {
                    // Inactive step
                    circle.className = 'step-circle flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 font-semibold transition-all duration-300';
                    text.className = 'step-text ml-2 text-sm font-medium text-gray-500 dark:text-gray-400 transition-all duration-300';
                }
            });
            
            // Update connectors
            stepConnectors.forEach((connector, index) => {
                const connectorStep = index + 1;
                if (connectorStep < this.currentStep) {
                    connector.className = 'step-connector w-16 h-0.5 bg-blue-500 transition-all duration-300';
                } else {
                    connector.className = 'step-connector w-16 h-0.5 bg-gray-300 dark:bg-gray-600 transition-all duration-300';
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
            return this.formData.project_type && this.formData.budget;
        },

        isStep3Valid() {
            return this.formData.requirements && this.formData.requirements.length >= 50;
        },

        handleKirim(event) {
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
                            ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                            : type === 'error'
                            ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>'
                            : '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 011-1 1 1 0 010 2v3a1 1 0 001 1h1a1 1 0 110 2v.01a1 1 0 01-1 1H9a1 1 0 01-1-1V10a1 1 0 011-1V6z" clip-rule="evenodd"></path></svg>'
                        }
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto flex-shrink-0 rounded-lg p-1 hover:bg-black/10">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
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

        /* Memuat...inner */
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