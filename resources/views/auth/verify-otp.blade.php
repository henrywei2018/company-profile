{{-- resources/views/auth/verify-otp.blade.php --}}
<x-guest-layout>
    <div class="min-h-screen flex">
        {{-- Left Side - Image/Branding --}}
        <div class="hidden lg:block relative w-0 flex-1">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-600 via-amber-600 to-orange-700">
                {{-- Background Pattern --}}
                <div class="absolute inset-0 bg-[url('/images/grid.svg')] bg-center opacity-20"></div>
                
                {{-- Content --}}
                <div class="absolute inset-0 flex flex-col justify-center px-12 text-white">
                    <div class="max-w-md">
                        <h2 class="text-4xl font-bold mb-6">
                            Almost There!
                        </h2>
                        <p class="text-xl text-orange-100 mb-8 leading-relaxed">
                            We've sent a verification code to your email. Enter it below to complete your registration and start building your dreams with us.
                        </p>
                        
                        {{-- Features --}}
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-orange-100">Secure email verification</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <span class="text-orange-100">Code expires in 10 minutes</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7"/>
                                    </svg>
                                </div>
                                <span class="text-orange-100">Quick & easy verification</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Decorative Elements --}}
                <div class="absolute top-20 left-20 w-32 h-32 bg-white/10 rounded-full animate-pulse"></div>
                <div class="absolute bottom-40 left-40 w-20 h-20 bg-white/5 rounded-full animate-pulse" style="animation-delay: 2s;"></div>
                <div class="absolute top-1/2 left-10 w-16 h-16 bg-white/15 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
            </div>
        </div>

        {{-- Right Side - Form --}}
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                {{-- Logo --}}
                <div class="text-center mb-8">
                    <a href="{{ route('home') }}" class="inline-flex items-center">
                        @if($siteLogo)
                            <img src="{{ $siteLogo }}" alt="{{ $companyName }}" class="w-48 h-26 object-contain rounded-xl mr-3">
                        @else
                            <div class="w-48 h-26 bg-gradient-to-r from-orange-600 to-amber-600 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        @endif
                    </a>
                </div>

                {{-- Header --}}
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Verify your email</h2>
                    <p class="mt-2 text-gray-600">Enter the 6-digit code sent to your email</p>
                </div>

                {{-- Status Message --}}
                @if (session('status'))
                    <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-green-800">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                {{-- Email Hint --}}
                <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Check your email</p>
                            <p>We sent a verification code to <span class="font-semibold">{{ Auth::user()->email }}</span></p>
                        </div>
                    </div>
                </div>

                {{-- OTP Verification Form --}}
                <form method="POST" action="{{ route('verification.otp.verify') }}" class="space-y-6">
                    @csrf

                    {{-- OTP Input --}}
                    <div>
                        <label for="otp" class="block text-sm font-medium text-gray-700 mb-2">
                            Verification Code
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <input id="otp" 
                                   name="otp" 
                                   type="text" 
                                   autocomplete="off" 
                                   required 
                                   maxlength="6"
                                   value="{{ old('otp') }}"
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 text-center text-lg font-mono tracking-widest @error('otp') border-red-500 ring-2 ring-red-200 @enderror"
                                   placeholder="000000">
                        </div>
                        @error('otp')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Timer Display --}}
                    <div class="text-center">
                        <div id="timer-display" class="text-sm text-gray-600">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Code expires in: <span id="countdown" class="font-mono font-semibold text-orange-600">10:00</span>
                            </span>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div>
                        <button type="submit" 
                                id="verify-btn"
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-orange-300 group-hover:text-orange-200 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <span class="loading-text">Verify Email</span>
                        </button>
                    </div>

                    {{-- Resend Code Section --}}
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-3">Didn't receive the code?</p>
                        
                        <button type="button"
                                id="resend-btn"
                                onclick="resendCode()"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span class="resend-text">Resend Code</span>
                        </button>
                    </div>

                    {{-- Back to Login Link --}}
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            Need to use a different email?
                            <a href="{{ route('login') }}" 
                               class="text-orange-600 hover:text-orange-700 font-medium transition-colors duration-200">
                                Sign in with different account
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    @push('scripts')
    <script>
        let countdownTimer;
        let timeLeft = 600; // 10 minutes in seconds

        // Initialize countdown
        function startCountdown() {
            countdownTimer = setInterval(function() {
                timeLeft--;
                updateTimerDisplay();
                
                if (timeLeft <= 0) {
                    clearInterval(countdownTimer);
                    showExpiredMessage();
                }
            }, 1000);
        }

        function updateTimerDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            document.getElementById('countdown').textContent = 
                minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
            
            // Change color when time is running out
            const countdownElement = document.getElementById('countdown');
            if (timeLeft <= 60) {
                countdownElement.className = 'font-mono font-semibold text-red-600';
            } else if (timeLeft <= 180) {
                countdownElement.className = 'font-mono font-semibold text-yellow-600';
            }
        }

        function showExpiredMessage() {
            document.getElementById('timer-display').innerHTML = `
                <span class="inline-flex items-center text-red-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    Code has expired
                </span>
            `;
            document.getElementById('verify-btn').disabled = true;
        }

        // OTP input formatting
        document.getElementById('otp').addEventListener('input', function(e) {
            // Only allow numbers
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            
            // Auto-submit when 6 digits are entered
            if (e.target.value.length === 6) {
                // Add small delay for better UX
                setTimeout(() => {
                    document.querySelector('form').submit();
                }, 500);
            }
        });

        // Resend code function
        function resendCode() {
            const resendBtn = document.getElementById('resend-btn');
            const resendText = resendBtn.querySelector('.resend-text');
            
            resendBtn.disabled = true;
            resendText.innerHTML = `
                <svg class="animate-spin h-4 w-4 text-gray-600 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Sending...
            `;

            // Create a form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("verification.otp.resend") }}';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            document.body.appendChild(form);
            form.submit();
        }

        // Form submission with loading state
        document.querySelector('form').addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            const loadingText = button.querySelector('.loading-text');
            
            button.disabled = true;
            loadingText.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Verifying...
            `;
        });

        // Enhanced focus effects
        document.getElementById('otp').addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-orange-500');
        });
        
        document.getElementById('otp').addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-orange-500');
        });

        // Start countdown when page loads
        document.addEventListener('DOMContentLoaded', function() {
            startCountdown();
            // Auto-focus on OTP input
            document.getElementById('otp').focus();
        });

        // Paste support for OTP
        document.getElementById('otp').addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const numbers = paste.replace(/[^0-9]/g, '').slice(0, 6);
            this.value = numbers;
            
            if (numbers.length === 6) {
                setTimeout(() => {
                    document.querySelector('form').submit();
                }, 500);
            }
        });
    </script>
    @endpush
</x-guest-layout>