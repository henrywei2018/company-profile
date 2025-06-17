{{-- resources/views/components/public/testimonial-card.blade.php --}}
@props(['testimonial'])

<div class="w-full flex-shrink-0 px-4">
    <div class="bg-gray-50 rounded-2xl p-8 text-center max-w-4xl mx-auto">
        {{-- Quote Icon --}}
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
            </svg>
        </div>
        
        {{-- Testimonial Content --}}
        <blockquote class="text-xl text-gray-700 mb-8 leading-relaxed">
            "{{ $testimonial->content }}"
        </blockquote>
        
        {{-- Client Info --}}
        <div class="flex items-center justify-center">
            @if($testimonial->avatar)
            <img src="{{ asset('storage/' . $testimonial->avatar) }}" 
                 alt="{{ $testimonial->client_name }}" 
                 class="w-12 h-12 rounded-full mr-4">
            @else
            <div class="w-12 h-12 bg-orange-200 rounded-full flex items-center justify-center mr-4">
                <span class="text-orange-600 font-semibold">
                    {{ substr($testimonial->client_name, 0, 1) }}
                </span>
            </div>
            @endif
            
            <div class="text-left">
                <div class="font-semibold text-gray-900">{{ $testimonial->client_name }}</div>
                @if($testimonial->client_company)
                <div class="text-gray-600">{{ $testimonial->client_company }}</div>
                @endif
                
                {{-- Rating --}}
                @if($testimonial->rating)
                <div class="flex items-center mt-1">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="w-4 h-4 {{ $i <= $testimonial->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    @endfor
                </div>
                @endif
            </div>
        </div>
    </div>
</div>