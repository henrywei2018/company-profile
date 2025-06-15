<x-layouts.app>
    <x-slot:title>Our Services</x-slot:title>
    <x-slot:meta_description>
        Discover our professional construction & general supplier services.
    </x-slot:meta_description>
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="text-center mb-10">
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-800 dark:text-gray-100">Our Services</h1>
            <p class="mt-3 text-gray-600 dark:text-gray-400 max-w-xl mx-auto">
                We offer a wide range of professional services to help you achieve your construction goals efficiently.
            </p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($services as $service)
                <a href="{{ route('services.show', $service->slug) }}" class="group flex flex-col h-full border border-gray-200 hover:border-transparent hover:shadow-lg transition-all duration-300 rounded-xl p-5 dark:border-gray-700 dark:hover:border-transparent dark:hover:shadow-black/[.4]">
                    <div class="flex items-center justify-center w-12 h-12 bg-amber-600 rounded-lg">
                        <svg class="flex-shrink-0 size-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 21h18M3 18h18M5 18V10c0-.6.4-1 1-1h12c.6 0 1 .4 1 1v8M7 10V7c0-.6.4-1 1-1h8c.6 0 1 .4 1 1v3"/>
                        </svg>
                    </div>
                    <div class="mt-5">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ $service->title }}</h3>
                        <p class="mt-3 text-gray-600 dark:text-gray-400">
                            {{ $service->short_description ?? Str::limit(strip_tags($service->description), 120) }}
                        </p>
                    </div>
                    <div class="mt-auto">
                        <span class="inline-flex items-center gap-x-1 text-amber-600 dark:text-amber-500">
                            Learn more
                            <svg class="flex-shrink-0 size-4 transition ease-in-out group-hover:translate-x-1"
                                 xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </span>
                    </div>
                </a>
            @empty
                <div class="col-span-3 text-center text-gray-400 dark:text-gray-500 py-10">
                    No services available at the moment.
                </div>
            @endforelse
        </div>
        <div class="mt-8">
            {{ $services->links() }}
        </div>
    </div>
</x-layouts.app>
