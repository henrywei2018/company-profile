{{-- resources/views/components/survey-widget.blade.php --}}
@props([
    'position' => 'bottom-right',
    'showAfterScroll' => 3000,
    'autoShow' => true,
    'showOnce' => true
])

@php
    $positionClasses = [
        'bottom-right' => 'bottom-6 right-6',
        'bottom-left' => 'bottom-6 left-6',
        'top-right' => 'top-6 right-6',
        'top-left' => 'top-6 left-6',
    ];
    
    $currentPosition = $positionClasses[$position] ?? $positionClasses['bottom-right'];
@endphp

<!-- Survey Widget Container -->
<div id="survey-widget" 
     class="fixed {{ $currentPosition }} z-50 font-sans"
     x-data="surveyWidget()"
     x-init="init()"
     x-show="showButton">
    
    <!-- Survey CTA Button -->
    <div class="relative group">
        <!-- Tooltip -->
        <div class="absolute bottom-full mb-3 right-0 bg-gray-900 text-white text-xs rounded-lg px-3 py-2 opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none whitespace-nowrap shadow-lg">
            Berikan feedback Anda
            <div class="absolute top-full right-6 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
        </div>
        
        <!-- Main Survey Button -->
        <button @click="openSurvey()" 
                class="w-14 h-14 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white rounded-full shadow-lg hover:shadow-xl transform hover:scale-110 transition-all duration-300 flex items-center justify-center group relative overflow-hidden hover:ring-4 hover:ring-orange-500/30"
                title="Survey Kepuasan"
                aria-label="Open satisfaction survey">
            
            <!-- Survey Icon (clipboard with checkmark) -->
            <svg class="w-7 h-7 transition-transform duration-200 group-hover:scale-110" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="2" 
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            
            <!-- Pulse Animation Ring -->
            <div class="absolute inset-0 rounded-full border-2 border-orange-400 opacity-75 animate-ping"></div>
            
            <!-- Ripple Effect -->
            <div class="absolute inset-0 rounded-full opacity-0 group-hover:opacity-20 bg-white transition-opacity duration-200"></div>
        </button>
        
        <!-- Notification Dot -->
        <div x-show="!hasSeenSurvey" 
             class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 border-2 border-white rounded-full shadow-sm animate-pulse">
        </div>
    </div>
</div>

<!-- Survey Modal -->
<div x-show="showModal" 
     class="fixed inset-0 z-[9999] overflow-y-auto"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="closeSurvey()">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50" @click="closeSurvey()"></div>
    
    <!-- Modal Container -->
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             @click.stop
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-orange-500 to-amber-500 p-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Survey Kepuasan</h3>
                            <p class="text-orange-100 text-sm">Bantu kami menjadi lebih baik</p>
                        </div>
                    </div>
                    <button @click="closeSurvey()" 
                            class="w-8 h-8 rounded-full hover:bg-white hover:bg-opacity-20 flex items-center justify-center transition-colors duration-200 text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal Content -->
            <div class="p-6">
                <form @submit.prevent="submitSurvey()" x-show="!submitted">
                    <!-- Question 1: Overall Satisfaction -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Seberapa puas Anda dengan website kami?
                        </label>
                        <div class="flex justify-between space-x-2">
                            <template x-for="rating in 5" :key="rating">
                                <button type="button"
                                        @click="surveyData.satisfaction = rating"
                                        class="flex-1 h-12 rounded-lg border-2 transition-all duration-200 flex items-center justify-center text-sm font-medium"
                                        :class="surveyData.satisfaction === rating ? 
                                            'border-orange-500 bg-orange-50 text-orange-700 dark:bg-orange-900 dark:text-orange-300' : 
                                            'border-gray-200 dark:border-gray-600 hover:border-orange-300 text-gray-600 dark:text-gray-400 hover:text-orange-600'">
                                    <div class="text-center">
                                        <div class="text-lg mb-1" x-text="getEmoji(rating)"></div>
                                        <div x-text="getRatingText(rating)"></div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Question 2: Ease of Use -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Seberapa mudah navigasi website ini?
                        </label>
                        <div class="space-y-2">
                            <template x-for="option in easeOptions" :key="option.value">
                                <label class="flex items-center p-3 rounded-lg border cursor-pointer transition-colors duration-200"
                                       :class="surveyData.ease_of_use === option.value ? 
                                           'border-orange-500 bg-orange-50 dark:bg-orange-900' : 
                                           'border-gray-200 dark:border-gray-600 hover:border-orange-300'">
                                    <input type="radio" 
                                           :value="option.value" 
                                           x-model="surveyData.ease_of_use"
                                           class="sr-only">
                                    <div class="w-4 h-4 rounded-full border-2 mr-3 flex items-center justify-center"
                                         :class="surveyData.ease_of_use === option.value ? 
                                             'border-orange-500' : 
                                             'border-gray-300'">
                                        <div x-show="surveyData.ease_of_use === option.value" 
                                             class="w-2 h-2 bg-orange-500 rounded-full"></div>
                                    </div>
                                    <span class="text-sm text-gray-700 dark:text-gray-300" x-text="option.label"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Question 3: Comments -->
                    <div class="mb-6">
                        <label for="comments" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Saran atau komentar (opsional)
                        </label>
                        <textarea x-model="surveyData.comments"
                                  id="comments"
                                  rows="3"
                                  placeholder="Bagikan pengalaman atau saran Anda..."
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"></textarea>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex space-x-3">
                        <button type="submit" 
                                :disabled="!surveyData.satisfaction || isSubmitting"
                                class="flex-1 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 disabled:from-gray-400 disabled:to-gray-500 text-white py-3 px-4 rounded-lg font-medium transition-all duration-200 disabled:cursor-not-allowed flex items-center justify-center">
                            <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim Feedback'"></span>
                        </button>
                        <button type="button" 
                                @click="closeSurvey()"
                                class="px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            Batal
                        </button>
                    </div>
                </form>
                
                <!-- Thank You Message -->
                <div x-show="submitted" class="text-center py-8">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Terima Kasih!</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Feedback Anda sangat berharga untuk kami</p>
                    <button @click="closeSurvey()" 
                            class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white py-2 px-6 rounded-lg font-medium transition-colors duration-200">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function surveyWidget() {
    return {
        // State
        showButton: false,
        showModal: false,
        submitted: false,
        isSubmitting: false,
        hasSeenSurvey: false,
        
        // Survey Data
        surveyData: {
            satisfaction: null,
            ease_of_use: null,
            comments: ''
        },
        
        // Options
        easeOptions: [
            { value: 'very_easy', label: 'Sangat mudah' },
            { value: 'easy', label: 'Mudah' },
            { value: 'neutral', label: 'Biasa saja' },
            { value: 'difficult', label: 'Sulit' },
            { value: 'very_difficult', label: 'Sangat sulit' }
        ],
        
        // Settings
        showAfterScroll: {{ $showAfterScroll }},
        autoShow: {{ $autoShow ? 'true' : 'false' }},
        showOnce: {{ $showOnce ? 'true' : 'false' }},
        
        // Initialize
        init() {
            // Ensure modal is closed on init
            this.showModal = false;
            
            // Check if user has already seen/completed survey
            this.hasSeenSurvey = localStorage.getItem('survey_completed') === 'true';
            
            if (this.showOnce && this.hasSeenSurvey) {
                return; // Don't show if already completed and showOnce is true
            }
            
            if (this.autoShow) {
                this.startAutoShow();
            } else {
                this.showButton = true;
            }
        },
        
        startAutoShow() {
            // Show after scroll amount
            let scrolled = false;
            const checkScroll = () => {
                if (!scrolled && window.scrollY > this.showAfterScroll) {
                    scrolled = true;
                    this.showButton = true;
                    window.removeEventListener('scroll', checkScroll);
                }
            };
            
            window.addEventListener('scroll', checkScroll);
            
            // Also show after time delay (fallback)
            setTimeout(() => {
                if (!scrolled) {
                    this.showButton = true;
                }
            }, 10000); // 10 seconds fallback
        },
        
        openSurvey() {
            console.log('Opening survey modal...');
            this.showModal = true;
            this.hasSeenSurvey = true;
            
            // Track event
            if (typeof gtag !== 'undefined') {
                gtag('event', 'survey_opened', {
                    'event_category': 'engagement',
                    'event_label': 'satisfaction_survey'
                });
            }
        },
        
        closeSurvey() {
            console.log('Closing survey modal...');
            this.showModal = false;
            if (this.submitted) {
                this.showButton = false; // Hide button after completion
            }
        },
        
        async submitSurvey() {
            this.isSubmitting = true;
            
            try {
                const response = await fetch('/api/survey/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        ...this.surveyData,
                        page_url: window.location.href,
                        user_agent: navigator.userAgent,
                        timestamp: new Date().toISOString()
                    })
                });
                
                if (response.ok) {
                    this.submitted = true;
                    localStorage.setItem('survey_completed', 'true');
                    
                    // Track completion
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'survey_completed', {
                            'event_category': 'engagement',
                            'event_label': 'satisfaction_survey',
                            'satisfaction_rating': this.surveyData.satisfaction,
                            'ease_of_use': this.surveyData.ease_of_use
                        });
                    }
                } else {
                    throw new Error('Failed to submit survey');
                }
            } catch (error) {
                console.error('Survey submission error:', error);
                alert('Maaf, terjadi kesalahan. Silakan coba lagi.');
            } finally {
                this.isSubmitting = false;
            }
        },
        
        getEmoji(rating) {
            const emojis = ['😭', '😞', '😐', '😊', '😍'];
            return emojis[rating - 1];
        },
        
        getRatingText(rating) {
            const texts = ['Buruk', 'Kurang', 'Biasa', 'Baik', 'Luar Biasa'];
            return texts[rating - 1];
        }
    }
}
</script>
@endpush