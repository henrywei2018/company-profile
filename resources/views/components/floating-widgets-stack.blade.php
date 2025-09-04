{{-- resources/views/components/floating-widgets-stack.blade.php --}}
@props([
    'position' => 'bottom-right',
    'spacing' => 'space-y-3',
    'showWhatsApp' => true,
    'showSurvey' => true,
    'showScrollTop' => true,
    'whatsappNumber' => null,
    'whatsappMessage' => null
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

<!-- Floating Widgets Stack Container -->
<div class="fixed {{ $currentPosition }} z-40 flex flex-col {{ $spacing }} items-end">
    
    {{-- Scroll to Top Button (Top of stack) --}}
    @if($showScrollTop)
    <div id="scroll-to-top-stack" class="scroll-widget opacity-0 transform translate-y-2 transition-all duration-300">
        <button onclick="scrollToTop()" 
                class="w-12 h-12 bg-gray-800 hover:bg-gray-900 text-white rounded-full shadow-lg hover:shadow-xl transform hover:scale-110 transition-all duration-300 flex items-center justify-center group relative overflow-hidden"
                title="Scroll to top"
                aria-label="Scroll to top">
            <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="2" 
                      d="M5 10l7-7m0 0l7 7m-7-7v18"/>
            </svg>
        </button>
    </div>
    @endif

    {{-- Survey Widget (Middle of stack) --}}
    @if($showSurvey)
    <div id="survey-widget-stack" class="survey-widget opacity-0 transform translate-y-2 transition-all duration-300">
        <div class="relative group">
            <!-- Tooltip -->
            <div class="absolute bottom-full mb-3 right-0 bg-gray-900 text-white text-xs rounded-lg px-3 py-2 opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none whitespace-nowrap shadow-lg">
                Berikan feedback Anda
                <div class="absolute top-full right-6 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
            </div>
            
            <button onclick="openSurveyModal()" 
                    class="w-14 h-14 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white rounded-full shadow-lg hover:shadow-xl transform hover:scale-110 transition-all duration-300 flex items-center justify-center group relative overflow-hidden hover:ring-4 hover:ring-orange-500/30"
                    title="Survey Kepuasan"
                    aria-label="Open satisfaction survey">
                
                <svg class="w-6 h-6 transition-transform duration-200 group-hover:scale-110" 
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
            </button>
        </div>
    </div>
    @endif

    {{-- WhatsApp Button (Bottom of stack) --}}
    @if($showWhatsApp && $whatsappNumber)
    <div class="whatsapp-widget">
        <div class="relative group">
            <!-- Tooltip -->
            <div class="absolute bottom-full mb-3 right-0 bg-gray-900 text-white text-xs rounded-lg px-3 py-2 opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none whitespace-nowrap shadow-lg">
                Hubungi via WhatsApp
                <div class="absolute top-full right-6 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
            </div>
            
            <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage ?? 'Halo! Saya ingin bertanya.') }}" 
               target="_blank"
               class="w-14 h-14 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-lg hover:shadow-xl transform hover:scale-110 transition-all duration-300 flex items-center justify-center group relative overflow-hidden hover:ring-4 hover:ring-green-500/30"
               title="Hubungi via WhatsApp"
               aria-label="Contact via WhatsApp">
                
                <svg class="w-7 h-7 transition-transform duration-200 group-hover:scale-110" 
                     fill="currentColor" 
                     viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.787"/>
                </svg>
                
                <!-- Pulse Ring -->
                <div class="absolute inset-0 rounded-full border-2 border-green-400 opacity-75 animate-ping"></div>
            </a>
        </div>
    </div>
    @endif

</div>

<!-- Survey Modal (Keep existing modal) -->
<div id="survey-modal-simple" 
     class="fixed inset-0 z-[9999] overflow-y-auto hidden"
     style="background-color: rgba(0, 0, 0, 0.5);">
    
    <!-- Modal Container -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all">
            
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
                    <button onclick="closeSurveyModal()" 
                            class="w-8 h-8 rounded-full hover:bg-white hover:bg-opacity-20 flex items-center justify-center transition-colors duration-200 text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal Content -->
            <div class="p-6">
                <div id="survey-form">
                    <!-- Question 1: Overall Satisfaction -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Seberapa puas Anda dengan website kami?
                        </label>
                        <div class="flex justify-between space-x-2" id="satisfaction-buttons">
                            <button type="button" onclick="selectSatisfaction(1)" class="satisfaction-btn flex-1 h-12 rounded-lg border-2 transition-all duration-200 flex items-center justify-center text-sm font-medium border-gray-200 hover:border-orange-300 text-gray-600 hover:text-orange-600">
                                <div class="text-center">
                                    <div class="text-lg mb-1">😭</div>
                                    <div>Buruk</div>
                                </div>
                            </button>
                            <button type="button" onclick="selectSatisfaction(2)" class="satisfaction-btn flex-1 h-12 rounded-lg border-2 transition-all duration-200 flex items-center justify-center text-sm font-medium border-gray-200 hover:border-orange-300 text-gray-600 hover:text-orange-600">
                                <div class="text-center">
                                    <div class="text-lg mb-1">😞</div>
                                    <div>Kurang</div>
                                </div>
                            </button>
                            <button type="button" onclick="selectSatisfaction(3)" class="satisfaction-btn flex-1 h-12 rounded-lg border-2 transition-all duration-200 flex items-center justify-center text-sm font-medium border-gray-200 hover:border-orange-300 text-gray-600 hover:text-orange-600">
                                <div class="text-center">
                                    <div class="text-lg mb-1">😐</div>
                                    <div>Biasa</div>
                                </div>
                            </button>
                            <button type="button" onclick="selectSatisfaction(4)" class="satisfaction-btn flex-1 h-12 rounded-lg border-2 transition-all duration-200 flex items-center justify-center text-sm font-medium border-gray-200 hover:border-orange-300 text-gray-600 hover:text-orange-600">
                                <div class="text-center">
                                    <div class="text-lg mb-1">😊</div>
                                    <div>Baik</div>
                                </div>
                            </button>
                            <button type="button" onclick="selectSatisfaction(5)" class="satisfaction-btn flex-1 h-12 rounded-lg border-2 transition-all duration-200 flex items-center justify-center text-sm font-medium border-gray-200 hover:border-orange-300 text-gray-600 hover:text-orange-600">
                                <div class="text-center">
                                    <div class="text-lg mb-1">😍</div>
                                    <div>Luar Biasa</div>
                                </div>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Question 2: Comments -->
                    <div class="mb-6">
                        <label for="survey-comments" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Saran atau komentar (opsional)
                        </label>
                        <textarea id="survey-comments"
                                  rows="3"
                                  placeholder="Bagikan pengalaman atau saran Anda..."
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"></textarea>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex space-x-3">
                        <button onclick="submitSurveySimple()" 
                                id="submit-btn"
                                disabled
                                class="flex-1 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 disabled:from-gray-400 disabled:to-gray-500 text-white py-3 px-4 rounded-lg font-medium transition-all duration-200 disabled:cursor-not-allowed flex items-center justify-center">
                            <span id="submit-text">Kirim Feedback</span>
                        </button>
                        <button onclick="closeSurveyModal()" 
                                class="px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            Batal
                        </button>
                    </div>
                </div>
                
                <!-- Thank You Message -->
                <div id="thank-you-message" class="text-center py-8 hidden">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Terima Kasih!</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Feedback Anda sangat berharga untuk kami</p>
                    <button onclick="closeSurveyModal()" 
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
// Survey functionality
let selectedSatisfaction = null;
let surveySubmitted = false;

// Initialize widgets
window.addEventListener('DOMContentLoaded', function() {
    initializeWidgets();
});

function initializeWidgets() {
    // Hide survey widget if already completed
    if (localStorage.getItem('survey_completed') === 'true') {
        const surveyWidget = document.getElementById('survey-widget-stack');
        if (surveyWidget) surveyWidget.style.display = 'none';
    } else {
        // Show survey widget after scroll or time
        showSurveyAfterTrigger();
    }
    
    // Show scroll to top when needed
    initializeScrollToTop();
}

function showSurveyAfterTrigger() {
    let hasShown = false;
    
    // Show after scroll
    window.addEventListener('scroll', function() {
        if (!hasShown && window.scrollY > 3000) {
            hasShown = true;
            showWidget('survey-widget-stack');
        }
    });
    
    // Fallback: show after 10 seconds
    setTimeout(function() {
        if (!hasShown) {
            hasShown = true;
            showWidget('survey-widget-stack');
        }
    }, 10000);
}

function initializeScrollToTop() {
    window.addEventListener('scroll', function() {
        const scrollWidget = document.getElementById('scroll-to-top-stack');
        if (window.scrollY > 300) {
            showWidget('scroll-to-top-stack');
        } else {
            hideWidget('scroll-to-top-stack');
        }
    });
}

function showWidget(widgetId) {
    const widget = document.getElementById(widgetId);
    if (widget) {
        widget.classList.remove('opacity-0', 'translate-y-2');
        widget.classList.add('opacity-100', 'translate-y-0');
    }
}

function hideWidget(widgetId) {
    const widget = document.getElementById(widgetId);
    if (widget) {
        widget.classList.remove('opacity-100', 'translate-y-0');
        widget.classList.add('opacity-0', 'translate-y-2');
    }
}

// Scroll to top functionality
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Survey modal functionality
function openSurveyModal() {
    document.getElementById('survey-modal-simple').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeSurveyModal() {
    document.getElementById('survey-modal-simple').classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    if (surveySubmitted) {
        const surveyWidget = document.getElementById('survey-widget-stack');
        if (surveyWidget) surveyWidget.style.display = 'none';
    }
}

function selectSatisfaction(rating) {
    selectedSatisfaction = rating;
    
    // Update button styles
    const buttons = document.querySelectorAll('.satisfaction-btn');
    buttons.forEach((btn, index) => {
        if (index + 1 === rating) {
            btn.className = 'satisfaction-btn flex-1 h-12 rounded-lg border-2 transition-all duration-200 flex items-center justify-center text-sm font-medium border-orange-500 bg-orange-50 text-orange-700';
        } else {
            btn.className = 'satisfaction-btn flex-1 h-12 rounded-lg border-2 transition-all duration-200 flex items-center justify-center text-sm font-medium border-gray-200 hover:border-orange-300 text-gray-600 hover:text-orange-600';
        }
    });
    
    // Enable submit button
    document.getElementById('submit-btn').disabled = false;
    document.getElementById('submit-btn').className = 'flex-1 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white py-3 px-4 rounded-lg font-medium transition-all duration-200 flex items-center justify-center';
}

async function submitSurveySimple() {
    if (!selectedSatisfaction) return;
    
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    
    // Show loading state
    submitBtn.disabled = true;
    submitText.textContent = 'Mengirim...';
    
    const comments = document.getElementById('survey-comments').value;
    
    try {
        const response = await fetch('/api/survey/submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                satisfaction: selectedSatisfaction,
                comments: comments || null,
                page_url: window.location.href,
                user_agent: navigator.userAgent,
                timestamp: new Date().toISOString()
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            surveySubmitted = true;
            localStorage.setItem('survey_completed', 'true');
            
            // Show thank you message
            document.getElementById('survey-form').classList.add('hidden');
            document.getElementById('thank-you-message').classList.remove('hidden');
            
            // Track with Google Analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'survey_completed', {
                    'event_category': 'engagement',
                    'event_label': 'satisfaction_survey',
                    'satisfaction_rating': selectedSatisfaction
                });
            }
        } else {
            throw new Error(data.message || 'Failed to submit survey');
        }
    } catch (error) {
        console.error('Survey submission error:', error);
        alert('Maaf, terjadi kesalahan. Silakan coba lagi.');
        
        // Reset button
        submitBtn.disabled = false;
        submitText.textContent = 'Kirim Feedback';
    }
}

// Close modal with ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeSurveyModal();
    }
});

// Close modal when clicking backdrop
if (document.getElementById('survey-modal-simple')) {
    document.getElementById('survey-modal-simple').addEventListener('click', function(event) {
        if (event.target === this) {
            closeSurveyModal();
        }
    });
}
</script>
@endpush