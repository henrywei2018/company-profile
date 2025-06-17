<!-- resources/views/pages/home.blade.php -->
<x-layouts.public>
    <x-slot:title>
        CV Usaha Prima Lestari - Professional Construction & General Supplier
    </x-slot:title>
    <x-slot:meta_description>
        Leading construction and general supplier company in Indonesia providing quality civil
        engineering, building maintenance, and project management services.
    </x-slot:meta_description>
    
    <!-- Hero Section -->
    <x-banner-slider 
    category="homepage-hero" 
    :limit="3" 
    height="h-[500px]"
    :show-navigation="true"
    :show-pagination="true"
    :autoplay="true"
    :autoplay-delay="4000"
    effect="slide"
    container-class="mb-8" />
    <!-- End Hero -->

    <!-- Services Section -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <!-- Title -->
        <div class="mx-auto max-w-2xl mb-8 lg:mb-14 text-center">
            <h2 class="text-3xl lg:text-4xl text-gray-800 font-bold dark:text-gray-200">
                Our Professional Services
            </h2>
            <p class="mt-3 text-gray-800 dark:text-gray-200">
                We provide a wide range of construction and general supplier services
            </p>
        </div>
        <!-- End Title -->

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($services ?? [] as $service)
                <!-- Card -->
                <a class="group flex flex-col h-full border border-gray-200 hover:border-transparent hover:shadow-lg transition-all duration-300 rounded-xl p-5 dark:border-gray-700 dark:hover:border-transparent dark:hover:shadow-black/[.4]"
                    href="{{ route('services.show', $service->slug) }}">
                    <div class="flex items-center justify-center w-12 h-12 bg-amber-600 rounded-lg">
                        <svg class="flex-shrink-0 size-6 text-white" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M3 21h18M3 18h18M5 18V10c0-.6.4-1 1-1h12c.6 0 1 .4 1 1v8M7 10V7c0-.6.4-1 1-1h8c.6 0 1 .4 1 1v3" />
                        </svg>
                    </div>
                    <div class="mt-5">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                            {{ $service->title }}
                        </h3>
                        <p class="mt-3 text-gray-600 dark:text-gray-400">
                            {{ $service->short_description ?? Str::limit(strip_tags($service->description), 120) }}
                        </p>
                    </div>
                    <div class="mt-auto">
                        <span class="inline-flex items-center gap-x-1 text-amber-600 dark:text-amber-500">
                            Learn more
                            <svg class="flex-shrink-0 size-4 transition ease-in-out group-hover:translate-x-1"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </span>
                    </div>
                </a>
                <!-- End Card -->
            @empty
                @for ($i = 1; $i <= 3; $i++)
                    <!-- Card -->
                    <a class="group flex flex-col h-full border border-gray-200 hover:border-transparent hover:shadow-lg transition-all duration-300 rounded-xl p-5 dark:border-gray-700 dark:hover:border-transparent dark:hover:shadow-black/[.4]"
                        href="{{ route('services.index') }}">
                        <div class="flex items-center justify-center w-12 h-12 bg-amber-600 rounded-lg">
                            <svg class="flex-shrink-0 size-6 text-white" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M3 21h18M3 18h18M5 18V10c0-.6.4-1 1-1h12c.6 0 1 .4 1 1v8M7 10V7c0-.6.4-1 1-1h8c.6 0 1 .4 1 1v3" />
                            </svg>
                        </div>
                        <div class="mt-5">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                {{ ['Construction Services', 'General Supplier', 'Project Management'][$i - 1] }}
                            </h3>
                            <p class="mt-3 text-gray-600 dark:text-gray-400">
                                {{ ['Professional construction services for all your building needs.', 'Quality construction materials and supplies at competitive prices.', 'Expert project management for successful construction projects.'][$i - 1] }}
                            </p>
                        </div>
                        <div class="mt-auto">
                            <span class="inline-flex items-center gap-x-1 text-amber-600 dark:text-amber-500">
                                Learn more
                                <svg class="flex-shrink-0 size-4 transition ease-in-out group-hover:translate-x-1"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="m9 18 6-6-6-6" />
                                </svg>
                            </span>
                        </div>
                    </a>
                    <!-- End Card -->
                @endfor
            @endforelse
        </div>

        <div class="mt-10 text-center">
            <a class="inline-flex justify-center items-center gap-x-2 text-center bg-white border hover:border-gray-300 text-sm text-amber-600 hover:text-amber-700 font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2 focus:ring-offset-white transition py-3 px-4 dark:bg-slate-900 dark:border-gray-700 dark:hover:border-gray-600 dark:text-amber-500 dark:hover:text-amber-400 dark:focus:ring-offset-gray-800"
                href="{{ route('services.index') }}">
                View All Services
            </a>
        </div>
    </div>
    <!-- End Services Section -->

    <!-- About Section -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="grid md:grid-cols-2 gap-12">
            <div class="lg:w-3/4">
                <h2 class="text-3xl text-gray-800 font-bold lg:text-4xl dark:text-white">
                    Building Excellence, Crafting Quality
                </h2>
                <p class="mt-3 text-gray-800 dark:text-gray-400">
                    {{ isset($companyProfile) ? Str::limit(strip_tags($companyProfile->about), 300) : 'CV Usaha Prima Lestari has established itself as a trusted name in construction and supply services. With years of experience in the industry, we\'ve built a reputation for quality, reliability, and excellence in all our projects.' }}
                </p>
                <p class="mt-5 inline-flex items-center gap-x-2 font-medium text-amber-600 dark:text-amber-500">
                    Learn more about what we do
                    <svg class="flex-shrink-0 size-4 transition ease-in-out group-hover:translate-x-1"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="m9 18 6-6-6-6" />
                    </svg>
                </p>
            </div>
            <!-- End Col -->

            <div class="space-y-6 lg:space-y-10">
                <!-- Stats -->
                <div class="grid grid-cols-2 gap-8">
                    <div class="p-6 bg-gray-100 rounded-lg dark:bg-slate-800">
                        <span
                            class="block text-3xl font-bold text-gray-800 dark:text-white">{{ isset($companyProfile) ? $companyProfile->projects_completed ?? '250+' : '250+' }}</span>
                        <span class="block text-gray-500 dark:text-gray-400">Projects completed</span>
                    </div>

                    <div class="p-6 bg-gray-100 rounded-lg dark:bg-slate-800">
                        <span
                            class="block text-3xl font-bold text-gray-800 dark:text-white">{{ isset($companyProfile) ? '15+' : '15+' }}</span>
                        <span class="block text-gray-500 dark:text-gray-400">Years of experience</span>
                    </div>

                    <div class="p-6 bg-gray-100 rounded-lg dark:bg-slate-800">
                        <span
                            class="block text-3xl font-bold text-gray-800 dark:text-white">{{ isset($companyProfile) ? '50+' : '50+' }}</span>
                        <span class="block text-gray-500 dark:text-gray-400">Professional team</span>
                    </div>

                    <div class="p-6 bg-gray-100 rounded-lg dark:bg-slate-800">
                        <span class="block text-3xl font-bold text-gray-800 dark:text-white">100%</span>
                        <span class="block text-gray-500 dark:text-gray-400">Client satisfaction</span>
                    </div>
                </div>
                <!-- End Stats -->
            </div>
            <!-- End Col -->
        </div>
        <!-- End Grid -->
    </div>
    <!-- End About Section -->

    <!-- Project Section -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <!-- Title -->
        <div class="max-w-2xl mx-auto text-center mb-10 lg:mb-14">
            <h2 class="text-3xl font-bold md:text-4xl md:leading-tight dark:text-white">Our Projects</h2>
            <p class="mt-3 text-gray-800 dark:text-gray-200">
                From residential to commercial, we've successfully completed a wide range of projects
            </p>
        </div>
        <!-- End Title -->

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($featuredProjects ?? [] as $project)
                <!-- Card -->
                <a class="group rounded-xl overflow-hidden dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                    href="{{ route('portfolio.show', $project->slug) }}">
                    <div class="relative pt-[50%] sm:pt-[70%] rounded-xl overflow-hidden">
                        <img class="size-full opacity-50 absolute top-0 start-0 object-cover group-hover:scale-105 transition-transform duration-500 ease-in-out rounded-xl"
                            src="{{ $project->getFeaturedImageUrlAttribute() }}" alt="{{ $project->title }}">
                        <span
                            class="absolute top-0 end-0 rounded-se-xl rounded-es-xl text-xs font-medium bg-gray-800 text-white py-1.5 px-3 dark:bg-gray-900">
                            {{ $project->category }}
                        </span>
                    </div>

                    <div class="mt-7">
                        <h3 class="text-xl font-semibold text-gray-800 group-hover:text-gray-600 dark:text-gray-200">
                            {{ $project->title }}
                        </h3>
                        <p class="mt-3 text-gray-800 dark:text-gray-200">
                            {{ Str::limit(strip_tags($project->description), 120) }}
                        </p>
                        <p
                            class="mt-5 inline-flex items-center gap-x-1 text-amber-600 decoration-2 group-hover:underline font-medium">
                            View project
                            <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </p>
                    </div>
                </a>
                <!-- End Card -->
            @empty
                @for ($i = 1; $i <= 3; $i++)
                    <!-- Card -->
                    <a class="group rounded-xl overflow-hidden dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                        href="{{ route('portfolio.index') }}">
                        <div class="relative pt-[50%] sm:pt-[70%] rounded-xl overflow-hidden">
                            <img class="size-full absolute top-0 start-0 object-cover group-hover:scale-105 transition-transform duration-500 ease-in-out rounded-xl"
                                src="{{ asset('images/project_' . $i . '.jpg') }}" alt="Project {{ $i }}">
                            <span
                                class="absolute top-0 end-0 rounded-se-xl rounded-es-xl text-xs font-medium bg-gray-800 text-white py-1.5 px-3 dark:bg-gray-900">
                                {{ ['Commercial', 'Residential', 'Industrial'][$i - 1] }}
                            </span>
                        </div>

                        <div class="mt-7">
                            <h3 class="text-xl font-semibold text-gray-800 group-hover:text-gray-600 dark:text-gray-200">
                                {{ ['Harmony Office Tower', 'Green Valley Residences', 'Sentosa Manufacturing Plant'][$i - 1] }}
                            </h3>
                            <p class="mt-3 text-gray-800 dark:text-gray-200">
                                {{ ['A modern 15-story office building located in the Central Business District of Jakarta.', 'Premium residential complex consisting of 5 apartment towers with a focus on sustainable living.', 'Modern industrial facility designed for electronics manufacturing with specialized requirements.'][$i - 1] }}
                            </p>
                            <p
                                class="mt-5 inline-flex items-center gap-x-1 text-amber-600 decoration-2 group-hover:underline font-medium">
                                View project
                                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m9 18 6-6-6-6" />
                                </svg>
                            </p>
                        </div>
                    </a>
                    <!-- End Card -->
                @endfor
            @endforelse
        </div>

        <div class="mt-10 text-center">
            <a class="inline-flex justify-center items-center gap-x-2 text-center bg-white border hover:border-gray-300 text-sm text-amber-600 hover:text-amber-700 font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2 focus:ring-offset-white transition py-3 px-4 dark:bg-slate-900 dark:border-gray-700 dark:hover:border-gray-600 dark:text-amber-500 dark:hover:text-amber-400 dark:focus:ring-offset-gray-800"
                href="{{ route('portfolio.index') }}">
                View All Projects
            </a>
        </div>
    </div>
    <!-- End Project Section -->

    <!-- Testimonials -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="lg:pe-8">
                <h2 class="text-3xl text-gray-800 font-bold sm:text-4xl lg:text-5xl lg:leading-tight dark:text-white">
                    What our clients are saying
                </h2>
                <p class="mt-3 text-gray-800 dark:text-gray-400">
                    Don't just take our word for it - hear what our clients have to say about their experience working with
                    us
                </p>
                <div class="mt-6">
                    <a class="inline-flex items-center gap-x-1 text-amber-600 dark:text-amber-500"
                        href="{{ route('contact.index') }}">
                        Get in Touch
                        <svg class="flex-shrink-0 size-4 transition ease-in-out group-hover:translate-x-1"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </a>
                </div>
            </div>
            <!-- End Col -->

            <div class="flex flex-col bg-amber-100 rounded-3xl p-8 dark:bg-slate-800">
                <div class="overflow-hidden">
                    <div class="relative overflow-hidden">
                        <!-- Testimonials -->
                        @forelse($testimonials ?? [] as $testimonial)
                            @if ($loop->first)
                                <div class="transition duration-500">
                                    <div class="flex flex-col justify-center">
                                        <p
                                            class="relative text-lg sm:text-xl md:text-2xl md:leading-normal font-medium text-gray-800 dark:text-gray-200">
                                            <span class="relative z-10 text-amber-600 dark:text-amber-500">"</span>
                                            {{ Str::limit($testimonial->content, 150) }}
                                            <span class="relative z-10 text-amber-600 dark:text-amber-500">"</span>
                                        </p>

                                        <div class="mt-6">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="size-12 flex justify-center items-center bg-amber-600 text-white text-xl font-bold rounded-full dark:bg-amber-500">
                                                        {{ substr($testimonial->client_name, 0, 1) }}
                                                    </div>
                                                </div>
                                                <div class="ms-4">
                                                    <div class="font-semibold text-gray-800 dark:text-gray-200">
                                                        {{ $testimonial->client_name }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $testimonial->client_position }}{{ $testimonial->client_company ? ', ' . $testimonial->client_company : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="transition duration-500">
                                <div class="flex flex-col justify-center">
                                    <p
                                        class="relative text-lg sm:text-xl md:text-2xl md:leading-normal font-medium text-gray-800 dark:text-gray-200">
                                        <span class="relative z-10 text-amber-600 dark:text-amber-500">"</span>
                                        Working with CV Usaha Prima Lestari has been an excellent experience. Their team is
                                        professional, responsive, and committed to delivering high-quality results. The
                                        project was completed ahead of schedule and exceeded our expectations in terms of
                                        quality and finish.
                                        <span class="relative z-10 text-amber-600 dark:text-amber-500">"</span>
                                    </p>

                                    <div class="mt-6">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="size-12 flex justify-center items-center bg-amber-600 text-white text-xl font-bold rounded-full dark:bg-amber-500">
                                                    J
                                                </div>
                                            </div>
                                            <div class="ms-4">
                                                <div class="font-semibold text-gray-800 dark:text-gray-200">John Smith
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Project Manager, PT
                                                    Maju Bersama</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                        <!-- End Testimonials -->
                    </div>
                </div>
            </div>
            <!-- End Col -->
        </div>
        <!-- End Grid -->
    </div>
</x-layouts.public>
