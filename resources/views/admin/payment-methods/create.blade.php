<x-layouts.admin>
    <x-slot name="title">Add Payment Method</x-slot>
    
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
                            <span class="ml-4 text-sm font-medium text-gray-500">Add Method</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-plus-circle text-blue-500 text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-medium text-gray-900">Add New Payment Method</h1>
                            <p class="text-sm text-gray-500">Create a new payment method for customers to use</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.payment-methods.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.payment-methods.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-6">
                @csrf
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Basic Information -->
                    <div class="col-span-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                    </div>
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Payment Method Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                               placeholder="e.g., BCA Bank Transfer" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Payment Type</label>
                        <select name="type" id="type" 
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="">Select payment type</option>
                            @foreach(App\Models\PaymentMethod::TYPES as $value => $label)
                                <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bank Transfer Details -->
                    <div class="col-span-2 bank-details" style="display: none;">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Bank Transfer Details</h3>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                            <div>
                                <label for="account_number" class="block text-sm font-medium text-gray-700">Account Number</label>
                                <input type="text" name="account_number" id="account_number" value="{{ old('account_number') }}" 
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                       placeholder="1234567890">
                                @error('account_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="account_name" class="block text-sm font-medium text-gray-700">Account Name</label>
                                <input type="text" name="account_name" id="account_name" value="{{ old('account_name') }}" 
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                       placeholder="John Doe">
                                @error('account_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="bank_code" class="block text-sm font-medium text-gray-700">Bank Code</label>
                                <input type="text" name="bank_code" id="bank_code" value="{{ old('bank_code') }}" 
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                       placeholder="BCA" maxlength="10">
                                @error('bank_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- E-Wallet Details -->
                    <div class="col-span-2 ewallet-details" style="display: none;">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">E-Wallet Details</h3>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" 
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                       placeholder="+62812345678" maxlength="20">
                                @error('phone_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="ewallet_account_name" class="block text-sm font-medium text-gray-700">Account Name</label>
                                <input type="text" name="account_name" id="ewallet_account_name" value="{{ old('account_name') }}" 
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                       placeholder="John Doe">
                            </div>
                        </div>
                    </div>

                    <!-- Logo Upload -->
                    <div class="col-span-2">
                        <label for="logo" class="block text-sm font-medium text-gray-700">Payment Method Logo</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <div id="logo-preview" class="hidden">
                                    <img id="logo-image" class="mx-auto h-20 w-auto" alt="Logo preview">
                                </div>
                                <div id="logo-placeholder">
                                    <i class="fas fa-image text-gray-400 text-3xl"></i>
                                    <div class="text-sm text-gray-600">
                                        <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload a logo</span>
                                            <input id="logo" name="logo" type="file" class="sr-only" accept="image/*">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                        </div>
                        @error('logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Instructions -->
                    <div class="col-span-2">
                        <label for="instructions" class="block text-sm font-medium text-gray-700">Payment Instructions</label>
                        <textarea name="instructions" id="instructions" rows="4" 
                                  class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                  placeholder="Provide step-by-step instructions for customers...">{{ old('instructions') }}</textarea>
                        @error('instructions')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Settings -->
                    <div class="col-span-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Settings</h3>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="sort_order" class="block text-sm font-medium text-gray-700">Sort Order</label>
                                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" 
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                       min="0">
                                <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                                @error('sort_order')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" 
                                       {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Active (available for customers)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.payment-methods.index') }}" 
                       class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        Create Payment Method
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const bankDetails = document.querySelector('.bank-details');
            const ewalletDetails = document.querySelector('.ewallet-details');
            const logoInput = document.getElementById('logo');
            const logoPreview = document.getElementById('logo-preview');
            const logoImage = document.getElementById('logo-image');
            const logoPlaceholder = document.getElementById('logo-placeholder');

            // Show/hide details based on payment type
            typeSelect.addEventListener('change', function() {
                bankDetails.style.display = 'none';
                ewalletDetails.style.display = 'none';
                
                // Disable all fields in hidden sections to prevent conflicts
                bankDetails.querySelectorAll('input').forEach(input => input.disabled = true);
                ewalletDetails.querySelectorAll('input').forEach(input => input.disabled = true);

                if (this.value === 'bank_transfer') {
                    bankDetails.style.display = 'block';
                    bankDetails.querySelectorAll('input').forEach(input => input.disabled = false);
                } else if (this.value === 'e_wallet') {
                    ewalletDetails.style.display = 'block';
                    ewalletDetails.querySelectorAll('input').forEach(input => input.disabled = false);
                }
            });

            // Logo preview
            logoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        logoImage.src = e.target.result;
                        logoPreview.classList.remove('hidden');
                        logoPlaceholder.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Trigger type change for initial state
            typeSelect.dispatchEvent(new Event('change'));
        });
    </script>
</x-layouts.admin>