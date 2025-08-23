{{-- resources/views/admin/orders/payment.blade.php --}}
<x-layouts.admin>
    <x-slot name="title">Payment Review - Order #{{ $order->order_number }}</x-slot>

    <div class="space-y-6">
        
        <!-- Header with Breadcrumb -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.orders.show', $order) }}" 
                   class="flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Order #{{ $order->order_number }}
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-gray-900 dark:text-white font-medium">Payment Review</span>
            </div>
        </div>

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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Payment Review -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Payment Status Card -->
                <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-6 py-4 border-b border-blue-200/50 dark:border-blue-700/50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Payment Information</h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $order->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                {{ $order->payment_status === 'proof_uploaded' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                {{ $order->payment_status === 'verified' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                {{ $order->payment_status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Method</label>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    {{ $order->payment_method ?? 'Not specified' }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </p>
                            </div>
                            
                            @if($order->payment_uploaded_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Uploaded At</label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $order->payment_uploaded_at->format('F j, Y \a\t g:i A') }}
                                    </p>
                                </div>
                            @endif
                            
                            @if($order->payment_verified_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Verified At</label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $order->payment_verified_at->format('F j, Y \a\t g:i A') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        
                        @if($order->payment_notes)
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Client Notes</label>
                                <div class="bg-gray-50 dark:bg-neutral-700 rounded-lg p-3">
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->payment_notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Proof -->
                @if($order->payment_proof)
                    <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Proof</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Review the uploaded payment receipt</p>
                        </div>
                        
                        <div class="p-6">
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $order->payment_proof) }}" 
                                     alt="Payment Proof" 
                                     class="max-w-full h-auto rounded-xl border border-gray-200 dark:border-gray-600 shadow-lg cursor-pointer mx-auto"
                                     onclick="openImageModal('{{ asset('storage/' . $order->payment_proof) }}')"
                                     style="max-height: 500px;">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">Click image to view full size</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 dark:bg-neutral-700 rounded-2xl p-8 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Payment Proof</h3>
                        <p class="text-gray-500 dark:text-gray-400">Client hasn't uploaded payment proof yet.</p>
                    </div>
                @endif

                <!-- Payment Verification Actions -->
                @if($order->payment_status === 'proof_uploaded')
                    <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Verification</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Verify or reject the payment proof</p>
                        </div>
                        
                        <div class="p-6">
                            <form action="{{ route('admin.orders.payment.verify', $order) }}" method="POST" class="space-y-4">
                                @csrf
                                
                                <div>
                                    <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Admin Notes (Optional)
                                    </label>
                                    <textarea name="admin_notes" id="admin_notes" rows="3" 
                                            class="block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-neutral-900 px-4 py-3 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors"
                                            placeholder="Add notes about the payment verification...">{{ old('admin_notes') }}</textarea>
                                    @error('admin_notes')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="flex space-x-4">
                                    <button type="submit" name="action" value="verify"
                                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center space-x-2"
                                            onclick="return confirm('Are you sure you want to verify this payment?')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Verify Payment</span>
                                    </button>
                                    
                                    <button type="submit" name="action" value="reject"
                                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center space-x-2"
                                            onclick="return confirm('Are you sure you want to reject this payment? The client will need to upload new payment proof.')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>Reject Payment</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- Order Info -->
                <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order Information</h3>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Order Number</span>
                                <span class="font-medium text-gray-900 dark:text-white">#{{ $order->order_number }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Client</span>
                                <span class="text-gray-900 dark:text-white">{{ $order->client->name ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Email</span>
                                <span class="text-gray-900 dark:text-white">{{ $order->client->email ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Order Date</span>
                                <span class="text-gray-900 dark:text-white">{{ $order->created_at->format('M j, Y') }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Items</span>
                                <span class="text-gray-900 dark:text-white">{{ $order->items->count() }} product(s)</span>
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-600">

                        <div class="flex justify-between">
                            <span class="text-base font-medium text-gray-900 dark:text-white">Total Amount</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                    </div>
                    
                    <div class="p-6 space-y-3">
                        <a href="{{ route('admin.orders.show', $order) }}" 
                           class="flex items-center w-full px-4 py-2 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Full Order
                        </a>
                        
                        @if($order->needs_negotiation)
                            <a href="{{ route('admin.orders.negotiation', $order) }}" 
                               class="flex items-center w-full px-4 py-2 text-sm text-orange-600 hover:text-orange-800 dark:text-orange-400 dark:hover:text-orange-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                Manage Negotiation
                            </a>
                        @endif
                        
                        <a href="{{ route('admin.orders.index') }}" 
                           class="flex items-center w-full px-4 py-2 text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900/20 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Back to Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-75" onclick="closeImageModal()">
        <div class="relative max-w-4xl max-h-full p-4">
            <img id="modalImage" src="" alt="Payment Proof" class="max-w-full max-h-full rounded-lg shadow-2xl">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
            document.getElementById('imageModal').classList.add('flex');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.getElementById('imageModal').classList.remove('flex');
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</x-layouts.admin>