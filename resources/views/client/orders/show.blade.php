{{-- resources/views/client/orders/show.blade.php --}}
<x-layouts.client>
    <x-slot name="title">Order #{{ $order->order_number }}</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('client.orders.index') }}" 
                   class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Order #{{ $order->order_number }}</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}
                    </p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex space-x-3">
                
                <a href="{{ route('client.messages.create', ['order_id' => $order->id]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a9.863 9.863 0 01-4.906-1.285L3 21l2.085-5.104A9.863 9.863 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                    </svg>
                    Contact Support
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                @if($order->needs_negotiation)
                    <!-- Negotiation Status -->
                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-orange-200 dark:border-orange-700">
                            <h3 class="text-lg font-medium text-orange-900 dark:text-orange-100">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                Price Negotiation Active
                            </h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $order->negotiation_status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                    {{ $order->negotiation_status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                    {{ $order->negotiation_status === 'accepted' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                    {{ $order->negotiation_status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                    {{ $order->negotiation_status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                    {{ $order->negotiation_status === 'completed' ? 'Completed' : ucfirst($order->negotiation_status) }}
                                </span>
                            </div>

                            <div class="space-y-3 text-sm">
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">Requested Total:</span>
                                    <span class="text-orange-600 dark:text-orange-400 font-medium">
                                        Rp {{ number_format($order->requested_total, 0, ',', '.') }}
                                    </span>
                                    <span class="text-gray-500 dark:text-gray-400">
                                        ({{ $order->requested_total < $order->total_amount ? 'Savings' : 'Increase' }}: 
                                        Rp {{ number_format(abs($order->total_amount - $order->requested_total), 0, ',', '.') }})
                                    </span>
                                </div>
                                
                                @if($order->negotiation_message)
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white block mb-1">Your Message:</span>
                                        <p class="text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-700 p-3 rounded-lg">
                                            {{ $order->negotiation_message }}
                                        </p>
                                    </div>
                                @endif
                                
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">Requested on:</span>
                                    <span class="text-gray-700 dark:text-gray-300">
                                        {{ $order->negotiation_requested_at->format('F j, Y \a\t g:i A') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Delivery Status & Admin Responses -->
                @if($order->status === 'delivered' || $order->delivery_disputed || $order->delivery_confirmed_by_client)
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4-8-4m16 0v10l-8 4-8-4V7"></path>
                                </svg>
                                Delivery Status
                            </h3>
                        </div>
                        
                        <div class="p-6">
                            @php
                                $displayStatus = $order->getDisplayStatus();
                            @endphp

                            @if($displayStatus === 'awaiting_confirmation')
                                <!-- Awaiting Confirmation -->
                                <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-lg p-4 mb-6">
                                    <div class="flex items-start">
                                        <svg class="w-6 h-6 text-orange-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4-8-4m16 0v10l-8 4-8-4V7"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-orange-800 dark:text-orange-200 mb-1">
                                                🚚 Your Order Has Been Delivered
                                            </h4>
                                            <p class="text-sm text-orange-700 dark:text-orange-300 mb-3">
                                                Our team has marked this order as delivered. Please confirm that you have received your order.
                                            </p>
                                            
                                            <!-- Delivery Confirmation Actions -->
                                            <div class="flex flex-col sm:flex-row gap-2">
                                                <button type="button" 
                                                        onclick="showConfirmDeliveryModal({{ $order->id }})"
                                                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Yes, I Received It
                                                </button>
                                                <button type="button" 
                                                        onclick="showDisputeModal({{ $order->id }})"
                                                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                    Report Issue
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @elseif($displayStatus === 'completed')
                                <!-- Completed -->
                                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-4">
                                    <div class="flex items-start">
                                        <svg class="w-6 h-6 text-green-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-green-800 dark:text-green-200 mb-1">
                                                ✅ Order Completed Successfully
                                            </h4>
                                            <p class="text-sm text-green-700 dark:text-green-300">
                                                You confirmed receipt of this order on {{ $order->delivery_confirmed_at->format('F j, Y \a\t g:i A') }}
                                            </p>
                                            @if($order->client_delivery_notes)
                                                <div class="mt-3 p-3 bg-green-100 dark:bg-green-900/30 rounded border border-green-200 dark:border-green-800">
                                                    <p class="text-sm text-green-700 dark:text-green-300">
                                                        <span class="font-medium">Your Notes:</span> {{ $order->client_delivery_notes }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            @elseif($displayStatus === 'disputed')
                                <!-- Disputed -->
                                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-4">
                                    <div class="flex items-start">
                                        <svg class="w-6 h-6 text-red-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-red-800 dark:text-red-200 mb-1">
                                                ⚠️ Delivery Issue Reported
                                            </h4>
                                            <p class="text-sm text-red-700 dark:text-red-300">
                                                You reported a delivery issue on {{ $order->dispute_reported_at->format('F j, Y \a\t g:i A') }}
                                            </p>
                                            
                                            <!-- Your Dispute Reason -->
                                            <div class="mt-3 p-3 bg-red-100 dark:bg-red-900/30 rounded border border-red-200 dark:border-red-800">
                                                <p class="text-sm text-red-700 dark:text-red-300">
                                                    <span class="font-medium">Issue Reported:</span> {{ $order->dispute_reason }}
                                                </p>
                                            </div>
                                            
                                            <!-- Admin Response Section -->
                                            @if($order->hasAdminResponse())
                                                <div class="mt-4">
                                                    <h5 class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2">Support Team Response:</h5>
                                                    
                                                    <!-- Admin's Response -->
                                                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded p-3 mb-3">
                                                        <div class="flex items-start">
                                                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                                            </svg>
                                                            <div class="flex-1">
                                                                <div class="flex items-center justify-between mb-1">
                                                                    <span class="text-xs font-medium text-blue-700 dark:text-blue-300">
                                                                        @if($order->dispute_status === 'acknowledged')
                                                                            💬 Issue Acknowledged
                                                                        @elseif($order->dispute_status === 'resolved')
                                                                            ✅ Resolution Proposed
                                                                        @endif
                                                                    </span>
                                                                    <span class="text-xs text-blue-600 dark:text-blue-400">
                                                                        {{ $order->admin_responded_at->format('M j, H:i') }}
                                                                    </span>
                                                                </div>
                                                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                                                    {{ $order->admin_dispute_response }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Client Response Section -->
                                                    @if($order->dispute_status === 'acknowledged' && !$order->client_dispute_feedback)
                                                        <!-- Client can respond to acknowledgment -->
                                                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded p-3">
                                                            <div class="flex items-start">
                                                                <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.013 8.013 0 01-4.19-1.16l-3.81 1.16 1.16-3.81A8.013 8.013 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                                                                </svg>
                                                                <div class="flex-1">
                                                                    <p class="text-sm text-green-700 dark:text-green-300 mb-2">
                                                                        <strong>Your Response Needed:</strong> Please provide additional information or feedback about this issue.
                                                                    </p>
                                                                    <button type="button" 
                                                                            onclick="showRespondToDisputeModal({{ $order->id }})"
                                                                            class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md transition-colors">
                                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.013 8.013 0 01-4.19-1.16l-3.81 1.16 1.16-3.81A8.013 8.013 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                                                                        </svg>
                                                                        Provide More Details
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    
                                                    @elseif($order->dispute_status === 'resolved')
                                                        <!-- Client can accept resolution -->
                                                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded p-3">
                                                            <div class="flex items-start">
                                                                <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                <div class="flex-1">
                                                                    <p class="text-sm text-green-700 dark:text-green-300 mb-2">
                                                                        <strong>Resolution Proposed:</strong> Please review and accept the resolution if it addresses your concern.
                                                                    </p>
                                                                    <button type="button" 
                                                                            onclick="showAcceptResolutionModal({{ $order->id }})"
                                                                            class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md transition-colors">
                                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                        </svg>
                                                                        Accept Resolution
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    
                                                    @elseif($order->client_dispute_feedback)
                                                        <!-- Show client's response -->
                                                        <div class="bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-700 rounded p-3">
                                                            <div class="flex items-start">
                                                                <svg class="w-5 h-5 text-gray-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                                </svg>
                                                                <div class="flex-1">
                                                                    <div class="flex items-center justify-between mb-1">
                                                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                                            Your Response
                                                                        </span>
                                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                                            {{ $order->client_responded_at->format('M j, H:i') }}
                                                                        </span>
                                                                    </div>
                                                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                                                        {{ $order->client_dispute_feedback }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded">
                                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Our support team is reviewing your issue and will respond soon.
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Payment Status -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-blue-200 dark:border-blue-700">
                        <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Payment Status
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $order->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                {{ $order->payment_status === 'proof_uploaded' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                {{ $order->payment_status === 'verified' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                {{ $order->payment_status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                            </span>
                        </div>

                        <div class="space-y-3 text-sm">
                            @if($order->payment_method)
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">Payment Method:</span>
                                    <span class="text-blue-600 dark:text-blue-400 font-medium">{{ $order->payment_method }}</span>
                                </div>
                            @endif
                            
                            @if($order->payment_uploaded_at)
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">Payment Proof Uploaded:</span>
                                    <span class="text-gray-700 dark:text-gray-300">
                                        {{ $order->payment_uploaded_at->format('F j, Y \a\t g:i A') }}
                                    </span>
                                </div>
                            @endif
                            
                            @if($order->payment_verified_at)
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">Payment Verified:</span>
                                    <span class="text-green-700 dark:text-green-300">
                                        {{ $order->payment_verified_at->format('F j, Y \a\t g:i A') }}
                                    </span>
                                </div>
                            @endif
                            
                            @if($order->payment_notes)
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white block mb-1">Payment Notes:</span>
                                    <p class="text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-700 p-3 rounded-lg">
                                        {{ $order->payment_notes }}
                                    </p>
                                </div>
                            @endif
                            
                            @if($order->hasPaymentProof())
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white block mb-2">Payment Proof:</span>
                                    <img src="{{ asset('storage/' . $order->payment_proof) }}" 
                                         alt="Payment Proof" 
                                         class="max-w-full h-48 object-contain rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer"
                                         onclick="window.open('{{ asset('storage/' . $order->payment_proof) }}', '_blank')">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Click to view full size</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Order Status -->
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Status</h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                {{ $order->status === 'shipped' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' : '' }}
                                {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            
                        </div>

                        <!-- Status Timeline -->
                        <div class="flex items-center space-x-4 text-sm">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                <span class="text-gray-600 dark:text-gray-400">Ordered</span>
                            </div>
                            
                            <div class="flex-1 h-px bg-gray-200 dark:bg-gray-600
                                {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'bg-green-500' : '' }}"></div>
                            
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full 
                                    {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                                <span class="text-gray-600 dark:text-gray-400">Processing</span>
                            </div>
                            
                            <div class="flex-1 h-px bg-gray-200 dark:bg-gray-600
                                {{ in_array($order->status, ['shipped', 'delivered']) ? 'bg-green-500' : '' }}"></div>
                            
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full 
                                    {{ in_array($order->status, ['shipped', 'delivered']) ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                                <span class="text-gray-600 dark:text-gray-400">Shipped</span>
                            </div>
                            
                            <div class="flex-1 h-px bg-gray-200 dark:bg-gray-600
                                {{ $order->status === 'delivered' ? 'bg-green-500' : '' }}"></div>
                            
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full 
                                    {{ $order->status === 'delivered' ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                                <span class="text-gray-600 dark:text-gray-400">Delivered</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Order Items ({{ $order->items->count() }})
                        </h3>
                    </div>
                    
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($order->items as $item)
                            <div class="p-6">
                                <div class="flex items-start space-x-4">
                                    
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        @php
                                            $featuredImage = $item->product->images->where('is_featured', true)->first();
                                            $firstImage = $featuredImage ?: $item->product->images->first();
                                        @endphp
                                        
                                        <div class="relative group cursor-pointer" onclick="window.location.href='{{ route('client.products.show', $item->product) }}'">
                                            @if($firstImage)
                                                <img src="{{ asset('storage/' . $firstImage->image_path) }}" 
                                                     alt="{{ $item->product->name }}"
                                                     class="w-20 h-20 object-cover rounded-lg border border-gray-200 dark:border-gray-600 transition-transform duration-200 group-hover:scale-105">
                                            @else
                                                <div class="w-20 h-20 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            
                                            @if($firstImage)
                                                <!-- Hover Overlay -->
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100 rounded-lg">
                                                    <div class="bg-white dark:bg-gray-800 p-2 rounded-full shadow-lg transform group-hover:scale-110 transition-all duration-200">
                                                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ $item->product->name }}
                                                </h4>
                                                
                                                @if($item->product->sku)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                        SKU: {{ $item->product->sku }}
                                                    </p>
                                                @endif

                                                @if($item->specifications)
                                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                                                        <span class="font-medium">Specifications:</span> {{ $item->specifications }}
                                                    </p>
                                                @endif

                                                <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                    <span>Quantity: {{ $item->quantity }}</span>
                                                    @if($item->unit_price > 0)
                                                        <span>Unit Price: Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Item Total -->
                                            <div class="text-right">
                                                <div class="text-lg font-medium text-gray-900 dark:text-white">
                                                    Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Delivery Information -->
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Delivery Information</h3>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Delivery Address
                            </label>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $order->delivery_address }}
                            </p>
                        </div>
                        
                        @if($order->needed_date)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Required Date
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    {{ $order->needed_date ? $order->needed_date->format('F j, Y') : 'Not specified' }}
                                </p>
                            </div>
                        @endif
                        
                        @if($order->notes)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Additional Notes
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    {{ $order->notes }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- Order Summary -->
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Summary</h3>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Order Number</span>
                                <span class="font-medium text-gray-900 dark:text-white">#{{ $order->order_number }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Order Date</span>
                                <span class="text-gray-900 dark:text-white">{{ $order->created_at->format('M j, Y') }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Items</span>
                                <span class="text-gray-900 dark:text-white">{{ $order->items->count() }} product(s)</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Total Quantity</span>
                                <span class="text-gray-900 dark:text-white">{{ $order->items->sum('quantity') }}</span>
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-600">

                        <!-- Pricing -->
                        <div class="space-y-2 text-sm">
                            @if($order->total_amount > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                    <span class="text-gray-900 dark:text-white">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Delivery</span>
                                    <span class="text-gray-900 dark:text-white">
                                        {{ $order->delivery_fee > 0 ? 'Rp ' . number_format($order->delivery_fee, 0, ',', '.') : 'TBD' }}
                                    </span>
                                </div>
                            @endif

                        </div>

                        <hr class="border-gray-200 dark:border-gray-600">

                        <!-- Total -->
                        <div class="flex justify-between">
                            <span class="text-base font-medium text-gray-900 dark:text-white">Total</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($order->total_amount + ($order->delivery_fee ?? 0), 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3 pt-4">
                            
                            <!-- Payment Actions -->
                            @if($order->canMakePayment())
                                <a href="{{ route('client.orders.payment', $order) }}" 
                                   class="block w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    Make Payment
                                </a>
                            @elseif($order->payment_status === 'proof_uploaded')
                                <div class="block w-full bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 font-medium py-2 px-4 rounded-lg border border-blue-200 dark:border-blue-700 text-center">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Payment Under Review
                                </div>
                            @elseif($order->payment_status === 'verified')
                                <div class="block w-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200 font-medium py-2 px-4 rounded-lg border border-green-200 dark:border-green-700 text-center">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Payment Verified
                                </div>
                            @elseif($order->payment_status === 'rejected')
                                <div class="block w-full bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200 font-medium py-2 px-4 rounded-lg border border-red-200 dark:border-red-700 text-center mb-3">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6m0 12L6 6"></path>
                                    </svg>
                                    Payment Rejected
                                </div>
                                <a href="{{ route('client.orders.payment', $order) }}" 
                                   class="block w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    Upload New Payment Proof
                                </a>
                            @endif
                            
                            @if($order->negotiation_status === 'completed')
                                <!-- Negotiation Completed -->
                                <div class="block w-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200 font-medium py-2 px-4 rounded-lg border border-green-200 dark:border-green-700 text-center">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Negotiation Completed
                                </div>
                            @elseif($order->canRequestNegotiation())
                                <a href="{{ route('client.orders.negotiate', $order) }}" 
                                   class="block w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    Request Price Negotiation
                                </a>
                            @elseif($order->canCounterNegotiate() || $order->canAcceptNegotiation())
                                <!-- Accept Admin's Offer -->
                                <form action="{{ route('client.orders.negotiate.accept', $order) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to accept the admin\'s current price offer? This will complete the negotiation.')" 
                                      class="w-full">
                                    @csrf
                                    <button type="submit" 
                                            class="block w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Accept Admin's Offer
                                    </button>
                                </form>
                                
                                <!-- Counter Negotiate -->
                                <a href="{{ route('client.orders.negotiate', $order) }}" 
                                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                    Make Counter Offer
                                </a>
                            @endif
                            
                            @if(in_array($order->status, ['pending', 'confirmed']))
                                <form action="{{ route('client.orders.cancel', $order) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.')" 
                                      class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="block w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Cancel Order
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('client.messages.create', ['order_id' => $order->id]) }}" 
                               class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center">
                                Contact Support
                            </a>
                            
                            <a href="{{ route('client.orders.index') }}" 
                               class="block w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center">
                                Back to Orders
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Actions</h3>
                    </div>
                    
                    <div class="p-6 space-y-3">
                        <a href="{{ route('client.products.index') }}" 
                           class="flex items-center px-4 py-2 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Browse Products
                        </a>
                        
                        
                        <a href="{{ route('client.orders.index') }}" 
                           class="flex items-center px-4 py-2 text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900/20 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            View All Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Delivery Modal -->
    <div id="confirmDeliveryModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" onclick="closeConfirmModal()"></div>
            
            <div class="inline-block align-bottom bg-white dark:bg-neutral-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="confirmDeliveryForm" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-neutral-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    Confirm Delivery
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Please confirm that you have received your order in good condition.
                                    </p>
                                    <div class="mt-4">
                                        <label for="delivery_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Delivery Notes (Optional)
                                        </label>
                                        <textarea name="notes" id="delivery_notes" rows="3" 
                                                  placeholder="Any feedback about the delivery or product condition..."
                                                  class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-neutral-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Yes, I Received It
                        </button>
                        <button type="button" onclick="closeConfirmModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Respond to Dispute Modal -->
    <div id="respondToDisputeModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" onclick="closeRespondToDisputeModal()"></div>
            
            <div class="inline-block align-bottom bg-white dark:bg-neutral-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="respondToDisputeForm" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-neutral-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.013 8.013 0 01-4.19-1.16l-3.81 1.16 1.16-3.81A8.013 8.013 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    Provide Additional Details
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Our support team has acknowledged your issue. Please provide any additional information that might help resolve this matter.
                                    </p>
                                    <div class="mt-4">
                                        <label for="client_response" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Additional Information <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="client_response" id="client_response" rows="4" required
                                                  placeholder="Please provide more details, answer any questions from our team, or clarify your concerns..."
                                                  class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-neutral-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Send Response
                        </button>
                        <button type="button" onclick="closeRespondToDisputeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Accept Resolution Modal -->
    <div id="acceptResolutionModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" onclick="closeAcceptResolutionModal()"></div>
            
            <div class="inline-block align-bottom bg-white dark:bg-neutral-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="acceptResolutionForm" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-neutral-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    Accept Resolution
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Are you satisfied with the proposed resolution? Accepting will complete your order and close the dispute.
                                    </p>
                                    <div class="mt-4">
                                        <label for="client_feedback" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Final Comments (Optional)
                                        </label>
                                        <textarea name="client_feedback" id="client_feedback" rows="3"
                                                  placeholder="Share your thoughts on the resolution or any final feedback..."
                                                  class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-neutral-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Accept Resolution
                        </button>
                        <button type="button" onclick="closeAcceptResolutionModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Dispute Modal -->
    <div id="disputeModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" onclick="closeDisputeModal()"></div>
            
            <div class="inline-block align-bottom bg-white dark:bg-neutral-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="disputeForm" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-neutral-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    Report Delivery Issue
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Please describe the issue with your delivery. Our support team will review and respond promptly.
                                    </p>
                                    <div class="mt-4">
                                        <label for="dispute_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Describe the Issue <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="reason" id="dispute_reason" rows="4" required
                                                  placeholder="Please provide details about the delivery issue (e.g., damaged items, missing items, wrong delivery, quality concerns, etc.)"
                                                  class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-neutral-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Report Issue
                        </button>
                        <button type="button" onclick="closeDisputeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Modal functions for delivery confirmation
        function showConfirmDeliveryModal(orderId) {
            const modal = document.getElementById('confirmDeliveryModal');
            const form = document.getElementById('confirmDeliveryForm');
            form.action = `/client/orders/${orderId}/confirm-delivery`;
            modal.classList.remove('hidden');
            
            // Focus on textarea after modal animation
            setTimeout(() => {
                document.getElementById('delivery_notes').focus();
            }, 150);
        }

        function closeConfirmModal() {
            const modal = document.getElementById('confirmDeliveryModal');
            modal.classList.add('hidden');
            document.getElementById('delivery_notes').value = '';
        }

        function showDisputeModal(orderId) {
            const modal = document.getElementById('disputeModal');
            const form = document.getElementById('disputeForm');
            form.action = `/client/orders/${orderId}/dispute-delivery`;
            modal.classList.remove('hidden');
            
            // Focus on textarea after modal animation
            setTimeout(() => {
                document.getElementById('dispute_reason').focus();
            }, 150);
        }

        function closeDisputeModal() {
            const modal = document.getElementById('disputeModal');
            modal.classList.add('hidden');
            document.getElementById('dispute_reason').value = '';
        }

        // New modal functions for dispute interactions
        function showRespondToDisputeModal(orderId) {
            const modal = document.getElementById('respondToDisputeModal');
            const form = document.getElementById('respondToDisputeForm');
            form.action = `/client/orders/${orderId}/respond-to-dispute`;
            modal.classList.remove('hidden');
            
            setTimeout(() => {
                document.getElementById('client_response').focus();
            }, 150);
        }

        function closeRespondToDisputeModal() {
            const modal = document.getElementById('respondToDisputeModal');
            modal.classList.add('hidden');
            document.getElementById('client_response').value = '';
        }

        function showAcceptResolutionModal(orderId) {
            const modal = document.getElementById('acceptResolutionModal');
            const form = document.getElementById('acceptResolutionForm');
            form.action = `/client/orders/${orderId}/accept-resolution`;
            modal.classList.remove('hidden');
            
            setTimeout(() => {
                document.getElementById('client_feedback').focus();
            }, 150);
        }

        function closeAcceptResolutionModal() {
            const modal = document.getElementById('acceptResolutionModal');
            modal.classList.add('hidden');
            document.getElementById('client_feedback').value = '';
        }

        // Handle form submissions with loading states
        document.addEventListener('DOMContentLoaded', function() {
            // Confirm delivery form
            document.getElementById('confirmDeliveryForm').addEventListener('submit', function(e) {
                const submitButton = this.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Confirming...
                `;
            });

            // Dispute form  
            document.getElementById('disputeForm').addEventListener('submit', function(e) {
                const submitButton = this.querySelector('button[type="submit"]');
                const textarea = this.querySelector('textarea[name="reason"]');
                
                if (!textarea.value.trim()) {
                    e.preventDefault();
                    textarea.focus();
                    alert('Please describe the issue before submitting.');
                    return;
                }
                
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Reporting...
                `;
            });

            // Respond to dispute form
            if (document.getElementById('respondToDisputeForm')) {
                document.getElementById('respondToDisputeForm').addEventListener('submit', function(e) {
                    const submitButton = this.querySelector('button[type="submit"]');
                    const textarea = this.querySelector('textarea[name="client_response"]');
                    
                    if (!textarea.value.trim()) {
                        e.preventDefault();
                        textarea.focus();
                        alert('Please provide additional information before submitting.');
                        return;
                    }
                    
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending...
                    `;
                });
            }

            // Accept resolution form
            if (document.getElementById('acceptResolutionForm')) {
                document.getElementById('acceptResolutionForm').addEventListener('submit', function(e) {
                    const submitButton = this.querySelector('button[type="submit"]');
                    
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Accepting...
                    `;
                });
            }

            // Close modals on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeConfirmModal();
                    closeDisputeModal();
                    closeRespondToDisputeModal();
                    closeAcceptResolutionModal();
                }
            });
        });
    </script>
    @endpush
</x-layouts.client>