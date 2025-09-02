{{-- resources/views/pages/contact.blade.php --}}
<x-layouts.public
    title="Hubungi Kami - {{ $siteConfig['site_title'] }}"
    description="Hubungi kami untuk konsultasi gratis. Informasi kontak dan formulir kontak untuk layanan konstruksi profesional."
    keywords="hubungi kami, kontak, konsultasi gratis, formulir kontak, perusahaan konstruksi"
    type="website"
>

{{-- Hero Section --}}
<section class="relative pt-32 pb-12 bg-gradient-to-br from-orange-50 via-white to-amber-50 overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 bg-[url('/images/grid.svg')] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]"></div>
    
    {{-- Floating Elements --}}
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-1/5 left-1/4 w-32 h-32 bg-orange-200/30 rounded-full animate-float"></div>
        <div class="absolute bottom-1/3 right-1/4 w-48 h-48 bg-amber-200/20 rounded-full animate-float animation-delay-1000"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
        {{-- Breadcrumbs --}}
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-orange-600 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Beranda
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-orange-600 md:ml-2 font-medium">Hubungi Kami</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="text-center max-w-4xl mx-auto">
            <div class="inline-flex items-center px-4 py-2 bg-orange-100 text-orange-600 rounded-full text-sm font-semibold mb-4">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                </svg>
                Konsultasi & Penawaran Gratis
            </div>
            
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                Hubungi 
                <span class="bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
                    Kami
                </span>
            </h1>
            <p class="text-xl text-gray-600 mb-6 leading-relaxed">
                Dapatkan konsultasi gratis dan penawaran awal untuk proyek Anda. Tim ahli kami siap membantu mewujudkan impian konstruksi Anda dengan solusi terbaik dan harga kompetitif.
            </p>
            
            {{-- Service Information --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 mb-8 border border-blue-100">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.894A1 1 0 0018 16V3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            Ingin Penawaran Detail? 
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Untuk mendapatkan penawaran detail dengan rincian lengkap dan akses ke sistem manajemen proyek, silakan 
                            <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-medium underline">daftar sebagai klien</a>
                            atau 
                            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium underline">login</a>
                            jika sudah memiliki akun.
                        </p>
                    </div>
                </div>
            </div>
            
            <p class="text-lg text-gray-500 mb-8">
                Siap memulai proyek Anda? Punya pertanyaan tentang layanan kami? Kami siap membantu. 
                Hubungi kami hari ini untuk konsultasi gratis dan mari wujudkan visi Anda.
            </p>
            
            {{-- Quick Kontak Actions --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                @if($contactInfo['phone'])
                <a href="tel:{{ $contactInfo['phone'] }}" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-600 to-amber-600 text-white font-semibold rounded-xl hover:from-orange-700 hover:to-amber-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    Call {{ $contactInfo['phone'] }}
                </a>
                @endif
                
                @if($contactInfo['email'])
                <a href="mailto:{{ $contactInfo['email'] }}" 
                   class="inline-flex items-center px-8 py-4 border-2 border-orange-600 text-orange-600 font-semibold rounded-xl hover:bg-orange-600 hover:text-white transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Send Email
                </a>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Kontak Information Cards --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            {{-- Phone Kontak --}}
            @if($contactInfo['phone'])
            <div class="text-center group">
                <div class="w-20 h-20 bg-gradient-to-r from-orange-500 to-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Call Us</h3>
                <p class="text-gray-600 mb-4">Mon - Fri from 8am to 5pm</p>
                <a href="tel:{{ $contactInfo['phone'] }}" class="text-orange-600 font-semibold hover:text-orange-700 transition-colors">
                    {{ $contactInfo['phone'] }}
                </a>
            </div>
            @endif
            
            {{-- Email Kontak --}}
            @if($contactInfo['email'])
            <div class="text-center group">
                <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Email Us</h3>
                <p class="text-gray-600 mb-4">We'll respond within 24 hours</p>
                <a href="mailto:{{ $contactInfo['email'] }}" class="text-orange-600 font-semibold hover:text-orange-700 transition-colors">
                    {{ $contactInfo['email'] }}
                </a>
            </div>
            @endif
            
            {{-- Lokasi --}}
            @if($contactInfo['address'])
            <div class="text-center group">
                <div class="w-20 h-20 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Visit Us</h3>
                <p class="text-gray-600 mb-4">Come and say hello at our office</p>
                <address class="text-orange-600 font-semibold not-italic">
                    {{ $contactInfo['address'] }}
                </address>
            </div>
            @endif
        </div>
    </div>
</section>

{{-- Kontak Form Section --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {{-- Kontak Form --}}
            <div>
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Send us a Message</h2>
                    <p class="text-gray-600 mb-8">Fill out the form below and we'll get back to you as soon as possible.</p>
                    
                    {{-- Success Message --}}
                    @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Kontak Form --}}
                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-6" id="contactForm">
                        @csrf
                        
                        {{-- Pre-fill service if coming from service page --}}
                        @if(request('service'))
                        <input type="hidden" name="subject" value="Inquiry about {{ request('service') }}">
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Name *
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-colors @error('name') border-red-500 @enderror">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat Email *
                                </label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-colors @error('email') border-red-500 @enderror">
                                @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Phone --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number
                                </label>
                                <input type="tel" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-colors @error('phone') border-red-500 @enderror">
                                @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            {{-- Company --}}
                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700 mb-2">
                                    Company Name
                                </label>
                                <input type="text" 
                                       id="company" 
                                       name="company" 
                                       value="{{ old('company') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-colors @error('company') border-red-500 @enderror">
                                @error('company')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        {{-- Subject --}}
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                Subject *
                            </label>
                            <input type="text" 
                                   id="subject" 
                                   name="subject" 
                                   value="{{ old('subject', request('service') ? 'Inquiry about ' . request('service') : '') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-colors @error('subject') border-red-500 @enderror">
                            @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Message --}}
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                Message *
                            </label>
                            <textarea id="message" 
                                      name="message" 
                                      rows="5" 
                                      required
                                      placeholder="Tell us about your project, requirements, or any questions you have..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-colors resize-none @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                            @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Tombol Kirim --}}
                        <div>
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-orange-600 to-amber-600 text-white font-semibold py-4 rounded-xl hover:from-orange-700 hover:to-amber-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl"
                                    id="submitBtn">
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    Send Message
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            {{-- Kontak Information & Why Choose Us --}}
            <div class="space-y-8">
                {{-- Kontak Details --}}
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Kontak Information</h3>
                    <div class="space-y-6">
                        @if($contactInfo['phone'])
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Phone</h4>
                                <a href="tel:{{ $contactInfo['phone'] }}" class="text-gray-600 hover:text-orange-600 transition-colors">
                                    {{ $contactInfo['phone'] }}
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if($contactInfo['email'])
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Email</h4>
                                <a href="mailto:{{ $contactInfo['email'] }}" class="text-gray-600 hover:text-orange-600 transition-colors">
                                    {{ $contactInfo['email'] }}
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if($contactInfo['address'])
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Alamat</h4>
                                <address class="text-gray-600 not-italic">
                                    {{ $contactInfo['address'] }}
                                </address>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Business Hours --}}
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Business Hours</h4>
                                <div class="text-gray-600 text-sm space-y-1">
                                    <div>Monday - Friday: 8:00 AM - 5:00 PM</div>
                                    <div>Saturday: 8:00 AM - 2:00 PM</div>
                                    <div>Minggu: Tutup</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Why Choose Us --}}
                <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl border border-orange-100 p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Why Choose Us?</h3>
                    <ul class="space-y-4">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Free consultation and project assessment</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Licensed and insured professionals</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Competitive pricing with no hidden costs</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Quality guarantee on all work</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">24/7 emergency support</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Map Section (Optional) --}}
{{-- Uncomment and add your coordinates if you want to include a map
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Find Us</h2>
            <p class="text-gray-600">Visit our office for in-person consultations</p>
        </div>
        
        <div class="bg-gray-200 rounded-2xl h-96 flex items-center justify-center">
                            <div class="text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-gray-500">Map integration can be added here</p>
            </div>
        </div>
    </div>
</section>
--}}

{{-- FAQ Section --}}
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
            <p class="text-gray-600">Quick answers to common questions about our services and process.</p>
        </div>
        
        <div class="space-y-4">
            <div class="bg-gray-50 rounded-xl shadow-md overflow-hidden">
                <button class="w-full px-6 py-4 text-left flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-orange-500 faq-toggle"
                        data-target="faq-0">
                    <span class="font-semibold text-gray-900">How do I get started with my project?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-6 pb-4" id="faq-0">
                    <p class="text-gray-600 leading-relaxed">Simply contact us through this form, phone, or email. We'll schedule a free consultation to discuss your project requirements, timeline, and budget. Our team will then provide you with a detailed proposal and quote.</p>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-xl shadow-md overflow-hidden">
                <button class="w-full px-6 py-4 text-left flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-orange-500 faq-toggle"
                        data-target="faq-1">
                    <span class="font-semibold text-gray-900">Do you provide free estimates?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-6 pb-4" id="faq-1">
                    <p class="text-gray-600 leading-relaxed">Yes! We provide free, no-obligation estimates for all projects. Our team will assess your requirements and provide a detailed quote with transparent pricing and no hidden costs.</p>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-xl shadow-md overflow-hidden">
                <button class="w-full px-6 py-4 text-left flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-orange-500 faq-toggle"
                        data-target="faq-2">
                    <span class="font-semibold text-gray-900">How long does a typical project take?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-6 pb-4" id="faq-2">
                    <p class="text-gray-600 leading-relaxed">Project timelines vary depending on scope and complexity. Small projects may take 1-2 weeks, while larger construction projects can take several months. We'll provide you with a detailed timeline during the planning phase.</p>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-xl shadow-md overflow-hidden">
                <button class="w-full px-6 py-4 text-left flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-orange-500 faq-toggle"
                        data-target="faq-3">
                    <span class="font-semibold text-gray-900">Are you licensed and insured?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-6 pb-4" id="faq-3">
                    <p class="text-gray-600 leading-relaxed">Yes, we are fully licensed and insured. All our work is covered by comprehensive liability insurance, and we maintain all necessary licenses and certifications for construction and engineering services.</p>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-xl shadow-md overflow-hidden">
                <button class="w-full px-6 py-4 text-left flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-orange-500 faq-toggle"
                        data-target="faq-4">
                    <span class="font-semibold text-gray-900">What payment methods do you accept?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-6 pb-4" id="faq-4">
                    <p class="text-gray-600 leading-relaxed">We accept various payment methods including bank transfers, checks, and credit cards. For larger projects, we offer flexible payment schedules tied to project milestones to help manage your budget effectively.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="py-20 bg-gradient-to-r from-orange-600 via-amber-600 to-orange-700">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            Siap Memulai Proyek Anda?
        </h2>
        <p class="text-xl text-orange-100 mb-8">
            Don't wait any longer. Kontak us today and let's bring your vision to life with professional expertise and quality craftsmanship.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @if($contactInfo['phone'])
            <a href="tel:{{ $contactInfo['phone'] }}" 
               class="inline-flex items-center px-8 py-4 bg-white text-orange-600 font-semibold rounded-xl hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                Call {{ $contactInfo['phone'] }}
            </a>
            @endif
            <a href="#contactForm" 
               class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-semibold rounded-xl hover:bg-white hover:text-orange-600 transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.405L3 21l2.595-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                </svg>
                Send Message
            </a>
        </div>
    </div>
</section>

{{-- JavaScript for Interactive Elements --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Toggle Functionality
    const faqToggles = document.querySelectorAll('.faq-toggle');
    
    faqToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const content = document.getElementById(targetId);
            const icon = this.querySelector('.faq-icon');
            
            // Tutup semua yang lain FAQs
            faqToggles.forEach(otherToggle => {
                if (otherToggle !== this) {
                    const otherTargetId = otherToggle.getAttribute('data-target');
                    const otherContent = document.getElementById(otherTargetId);
                    const otherIcon = otherToggle.querySelector('.faq-icon');
                    
                    otherContent.classList.add('hidden');
                    otherIcon.classList.remove('rotate-180');
                }
            });
            
            // Toggle current FAQ
            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        });
    });
    
    // Form submission handling
    const contactForm = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');
    const originalBtnText = submitBtn.innerHTML;
    
    if (contactForm) {
        contactForm.addEventListener('submit', function() {
            // Show loading state
            submitBtn.innerHTML = `
                <span class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sending...
                </span>
            `;
            submitBtn.disabled = true;
        });
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Form validation enhancement
    const inputs = document.querySelectorAll('input, textarea');
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
        const value = field.value.trim();
        const isRequired = field.hasAttribute('required');
        const isEmail = field.type === 'email';
        
        // Remove existing error styling
        field.classList.remove('border-red-500', 'border-green-500');
        
        if (isRequired && !value) {
            field.classList.add('border-red-500');
            return false;
        }
        
        if (isEmail && value && !isValidEmail(value)) {
            field.classList.add('border-red-500');
            return false;
        }
        
        if (value) {
            field.classList.add('border-green-500');
        }
        
        return true;
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Auto-resize textarea
    const textarea = document.getElementById('message');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
});
</script>
@endpush

{{-- Additional CSS --}}
@push('styles')
<style>
/* Animation keyframes */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

/* Animation classes */
.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animation-delay-1000 {
    animation-delay: 1000ms;
}

/* Enhanced transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Form focus enhancements */
input:focus, textarea:focus {
    transform: scale(1.02);
}

/* Custom gradient backgrounds */
.bg-gradient-orange {
    background: linear-gradient(135deg, #f97316 0%, #fb923c 25%, #fbbf24 50%, #f59e0b 75%, #ea580c 100%);
}

/* Enhanced hover effects */
.hover-lift:hover {
    transform: translateY(-4px);
}

/* Loading spinner */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Success message animation */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.success-message {
    animation: slideDown 0.3s ease-out;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .text-4xl {
        font-size: 2.25rem;
        line-height: 2.5rem;
    }
    
    .text-6xl {
        font-size: 3rem;
        line-height: 1;
    }
}
</style>
@endpush

</x-layouts.public>