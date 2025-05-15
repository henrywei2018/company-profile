{{-- resources/views/pages/about.blade.php --}}
@extends('layouts.app')

@section('title', 'About Us')
@section('meta_description', 'Learn about CV Usaha Prima Lestari, our history, mission, vision, and values as a leading construction and general supplier company in Indonesia.')

@section('content')
    <!-- Page Header -->
    <section class="bg-gradient-to-r from-gray-900 to-gray-800 py-20">
        <div class="container mx-auto px-4">
            <div class="flex flex-col items-center">
                <h1 class="text-4xl font-bold text-white mb-4">About Us</h1>
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('home') }}" class="text-sm text-gray-300 hover:text-white">
                                Home
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                                <span class="ml-1 text-sm font-medium text-gray-100 md:ml-2">About Us</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <!-- Company Overview -->
    <section class="py-16 bg-white dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right" data-aos-delay="100">
                    @if($companyProfile && $companyProfile->logo)
                        <img src="{{ $companyProfile->logoUrl }}" alt="{{ $companyProfile->company_name }}" class="mb-8 max-w-xs">
                    @endif
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Our Story</h2>
                    <div class="prose dark:prose-invert max-w-none">
                        @if($companyProfile && $companyProfile->about)
                            {!! $companyProfile->about !!}
                        @else
                            <p>
                                CV Usaha Prima Lestari was established in {{ $companyProfile->established_year ?? '2008' }} with a vision to become a leading construction and general supplier company in Indonesia. We started as a small team with big dreams, and over the years, we have grown into a trusted name in the industry.
                            </p>
                            <p>
                                With over {{ $companyProfile->projects_completed ?? '250+' }} successful projects completed and a team of {{ $companyProfile->employees_count ?? '50+' }} skilled professionals, we continue to deliver excellence in every project we undertake. Our commitment to quality, innovation, and customer satisfaction has been the cornerstone of our success.
                            </p>
                        @endif
                    </div>
                </div>
                <div class="relative" data-aos="fade-left" data-aos-delay="200">
                    <img src="{{ asset('images/about-main.jpg') }}" alt="Our Company" class="rounded-lg shadow-lg w-full">
                    <div class="absolute -bottom-6 -left-6 bg-amber-600 rounded-lg p-6 shadow-lg w-48 md:w-64">
                        <div class="text-white text-center">
                            <p class="text-sm font-medium">Established</p>
                            <p class="text-3xl font-bold">{{ $companyProfile->established_year ?? '2008' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission, Vision, Values -->
    <section class="py-16 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-12 h-1 bg-amber-600 mr-3"></div>
                    <p class="text-amber-600 font-medium uppercase">Our Principles</p>
                    <div class="w-12 h-1 bg-amber-600 ml-3"></div>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Mission, Vision & Values</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Mission -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center mb-6 mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 text-center">Our Mission</h3>
                    <div class="prose dark:prose-invert max-w-none">
                        @if($companyProfile && $companyProfile->mission)
                            {!! $companyProfile->mission !!}
                        @else
                            <p>
                                To deliver high-quality construction and supply services that exceed customer expectations, while adhering to the highest standards of safety, integrity, and professionalism.
                            </p>
                        @endif
                    </div>
                </div>
                
                <!-- Vision -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center mb-6 mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 text-center">Our Vision</h3>
                    <div class="prose dark:prose-invert max-w-none">
                        @if($companyProfile && $companyProfile->vision)
                            {!! $companyProfile->vision !!}
                        @else
                            <p>
                                To become the most trusted and preferred construction and general supplier company in Indonesia, recognized for excellence, innovation, and sustainable development practices.
                            </p>
                        @endif
                    </div>
                </div>
                
                <!-- Values -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center mb-6 mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 text-center">Our Values</h3>
                    <div class="prose dark:prose-invert max-w-none">
                        @if($companyProfile && isset($companyProfile->values) && is_array($companyProfile->values))
                            <ul>
                            @foreach($companyProfile->values as $value)
                                <li>{{ $value }}</li>
                            @endforeach
                            </ul>
                        @else
                            <ul>
                                <li><strong>Integrity:</strong> We conduct our business with honesty, transparency, and ethical practices.</li>
                                <li><strong>Excellence:</strong> We strive for the highest standards in all aspects of our work.</li>
                                <li><strong>Innovation:</strong> We embrace new technologies and approaches to deliver better solutions.</li>
                                <li><strong>Safety:</strong> We prioritize the well-being of our employees, clients, and communities.</li>
                                <li><strong>Collaboration:</strong> We value teamwork and partnerships to achieve shared goals.</li>
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Team Section -->
    <section class="py-16 bg-white dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-12 h-1 bg-amber-600 mr-3"></div>
                    <p class="text-amber-600 font-medium uppercase">Our Team</p>
                    <div class="w-12 h-1 bg-amber-600 ml-3"></div>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Meet Our Leadership</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                    Our success is driven by our dedicated team of professionals with extensive experience in construction and supply chain management.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($teamMembers as $member)
                    <x-team-card :member="$member" class="h-full" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}"/>
                @empty
                    <div class="col-span-full text-center">
                        <p class="text-gray-500 dark:text-gray-400">Team information will be available soon.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ route('team.index') }}" class="inline-flex items-center px-6 py-3 border border-amber-600 text-base font-medium rounded-md text-amber-600 hover:bg-amber-600 hover:text-white transition">
                    View All Team Members
                </a>
            </div>
        </div>
    </section>

    <!-- Certifications Section -->
    <section class="py-16 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-12 h-1 bg-amber-600 mr-3"></div>
                    <p class="text-amber-600 font-medium uppercase">Our Credentials</p>
                    <div class="w-12 h-1 bg-amber-600 ml-3"></div>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Certifications & Achievements</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                    We are proud to be recognized for our commitment to quality, safety, and excellence.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($certifications as $certification)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 text-center" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        @if($certification->image)
                            <img src="{{ asset('storage/' . $certification->image) }}" alt="{{ $certification->name }}" class="h-24 mx-auto mb-4">
                        @endif
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $certification->name }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mb-2">{{ $certification->issuer }}</p>
                        @if($certification->issue_date)
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ $certification->issue_date->format('Y') }}
                                @if($certification->expiry_date)
                                    - {{ $certification->expiry_date->format('Y') }}
                                @endif
                            </p>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full text-center">
                        <p class="text-gray-500 dark:text-gray-400">Certification information will be available soon.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-16 bg-white dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-12 h-1 bg-amber-600 mr-3"></div>
                    <p class="text-amber-600 font-medium uppercase">Why Choose Us</p>
                    <div class="w-12 h-1 bg-amber-600 ml-3"></div>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Our Advantages</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                    Discover why clients trust CV Usaha Prima Lestari for their construction and supply needs.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Advantage 1 -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Quality Assurance</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        We adhere to the highest standards of quality in all our projects, ensuring durability, functionality, and aesthetic appeal.
                    </p>
                </div>
                
                <!-- Advantage 2 -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">On-Time Delivery</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        We understand the importance of timelines in construction projects and ensure on-time delivery without compromising on quality.
                    </p>
                </div>
                
                <!-- Advantage 3 -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Experienced Team</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Our team of professionals brings years of experience and expertise to every project, ensuring optimal solutions for our clients.
                    </p>
                </div>
                
                <!-- Advantage 4 -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Customer-Centric Approach</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        We prioritize our clients' needs and work closely with them throughout the project lifecycle to ensure complete satisfaction.
                    </p>
                </div>
                
                <!-- Advantage 5 -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8" data-aos="fade-up" data-aos-delay="500">
                    <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Innovative Solutions</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        We leverage the latest technologies and methodologies to provide innovative solutions that are cost-effective and sustainable.
                    </p>
                </div>
                
                <!-- Advantage 6 -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8" data-aos="fade-up" data-aos-delay="600">
                    <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Comprehensive Services</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        From planning to execution, we offer end-to-end services for all construction and supply needs, making us your one-stop solution.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-amber-600 to-amber-700 text-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div data-aos="fade-right" data-aos-delay="100">
                    <h2 class="text-3xl font-bold mb-4">Ready to Start Your Project?</h2>
                    <p class="text-lg text-amber-100 mb-8">
                        Contact us today for a free consultation and estimate for your construction or supplier needs. Our team is ready to help you bring your vision to life.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('quotation.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-amber-700 bg-