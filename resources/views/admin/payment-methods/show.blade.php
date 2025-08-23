<x-layouts.admin>
    <x-slot name="title">{{ $paymentMethod->name }} Details</x-slot>
    
    <div class="container mx-auto px-6 py-8">
        <div class="mb-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol role="list" class="flex items-center space-x-4">
                    <li>
                        <div>
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-home"></i>
                                <span class="sr-only">Home</span>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-300 mr-4"></i>
                            <a href="{{ route('admin.payment-methods.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Payment Methods</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-300 mr-4"></i>
                            <span class="ml-4 text-sm font-medium text-gray-500">{{ $paymentMethod->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="bg-white shadow rounded-lg">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            @if($paymentMethod->logo)
                                <img src="{{ $paymentMethod->logo_url }}" alt="{{ $paymentMethod->name }} logo" class="h-10 w-auto">
                            @else
                                <i class="fas fa-credit-card text-blue-500 text-lg"></i>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-lg font-medium text-gray-900 flex items-center space-x-2">
                                <span>{{ $paymentMethod->name }}</span>
                                @if($paymentMethod->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Inactive
                                    </span>
                                @endif
                            </h1>
                            <p class="text-sm text-gray-500">{{ App\Models\PaymentMethod::TYPES[$paymentMethod->type] ?? $paymentMethod->type }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <form action="{{ route('admin.payment-methods.toggle-status', $paymentMethod) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                @if($paymentMethod->is_active)
                                    <i class="fas fa-pause mr-2"></i>
                                    Deactivate
                                @else
                                    <i class="fas fa-play mr-2"></i>
                                    Activate
                                @endif
                            </button>
                        </form>
                        <a href="{{ route('admin.payment-methods.edit', $paymentMethod) }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-edit mr-2"></i>
                            Edit
                        </a>
                        <a href="{{ route('admin.payment-methods.index') }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Name:</span>
                                <span class="text-sm text-gray-900">{{ $paymentMethod->name }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Type:</span>
                                <span class="text-sm text-gray-900">{{ App\Models\PaymentMethod::TYPES[$paymentMethod->type] ?? $paymentMethod->type }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Status:</span>
                                @if($paymentMethod->is_active)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Inactive
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Sort Order:</span>
                                <span class="text-sm text-gray-900">{{ $paymentMethod->sort_order }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Created:</span>
                                <span class="text-sm text-gray-900">{{ $paymentMethod->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Updated:</span>
                                <span class="text-sm text-gray-900">{{ $paymentMethod->updated_at->format('M d, Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            @if($paymentMethod->isBankTransfer())
                                @if($paymentMethod->account_number)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Account Number:</span>
                                        <span class="text-sm text-gray-900 font-mono">{{ $paymentMethod->account_number }}</span>
                                    </div>
                                @endif
                                @if($paymentMethod->account_name)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Account Name:</span>
                                        <span class="text-sm text-gray-900">{{ $paymentMethod->account_name }}</span>
                                    </div>
                                @endif
                                @if($paymentMethod->bank_code)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Bank Code:</span>
                                        <span class="text-sm text-gray-900">{{ $paymentMethod->bank_code }}</span>
                                    </div>
                                @endif
                            @elseif($paymentMethod->isEWallet())
                                @if($paymentMethod->phone_number)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Phone Number:</span>
                                        <span class="text-sm text-gray-900 font-mono">{{ $paymentMethod->phone_number }}</span>
                                    </div>
                                @endif
                                @if($paymentMethod->account_name)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Account Name:</span>
                                        <span class="text-sm text-gray-900">{{ $paymentMethod->account_name }}</span>
                                    </div>
                                @endif
                            @else
                                <p class="text-sm text-gray-500 italic">No specific payment details configured.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Logo -->
                    @if($paymentMethod->logo)
                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Logo</h3>
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <img src="{{ $paymentMethod->logo_url }}" alt="{{ $paymentMethod->name }} logo" class="mx-auto h-24 w-auto">
                        </div>
                    </div>
                    @endif

                    <!-- Instructions -->
                    @if($paymentMethod->instructions)
                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Instructions</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-sm text-gray-700 whitespace-pre-line">{{ $paymentMethod->instructions }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Additional Info -->
                @if($paymentMethod->additional_info && count($paymentMethod->additional_info) > 0)
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <pre class="text-sm text-gray-700">{{ json_encode($paymentMethod->additional_info, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                @endif
            </div>

            <!-- Action Bar -->
            <div class="bg-gray-50 px-6 py-3 flex items-center justify-between rounded-b-lg">
                <div class="text-sm text-gray-500">
                    Last updated {{ $paymentMethod->updated_at->diffForHumans() }}
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="confirmDelete()" 
                            class="inline-flex items-center px-3 py-1.5 border border-red-300 text-xs leading-4 font-medium rounded text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-trash mr-1"></i>
                        Delete
                    </button>
                    <a href="{{ route('admin.payment-methods.edit', $paymentMethod) }}" 
                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs leading-4 font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-edit mr-1"></i>
                        Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Form -->
    <form id="delete-form" action="{{ route('admin.payment-methods.destroy', $paymentMethod) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this payment method? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
</x-layouts.admin>