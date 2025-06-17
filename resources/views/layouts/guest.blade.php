{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/css/orange-theme.css', 'resources/js/app.js'])

        <!-- Additional Styles -->
        @stack('styles')
        
        <style>
            /* Custom styles for auth pages */
            .auth-bg-pattern {
                background-image: 
                    radial-gradient(circle at 25px 25px, rgba(255, 255, 255, 0.2) 2px, transparent 0),
                    radial-gradient(circle at 75px 75px, rgba(255, 255, 255, 0.1) 2px, transparent 0);
                background-size: 100px 100px;
            }
            
            .floating-animation {
                animation: float 6s ease-in-out infinite;
            }
            
            .floating-animation:nth-child(2) {
                animation-delay: 2s;
            }
            
            .floating-animation:nth-child(3) {
                animation-delay: 4s;
            }
            
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            
            /* Smooth transitions for all elements */
            * {
                transition: all 0.3s ease;
            }
            
            /* Focus styles */
            input:focus, select:focus, textarea:focus, button:focus {
                outline: none;
                box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.2);
            }
            
            /* Custom scrollbar for the page */
            ::-webkit-scrollbar {
                width: 8px;
            }
            
            ::-webkit-scrollbar-track {
                background: #f1f1f1;
            }
            
            ::-webkit-scrollbar-thumb {
                background: linear-gradient(180deg, #f97316, #fbbf24);
                border-radius: 4px;
            }
            
            ::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(180deg, #ea580c, #f59e0b);
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50">
        <!-- Background Elements -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <!-- Gradient Background -->
            <div class="absolute inset-0 bg-gradient-to-br from-orange-50 via-white to-amber-50"></div>
            
            <!-- Floating Shapes -->
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-orange-200/20 rounded-full floating-animation blur-3xl"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-amber-200/20 rounded-full floating-animation blur-3xl"></div>
            <div class="absolute top-1/2 right-1/3 w-48 h-48 bg-orange-300/10 rounded-full floating-animation blur-3xl"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 min-h-screen">
            {{ $slot }}
        </div>

        <!-- Scripts -->
        @stack('scripts')
        
        <script>
            // Enhanced form interactions
            document.addEventListener('DOMContentLoaded', function() {
                // Add loading states to all forms
                const forms = document.querySelectorAll('form');
                forms.forEach(form => {
                    form.addEventListener('submit', function() {
                        const submitButton = this.querySelector('button[type="submit"]');
                        if (submitButton && !submitButton.disabled) {
                            submitButton.disabled = true;
                            submitButton.style.opacity = '0.7';
                            submitButton.style.transform = 'scale(0.98)';
                        }
                    });
                });

                // Enhanced input focus effects
                const inputs = document.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        this.style.transform = 'scale(1.02)';
                    });
                    
                    input.addEventListener('blur', function() {
                        this.style.transform = 'scale(1)';
                    });
                });

                // Smooth page transitions
                const links = document.querySelectorAll('a[href^="/"]');
                links.forEach(link => {
                    link.addEventListener('click', function(e) {
                        if (!e.ctrlKey && !e.metaKey) {
                            this.style.transform = 'scale(0.95)';
                            setTimeout(() => {
                                this.style.transform = 'scale(1)';
                            }, 150);
                        }
                    });
                });

                // Add ripple effect to buttons
                const buttons = document.querySelectorAll('button, .btn');
                buttons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        const ripple = document.createElement('span');
                        const rect = this.getBoundingClientRect();
                        const size = Math.max(rect.width, rect.height);
                        const x = e.clientX - rect.left - size / 2;
                        const y = e.clientY - rect.top - size / 2;
                        
                        ripple.style.width = ripple.style.height = size + 'px';
                        ripple.style.left = x + 'px';
                        ripple.style.top = y + 'px';
                        ripple.classList.add('ripple');
                        
                        this.appendChild(ripple);
                        
                        setTimeout(() => {
                            ripple.remove();
                        }, 600);
                    });
                });

                // Auto-hide flash messages
                const flashMessages = document.querySelectorAll('[data-flash]');
                flashMessages.forEach(message => {
                    setTimeout(() => {
                        message.style.opacity = '0';
                        message.style.transform = 'translateY(-100%)';
                        setTimeout(() => {
                            message.remove();
                        }, 300);
                    }, 5000);
                });
            });
        </script>

        <style>
            /* Ripple effect */
            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                transform: scale(0);
                animation: ripple-animation 0.6s linear;
                pointer-events: none;
            }

            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }

            /* Enhanced button hover effects */
            button:hover, .btn:hover {
                box-shadow: 0 10px 25px -5px rgba(249, 115, 22, 0.4);
            }

            /* Loading spinner for buttons */
            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            .animate-spin {
                animation: spin 1s linear infinite;
            }

            /* Enhanced focus ring */
            .focus\:ring-orange-500:focus {
                --tw-ring-color: rgba(249, 115, 22, 0.5);
            }

            /* Custom gradient text */
            .gradient-text {
                background: linear-gradient(135deg, #f97316, #fbbf24);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            /* Enhanced shadows */
            .shadow-orange {
                box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.1), 0 4px 6px -2px rgba(249, 115, 22, 0.05);
            }

            .shadow-orange-lg {
                box-shadow: 0 20px 25px -5px rgba(249, 115, 22, 0.1), 0 10px 10px -5px rgba(249, 115, 22, 0.04);
            }

            /* Smooth page load animation */
            body {
                animation: fadeIn 0.5s ease-in;
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            /* Enhanced card styles */
            .auth-card {
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            /* Mobile optimizations */
            @media (max-width: 768px) {
                .floating-animation {
                    display: none;
                }
                
                body {
                    background-attachment: scroll;
                }
            }

            /* Dark mode support (if needed) */
            @media (prefers-color-scheme: dark) {
                body {
                    background: linear-gradient(135deg, #1f2937, #374151);
                }
            }

            /* Accessibility improvements */
            @media (prefers-reduced-motion: reduce) {
                .floating-animation,
                .ripple {
                    animation: none;
                }
                
                * {
                    transition: none !important;
                }
            }

            /* High contrast mode support */
            @media (prefers-contrast: high) {
                .gradient-text {
                    background: none;
                    color: #ea580c;
                    -webkit-text-fill-color: unset;
                }
                
                button, .btn {
                    border: 2px solid currentColor;
                }
            }
        </style>
    </body>
</html>