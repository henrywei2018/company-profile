{{-- resources/views/auth/login.blade.php --}}
<x-guest-layout>
    <div class="min-h-screen flex">
        {{-- Left Side - Form --}}
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
                    <h2 class="text-3xl font-bold text-gray-900">Welcome back</h2>
                    <p class="mt-2 text-gray-600">Sign in to your account to continue</p>
                </div>

                {{-- Session Status --}}
                <x-auth-session-status class="mb-4" :status="session('status')" />

                {{-- Login Form --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

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
                                   autocomplete="current-password" 
                                   required
                                   class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 @error('password') border-red-500 ring-2 ring-red-200 @enderror"
                                   placeholder="Enter your password">
                            <button type="button" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                    onclick="togglePassword()">
                                <svg id="eye-icon" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
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

                    {{-- Remember Me & Forgot Password --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" 
                                   name="remember" 
                                   type="checkbox" 
                                   class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                                Remember me
                            </label>
                        </div>

                        @if (Route::has('password.request'))
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" 
                               class="text-orange-600 hover:text-orange-700 font-medium transition-colors duration-200">
                                Forgot password?
                            </a>
                        </div>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-orange-300 group-hover:text-orange-200 transition-colors duration-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                            <span class="loading-text">Sign in to your account</span>
                        </button>
                    </div>

                    {{-- Register Link --}}
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            Don't have an account?
                            <a href="{{ route('register') }}" 
                               class="text-orange-600 hover:text-orange-700 font-medium transition-colors duration-200">
                                Create one now
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Side - Image/Branding --}}
        <div class="hidden lg:block relative w-0 flex-1">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-600 via-amber-600 to-orange-700">
                {{-- Background Pattern --}}
                <div class="absolute inset-0 bg-[url('/images/grid.svg')] bg-center opacity-20"></div>
                
                {{-- Content --}}
                <div class="absolute inset-0 flex flex-col justify-center px-12 text-white">
                    <div class="max-w-md">
                        <h2 class="text-4xl font-bold mb-6">
                            Build Your Future with Us
                        </h2>
                        <p class="text-xl text-orange-100 mb-8 leading-relaxed">
                            Join thousands of satisfied clients who trust us with their construction and engineering projects.
                        </p>
                        
                        {{-- Stats --}}
                        <div class="grid grid-cols-2 gap-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold mb-2">{{ $authStats['completed_projects'] }}+</div>
                                <div class="text-orange-200 text-sm">Projects Completed</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold mb-2">{{ $authStats['years_experience'] }}+</div>
                                <div class="text-orange-200 text-sm">Years Experience</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Decorative Elements --}}
                <div class="absolute top-20 right-20 w-32 h-32 bg-white/10 rounded-full animate-pulse"></div>
                <div class="absolute bottom-40 right-40 w-20 h-20 bg-white/5 rounded-full animate-pulse" style="animation-delay: 2s;"></div>
                <div class="absolute top-1/2 right-10 w-16 h-16 bg-white/15 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    @push('scripts')
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
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
                Signing in...
            `;
        });

        // Enhanced focus effects
        document.querySelectorAll('input[type="email"], input[type="password"]').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('ring-2', 'ring-orange-500');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('ring-2', 'ring-orange-500');
            });
        });
    </script>

    <script>
        /**
         * Login Autofill Script for Development Testing
         * Only loads in development environments
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Test user credentials
            const testUsers = {
                admin: {
                    email: 'superadmin@usahaprimaestari.com',
                    password: 'password',
                    label: 'Super Admin',
                    color: 'bg-red-500 hover:bg-red-600'
                },
                client: {
                    email: 'info@majubersama.com',
                    password: 'password', 
                    label: 'Client User',
                    color: 'bg-green-500 hover:bg-green-600'
                },
            };

            // Find login form elements
            const emailInput = document.querySelector('input[name="email"]');
            const passwordInput = document.querySelector('input[name="password"]');

            if (!emailInput || !passwordInput) return;

            // Create autofill panel
            const autofillPanel = document.createElement('div');
            autofillPanel.className = 'fixed top-4 right-4 z-50 bg-white rounded-lg shadow-xl border border-orange-200 p-4 max-w-xs transform transition-all duration-300';
            autofillPanel.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-orange-600">🧪 Quick Login</h3>
                    <button id="close-autofill" class="text-gray-400 hover:text-orange-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="space-y-2 mb-3">
                    ${Object.entries(testUsers).map(([key, user]) => `
                        <button type="button" 
                                class="autofill-btn w-full text-white text-xs px-3 py-2 rounded font-medium transition-all transform hover:scale-105 ${user.color}"
                                data-email="${user.email}" 
                                data-password="${user.password}"
                                title="Double-click to auto-login">
                            ${user.label}
                        </button>
                    `).join('')}
                </div>
                <button type="button" id="clear-form" class="w-full text-gray-600 text-xs px-3 py-2 rounded border border-gray-300 hover:bg-gray-50 transition-colors">
                    Clear Form
                </button>
                <div class="mt-2 text-xs text-gray-500 text-center">
                    Dev Environment Only
                </div>
            `;

            document.body.appendChild(autofillPanel);

            // Autofill functionality
            document.querySelectorAll('.autofill-btn').forEach(button => {
                // Single click - fill form
                button.addEventListener('click', function() {
                    const email = this.dataset.email;
                    const password = this.dataset.password;
                    
                    emailInput.value = email;
                    passwordInput.value = password;
                    
                    // Visual feedback
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => this.style.transform = 'scale(1)', 150);
                    
                    // Focus password field
                    passwordInput.focus();
                });
                
                // Double click - fill and submit
                button.addEventListener('dblclick', function() {
                    const email = this.dataset.email;
                    const password = this.dataset.password;
                    
                    emailInput.value = email;
                    passwordInput.value = password;
                    
                    // Submit after brief delay
                    setTimeout(() => {
                        document.querySelector('form').submit();
                    }, 300);
                });
            });

            // Clear form
            document.getElementById('clear-form').addEventListener('click', function() {
                emailInput.value = '';
                passwordInput.value = '';
                emailInput.focus();
            });

            // Close panel
            document.getElementById('close-autofill').addEventListener('click', function() {
                autofillPanel.remove();
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.shiftKey) {
                    const users = Object.values(testUsers);
                    const keyIndex = parseInt(e.key) - 1;
                    
                    if (keyIndex >= 0 && keyIndex < users.length) {
                        e.preventDefault();
                        const user = users[keyIndex];
                        emailInput.value = user.email;
                        passwordInput.value = user.password;
                    }
                }
            });

            // Console helper
            window.quickLogin = function(role = 'admin') {
                const user = testUsers[role];
                if (user) {
                    emailInput.value = user.email;
                    passwordInput.value = user.password;
                    console.log(`✅ Filled with ${role} credentials`);
                } else {
                    console.log('Available roles:', Object.keys(testUsers));
                }
            };

            console.log('🧪 Dev autofill loaded! Use quickLogin("admin") or keyboard shortcuts Ctrl+Shift+1-4');
        });
    </script>
    @endpush
</x-guest-layout>