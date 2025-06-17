{{-- resources/views/components/public/hero-stats.blade.php --}}
@props(['stats'])

<div class="absolute bottom-8 left-8 right-8 z-10">
    <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg p-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div>
                <div class="text-2xl font-bold text-orange-600">{{ $stats['completed_projects'] }}+</div>
                <div class="text-sm text-gray-600">Completed Projects</div>
            </div>
            <div>
                <div class="text-2xl font-bold text-orange-600">{{ $stats['happy_clients'] }}+</div>
                <div class="text-sm text-gray-600">Happy Clients</div>
            </div>
            <div>
                <div class="text-2xl font-bold text-orange-600">{{ $stats['years_experience'] }}+</div>
                <div class="text-sm text-gray-600">Years Experience</div>
            </div>
            <div>
                <div class="text-2xl font-bold text-orange-600">{{ $stats['active_services'] }}+</div>
                <div class="text-sm text-gray-600">Services</div>
            </div>
        </div>
    </div>
</div>