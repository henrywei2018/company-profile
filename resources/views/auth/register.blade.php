{{-- resources/views/auth/register.blade.php --}}
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
                            Start Your Journey Today
                        </h2>
                        <p class="text-xl text-orange-100 mb-8 leading-relaxed">
                            Create your account and join our community of satisfied clients who build their dreams with us.
                        </p>
                        
                        {{-- Features --}}
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-orange-100">Free project consultations</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-orange-100">Track project progress</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-orange-100">24/7 customer support</span>
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
                    <h2 class="text-3xl font-bold text-gray-900">Create your account</h2>
                    <p class="mt-2 text-gray-600">Start your construction journey with us</p>
                </div>

                {{-- Register Form --}}
                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input id="name" 
                                   name="name" 
                                   type="text" 
                                   autocomplete="name" 
                                   required 
                                   value="{{ old('name') }}"
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 @error('name') border-red-500 ring-2 ring-red-200 @enderror"
                                   placeholder="Enter your full name">
                        </div>
                        @error('name')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Email Address --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input id="email" 
                                   name="email" 
                                   type="email" 
                                   autocomplete="email" 
                                   required 
                                   value="{{ old('email') }}"
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 @error('email') border-red-500 ring-2 ring-red-200 @enderror"
                                   placeholder="Enter your email">
                        </div>
                        @error('email')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Phone (Optional) --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number <span class="text-gray-400 text-xs">(optional)</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <input id="phone" 
                                   name="phone" 
                                   type="tel" 
                                   autocomplete="tel" 
                                   value="{{ old('phone') }}"
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Enter your phone number">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input id="password" 
                                   name="password" 
                                   type="password" 
                                   autocomplete="new-password" 
                                   required
                                   class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 @error('password') border-red-500 ring-2 ring-red-200 @enderror"
                                   placeholder="Create a strong password">
                            <button type="button" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                    onclick="togglePassword('password')">
                                <svg id="password-eye-icon" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        {{-- Password Strength Indicator --}}
                        <div class="mt-2">
                            <div class="flex space-x-1">
                                <div id="strength-1" class="h-1 w-1/4 bg-gray-200 rounded"></div>
                                <div id="strength-2" class="h-1 w-1/4 bg-gray-200 rounded"></div>
                                <div id="strength-3" class="h-1 w-1/4 bg-gray-200 rounded"></div>
                                <div id="strength-4" class="h-1 w-1/4 bg-gray-200 rounded"></div>
                            </div>
                            <p id="strength-text" class="text-xs text-gray-500 mt-1">Password strength</p>
                        </div>
                        @error('password')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input id="password_confirmation" 
                                   name="password_confirmation" 
                                   type="password" 
                                   autocomplete="new-password" 
                                   required
                                   class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 @error('password_confirmation') border-red-500 ring-2 ring-red-200 @enderror"
                                   placeholder="Confirm your password">
                            <button type="button" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                    onclick="togglePassword('password_confirmation')">
                                <svg id="confirm-password-eye-icon" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Terms & Privacy --}}
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="terms" 
                                   name="terms" 
                                   type="checkbox" 
                                   required
                                   class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded @error('terms') border-red-500 @enderror">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="text-gray-700">
                                I agree to the 
                                <a href="#" class="text-orange-600 hover:text-orange-700 font-medium">Terms of Service</a>
                                and 
                                <a href="#" class="text-orange-600 hover:text-orange-700 font-medium">Privacy Policy</a>
                            </label>
                        </div>
                    </div>
                    @error('terms')
                    <p class="text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror

                    {{-- Submit Button --}}
                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-orange-300 group-hover:text-orange-200 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </span>
                            <span class="loading-text">Create your account</span>
                        </button>
                    </div>

                    {{-- Login Link --}}
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            Already have an account?
                            <a href="{{ route('login') }}" 
                               class="text-orange-600 hover:text-orange-700 font-medium transition-colors duration-200">
                                Sign in here
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
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId === 'password' ? 'password-eye-icon' : 'confirm-password-eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        }

        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            const checks = {
                length: password.length >= 8,
                lowercase: /[a-z]/.test(password),
                uppercase: /[A-Z]/.test(password),
                numbers: /\d/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
            };

            Object.values(checks).forEach(check => {
                if (check) strength++;
            });

            return { strength, checks };
        }

        function updatePasswordStrength(password) {
            const result = checkPasswordStrength(password);
            const strengthBars = document.querySelectorAll('[id^="strength-"]');
            const strengthText = document.getElementById('strength-text');

            // Reset all bars
            strengthBars.forEach(bar => {
                bar.className = 'h-1 w-1/4 bg-gray-200 rounded';
            });

            // Update based on strength
            if (result.strength >= 1) {
                strengthBars[0].className = 'h-1 w-1/4 bg-red-500 rounded';
                strengthText.textContent = 'Weak password';
                strengthText.className = 'text-xs text-red-500 mt-1';
            }
            if (result.strength >= 2) {
                strengthBars[1].className = 'h-1 w-1/4 bg-yellow-500 rounded';
                strengthText.textContent = 'Fair password';
                strengthText.className = 'text-xs text-yellow-600 mt-1';
            }
            if (result.strength >= 3) {
                strengthBars[2].className = 'h-1 w-1/4 bg-blue-500 rounded';
                strengthText.textContent = 'Good password';
                strengthText.className = 'text-xs text-blue-600 mt-1';
            }
            if (result.strength >= 4) {
                strengthBars[3].className = 'h-1 w-1/4 bg-green-500 rounded';
                strengthText.textContent = 'Strong password';
                strengthText.className = 'text-xs text-green-600 mt-1';
            }
        }

        // Password input event listener
        document.getElementById('password').addEventListener('input', function(e) {
            updatePasswordStrength(e.target.value);
        });

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
                Creating account...
            `;
        });

        // Enhanced focus effects
        document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="password"]').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('ring-2', 'ring-orange-500');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('ring-2', 'ring-orange-500');
            });
        });
    </script>
    @endpush
</x-guest-layout>