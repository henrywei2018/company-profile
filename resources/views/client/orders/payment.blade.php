{{-- resources/views/client/orders/payment.blade.php --}}
<x-layouts.client>
    <x-slot name="title">Payment - Order #{{ $order->order_number }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-8">
        
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <p class="text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Header with Breadcrumb -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('client.orders.show', $order) }}" 
               class="flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Order #{{ $order->order_number }}
            </a>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-gray-900 dark:text-white font-medium">Payment</span>
        </div>

        <!-- Payment Steps Progress -->
        <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-center space-x-8">
                <div class="flex items-center text-green-600 dark:text-green-400">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="ml-3 text-sm font-medium">Order Confirmed</span>
                </div>
                
                <div class="flex-1 h-px bg-gray-200 dark:bg-gray-600"></div>
                
                <div class="flex items-center text-blue-600 dark:text-blue-400">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <div class="w-3 h-3 bg-blue-600 dark:bg-blue-400 rounded-full animate-pulse"></div>
                    </div>
                    <span class="ml-3 text-sm font-medium">Payment</span>
                </div>
                
                <div class="flex-1 h-px bg-gray-200 dark:bg-gray-600"></div>
                
                <div class="flex items-center text-gray-400">
                    <div class="w-8 h-8 border-2 border-gray-300 dark:border-gray-600 rounded-full flex items-center justify-center">
                        <span class="text-xs font-medium">3</span>
                    </div>
                    <span class="ml-3 text-sm">Processing</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Payment Form -->
            <div class="space-y-6">
                
                <!-- Order Summary Card -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-6 border border-blue-200/50 dark:border-blue-700/50">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">Payment Required</h3>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-700 dark:text-blue-300">Total Amount</p>
                            <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-blue-700 dark:text-blue-300">Order #{{ $order->order_number }}</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400">{{ $order->items->count() }} item(s)</p>
                        </div>
                    </div>
                </div>

                <!-- Upload Form -->
                <form action="{{ route('client.orders.payment.upload', $order) }}" method="POST" enctype="multipart/form-data" id="paymentForm" class="space-y-6">
                    @csrf
                    
                    <!-- Step 1: Select Payment Method -->
                    <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gray-50 dark:bg-neutral-700 px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-blue-600 dark:text-blue-400 text-sm font-bold">1</span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select Payment Method</h3>
                            </div>
                        </div>
                        
                        <!-- Payment Methods -->
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 gap-4">
                                    @forelse($paymentMethods as $index => $method)
                                        <label class="payment-method-card">
                                            <input type="radio" name="payment_method" value="{{ $method->name }}" class="sr-only" {{ $index === 0 ? 'required' : '' }}>
                                            <div class="payment-option">
                                                <div class="flex items-center justify-between p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl hover:border-blue-300 dark:hover:border-blue-600 cursor-pointer transition-all duration-200">
                                                    <div class="flex items-center">
                                                        @if($method->logo)
                                                            <div class="w-12 h-12 rounded-xl overflow-hidden mr-4 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                                                <img src="{{ $method->logo_url }}" alt="{{ $method->name }}" class="w-10 h-10 object-contain">
                                                            </div>
                                                        @else
                                                            @php
                                                                $colors = ['bg-blue-600', 'bg-green-600', 'bg-orange-600', 'bg-purple-600', 'bg-red-600', 'bg-indigo-600'];
                                                                $color = $colors[$index % count($colors)];
                                                                $initials = collect(explode(' ', $method->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->implode('');
                                                            @endphp
                                                            <div class="w-12 h-12 {{ $color }} rounded-xl flex items-center justify-center mr-4">
                                                                <span class="text-white font-bold text-sm">{{ $initials }}</span>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <p class="font-semibold text-gray-900 dark:text-white">{{ $method->name }}</p>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                {{ \App\Models\PaymentMethod::TYPES[$method->type] ?? $method->type }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="radio-indicator"></div>
                                                </div>
                                                <div class="payment-details mt-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-700 hidden">
                                                    <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-3">{{ $method->name }} Payment Details</h4>
                                                    <div class="space-y-2 text-sm">
                                                        @foreach($method->display_details as $label => $value)
                                                            @if($value)
                                                                <div class="flex justify-between">
                                                                    <span class="text-blue-700 dark:text-blue-300">{{ $label }}:</span>
                                                                    <span class="font-mono font-semibold text-blue-900 dark:text-blue-100">{{ $value }}</span>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                        
                                                        @if($method->instructions)
                                                            <div class="mt-3 pt-3 border-t border-blue-200 dark:border-blue-600">
                                                                <p class="text-blue-700 dark:text-blue-300 text-xs leading-relaxed">{{ $method->instructions }}</p>
                                                            </div>
                                                        @endif
                                                        
                                                        @if(empty($method->display_details))
                                                            <div class="text-center py-2">
                                                                <p class="text-blue-600 dark:text-blue-400 text-sm">Payment details will be provided upon selection</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    @empty
                                        <div class="text-center py-8">
                                            <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                </svg>
                                            </div>
                                            <p class="text-gray-500 dark:text-gray-400">No payment methods available</p>
                                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Please contact support for assistance</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            
                            @error('payment_method')
                                <p class="mt-4 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Step 2: Upload Payment Proof -->
                    <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gray-50 dark:bg-neutral-700 px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-blue-600 dark:text-blue-400 text-sm font-bold">2</span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upload Payment Proof</h3>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <!-- Upload Area -->
                            <div class="upload-area" id="uploadArea">
                                <input type="file" id="payment_proof" name="payment_proof" accept="image/*" class="sr-only" required>
                                <label for="payment_proof" class="block cursor-pointer">
                                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl p-8 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors duration-200">
                                        <div class="upload-icon mb-4">
                                            <svg class="w-16 h-16 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                            Drop your payment receipt here
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                            or click to browse files
                                        </p>
                                        <div class="inline-flex items-center px-4 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg text-sm font-medium">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Choose File
                                        </div>
                                        <p class="text-xs text-gray-400 mt-3">
                                            Supported: JPG, PNG, GIF (max 2MB)
                                        </p>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- File Preview Area -->
                            <div id="filePreview" class="hidden mt-4"></div>
                            
                            @error('payment_proof')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            
                            <!-- Notes -->
                            <div class="mt-6">
                                <label for="payment_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Additional Notes (Optional)
                                </label>
                                <textarea name="payment_notes" id="payment_notes" rows="3" 
                                        class="block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-neutral-900 px-4 py-3 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors"
                                        placeholder="Any additional information about your payment...">{{ old('payment_notes') }}</textarea>
                                @error('payment_notes')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Kirim Button -->
                    <button type="submit" id="submitBtn" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-4 px-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2">
                        <svg id="submitIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <span id="submitText">Kirim Bukti Pembayaran</span>
                    </button>
                </form>
            </div>

            <!-- Help & Informasi -->
            <div class="space-y-6">
                
                <!-- Payment Instructions -->
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-amber-900 dark:text-amber-100 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        How to Pay
                    </h3>
                    <ol class="space-y-3 text-sm text-amber-800 dark:text-amber-200">
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-200 rounded-full text-xs font-semibold mr-3 mt-0.5">1</span>
                            <span>Pilih preferesi metode pembayaran anda</span>
                        </li>
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-200 rounded-full text-xs font-semibold mr-3 mt-0.5">2</span>
                            <span>Transfer sesuai dengan <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong> to the account</span>
                        </li>
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-200 rounded-full text-xs font-semibold mr-3 mt-0.5">3</span>
                            <span>Take a screenshot of your payment receipt</span>
                        </li>
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-200 rounded-full text-xs font-semibold mr-3 mt-0.5">4</span>
                            <span>Upload the receipt using the form</span>
                        </li>
                    </ol>
                </div>

                <!-- FAQ -->
                <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Frequently Asked Questions</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-1">When will my payment be verified?</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Payment verification typically takes 1-2 business days. You'll receive a notification once verified.</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-1">What if I made a wrong transfer amount?</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Contact our support team immediately with your transfer details. We'll help resolve the issue.</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-1">Can I change the payment method?</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Yes, you can select a different payment method before uploading your proof.</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Need help with payment?</p>
                    <a href="{{ route('client.messages.create', ['order_id' => $order->id]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a9.863 9.863 0 01-4.906-1.285L3 21l2.085-5.104A9.863 9.863 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                        </svg>
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Payment Method Cards */
        .payment-method-card input:checked + .payment-option > div:first-child {
            @apply border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900/20;
        }
        
        .payment-method-card input:checked + .payment-option .payment-details {
            display: block !important;
        }
        
        .payment-details {
            display: none !important;
        }
        
        .payment-details:not(.hidden) {
            display: block !important;
        }
        
        /* Radio Indicator */
        .radio-indicator {
            @apply w-5 h-5 border-2 border-gray-300 dark:border-gray-600 rounded-full transition-colors duration-200;
        }
        
        .payment-method-card input:checked + .payment-option .radio-indicator {
            @apply border-blue-500 dark:border-blue-400 bg-blue-500 dark:bg-blue-400;
            position: relative;
        }
        
        .payment-method-card input:checked + .payment-option .radio-indicator::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
        }
        
        /* Upload Area */
        .upload-area.dragover {
            @apply border-blue-400 dark:border-blue-500 bg-blue-50 dark:bg-blue-900/20;
        }
        
        .file-preview {
            @apply mt-4 p-4 bg-gray-50 dark:bg-neutral-700 rounded-xl border border-gray-200 dark:border-gray-600;
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('payment_proof');
        const uploadArea = document.getElementById('uploadArea');
        const filePreview = document.getElementById('filePreview');
        const form = document.getElementById('paymentForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitIcon = document.getElementById('submitIcon');
        
        // Payment method selection functionality
        const paymentCards = document.querySelectorAll('.payment-method-card');
        
        paymentCards.forEach(card => {
            const input = card.querySelector('input[type="radio"]');
            const paymentOption = card.querySelector('.payment-option');
            const paymentDetails = card.querySelector('.payment-details');
            
            // Handle click on the payment option
            paymentOption.addEventListener('click', function() {
                // Uncheck all other radio buttons and hide their details
                paymentCards.forEach(otherCard => {
                    const otherInput = otherCard.querySelector('input[type="radio"]');
                    const otherDetails = otherCard.querySelector('.payment-details');
                    otherInput.checked = false;
                    if (otherDetails) {
                        otherDetails.classList.add('hidden');
                    }
                });
                
                // Check this radio button and show its details
                input.checked = true;
                if (paymentDetails) {
                    paymentDetails.classList.remove('hidden');
                }
            });
        });
        
        // Drag and drop functionality
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight(e) {
            uploadArea.classList.add('dragover');
        }
        
        function unhighlight(e) {
            uploadArea.classList.remove('dragover');
        }
        
        uploadArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(files[0]);
            }
        }
        
        // File input change
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });
        
        function handleFileSelect(file) {
            // Validate file
            if (!file.type.startsWith('image/')) {
                showKesalahan('Please select an image file (JPG, PNG, GIF)');
                return;
            }
            
            if (file.size > 2 * 1024 * 1024) {
                showKesalahan('File size must be less than 2MB');
                return;
            }
            
            // Show file preview
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            
            filePreview.className = 'file-preview';
            filePreview.innerHTML = `
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white">${fileName}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Size: ${fileSize} MB</p>
                        <div id="imagePreview" class="mt-3"></div>
                    </div>
                    <button type="button" onclick="clearFile()" class="flex-shrink-0 text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            // Show image preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').innerHTML = `
                    <img src="${e.target.result}" alt="Payment proof preview" 
                         class="w-full max-w-xs h-24 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                `;
            };
            reader.readAsDataURL(file);
        }
        
        function clearFile() {
            fileInput.value = '';
            filePreview.innerHTML = '';
            filePreview.className = 'hidden';
        }
        
        function showKesalahan(message) {
            // You can implement a toast notification here
            alert(message);
        }
        
        // Form submission
        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            submitText.textContent = 'Uploading Payment Proof...';
            submitIcon.innerHTML = `
                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
        });
        
        // Make clearFile globally accessible
        window.clearFile = clearFile;
    });
    </script>
</x-layouts.client>