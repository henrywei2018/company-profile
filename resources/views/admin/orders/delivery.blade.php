{{-- resources/views/admin/orders/delivery.blade.php --}}
<x-layouts.admin>
    <x-slot name="title">Delivery Management - Order #{{ $order->order_number }}</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Delivery Management
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Manage delivery confirmation and resolve disputes for Order #{{ $order->order_number }}
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.orders.show', $order) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Order
                </a>
            </div>
        </div>

        <!-- Order Summary Card -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Order Info -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Order Number:</span>
                            <span class="text-sm text-gray-900 dark:text-white">#{{ $order->order_number }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Client:</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $order->client->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Amount:</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $order->status === 'delivered' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Delivery Address -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Delivery Information</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Delivery Address:</span>
                            <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $order->delivery_address }}</p>
                        </div>
                        @if($order->needed_date)
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Needed Date:</span>
                                <span class="text-sm text-gray-900 dark:text-white">{{ $order->needed_date->format('M j, Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Status Card -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delivery Status</h3>
            </div>
            <div class="p-6">
                @php
                    $displayStatus = $order->getDisplayStatus();
                @endphp

                @if($displayStatus === 'awaiting_confirmation')
                    <!-- Awaiting Confirmation -->
                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-orange-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-medium text-orange-800 dark:text-orange-200">
                                    🚚 Awaiting Client Confirmation
                                </h4>
                                <p class="mt-1 text-sm text-orange-700 dark:text-orange-300">
                                    The order has been marked as delivered and is waiting for client confirmation.
                                </p>
                                <div class="mt-4 flex flex-col sm:flex-row gap-2">
                                    <button type="button" onclick="showForceConfirmModal()"
                                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Force Confirm Delivery
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($displayStatus === 'completed')
                    <!-- Completed -->
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-medium text-green-800 dark:text-green-200">
                                    ✅ Delivery Confirmed
                                </h4>
                                <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                                    Client confirmed delivery on {{ $order->delivery_confirmed_at->format('M j, Y \a\t g:i A') }}
                                </p>
                                @if($order->client_delivery_notes)
                                    <div class="mt-3 p-3 bg-green-100 dark:bg-green-900/30 rounded border border-green-200 dark:border-green-800">
                                        <p class="text-sm text-green-700 dark:text-green-300">
                                            <span class="font-medium">Client Notes:</span> {{ $order->client_delivery_notes }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                @elseif($displayStatus === 'disputed')
                    <!-- Disputed -->
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-medium text-red-800 dark:text-red-200">
                                    ⚠️ Delivery Disputed
                                </h4>
                                <p class="mt-1 text-sm text-red-700 dark:text-red-300">
                                    Client reported a delivery issue on {{ $order->dispute_reported_at->format('M j, Y \a\t g:i A') }}
                                </p>
                                
                                <!-- Dispute Reason -->
                                <div class="mt-3 p-3 bg-red-100 dark:bg-red-900/30 rounded border border-red-200 dark:border-red-800">
                                    <p class="text-sm text-red-700 dark:text-red-300">
                                        <span class="font-medium">Issue Reported:</span> {{ $order->dispute_reason }}
                                    </p>
                                </div>

                                <!-- Dispute Actions -->
                                <div class="mt-4 flex flex-col sm:flex-row gap-2">
                                    <button type="button" onclick="showResolveDisputeModal('acknowledge')"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.013 8.013 0 01-4.19-1.16l-3.81 1.16 1.16-3.81A8.013 8.013 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                                        </svg>
                                        Acknowledge Dispute
                                    </button>
                                    <button type="button" onclick="showResolveDisputeModal('resolve')"
                                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Resolve Dispute
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                @else
                    <!-- Other Status -->
                    <div class="bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                    Order Status: {{ ucfirst($order->status) }}
                                </h4>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    This order has not been delivered yet or delivery confirmation features are not applicable.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order Items ({{ $order->items->count() }})</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-neutral-700 rounded-lg">
                            <div class="flex items-center space-x-3">
                                @php
                                    $featuredImage = $item->product->images->where('is_featured', true)->first();
                                    $firstImage = $featuredImage ?: $item->product->images->first();
                                @endphp
                                
                                @if($firstImage)
                                    <img src="{{ asset('storage/' . $firstImage->image_path) }}" 
                                         alt="{{ $item->product->name }}"
                                         class="w-12 h-12 object-cover rounded-lg">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    </div>
                                @endif

                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Qty: {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Force Confirm Modal -->
    <div id="forceConfirmModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" onclick="closeForceConfirmModal()"></div>
            
            <div class="inline-block align-bottom bg-white dark:bg-neutral-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="POST" action="{{ route('admin.orders.force-confirm', $order) }}">
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
                                    Force Confirm Delivery
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        This will mark the order as completed without client confirmation. Please provide a reason.
                                    </p>
                                    <div class="mt-4">
                                        <label for="admin_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Reason <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="admin_reason" id="admin_reason" rows="3" required
                                                  placeholder="Explain why you are force-confirming this delivery..."
                                                  class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500"></textarea>
                                    </div>
                                    <div class="mt-4">
                                        <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Additional Notes (Optional)
                                        </label>
                                        <textarea name="admin_notes" id="admin_notes" rows="2"
                                                  placeholder="Any additional notes..."
                                                  class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-neutral-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Force Confirm
                        </button>
                        <button type="button" onclick="closeForceConfirmModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Resolve Dispute Modal -->
    <div id="resolveDisputeModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" onclick="closeResolveDisputeModal()"></div>
            
            <div class="inline-block align-bottom bg-white dark:bg-neutral-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="POST" action="{{ route('admin.orders.resolve-dispute', $order) }}" id="resolveDisputeForm">
                    @csrf
                    <input type="hidden" name="action" id="dispute_action">
                    <div class="bg-white dark:bg-neutral-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10" id="dispute_icon">
                                <!-- Icon will be set by JavaScript -->
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="dispute_title">
                                    <!-- Title will be set by JavaScript -->
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400" id="dispute_description">
                                        <!-- Description will be set by JavaScript -->
                                    </p>
                                    <div class="mt-4">
                                        <label for="admin_resolution" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Response <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="admin_resolution" id="admin_resolution" rows="3" required
                                                  placeholder="Your response to the dispute..."
                                                  class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500"></textarea>
                                    </div>
                                    <div class="mt-4">
                                        <label for="admin_notes_dispute" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Additional Notes (Optional)
                                        </label>
                                        <textarea name="admin_notes" id="admin_notes_dispute" rows="2"
                                                  placeholder="Any additional notes..."
                                                  class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-neutral-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" id="dispute_submit_btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                            <!-- Button text will be set by JavaScript -->
                        </button>
                        <button type="button" onclick="closeResolveDisputeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Force Confirm Modal
        function showForceConfirmModal() {
            document.getElementById('forceConfirmModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('admin_reason').focus(), 150);
        }

        function closeForceConfirmModal() {
            document.getElementById('forceConfirmModal').classList.add('hidden');
            document.getElementById('admin_reason').value = '';
            document.getElementById('admin_notes').value = '';
        }

        // Resolve Dispute Modal
        function showResolveDisputeModal(action) {
            const modal = document.getElementById('resolveDisputeModal');
            const actionInput = document.getElementById('dispute_action');
            const icon = document.getElementById('dispute_icon');
            const title = document.getElementById('dispute_title');
            const description = document.getElementById('dispute_description');
            const submitBtn = document.getElementById('dispute_submit_btn');

            actionInput.value = action;

            if (action === 'acknowledge') {
                icon.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 sm:mx-0 sm:h-10 sm:w-10';
                icon.innerHTML = '<svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.013 8.013 0 01-4.19-1.16l-3.81 1.16 1.16-3.81A8.013 8.013 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path></svg>';
                title.textContent = 'Acknowledge Dispute';
                description.textContent = 'Acknowledge the dispute and provide your response to the client. This will not resolve the dispute.';
                submitBtn.className = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm';
                submitBtn.textContent = 'Acknowledge';
            } else {
                icon.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/30 sm:mx-0 sm:h-10 sm:w-10';
                icon.innerHTML = '<svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                title.textContent = 'Resolve Dispute';
                description.textContent = 'Resolve the dispute and mark the order as completed. This will clear the dispute flag.';
                submitBtn.className = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm';
                submitBtn.textContent = 'Resolve';
            }

            modal.classList.remove('hidden');
            setTimeout(() => document.getElementById('admin_resolution').focus(), 150);
        }

        function closeResolveDisputeModal() {
            document.getElementById('resolveDisputeModal').classList.add('hidden');
            document.getElementById('admin_resolution').value = '';
            document.getElementById('admin_notes_dispute').value = '';
        }

        // Close modals on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeForceConfirmModal();
                closeResolveDisputeModal();
            }
        });
    </script>
    @endpush
</x-layouts.admin>