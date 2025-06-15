{{-- resources/views/about.blade.php --}}
<x-layouts.app 
    :title="$seoData['title']"
    :description="$seoData['description']" 
    :keywords="$seoData['keywords']"
    :breadcrumbs="$seoData['breadcrumbs']"
>
    {{-- Hero Section --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-white to-purple-50 py-20 lg:py-32">
        <div class="absolute inset-0 bg-grid-slate-100 [mask-image:linear-gradient(0deg,white,rgba(255,255,255,0.6))] -z-10"></div>
        
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <div class="mb-8">
                    @if($companyProfile->logo_url)
                        <img src="{{ $companyProfile->logo_url }}" 
                             alt="{{ $companyProfile->company_name }}" 
                             class="h-16 w-auto mx-auto mb-6">
                    @endif
                    
                    <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                        Tentang 
                        <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            {{ $companyProfile->company_name ?? config('app.name') }}
                        </span>
                    </h1>
                    
                    @if($companyProfile->tagline)
                        <p class="text-xl md:text-2xl text-gray-600 mb-8 leading-relaxed">
                            {{ $companyProfile->tagline }}
                        </p>
                    @endif
                    
                    @if($companyProfile->about)
                        <div class="prose prose-lg mx-auto text-gray-700">
                            {!! nl2br(e($companyProfile->about)) !!}
                        </div>
                    @endif
                </div>
                
                {{-- Statistics Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-16">
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-white/20 hover:shadow-xl transition-all duration-300">
                        <div class="text-3xl font-bold text-blue-600 mb-2">{{ $statistics['years_experience'] }}+</div>
                        <div class="text-sm text-gray-600 font-medium">Tahun Pengalaman</div>
                    </div>
                    
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-white/20 hover:shadow-xl transition-all duration-300">
                        <div class="text-3xl font-bold text-green-600 mb-2">{{ $statistics['projects_completed'] }}+</div>
                        <div class="text-sm text-gray-600 font-medium">Project Selesai</div>
                    </div>
                    
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-white/20 hover:shadow-xl transition-all duration-300">
                        <div class="text-3xl font-bold text-purple-600 mb-2">{{ $statistics['happy_clients'] }}+</div>
                        <div class="text-sm text-gray-600 font-medium">Klien Puas</div>
                    </div>
                    
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-white/20 hover:shadow-xl transition-all duration-300">
                        <div class="text-3xl font-bold text-orange-600 mb-2">{{ $statistics['team_members'] }}+</div>
                        <div class="text-sm text-gray-600 font-medium">Anggota Tim</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Vision & Mission Section --}}
    @if($companyProfile->vision || $companyProfile->mission)
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">
                <div class="grid md:grid-cols-2 gap-12">
                    @if($companyProfile->vision)
                    <div class="group">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-3xl p-8 h-full border border-blue-200 group-hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center mb-6">
                                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900">Visi Kami</h3>
                            </div>
                            <p class="text-gray-700 leading-relaxed">{{ $companyProfile->vision }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($companyProfile->mission)
                    <div class="group">
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-3xl p-8 h-full border border-purple-200 group-hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center mb-6">
                                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900">Misi Kami</h3>
                            </div>
                            <p class="text-gray-700 leading-relaxed">{{ $companyProfile->mission }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Company Values Section --}}
    @if($companyValues && count($companyValues) > 0)
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Nilai-Nilai Kami</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Nilai-nilai yang menjadi fondasi dalam setiap langkah dan keputusan yang kami ambil.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($companyValues as $value)
                    <div class="group">
                        <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-full">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @switch($value['icon'])
                                        @case('lightbulb')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                            @break
                                        @case('award')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                            @break
                                        @case('users')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                            @break
                                        @case('shield-check')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            @break
                                        @default
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    @endswitch
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $value['title'] }}</h3>
                            <p class="text-gray-600 leading-relaxed">{{ $value['description'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Featured Team Section --}}
    @if($featuredTeamMembers->count() > 0)
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Tim Profesional Kami</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Berkenalan dengan tim ahli yang berpengalaman dan berkomitmen memberikan hasil terbaik.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                    @foreach($featuredTeamMembers as $member)
                    <div class="group">
                        <div class="bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
                            <div class="aspect-square relative overflow-hidden">
                                @if($member->hasPhoto())
                                    <img src="{{ $member->photo_url }}" 
                                         alt="{{ $member->name }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                @endif
                                
                                {{-- Social Media Overlay --}}
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                    <div class="flex space-x-3">
                                        @if($member->linkedin)
                                            <a href="{{ $member->linkedin }}" target="_blank" 
                                               class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-blue-600 hover:bg-blue-50 transition-colors">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                                </svg>
                                            </a>
                                        @endif
                                        
                                        @if($member->twitter)
                                            <a href="{{ $member->twitter }}" target="_blank" 
                                               class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-blue-400 hover:bg-blue-50 transition-colors">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $member->name }}</h3>
                                <p class="text-blue-600 font-medium mb-2">{{ $member->position }}</p>
                                @if($member->department)
                                    <p class="text-sm text-gray-500 mb-3">{{ $member->department->name }}</p>
                                @endif
                                @if($member->bio)
                                    <p class="text-gray-600 text-sm leading-relaxed">{{ Str::limit($member->bio, 100) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="text-center">
                    <a href="{{ route('about.team') }}" 
                       class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg">
                        <span>Lihat Semua Tim</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Services Section --}}
    @if($services && count($services) > 0)
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Layanan Kami</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Kami menyediakan berbagai layanan teknologi untuk mendukung kesuksesan bisnis Anda.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($services as $service)
                    <div class="group">
                        <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-full">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @switch($service['icon'])
                                        @case('code')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                            @break
                                        @case('smartphone')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a1 1 0 001-1V4a1 1 0 00-1-1H8a1 1 0 00-1 1v16a1 1 0 001 1z"/>
                                            @break
                                        @case('palette')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM7 3H5a2 2 0 00-2 2v12a4 4 0 004 4h2a2 2 0 002-2V5a2 2 0 00-2-2z"/>
                                            @break
                                        @case('trending-up')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                            @break
                                        @case('cloud')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                            @break
                                        @case('users')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            @break
                                        @default
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    @endswitch
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $service['title'] }}</h3>
                            <p class="text-gray-600 leading-relaxed">{{ $service['description'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Timeline/Milestones Section --}}
    @if($milestones && count($milestones) > 0)
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Perjalanan Kami</h2>
                    <p class="text-xl text-gray-600">
                        Melihat kembali pencapaian dan milestone penting dalam perjalanan perusahaan.
                    </p>
                </div>

                <div class="relative">
                    {{-- Timeline Line --}}
                    <div class="absolute left-8 md:left-1/2 transform md:-translate-x-1/2 w-1 h-full bg-gradient-to-b from-blue-500 to-purple-600 rounded-full"></div>
                    
                    @foreach($milestones as $index => $milestone)
                    <div class="relative flex items-center mb-12 {{ $index % 2 == 0 ? 'md:flex-row' : 'md:flex-row-reverse' }}">
                        {{-- Timeline Dot --}}
                        <div class="absolute left-4 md:left-1/2 transform md:-translate-x-1/2 w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full border-4 border-white shadow-lg z-10"></div>
                        
                        {{-- Content --}}
                        <div class="ml-16 md:ml-0 md:w-5/12 {{ $index % 2 == 0 ? 'md:mr-auto md:pr-8' : 'md:ml-auto md:pl-8' }}">
                            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                                <div class="text-2xl font-bold text-blue-600 mb-2">{{ $milestone['year'] }}</div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $milestone['title'] }}</h3>
                                <p class="text-gray-600 leading-relaxed">{{ $milestone['description'] }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Certifications Section --}}
    @if($certifications && count($certifications) > 0)
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Sertifikasi & Penghargaan</h2>
                    <p class="text-xl text-gray-600">
                        Pengakuan atas komitmen kami terhadap kualitas dan standar industri terbaik.
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8">
                    @foreach($certifications as $cert)
                    <div class="group">
                        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-full flex flex-col items-center text-center">
                            @if(isset($cert['image']) && $cert['image'])
                                <img src="{{ asset('storage/' . $cert['image']) }}" 
                                     alt="{{ $cert['name'] ?? $cert['title'] }}"
                                     class="w-16 h-16 object-contain mb-4">
                            @else
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                </div>
                            @endif
                            
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">{{ $cert['name'] ?? $cert['title'] }}</h3>
                            @if(isset($cert['issuer']))
                                <p class="text-xs text-gray-600">{{ $cert['issuer'] }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Call to Action Section --}}
    <section class="py-20 bg-gradient-to-br from-blue-600 via-purple-600 to-blue-800 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-4xl mx-auto text-center text-white">
                <h2 class="text-3xl md:text-5xl font-bold mb-6">
                    Siap Berkolaborasi dengan Kami?
                </h2>
                <p class="text-xl md:text-2xl mb-8 opacity-90">
                    Mari wujudkan visi teknologi Anda bersama tim profesional kami.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('contact.index') }}" 
                       class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-bold rounded-xl hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 shadow-lg">
                        <span>Hubungi Kami</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </a>
                    @if(Route::has('services'))
                    <a href="{{ route('services') }}" 
                       class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-bold rounded-xl hover:bg-white hover:text-blue-600 transform hover:scale-105 transition-all duration-300">
                        <span>Lihat Layanan</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        // Smooth scroll animation for statistics
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, observerOptions);

        // Observe all sections for animation
        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });

        // Counter animation for statistics
        function animateCounter(element, target) {
            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    element.textContent = target + '+';
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current) + '+';
                }
            }, 20);
        }

        // Trigger counter animation when statistics come into view
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = entry.target.querySelectorAll('.text-3xl');
                    counters.forEach(counter => {
                        const target = parseInt(counter.textContent.replace('+', ''));
                        animateCounter(counter, target);
                    });
                    statsObserver.unobserve(entry.target);
                }
            });
        }, observerOptions);

        const statsSection = document.querySelector('.grid.grid-cols-2.md\\:grid-cols-4');
        if (statsSection) {
            statsObserver.observe(statsSection);
        }
    </script>

    <style>
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bg-grid-slate-100 {
            background-image: url("data:image/svg+xml,%3csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3e%3cg fill='%23f1f5f9' fill-opacity='0.4' fill-rule='evenodd'%3e%3cpath d='m0 40l40-40h-40z'/%3e%3cpath d='m40 40v-40h-40z'/%3e%3c/g%3e%3c/svg%3e");
        }
    </style>
    @endpush
</x-layouts.app>