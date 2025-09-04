{{-- resources/views/components/survey-widget-simple.blade.php --}}
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
    
    $currentPosition = $positionClasses[$position] ?? $positionClasses['bottom-left'];
@endphp

<!-- Survey Widget Container -->
<div id="survey-widget-simple" class="fixed {{ $currentPosition }} z-40 font-sans">
    <!-- Survey CTA Button -->
    <div class="relative group">
        <!-- Tooltip -->
        <div class="absolute bottom-full mb-3 right-0 bg-gray-900 text-white text-xs rounded-lg px-3 py-2 opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none whitespace-nowrap shadow-lg">
            Berikan feedback Anda
            <div class="absolute top-full right-6 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
        </div>
        
        <!-- Main Survey Button -->
        <button onclick="openSurveyModal()" 
                class="w-14 h-14 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white rounded-full shadow-lg hover:shadow-xl transform hover:scale-110 transition-all duration-300 flex items-center justify-center group relative overflow-hidden hover:ring-4 hover:ring-orange-500/30"
                title="Survey Kepuasan"
                aria-label="Open satisfaction survey">
            
            <!-- Survey Icon -->
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
        </button>
    </div>
</div>

<!-- Survey Modal -->
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

<script>
let selectedSatisfaction = null;
let surveySubmitted = false;

// Check if survey was already completed
window.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('survey_completed') === 'true') {
        document.getElementById('survey-widget-simple').style.display = 'none';
        return;
    }
    
    // Show widget after scroll or time
    let hasShown = false;
    
    // Show after scroll
    window.addEventListener('scroll', function() {
        if (!hasShown && window.scrollY > {{ $showAfterScroll }}) {
            hasShown = true;
            document.getElementById('survey-widget-simple').style.display = 'block';
        }
    });
    
    // Fallback: show after 10 seconds
    setTimeout(function() {
        if (!hasShown) {
            hasShown = true;
            document.getElementById('survey-widget-simple').style.display = 'block';
        }
    }, 10000);
});

function openSurveyModal() {
    console.log('Opening survey modal...');
    document.getElementById('survey-modal-simple').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeSurveyModal() {
    console.log('Closing survey modal...');
    document.getElementById('survey-modal-simple').classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    if (surveySubmitted) {
        document.getElementById('survey-widget-simple').style.display = 'none';
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
document.getElementById('survey-modal-simple').addEventListener('click', function(event) {
    if (event.target === this) {
        closeSurveyModal();
    }
});
</script>