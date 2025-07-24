{{-- resources/views/auth/forgot-password.blade.php --}}
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
                    <h2 class="text-3xl font-bold text-gray-900">Forgot Password?</h2>
                    <p class="mt-2 text-gray-600">No problem! Enter your email and we'll send you a reset link</p>
                </div>

                {{-- Session Status --}}
                <x-auth-session-status class="mb-4" :status="session('status')" />

                {{-- Forgot Password Form --}}
                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
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
                                   autofocus
                                   value="{{ old('email') }}"
                                   class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 @error('email') border-red-500 ring-2 ring-red-200 @enderror"
                                   placeholder="Enter your email address">
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

                    {{-- Submit Button --}}
                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-orange-300 group-hover:text-orange-200 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <span class="loading-text">Send Reset Link</span>
                        </button>
                    </div>

                    {{-- Back to Login Link --}}
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            Remember your password?
                            <a href="{{ route('login') }}" 
                               class="text-orange-600 hover:text-orange-700 font-medium transition-colors duration-200">
                                Sign in here
                            </a>
                        </p>
                    </div>
                </form>

                {{-- Additional Info --}}
                <div class="mt-8 p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-700">
                                <span class="font-medium">Need help?</span> 
                                If you don't receive the reset email within a few minutes, check your spam folder or contact our support team.
                            </p>
                        </div>
                    </div>
                </div>
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
                            Reset Your Password
                        </h2>
                        <p class="text-xl text-orange-100 mb-8 leading-relaxed">
                            Don't worry, it happens to the best of us. We'll help you get back to building your dreams.
                        </p>
                        
                        {{-- Security Features --}}
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <span class="text-orange-100">Secure password reset</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="text-orange-100">Email verification</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-orange-100">Link expires in 60 minutes</span>
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
                Sending reset link...
            `;
        });

        // Enhanced focus effects
        document.querySelectorAll('input[type="email"]').forEach(input => {
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