<!-- resources/views/components/admin/accordion.blade.php -->
@props([
    'items' => [],
    'openFirst' => false,
    'bordered' => true,
    'flush' => false, // No padding or border if true
    'multiple' => false // Allow multiple items to be open at once
])

<div 
    {{ $attributes->merge(['class' => $bordered ? 'border border-gray-200 rounded-xl overflow-hidden dark:border-neutral-700' : '']) }}
    x-data="{ activeItems: {{ $openFirst ? '[0]' : '[]' }} }"
>
    @if(count($items) > 0)
        @foreach($items as $index => $item)
            <div class="{{ !$loop->first ? 'border-t border-gray-200 dark:border-neutral-700' : '' }}">
                <button 
                    type="button" 
                    @click="{{ $multiple ? 'activeItems.includes('.$index.') ? activeItems = activeItems.filter(item => item !== '.$index.') : activeItems.push('.$index.')' : 'activeItems = activeItems.includes('.$index.') ? [] : ['.$index.']' }}"
                    class="flex items-center justify-between w-full {{ $flush ? 'px-0' : 'px-4 sm:px-6' }} py-4 text-left"
                    :aria-expanded="activeItems.includes({{ $index }})"
                >
                    <span class="text-base font-medium text-gray-800 dark:text-neutral-200">{{ $item['title'] }}</span>
                    <span class="ml-4">
                        <svg class="w-5 h-5 text-gray-500 dark:text-neutral-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            :class="{'rotate-180': activeItems.includes({{ $index }}), 'rotate-0': !activeItems.includes({{ $index }})}"
                            style="transition: transform 0.2s ease-in-out;"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </button>
                <div 
                    x-show="activeItems.includes({{ $index }})"
                    x-collapse
                    class="{{ $flush ? 'px-0' : 'px-4 sm:px-6' }} pb-4"
                >
                    <div class="text-base text-gray-600 dark:text-neutral-400">
                        {{ $item['content'] }}
                    </div>
                </div>
            </div>
        @endforeach
    @else
        {{ $slot }}
    @endif
</div>

@once
@push('scripts')
<script>
    // Add x-collapse directive if not already defined
    if (typeof Alpine !== 'undefined' && !Alpine.directive('collapse')) {
        Alpine.directive('collapse', (el, { modifiers, expression }, { effect, cleanup }) => {
            const duration = modifiers.includes('slow') ? 300 : (modifiers.includes('fast') ? 100 : 200);
            
            effect(() => {
                if (el._x_isShown === true) return;
                el._x_isShown = true;
                
                // Set initial styles before animation starts
                el.style.overflow = 'hidden';
                el.style.height = '0px';
                
                // Start the animation
                setTimeout(() => {
                    el.style.transition = `height ${duration}ms cubic-bezier(0.4, 0, 0.2, 1)`;
                    el.style.height = `${el.scrollHeight}px`;
                }, 5);
                
                // Clean up after animation completes
                setTimeout(() => {
                    el.style.overflow = 'visible';
                    el.style.height = 'auto';
                }, duration + 5);
            });
            
            effect(() => {
                if (el._x_isShown === false) return;
                el._x_isShown = false;
                
                // Set height explicitly before animation
                el.style.height = `${el.scrollHeight}px`;
                el.style.overflow = 'hidden';
                
                // Force a reflow
                el.offsetHeight;
                
                // Start the animation
                setTimeout(() => {
                    el.style.transition = `height ${duration}ms cubic-bezier(0.4, 0, 0.2, 1)`;
                    el.style.height = '0px';
                }, 5);
            });
            
            cleanup(() => {
                el.style.transition = '';
                el.style.height = '';
                el.style.overflow = '';
            });
        });
    }
</script>
@endpush
@endonce"
  }