@extends('layouts.app')

@section('title', 'Services - CV Usaha Prima Lestari')
@section('meta_description', 'Get in touch with CV Usaha Prima Lestari for all your construction and supply needs. We offer professional construction services, general supplies, and more.')

@section('content')
    <!-- Page Header -->
    <section class="page-header bg-amber-600 py-20 text-white">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Services</h1>
            <nav class="flex justify-center" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-white hover:text-amber-200">
                            <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                            </svg>
                            Home
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-amber-300 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ml-1 text-sm font-medium md:ml-2">Service</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Contact Info Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                <!-- Contact Card 1 -->
                <div class="bg-gray-50 rounded-lg p-8 text-center shadow-md" data-aos="fade-up">
                    <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full mx-auto flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Our Location</h3>
                    <p class="text-gray-600">
                        {{ isset($companyProfile) && $companyProfile->address ? $companyProfile->address : 'Jl. Raya Bogor No. 123, Jakarta, Indonesia' }}
                    </p>
                </div>

                <!-- Contact Card 2 -->
                <div class="bg-gray-50 rounded-lg p-8 text-center shadow-md" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full mx-auto flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Phone Number</h3>
                    <p class="text-gray-600">
                        <a href="tel:{{ isset($companyProfile) && $companyProfile->phone ? $companyProfile->phone : '+62 21 123 4567' }}" class="hover:text-amber-600">
                            {{ isset($companyProfile) && $companyProfile->phone ? $companyProfile->phone : '+62 21 123 4567' }}
                        </a>
                    </p>
                </div>

                <!-- Contact Card 3 -->
                <div class="bg-gray-50 rounded-lg p-8 text-center shadow-md" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full mx-auto flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Email Address</h3>
                    <p class="text-gray-600">
                        <a href="mailto:{{ isset($companyProfile) && $companyProfile->email ? $companyProfile->email : 'info@cvupl.com' }}" class="hover:text-amber-600">
                            {{ isset($companyProfile) && $companyProfile->email ? $companyProfile->email : 'info@cvupl.com' }}
                        </a>
                    </p>
                </div>
            </div>

            <!-- Contact Form & Map Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div data-aos="fade-right">
                    <h2 class="text-3xl font-bold mb-6">Get In Touch</h2>
                    <p class="text-gray-600 mb-8">
                        Have questions or need a quote? Fill out the form below and our team will get back to you as soon as possible.
                    </p>

                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                        @csrf

                        @if(session('success'))
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                                <p>{{ session('success') }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-gray-700 font-medium mb-2">Your Name *</label>
                                <input type="text" id="name" name="name" required value="{{ old('name') }}" class="w-full border border-gray-300 rounded-md py-2 px-4 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-gray-700 font-medium mb-2">Your Email *</label>
                                <input type="email" id="email" name="email" required value="{{ old('email') }}" class="w-full border border-gray-300 rounded-md py-2 px-4 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('email') border-red-500 @enderror">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="w-full border border-gray-300 rounded-md py-2 px-4 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="company" class="block text-gray-700 font-medium mb-2">Company Name</label>
                                <input type="text" id="company" name="company" value="{{ old('company') }}" class="w-full border border-gray-300 rounded-md py-2 px-4 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('company') border-red-500 @enderror">
                                @error('company')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="subject" class="block text-gray-700 font-medium mb-2">Subject *</label>
                            <input type="text" id="subject" name="subject" required value="{{ old('subject') }}" class="w-full border border-gray-300 rounded-md py-2 px-4 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('subject') border-red-500 @enderror">
                            @error('subject')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="message" class="block text-gray-700 font-medium mb-2">Your Message *</label>
                            <textarea id="message" name="message" rows="5" required class="w-full border border-gray-300 rounded-md py-2 px-4 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <button type="submit" class="bg-amber-600 text-white py-3 px-6 rounded-md hover:bg-amber-700 transition-colors focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Map -->
                <div data-aos="fade-left">
                    <h2 class="text-3xl font-bold mb-6">Find Us</h2>
                    <div class="h-96 bg-gray-200 rounded-lg overflow-hidden">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126916.04020069465!2d106.7891455!3d-6.229728!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e945e34b9d%3A0x100c2651609b318!2sJakarta%2C%20Indonesia!5e0!3m2!1sen!2sus!4v1651234567890!5m2!1sen!2sus" 
                            class="w-full h-full" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-xl font-semibold mb-4">Business Hours</h3>
                        <ul class="space-y-3 text-gray-600">
                            <li class="flex justify-between">
                                <span>Monday - Friday:</span>
                                <span class="font-medium">8:00 AM - 5:00 PM</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Saturday:</span>
                                <span class="font-medium">9:00 AM - 1:00 PM</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Sunday:</span>
                                <span class="font-medium">Closed</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-amber-600 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4" data-aos="fade-up">Need a Quote for Your Project?</h2>
            <p class="text-xl mb-8 max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                We offer competitive pricing and excellent service for all your construction and supply needs.
            </p>
            <a href="{{ route('quotation.create') }}" class="inline-block bg-white text-amber-600 py-3 px-8 rounded-md hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-amber-600" data-aos="fade-up" data-aos-delay="200">
                Request a Quote
            </a>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
        });
    });
</script>
@endpush