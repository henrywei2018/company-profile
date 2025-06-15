{{-- resources/views/about/team.blade.php --}}
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
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                    Tim 
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Profesional
                    </span>
                </h1>
                
                <p class="text-xl md:text-2xl text-gray-600 mb-8 leading-relaxed">
                    Berkenalan dengan tim ahli yang berpengalaman dan berkomitmen memberikan hasil terbaik untuk setiap project.
                </p>

                {{-- Department Filter --}}
                @if($departments->count() > 0)
                <div class="flex flex-wrap justify-center gap-3 mb-8">
                    <button onclick="filterTeam('all')" 
                            class="filter-btn active px-6 py-3 rounded-full font-medium transition-all duration-300">
                        Semua Tim ({{ $teamMembers->count() }})
                    </button>
                    @foreach($departments as $dept)
                        <button onclick="filterTeam('{{ $dept->slug }}')" 
                                class="filter-btn px-6 py-3 rounded-full font-medium transition-all duration-300">
                            {{ $dept->name }} ({{ $dept->active_team_members_count }})
                        </button>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Team Members Section --}}
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                @if($teamByDepartment->count() > 0)
                    @foreach($teamByDepartment as $departmentName => $members)
                    <div class="department-section mb-16" data-department="{{ Str::slug($departmentName) }}">
                        {{-- Department Header --}}
                        <div class="text-center mb-12">
                            <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $departmentName }}</h2>
                            <div class="w-24 h-1 bg-gradient-to-r from-blue-600 to-purple-600 mx-auto rounded-full"></div>
                        </div>

                        {{-- Team Members Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                            @foreach($members as $member)
                            <div class="team-member-card group" data-department="{{ $member->department ? $member->department->slug : 'other' }}">
                                <div class="bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
                                    {{-- Photo --}}
                                    <div class="aspect-square relative overflow-hidden">
                                        @if($member->hasPhoto())
                                            <img src="{{ $member->photo_url }}" 
                                                 alt="{{ $member->name }}"
                                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                                <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        {{-- Social Media Overlay --}}
                                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                            <div class="flex space-x-3">
                                                @if($member->linkedin)
                                                    <a href="{{ $member->linkedin }}" target="_blank" 
                                                       class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-blue-600 hover:bg-blue-50 transition-colors transform hover:scale-110">
                                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                                
                                                @if($member->twitter)
                                                    <a href="{{ $member->twitter }}" target="_blank" 
                                                       class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-blue-400 hover:bg-blue-50 transition-colors transform hover:scale-110">
                                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                                
                                                @if($member->facebook)
                                                    <a href="{{ $member->facebook }}" target="_blank" 
                                                       class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-blue-800 hover:bg-blue-50 transition-colors transform hover:scale-110">
                                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                                
                                                @if($member->instagram)
                                                    <a href="{{ $member->instagram }}" target="_blank" 
                                                       class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-pink-600 hover:bg-pink-50 transition-colors transform hover:scale-110">
                                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.346-1.049-2.346-2.346S7.152 12.296 8.449 12.296s2.346 1.049 2.346 2.346-1.049 2.346-2.346 2.346zm7.719 0c-1.297 0-2.346-1.049-2.346-2.346s1.049-2.346 2.346-2.346 2.346 1.049 2.346 2.346-1.049 2.346-2.346 2.346z"/>
                                                        </svg>
                                                    </a>
                                                @endif

                                                @if($member->email)
                                                    <a href="mailto:{{ $member->email }}" 
                                                       class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors transform hover:scale-110">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Member Info --}}
                                    <div class="p-6">
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $member->name }}</h3>
                                        <p class="text-blue-600 font-medium mb-3">{{ $member->position }}</p>
                                        
                                        @if($member->bio)
                                            <p class="text-gray-600 text-sm leading-relaxed mb-4">{{ $member->bio }}</p>
                                        @endif
                                        
                                        @if($member->phone)
                                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                                {{ $member->phone }}
                                            </div>
                                        @endif
                                        
                                        @if($member->email)
                                            <div class="flex items-center text-sm text-gray-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                {{ $member->email }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-20">
                        <div class="max-w-md mx-auto">
                            <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <h3 class="text-xl font-medium text-gray-900 mb-2">Belum Ada Tim Member</h3>
                            <p class="text-gray-500">Tim kami sedang dalam proses pengembangan.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Join Our Team Section --}}
    <section class="py-20 bg-gradient-to-br from-blue-600 via-purple-600 to-blue-800 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-4xl mx-auto text-center text-white">
                <h2 class="text-3xl md:text-5xl font-bold mb-6">
                    Bergabung dengan Tim Kami
                </h2>
                <p class="text-xl md:text-2xl mb-8 opacity-90">
                    Kami selalu mencari talenta terbaik untuk berkembang bersama dan menciptakan dampak positif.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('contact.index') }}?subject=Career" 
                       class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-bold rounded-xl hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 shadow-lg">
                        <span>Kirim CV Anda</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </a>
                    <a href="{{ route('about') }}" 
                       class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-bold rounded-xl hover:bg-white hover:text-blue-600 transform hover:scale-105 transition-all duration-300">
                        <span>Tentang Kami</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        // Team filter functionality
        function filterTeam(department) {
            const allSections = document.querySelectorAll('.department-section');
            const allCards = document.querySelectorAll('.team-member-card');
            const filterBtns = document.querySelectorAll('.filter-btn');
            
            // Update active button
            filterBtns.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            if (department === 'all') {
                // Show all sections and cards
                allSections.forEach(section => {
                    section.style.display = 'block';
                    section.classList.add('animate-fade-in');
                });
                allCards.forEach(card => {
                    card.style.display = 'block';
                    card.classList.add('animate-fade-in');
                });
            } else {
                // Hide all sections first
                allSections.forEach(section => {
                    section.style.display = 'none';
                    section.classList.remove('animate-fade-in');
                });
                
                // Show only matching cards
                allCards.forEach(card => {
                    const cardDept = card.getAttribute('data-department');
                    if (cardDept === department) {
                        card.style.display = 'block';
                        card.classList.add('animate-fade-in');
                        // Show parent section
                        const parentSection = card.closest('.department-section');
                        if (parentSection) {
                            parentSection.style.display = 'block';
                            parentSection.classList.add('animate-fade-in');
                        }
                    } else {
                        card.style.display = 'none';
                        card.classList.remove('animate-fade-in');
                    }
                });
            }
        }

        // Intersection Observer for animations
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

        // Observe team member cards
        document.querySelectorAll('.team-member-card').forEach(card => {
            observer.observe(card);
        });

        // Lazy loading for images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.classList.add('loaded');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('.team-member-card img').forEach(img => {
                imageObserver.observe(img);
            });
        }
    </script>

    <style>
        .filter-btn {
            @apply bg-white text-gray-600 border border-gray-300 hover:bg-gray-50 hover:text-gray-900;
        }

        .filter-btn.active {
            @apply bg-gradient-to-r from-blue-600 to-purple-600 text-white border-transparent;
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
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

        /* Image loading animation */
        .team-member-card img {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .team-member-card img.loaded {
            opacity: 1;
        }

        /* Social media hover effects */
        .team-member-card .social-link {
            transform: translateY(4px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .team-member-card:hover .social-link {
            transform: translateY(0);
            opacity: 1;
        }

        .team-member-card:hover .social-link:nth-child(1) { transition-delay: 0.1s; }
        .team-member-card:hover .social-link:nth-child(2) { transition-delay: 0.2s; }
        .team-member-card:hover .social-link:nth-child(3) { transition-delay: 0.3s; }
        .team-member-card:hover .social-link:nth-child(4) { transition-delay: 0.4s; }
    </style>
    @endpush
</x-layouts.app>