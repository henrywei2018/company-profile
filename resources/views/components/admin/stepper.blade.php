<!-- resources/views/components/admin/stepper.blade.php -->
@props([
    'steps' => [],
    'currentStep' => 1,
    'variant' => 'default', // Options: default, dots, vertical
    'numbered' => true,
    'completed' => 'check', // Options: check, number, none
    'lineStyle' => 'solid' // Options: solid, dashed, dotted
])

@php
    // Determine line style classes
    $lineStyleClasses = [
        'solid' => 'border-t',
        'dashed' => 'border-t border-dashed',
        'dotted' => 'border-t border-dotted'
    ][$lineStyle] ?? 'border-t';
    
    // Counter for rendering step numbers
    $stepCount = count($steps);
@endphp

@if($variant === 'vertical')
    <!-- Vertical Stepper -->
    <div class="space-y-6">
        @foreach($steps as $index => $step)
            @php
                $stepNumber = $index + 1;
                $isActive = $stepNumber === $currentStep;
                $isCompleted = $stepNumber < $currentStep;
                $isPending = $stepNumber > $currentStep;
                
                // Status classes
                $activeClasses = 'bg-blue-600 text-white border-blue-600 dark:bg-blue-500 dark:border-blue-500';
                $completedClasses = 'bg-blue-100 text-blue-600 border-blue-600 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-500';
                $pendingClasses = 'bg-white text-gray-500 border-gray-300 dark:bg-neutral-800 dark:text-neutral-400 dark:border-neutral-700';
                $statusClasses = $isActive ? $activeClasses : ($isCompleted ? $completedClasses : $pendingClasses);
                
                // Line classes
                $lineClasses = $isCompleted ? 'border-blue-600 dark:border-blue-500' : 'border-gray-300 dark:border-neutral-700';
            @endphp
            
            <div class="relative flex">
                <!-- Step indicator -->
                <div class="flex flex-col items-center">
                    <div class="size-10 flex items-center justify-center rounded-full border-2 {{ $statusClasses }}">
                        @if($isCompleted && $completed === 'check')
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @elseif($numbered || ($isCompleted && $completed === 'number'))
                            <span>{{ $stepNumber }}</span>
                        @endif
                    </div>
                    
                    @if(!$loop->last)
                        <div class="w-px h-full {{ $lineStyleClasses }} {{ $lineClasses }}"></div>
                    @endif
                </div>
                
                <!-- Step content -->
                <div class="ml-4 mt-1 pb-8">
                    <h3 class="text-base font-medium {{ $isActive || $isCompleted ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-neutral-400' }}">
                        {{ $step['title'] ?? 'Step ' . $stepNumber }}
                    </h3>
                    
                    @if(isset($step['description']))
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">{{ $step['description'] }}</p>
                    @endif
                    
                    @if(isset($step['content']) && $isActive)
                        <div class="mt-4">
                            {{ $step['content'] }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@elseif($variant === 'dots')
    <!-- Dots Stepper -->
    <div class="flex items-center justify-between">
        @foreach($steps as $index => $step)
            @php
                $stepNumber = $index + 1;
                $isActive = $stepNumber === $currentStep;
                $isCompleted = $stepNumber < $currentStep;
                $isPending = $stepNumber > $currentStep;
                
                // Status classes
                $activeClasses = 'bg-blue-600 border-blue-600 dark:bg-blue-500 dark:border-blue-500';
                $completedClasses = 'bg-blue-600 border-blue-600 dark:bg-blue-500 dark:border-blue-500';
                $pendingClasses = 'bg-gray-200 border-gray-300 dark:bg-neutral-700 dark:border-neutral-600';
                $statusClasses = $isActive ? $activeClasses : ($isCompleted ? $completedClasses : $pendingClasses);
                
                // Line classes
                $lineClasses = $isCompleted ? 'border-blue-600 dark:border-blue-500' : 'border-gray-300 dark:border-neutral-700';
            @endphp
            
            <div class="flex items-center">
                <div class="flex flex-col items-center relative">
                    <div class="size-3 rounded-full {{ $statusClasses }}"></div>
                    
                    @if($numbered || isset($step['title']))
                        <div class="absolute mt-8 text-center">
                            @if($numbered)
                                <span class="text-xs font-medium {{ $isActive || $isCompleted ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-neutral-400' }}">{{ $stepNumber }}</span>
                            @endif
                            
                            @if(isset($step['title']))
                                <p class="mt-1 text-xs whitespace-nowrap {{ $isActive || $isCompleted ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-neutral-400' }}">
                                    {{ $step['title'] }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
                
                @if(!$loop->last)
                    <div class="flex-auto w-full {{ $lineStyleClasses }} {{ $lineClasses }}"></div>
                @endif
            </div>
        @endforeach
    </div>
@else
    <!-- Default Horizontal Stepper -->
    <div class="flex items-center">
        @foreach($steps as $index => $step)
            @php
                $stepNumber = $index + 1;
                $isActive = $stepNumber === $currentStep;
                $isCompleted = $stepNumber < $currentStep;
                $isPending = $stepNumber > $currentStep;
                
                // Status classes
                $activeClasses = 'bg-blue-600 text-white border-blue-600 dark:bg-blue-500 dark:border-blue-500';
                $completedClasses = 'bg-blue-100 text-blue-600 border-blue-600 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-500';
                $pendingClasses = 'bg-white text-gray-500 border-gray-300 dark:bg-neutral-800 dark:text-neutral-400 dark:border-neutral-700';
                $statusClasses = $isActive ? $activeClasses : ($isCompleted ? $completedClasses : $pendingClasses);
                
                // Line classes
                $lineClasses = $isCompleted ? 'border-blue-600 dark:border-blue-500' : 'border-gray-300 dark:border-neutral-700';
            @endphp
            
            <div class="flex items-center relative">
                <div class="size-10 flex items-center justify-center rounded-full border-2 {{ $statusClasses }}">
                    @if($isCompleted && $completed === 'check')
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    @elseif($numbered || ($isCompleted && $completed === 'number'))
                        <span>{{ $stepNumber }}</span>
                    @endif
                </div>
                
                @if(isset($step['title']))
                    <div class="absolute mt-16 left-1/2 -translate-x-1/2 whitespace-nowrap text-center">
                        <p class="text-sm font-medium {{ $isActive || $isCompleted ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-neutral-400' }}">
                            {{ $step['title'] }}
                        </p>
                    </div>
                @endif
                
                @if(!$loop->last)
                    <div class="flex-auto mx-4 {{ $lineStyleClasses }} {{ $lineClasses }}"></div>
                @endif
            </div>
        @endforeach
    </div>
@endif"
  }